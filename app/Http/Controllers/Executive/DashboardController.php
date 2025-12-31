<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard landing
    public function index()
    {
        return view('executive.dashboard');
    }
    public function managedTargets()
    {
        $executiveId = auth()->id();

        $targets = Target::with(['product','children','sales'])
            ->whereNull('parent_id')
            ->where(function ($q) use ($executiveId) {
                $q->where('executive_id', $executiveId)
                  ->orWhere('created_by', $executiveId);
            })
            ->latest()
            ->paginate(10);

        return view('executive.targets.managed', compact('targets'));
    }
    public function assignedTargets()
    {
        $executiveId = auth()->id();

        $assignedTargets = Target::where('executive_id', $executiveId)
            ->whereNotNull('parent_id')
            ->whereHas('creator', fn ($q) => $q->role('Executive'))
            ->with(['product','creator'])
            ->latest()
            ->paginate(10);

        return view('executive.targets.assigned', compact('assignedTargets'));
    }
    // View sales for a target
    public function sales(Target $target)
    {
        abort_if(
            $target->executive_id !== auth()->id() &&
            $target->created_by !== auth()->id(),
            403
        );

        $target->load(['sales.executive', 'children.sales.executive']);

        return view('executive.target-sales', compact('target'));
    }

    // Split target view
    public function splitView(Target $target)
    {
        $executives = User::role('Executive')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        return view('executive.target-split', compact('target', 'executives'));
    }

    // Show target details
    public function show(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);
        $target->load(['product', 'sales', 'children.sales']);
        return view('executive.target-details', compact('target'));
    }

    // Accept target
    public function accept(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);

        if ($target->status !== 'pending') {
            return response()->json(['success'=>false, 'message'=>'Target already processed'], 422);
        }

        $target->update(['status'=>'accepted']);

        return response()->json(['success'=>true, 'message'=>'Target accepted successfully']);
    }

    // Reject target
    public function reject(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);

        $target->update(['status'=>'rejected']);

        return response()->json(['success'=>true, 'message'=>'Target rejected successfully']);
    }

    // Create sale form
    public function createSale(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);
        return view('executive.sales.create', compact('target'));
    }

    // Store sale
    public function storeSale(Request $request)
    {
        $request->validate([
            'target_id'=>'required|exists:targets,id',
            'boxes_sold'=>'nullable|integer|min:1',
            'amount'=>'nullable|numeric|min:1',
            'party_name'=>'required|string|max:255',
            'sale_date'=>'required|date',
        ]);

        $target = Target::with('sales')
            ->where('id', $request->target_id)
            ->where('executive_id', auth()->id())
            ->firstOrFail();

        $field = $target->target_type==='box' ? 'boxes_sold' : 'amount';
        $value = $request->$field ?? 0;

        if ($value > $target->remainingValue() || $value <= 0) {
            return response()->json(['success'=>false, 'errors'=>[$field=>["Invalid value"]]],422);
        }

        $sale = Sale::create([
            'target_id'=>$target->id,
            'boxes_sold'=>$target->target_type==='box' ? $value : null,
            'amount'=>$target->target_type==='amount' ? $value : null,
            'party_name'=>$request->party_name,
            'sale_date'=>$request->sale_date,
            'status'=>'pending',
            'executive_id'=>auth()->id(),
        ]);

        // Notify admins
        User::role('Admin')->each(fn($admin)=>$admin->notify(new \App\Notifications\SaleAddedNotification($sale)));

        return response()->json(['success'=>true,'message'=>'Sale added successfully']);
    }

    // Reassign target
    public function reassign(Request $request, Target $target)
    {
        abort_if($target->executive_id !== auth()->id(),403);

        $request->validate(['executive_id'=>'required|exists:users,id']);

        $newExec = User::findOrFail($request->executive_id);
        abort_if($newExec->company_id !== auth()->user()->company_id,403);

        $target->update(['executive_id'=>$newExec->id]);

        return back()->with('success','Target reassigned successfully!');
    }

    // Split target
    public function split(Request $request, Target $target)
    {
        abort_if($target->executive_id !== auth()->id(),403);
        abort_if($target->parent_id !== null,403,"Cannot split child target");
        abort_if($target->status !== 'accepted',422,"Accept target before splitting");

        $request->validate([
            'executive_id'=>'required|exists:users,id',
            'value'=>'required|integer|min:1'
        ]);

        abort_if($request->value > $target->remainingValue(),422,"Value exceeds remaining target");

        $newExec = User::findOrFail($request->executive_id);
        abort_if($newExec->company_id !== auth()->user()->company_id,403);

        Target::create([
            'product_id'=>$target->product_id,
            'executive_id'=>$newExec->id,
            'target_type'=>$target->target_type,
            'target_value'=>$request->value,
            'start_date'=>$target->start_date,
            'end_date'=>$target->end_date,
            'status'=>'pending',
            'parent_id'=>$target->id,
            'created_by'=>auth()->id()
        ]);

        return response()->json(['success'=>true,'message'=>'Target split successfully']);
    }

    // Executive report with filters
    public function report(Request $request)
    {
        $executiveId = auth()->id();
        $from = $request->from;
        $to = $request->to;

        $targetsQuery = Target::with([
            'product',
            'sales'=>fn($q)=>$q->where('executive_id',$executiveId)->where('status','approved')
                ->when($from, fn($q)=>$q->whereDate('sale_date','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('sale_date','<=',$to)),
            'children.sales.executive',
            'creator'
        ])->where('executive_id',$executiveId)
          ->when($from, fn($q)=>$q->whereDate('start_date','>=',$from))
          ->when($to, fn($q)=>$q->whereDate('end_date','<=',$to));

        $targets = $targetsQuery->get();

        return view('executive.report', compact('targets','from','to'));
    }
}
