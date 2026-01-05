<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        // Show only sales that are approved by admin but pending accountant approval
        $sales = Sale::with('target.product')
            // ->where('status', 'approved')
            // ->where('accountant_status', 'pending')
            ->get();

        return view('accountant.dashboard', compact('sales'));
    }

    public function approve(Sale $sale)
    {
        $sale->update(['accountant_status' => 'approved']);
        return response()->json(['success' => true]);
    }

    public function reject(Sale $sale)
    {
        $sale->update(['accountant_status' => 'rejected']);
        return response()->json(['success' => true]);
    }
}