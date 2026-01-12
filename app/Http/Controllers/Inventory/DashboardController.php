<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\InventoryNotification;

class DashboardController extends Controller
{
    // ================= DASHBOARD =================
    public function index(Request $request)
    {
        $products = Product::query()
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addMonths(6))
            ->orderBy('expiry_date')
            ->paginate(15);

        $executives = User::role('Executive')->get();

        return view('inventory.dashboard', compact('products','executives'));
    }

    // ================= STORE PRODUCT =================
    public function storeTarget(Request $request)
    {
        try {
            $request->validate([
                'name'        => 'required|string|max:255',
                'expiry_date' => 'nullable|date',
                'composition' => 'nullable|string|max:255',
                'type'        => 'required|in:expiry,new',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $imagePath = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

            $product = Product::create([
                'name'         => $request->name,
                'expiry_date'  => $request->expiry_date,
                'composition'  => $request->composition,
                'type'         => $request->type,
                'image'        => $imagePath,
            ]);

            // Fetch admins inside method
            $admins = User::role('Admin')->get();

            foreach ($admins as $admin) {
                try {
                    $admin->notify(new InventoryNotification(
                        auth()->user()->name.' added a new product: ' . $product->name,
                        route('inventory.products', $product->id)
                    ));

                } catch (\Exception $e) {
                    \Log::error('InventoryNotification failed', [
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }



            return response()->json([
                'success' => true,
                'message' => 'Product added successfully!',
                'product' => $product
            ]);

        } catch (\Exception $e) {
            // Return JSON with error message instead of HTML
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ================= NOTIFY ADMIN =================
    public function notifyAdmin(Product $product)
    {
        abort_if(!$product->expiry_date, 404);

        if ($product->notified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Admin already notified'
            ]);
        }

        // Update notified_at column
        $product->update(['notified_at' => now()]);

        // Notify admins safely
        $admins = User::role('Admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new InventoryNotification(
                    auth()->user()->name.' flagged product for expiry: '.$product->name,
                    route('inventory.products', $product->id)
                ));
            } catch (\Exception $e) {
                \Log::error('NotifyAdmin failed: '.$e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin notified successfully'
        ]);
    }
    public function targets_listing()
    {
        // Load targets with product and executive, 10 per page
        $targets = Target::with(['product', 'executive', 'sales'])
                         ->whereNull('parent_id')
                         ->latest()
                         ->paginate(10); // change 10 to any number of rows per page

        return view('inventory.targets', compact('targets'));
    }

    // Show details of a single product with targets and sales
    public function productDetails($id)
    {
        $product = Product::with([
                    'targets' => function ($query) {
                        // $query->where('created_by', auth()->id()); //target of one layer
                    },
                    'targets.executive',
                    'targets.sales'
                ])->findOrFail($id);
        return view('inventory.product_details', compact('product'));
    }
    public function reports(Request $request)
    {
        $products = Product::with(['targets.sales', 'targets.executive'])
            ->latest()
            ->paginate(12);

        // Aggregate totals
        $totalTarget = $products->sum(function($product) {
            return $product->targets->sum('target_value');
        });

        $achieved = $products->sum(function($product) {
            return $product->targets->sum(function($target){
                return $target->target_type === 'box'
                    ? $target->sales->sum('boxes_sold')
                    : $target->sales->sum('amount');
            });
        });

        $remaining = max($totalTarget - $achieved, 0);

        $totalSales = $products->sum(function($product){
            return $product->targets->sum(function($target){
                return $target->target_type === 'box'
                    ? $target->sales->sum('boxes_sold')
                    : $target->sales->sum('amount');
            });
        });

        return view('inventory.reports', compact('products', 'totalTarget', 'achieved', 'remaining', 'totalSales'));
    }
}
