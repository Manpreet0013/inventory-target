<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExpiryProductNotification;
use App\Notifications\TargetCreatedNotification;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addMonths(6))
            ->orderBy('expiry_date')
            ->paginate(15); // ✅ pagination for future data

        $executives = User::role('Executive')->get();

        return view('inventory.dashboard', compact('products','executives'));
    }

    public function notifyAdmin(Product $product)
    {
        // 🔒 extra safety
        abort_if(!$product->expiry_date, 404);

        // Optional: prevent duplicate notifications
        if ($product->notified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Admin already notified'
            ]);
        }

        $admins = User::role('Admin')->get();

        Notification::send($admins, new ExpiryProductNotification($product));

        // Optional column (recommended)
        $product->update(['notified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Admin notified successfully'
        ]);
    }
    public function storeTarget(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'target_type'  => 'required|in:box,amount',
            'target_value' => 'required|integer|min:1',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
        ]);

        $product = \App\Models\Product::firstOrCreate(
                        ['name' => $request->product_name], // search by name
                        [
                            'type' => 'expiry',               // set type
                            'expiry_date' => $request->end_date, // set expiry_date
                        ]
                    );


        // Enforce one target per product
        if ($product->targets()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Target already exists for this product'
            ]);
        }

        $target = \App\Models\Target::create([
            'product_id'   => $product->id,
            'target_type'  => $request->target_type,
            'target_value' => $request->target_value,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status'       => 'pending',
            'executive_id' => $request->executive_id,
            'created_by'   => auth()->id(),
        ]);

        // Notify Admin
        $admins = \App\Models\User::role('Admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\TargetCreatedNotification($target));

        return response()->json([
            'success' => true,
            'message' => 'Target created successfully!'
        ]);
    }
    public function targets_listing()
    {
        $targets = Target::with(['product', 'executive'])->whereNull('parent_id')->latest()->get();
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
