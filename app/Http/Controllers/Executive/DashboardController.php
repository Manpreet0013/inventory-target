<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TargetAssignment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $executive = auth()->user();

        $assignments = TargetAssignment::with('target.product')
            ->where('executive_id', $executive->id)
            ->get();

        $targets = Target::where('executive_id', $executive->id)
            //->whereNull('parent_id')
            ->with(['product', 'children.executive'])
            ->paginate(10);

        return view('executive.dashboard', compact(
            'assignments',
            'targets',
            'executive'
        ));
    }

    public function show(Target $target)
    {
        // Security: only owner executive can view
        if ($target->executive_id !== auth()->id()) {
            abort(403);
        }

        $target->load(['product', 'sales']);

        return view('executive.target-details', compact('target'));
    }

    public function accept(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);

        if ($target->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Target already processed'
            ], 422);
        }

        $target->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Target accepted successfully'
        ]);
    }

    public function reject(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);

        $target->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Target rejected successfully'
        ]);
    }
    public function createSale(Target $target)
    {
        // Security: target must belong to logged-in executive
        abort_if($target->executive_id !== auth()->id(), 403);

        return view('executive.sales.create', compact('target'));
    }



    public function storeSale(Request $request)
    {
        // Validate request
        $request->validate([
            'target_id'   => 'required|exists:targets,id',
            'boxes_sold'  => 'nullable|integer|min:1',
            'amount'      => 'nullable|numeric|min:1',
            'party_name'  => 'required|string|max:255',
            'sale_date'   => 'required|date',
        ]);

        // Get target and ensure it belongs to logged-in executive
        $target = Target::with('sales')
            ->where('id', $request->target_id)
            ->where('executive_id', auth()->id())
            ->firstOrFail();

        // Check if sale date is within target period
        if (!is_null($target->end_date)) {
            if ($request->sale_date < $target->start_date || $request->sale_date > $target->end_date) {
                return response()->json([
                    'success' => false,
                    'errors' => ['sale_date' => ['Sale date must be within target period']]
                ], 422);
            }
        }
        // Determine target type and remaining value
        $field = $target->target_type === 'box' ? 'boxes_sold' : 'amount';
        $value = $request->$field ?? 0;

        $remaining = $target->remainingValue();


        if ($value > $remaining) {
            return response()->json([
                'success' => false,
                'errors' => [$field => ["Value cannot exceed remaining target ($remaining)"]]
            ], 422);
        }

        if ($value <= 0) {
            return response()->json([
                'success' => false,
                'errors' => [$field => ["Value must be at least 1"]]
            ], 422);
        }

        // Store sale
        $sale = Sale::create([
            'target_id'    => $target->id,
            'boxes_sold'   => $target->target_type === 'box' ? $value : null,
            'amount'       => $target->target_type === 'amount' ? $value : null,
            'party_name'   => $request->party_name,
            'sale_date'    => $request->sale_date,
            'status'       => 'pending',
            'executive_id' => auth()->id(),
        ]);

        $admins = User::role('Admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SaleAddedNotification($sale));
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale added successfully!'
        ]);
    }

    public function reassign(Request $request, Target $target)
    {
        if ($target->executive_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'executive_id' => 'required|exists:users,id',
        ]);

        $newExec = User::find($request->executive_id);

        if ($newExec->company_id != auth()->user()->company_id) {
            abort(403, 'Cannot assign outside your company');
        }

        $target->executive_id = $newExec->id;
        $target->save();

        return back()->with('success', 'Target reassigned successfully!');
    }

    public function split(Request $request, Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);

        // Child targets cannot split
        if ($target->parent_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This target cannot be split further'
            ], 403);
        }

        // Must be accepted before split
        if ($target->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Accept target before splitting'
            ], 422);
        }

        $request->validate([
            'executive_id' => 'required|exists:users,id',
            'value'        => 'required|integer|min:1'
        ]);

        // Remaining validation (MODEL LOGIC)
        if ($request->value > $target->remainingValue()) {
            return response()->json([
                'success' => false,
                'message' => 'Value exceeds remaining target'
            ], 422);
        }

        $newExec = User::findOrFail($request->executive_id);

        if ($newExec->company_id !== auth()->user()->company_id) {
            abort(403, 'Outside company assignment blocked');
        }

        Target::create([
            'product_id'   => $target->product_id,
            'executive_id' => $newExec->id,
            'target_type'  => $target->target_type,
            'target_value' => $request->value,
            'start_date'   => $target->start_date,
            'end_date'     => $target->end_date,
            'status'       => 'pending',
            'parent_id'    => $target->id,
            'created_by'   => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Target split successfully'
        ]);
    }
    public function report(Request $request)
    {
        $executiveId = auth()->id();

        // Get all targets assigned to this executive
        $targets = Target::with([
            'product',
            'sales' => function($q) use ($executiveId) {
                // Only sales of logged-in executive and approved
                $q->where('executive_id', $executiveId)
                  ->where('status', 'approved'); 
            },
            'creator'
        ])
        ->where('executive_id', $executiveId)
        ->get();

        return view('executive.report', compact('targets'));
    }


}
