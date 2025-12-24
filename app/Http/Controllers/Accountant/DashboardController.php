<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $sales = Sale::with('target.product')
            ->where('status','pending')
            ->get();

        return view('accountant.dashboard', compact('sales'));
    }

    public function approve(Sale $sale)
    {
        $sale->update(['status'=>'approved']);
        return response()->json(['success'=>true]);
    }

    public function reject(Sale $sale)
    {
        $sale->update(['status'=>'rejected']);
        return response()->json(['success'=>true]);
    }
}
