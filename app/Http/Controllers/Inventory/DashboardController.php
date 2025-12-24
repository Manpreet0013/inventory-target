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
    public function index()
    {
        $products = Product::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addMonths(6))
            ->orderBy('expiry_date')
            ->get();

        return view('inventory.dashboard', compact('products'));
    }

    public function notifyAdmin($id)
    {
        $product = Product::findOrFail($id);

        $admins = User::role('Admin')->get();

        Notification::send($admins, new ExpiryProductNotification($product));

        return response()->json(['success' => true]);
    }
}
