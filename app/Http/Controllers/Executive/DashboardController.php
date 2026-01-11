<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Notifications\InventoryNotification;

class DashboardController extends Controller
{
    protected $admins;

    public function __construct()
    {
        $this->admins = User::role('Admin')->get();
    }

    // ================= DASHBOARD =================
    public function index()
    {
        return view('executive.dashboard');
    }

    // ================= MANAGED TARGETS =================
    public function managedTargets()
    {
        $executiveId = auth()->id();

        $targets = Target::with(['product','children','sales'])
            ->whereNull('parent_id')
            ->where('status','!=','rejected')
            ->where(function ($q) use ($executiveId) {
                $q->where('executive_id',$executiveId)
                  ->orWhere('created_by',$executiveId);
            })
            ->latest()
            ->paginate(10);

        return view('executive.targets.managed', compact('targets'));
    }

    // ================= ASSIGNED TARGETS =================
    public function assignedTargets()
    {
        $executiveId = auth()->id();

        $assignedTargets = Target::where('executive_id',$executiveId)
            ->whereNotNull('parent_id')
            ->whereHas('creator', fn ($q) => $q->role('Executive'))
            ->with(['product','creator'])
            ->latest()
            ->paginate(10);

        return view('executive.targets.assigned', compact('assignedTargets'));
    }

