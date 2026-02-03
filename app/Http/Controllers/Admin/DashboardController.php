<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Target;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Notifications\InventoryNotification;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // =========================
        // PRODUCTS
        // =========================
        $totalProducts = Product::count();

        $targetSetProducts = Product::whereHas('targets', function ($q) {
            $q->whereNull('parent_id');
        })->count();

        $targetNotSetProducts = $totalProducts - $targetSetProducts;

        // =========================
        // TARGETS (USE SAME LOGIC AS TABLE)
        // =========================
        $targets = Target::with('sales')
            ->whereNull('parent_id')
            ->get();

        $totalTargets     = $targets->count();
        $currentTargets   = 0;
        $expiredTargets   = 0;
        $achievedFully    = 0;
        $achievedPartial  = 0;

        foreach ($targets as $target) {

            // ===== TARGET EXPIRED =====
            if ($target->end_date && $today->gt(Carbon::parse($target->end_date))) {
                $expiredTargets++;
                continue;
            }

            $currentTargets++;

            // ===== SAME VALUE COLUMN LOGIC =====
            $valueColumn = $target->target_type === 'amount'
                ? 'amount'
                : 'boxes_sold';

            // ===== VALID SALES =====
            $validSales = $target->sales->filter(function ($sale) {
                return $sale->status === 'approved'
                    && $sale->accountant_status === 'approved';
            });

            $totalSales = $validSales->sum($valueColumn);

            // ===== EXECUTIVE PARTICIPATION =====
            $executiveCount = $target->executives_count ?? 1;
            $perExecutiveTarget = $target->target_value / max($executiveCount, 1);

            $executivesMetTarget = $validSales
                ->groupBy('executive_id')
                ->filter(fn ($sales) => $sales->sum($valueColumn) >= $perExecutiveTarget)
                ->count();

            // ===== ACHIEVEMENT STATUS =====
            if ($totalSales >= $target->target_value) {

                if ($executivesMetTarget == $executiveCount) {
                    $achievedFully++;
                } else {
                    $achievedPartial++;
                }

            }
        }

        return view('admin.dashboard', compact(
            'totalTargets',
            'currentTargets',
            'expiredTargets',
            'achievedFully',
            'achievedPartial',
            'totalProducts',
            'targetSetProducts',
            'targetNotSetProducts'
        ));
    }

    public function products()
    {
        return view('admin.products.index', [
            'products' => Product::latest()->get()
        ]);
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'composition' => 'required|string',
            'type' => 'required|in:expiry,new',
            'expiry_date' => 'nullable|date|required_if:type,expiry',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product saved successfully'
        ]);
    }


    public function targets()
    {
        $products = Product::whereDoesntHave('targets') // NO targets at all
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->get();

        $executives = User::role('Executive')->get();

        return view('admin.targets.index', compact('products', 'executives'));
    }

    

    public function storeTarget(Request $request)
    {
        $validated = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'executive_id' => 'required|exists:users,id',
            'target_type'  => 'required|in:box,amount',
            'target_value' => 'required|integer|min:1',
        ]);

        $product = Product::with('targets.sales')->find($validated['product_id']);

        // product not found (extra safety)
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // expired product
        if ($product->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is expired.'
            ], 422);
        }

        // block duplicate MAIN target
        $existingTarget = Target::where('product_id', $product->id)
            ->whereNull('parent_id')
            ->exists();

        if ($existingTarget) {
            return response()->json([
                'success' => false,
                'message' => 'A target is already assigned for this product.'
            ], 422);
        }

        //  create MAIN target
        $target = Target::create([
            'product_id'   => $product->id,
            'executive_id' => $validated['executive_id'],
            'target_type'  => $validated['target_type'],
            'target_value' => $validated['target_value'],
            'start_date'   => $product->created_at->toDateString(),
            'end_date'     => $product->expiry_date,
            'parent_id'    => null,
            'created_by'   => auth()->id(),
            'status'       => 'pending',
        ]);

        $executive = User::find($validated['executive_id']);

        if ($executive) {
            $executive->notify(new InventoryNotification(
                'New target assigned for ' . $product->name,
                route('executive.targets.managed', $target->id)
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Target assigned successfully!',
            'product_id' => $product->id
        ]);
    }

    // List all products
    public function product_listing()
    {
        $products =  Product::latest()->get();
        return view('admin.products.products', compact('products'));
    }

   // Show product details with targets and sales
    public function productDetails($id)
    {
        $product = Product::with([
            // Only parent targets
            'targets' => fn($q) => $q->whereNull('parent_id'),
            'targets.sales',      // parent sales
            'targets.executive',  // parent executive
            'targets.children' => fn($q) => $q->with('executive','sales') // child targets
        ])->findOrFail($id);

        return view('admin.products.product_details', compact('product'));
    }
    public function saleListing(Product $product, Target $target)
    {
        $target->load([
            'executive',          // parent executive
            'sales',              // parent sales
            'children.executive', // child executives
            'children.sales'      // child sales
        ]);

        $childTargets = $target->children; // eager-loaded

        return view('admin.products.product_details', compact('product','target','childTargets'));
    }

    public function targets_listing(Request $request)
    {
        $statusFilter = $request->status;
        $today = Carbon::today();

        $targets = Target::with([
                'product',
                'executive',
                'sales' => function ($q) {
                    $q->where('status', 'approved')
                      ->where('accountant_status', 'approved');
                }
            ])
            ->whereNull('parent_id')
            ->latest()
            ->get()
            ->filter(function ($target) use ($statusFilter, $today) {

                if (!$statusFilter) {
                    return true; // no filter applied
                }

                // Expired
                if ($today->gt(Carbon::parse($target->end_date))) {
                    return $statusFilter === 'expired';
                }

                $valueColumn = $target->target_type === 'amount' ? 'amount' : 'boxes_sold';
                $totalSales = $target->sales->sum($valueColumn);

                $executiveCount = $target->executives_count ?? 1;
                $perExecutiveTarget = $target->target_value / max($executiveCount, 1);

                $executivesMetTarget = $target->sales
                    ->groupBy('executive_id')
                    ->filter(fn ($sales) => $sales->sum($valueColumn) >= $perExecutiveTarget)
                    ->count();

                if ($totalSales >= $target->target_value) {
                    if ($executivesMetTarget == $executiveCount) {
                        return $statusFilter === 'achieved_full';
                    }
                    return $statusFilter === 'achieved_partial';
                }

                return $statusFilter === 'not_achieved';
            });

        return view('admin.targets.targets', compact('targets', 'statusFilter'));
    }

    public function sales(Request $request)
    {
        $status        = $request->status;
        $targetId      = $request->target_id;
        $executiveId   = $request->executive_id;

        // BASE QUERY
        $salesQuery = Sale::with(['executive', 'target.product'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($targetId, fn ($q) => $q->where('target_id', $targetId))
            ->when($executiveId, fn ($q) => $q->where('executive_id', $executiveId));

        // STATS (respect filters)
        $stats = [
            'total'    => (clone $salesQuery)->count(),
            'approved' => (clone $salesQuery)->where('status','approved')->sum('amount'),
            'pending'  => (clone $salesQuery)->where('status','pending')->count(),
            'rejected' => (clone $salesQuery)->where('status','rejected')->count(),
        ];

        // SALES DATA
        $sales = $salesQuery
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // FILTER DATA
        $targets = Target::with('product')
                    ->whereNull('parent_id') // ONLY ADMIN / MAIN TARGETS
                    ->latest()
                    ->get();
        $executives = User::role('Executive')->get();

        return view('admin.sales.index', compact(
            'sales',
            'status',
            'stats',
            'targets',
            'executives'
        ));
    }

    public function bulkApprove(Request $request)
    {
        Sale::whereIn('id', $request->sales ?? [])
            ->update(['status' => 'approved']);

        return back()->with('success','Selected sales approved');
    }
    public function export(Request $request)
    {
        $sales = Sale::with(['executive','target.product'])
            ->when($request->status, fn ($q) => $q->where('status',$request->status))
            ->when($request->target_id, fn ($q) => $q->where('target_id',$request->target_id))
            ->when($request->executive_id, fn ($q) => $q->where('executive_id',$request->executive_id))
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=sales.csv",
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Invoice',
                'Party',
                'Target',
                'Target Type',
                'Executive',
                'Amount / Qty',
                'Status',
                'Sale Date'
            ]);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->invoice_number,
                    $sale->party_name,
                    $sale->target?->product?->name,
                    $sale->target?->target_type,
                    $sale->executive?->name,
                    $sale->amount,
                    $sale->status,
                    $sale->sale_date
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $sale->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'status'  => $sale->status
        ]);
    }
    public function notification()
    {
        $notifications = Auth::user()->notifications()->latest()->get(); 
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product and related targets deleted successfully.');
    }

}
