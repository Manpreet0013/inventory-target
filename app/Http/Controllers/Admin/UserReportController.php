<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Target;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function show(User $user, Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        // Prepare base query for targets assigned by admin (parent_id = null)
        $targetsQueryByAdmin = $user->targets()
            ->whereNull('parent_id')
            ->with([
                'children' => function($q) use ($from, $to) {
                    if ($from) $q->whereDate('start_date', '>=', $from);
                    if ($to) $q->whereDate('end_date', '<=', $to);
                    $q->with(['sales' => function($sq) use ($from, $to) {
                        if ($from) $sq->whereDate('sale_date', '>=', $from);
                        if ($to) $sq->whereDate('sale_date', '<=', $to);
                        $sq->where('status', 'approved'); // Only approved sales
                    }]);
                },
                'sales' => function($sq) use ($from, $to) {
                    if ($from) $sq->whereDate('sale_date', '>=', $from);
                    if ($to) $sq->whereDate('sale_date', '<=', $to);
                    //$sq->where('status', 'approved'); // Only approved sales
                },
                'creator',
                'executive'
            ]);

        if ($from) $targetsQueryByAdmin->whereDate('start_date', '>=', $from);
        if ($to) $targetsQueryByAdmin->whereDate('end_date', '<=', $to);

        $targetsAssignedByAdmin = $targetsQueryByAdmin->paginate(10);

        // Targets assigned to executive (child targets)
        $targetsQueryToExec = Target::where('executive_id', $user->id)
            ->whereNotNull('parent_id')
            ->with([
                'parent.creator',
                'product',
                'sales' => function($sq) use ($from, $to) {
                    if ($from) $sq->whereDate('sale_date', '>=', $from);
                    if ($to) $sq->whereDate('sale_date', '<=', $to);
                    $sq->where('status', 'approved'); // Only approved sales
                }
            ]);

        if ($from) $targetsQueryToExec->whereDate('start_date', '>=', $from);
        if ($to) $targetsQueryToExec->whereDate('end_date', '<=', $to);

        $targetsAssignedToExecutive = $targetsQueryToExec->paginate(10);

        return view('admin.users.report', compact(
            'user',
            'targetsAssignedByAdmin',
            'targetsAssignedToExecutive'
        ));
    }


}