    // ================= ACCEPT PARTIAL =================
    public function partialAccept(Request $request, Target $target)
    {
        abort_if($target->executive_id !== auth()->id(),403);
        abort_if($target->status !== 'pending',422);

        $request->validate([
            'accepted_value'=>'required|integer|min:1|max:'.$target->target_value
        ]);

        $target->update(['status'=>'rejected']);

        Target::create([
            'product_id'=>$target->product_id,
            'executive_id'=>$target->executive_id,
            'target_type'=>$target->target_type,
            'target_value'=>$request->accepted_value,
            'start_date'=>$target->start_date,
            'end_date'=>$target->end_date,
            'status'=>'accepted',
            'parent_id'=>$target->id,
            'created_by'=>$target->created_by,
        ]);

        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                auth()->user()->name.' accepted the target'
            ));
        }

        return response()->json(['success'=>true]);
    }

    // ================= SALES LIST =================
    public function sales(Target $target)
    {
        abort_if(
            $target->executive_id !== auth()->id() &&
            $target->created_by !== auth()->id(),
            403
        );

        $target->load(['sales.executive','children.sales.executive']);

        return view('executive.target-sales', compact('target'));
    }

    // ================= SPLIT VIEW =================
    public function splitView(Target $target)
    {
        if ($target->parent_id !== null) {
            $target = Target::findOrFail($target->parent_id);
        }

        $target->load([
            'children.executive',
            'children.sales'
        ]);

        // 🔥 GROUP BY EXECUTIVE
        $team = $target->children
            ->groupBy('executive_id')
            ->map(function ($rows) {
                return [
                    'executive' => $rows->first()->executive,
                    'assigned'  => $rows->sum('target_value'),
                    'achieved'  => $rows->sum(fn ($t) => $t->achievedValue()),
                    'remaining' => $rows->sum(fn ($t) => $t->remainingValue()),
                    'status'    => $rows->pluck('status')->contains('pending')
                                        ? 'pending'
                                        : 'accepted',
                ];
            });

        $executives = User::role('Executive')
            ->where('id', '!=', auth()->id())
            ->get();

        return view('executive.target-split', compact('target', 'executives', 'team'));
    }

    // ================= SHOW TARGET =================
    public function show(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(),403);

        $target->load(['product','sales','children.sales']);

        return view('executive.target-details', compact('target'));
    }

    // ================= ACCEPT TARGET =================
    public function accept(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(),403);

        if ($target->status !== 'pending') {
            return response()->json(['success'=>false],422);
        }

        $target->update(['status'=>'accepted']);

        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                auth()->user()->name.' accepted the target',
                route('admin.products.details',$target->product_id)
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Target accepted successfully'
        ]);

    }

    // ================= REJECT TARGET =================
    public function reject(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(),403);

        $target->update(['status'=>'rejected']);

        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                auth()->user()->name.' rejected the target',
                route('admin.products.details',$target->product_id)
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Target rejected successfully'
        ]);

    }

    // ================= CREATE SALE =================
    public function createSale(Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);

        $target->load(['sales.executive']);

        return view('executive.sales.create', compact('target'));
    }

    // ================= STORE SALE =================
    public function storeSale(Request $request)
    {
        $request->validate([
            'target_id'=>'required|exists:targets,id',
            'boxes_sold'=>'nullable|integer|min:1',
            'amount'=>'nullable|numeric|min:1',
            'party_name'=>'required|string|max:255',
            'sale_date'=>'required|date',
            'invoice_number'=>'nullable|string|max:50|unique:sales,invoice_number',
        ]);

        $target = Target::with('sales')
            ->where('id',$request->target_id)
            ->where('executive_id',auth()->id())
            ->firstOrFail();

        $status = 'pending';
        if($target->parent_id === NULL){
            $status = 'approved';
        }

        $field = $target->target_type === 'box' ? 'boxes_sold' : 'amount';
        $value = $request->$field;

        abort_if($value <= 0 || $value > $target->remainingValue(),422);

        Sale::create([
            'target_id'=>$target->id,
            'boxes_sold'=>$target->target_type==='box' ? $value : null,
            'amount'=>$target->target_type==='amount' ? $value : null,
            'party_name'=>$request->party_name,
            'sale_date'=>$request->sale_date,
            'status'=>$status,
            'executive_id'=>auth()->id(),
            'invoice_number'=>$request->invoice_number,
        ]);

        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                auth()->user()->name.' added a new sale',
                route('admin.sales.index')
            ));
        }

        return response()->json(['success'=>true]);
    }

    // ================= SPLIT TARGET =================
    public function split(Request $request, Target $target)
    {
        abort_if($target->executive_id !== auth()->id(), 403);
        abort_if($target->parent_id !== null, 403);
        abort_if($target->status !== 'accepted', 422);

        $request->validate([
            'executive_id' => 'required|exists:users,id',
            'value'        => 'required|integer|min:1'
        ]);

        abort_if($request->value > $target->remainingValue(), 422);

        $newExec = User::findOrFail($request->executive_id);

        /* =====================================================
           CHECK EXISTING PENDING TARGET FOR SAME EXECUTIVE
        ===================================================== */
        $existingTarget = Target::where('parent_id', $target->id)
            ->where('executive_id', $newExec->id)
            ->where('status', 'pending')
            ->first();

        if ($existingTarget) {

            // 🔁 MERGE VALUE INTO EXISTING TARGET
            $existingTarget->increment('target_value', $request->value);

        } else {

            // ➕ CREATE NEW TARGET ENTRY
            Target::create([
                'product_id'   => $target->product_id,
                'executive_id' => $newExec->id,
                'target_type'  => $target->target_type,
                'target_value' => $request->value,
                'start_date'   => $target->start_date,
                'end_date'     => $target->end_date,
                'status'       => 'pending',
                'parent_id'    => $target->id,
                'created_by'   => auth()->id(),
            ]);
        }

        /* =====================================================
           NOTIFY ADMINS
        ===================================================== */
        foreach ($this->admins as $admin) {
            $admin->notify(new InventoryNotification(
                'Target assigned to ' . $newExec->name,
                route('admin.products.details', $target->product_id)
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Target split successfully'
        ]);
    }


    // ================= EXECUTIVE REPORT =================
    public function report(Request $request)
    {
        $user = auth()->user();
        $executiveId = $user->id;

        $adminProductCount = Product::whereHas('targets', function ($q) use ($executiveId) {
            $q->whereNull('parent_id')
              ->where('status','!=','rejected')
              ->where(function ($q) use ($executiveId) {
                  $q->where('executive_id',$executiveId)
                    ->orWhere('created_by',$executiveId);
              });
        })->distinct()->count();

        $executiveProductCount = Product::whereHas('targets', function ($q) use ($executiveId) {
            $q->where('created_by',$executiveId);
        })->distinct()->count();

        return view('executive.report',[
            'adminProductCount'=>$adminProductCount,
            'executiveProductCount'=>$executiveProductCount,
            'notifications'=>$user->notifications()->latest()->take(5)->get(),
        ]);
    }
}
