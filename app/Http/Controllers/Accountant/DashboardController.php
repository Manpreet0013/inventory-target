<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\InventoryNotification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $admins;

    public function __construct()
    {
        // Load admins once
        $this->admins = User::role('Admin')->get();
    }

    public function index()
    {
        // Show only admin-approved sales pending accountant action
        $sales = Sale::with('target.product')
            ->where('status', 'approved')
            //->whereNull('accountant_status') // or ->where('accountant_status','pending')
            ->orderByDesc('created_at')   // latest on top
            ->paginate(10);

        return view('accountant.dashboard', compact('sales'));
    }

    public function approve(Sale $sale)
    {
        // Safety check
        abort_if($sale->accountant_status === 'approved', 422, 'Already approved');

        $sale->update([
            'accountant_status' => 'approved'
        ]);

        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                auth()->user()->name . ' approved a sale',
                route('admin.sales.index')
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale approved successfully'
        ]);
    }

    public function reject(Sale $sale)
    {
        abort_if($sale->accountant_status === 'rejected', 422, 'Already rejected');

        $sale->update([
            'accountant_status' => 'rejected'
        ]);

        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                auth()->user()->name . ' rejected a sale',
                route('admin.sales.index')
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale rejected successfully'
        ]);
    }
}
