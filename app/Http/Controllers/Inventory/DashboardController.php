<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExpiryProductNotification;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addMonths(6))
            ->orderBy('expiry_date')
            ->paginate(15); // ✅ pagination for future data

        return view('inventory.dashboard', compact('products'));
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
}
