<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================
        // MAIN TARGETS ONLY
        // =====================
        $mainTargets = Target::whereNull('parent_id');

        // Summary
        $totalProducts    = Product::count();
        $totalExecutives  = User::role('Executive')->count();
        $totalTargets     = $mainTargets->count();

        $activeTargets = (clone $mainTargets)->where('status', 'accepted')->count();
        $pendingTargets = (clone $mainTargets)->where('status', 'pending')->count();
        $expiredTargets = (clone $mainTargets)->where('status', 'rejected')->count();

        // Latest MAIN Targets
        $latestTargets = Target::with(['product', 'executive', 'creator'])
            ->whereNull('parent_id')
            ->latest()
            ->take(5)
            ->get();

        // Chart: Status
        $chartStatusData = [
            'active'  => $activeTargets,
            'pending' => $pendingTargets,
            'expired' => $expiredTargets,
        ];

        // Chart: Targets by Product (MAIN ONLY)
        $targetsByProduct = Product::withCount([
            'targets as targets_count' => function ($q) {
                $q->whereNull('parent_id');
            }
        ])->pluck('targets_count', 'name');

        // Chart: Targets by Executive (MAIN ONLY)
        $targetsByExecutive = User::role('Executive')
            ->withCount([
                'targets as targets_count' => function ($q) {
                    $q->whereNull('parent_id');
                }
            ])
            ->pluck('targets_count', 'name');

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalExecutives',
            'totalTargets',
            'activeTargets',
            'expiredTargets',
            'pendingTargets',
            'latestTargets',
            'chartStatusData',
            'targetsByProduct',
            'targetsByExecutive'
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

        $target->executive->notify(
            new \App\Notifications\TargetAssignedNotification($target)
        );

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

    // Show details of a single product with targets and sales
    public function productDetails($id)
    {
        $product = Product::with([
                    'targets' => function ($query) {
                        $query->where('created_by', auth()->id()); //target of one layer
                    },
                    'targets.executive',
                    'targets.sales'
                ])->findOrFail($id);
        return view('admin.products.product_details', compact('product'));
    }

    public function targets_listing()
    {
        $targets = Target::with(['product', 'executive'])->where(['created_by'=>auth()->id()])->latest()->get();
        return view('admin.targets.targets', compact('targets'));
    }

    public function saleListing(Product $product, Target $target)
    {
        // Load main target sales
        $target->load(['executive', 'sales']);

        // Load child targets recursively (only children of this target)
        $childTargets = Target::with(['executive', 'sales', 'children.executive', 'children.sales'])
            ->where('parent_id', $target->id)
            ->get();

        return view('admin.sales.details', compact('product', 'target', 'childTargets'));
    }



}
