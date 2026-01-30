@extends('layouts.admin')

@section('title','Targets Listing')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">🎯 Targets</h1>

        <a href="{{ route('admin.targets') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow">
            + Assign New Target
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- FILTER BAR -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.list') }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold
           {{ empty($statusFilter) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            All
        </a>

        <a href="?status=achieved_full"
           class="px-4 py-2 rounded-lg text-sm font-semibold
           {{ $statusFilter === 'achieved_full' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700' }}">
            Achieved (Full)
        </a>

        <a href="?status=achieved_partial"
           class="px-4 py-2 rounded-lg text-sm font-semibold
           {{ $statusFilter === 'achieved_partial' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700' }}">
            Achieved (Partial)
        </a>

        <a href="?status=not_achieved"
           class="px-4 py-2 rounded-lg text-sm font-semibold
           {{ $statusFilter === 'not_achieved' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700' }}">
            Not Achieved
        </a>

        <a href="?status=expired"
           class="px-4 py-2 rounded-lg text-sm font-semibold
           {{ $statusFilter === 'expired' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            Expired
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Executive</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Target</th>
                    <th class="px-4 py-3">Pending</th>
                    <th class="px-4 py-3">Start Date</th>
                    <th class="px-4 py-3">End Date</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">View</th>
                </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($targets as $index => $target)
                @php
                    $today = \Carbon\Carbon::today();
                    $startDate = \Carbon\Carbon::parse($target->start_date);
                    $endDate   = \Carbon\Carbon::parse($target->end_date);

                    $status = '';
                    $pendingValue = 0;
                    $rowClass = '';

                    // Amount / Boxes logic
                    $valueColumn = $target->target_type === 'amount' ? 'amount' : 'boxes_sold';

                    // Approved sales only
                    $validSales = $target->sales->filter(fn($sale) =>
                        $sale->status === 'approved' &&
                        $sale->accountant_status === 'approved'
                    );

                    $totalSales = $validSales->sum($valueColumn);

                    // ⏳ PENDING
                    if ($today->lt($startDate)) {
                        $status = 'Target Pending';
                        $pendingValue = $target->target_value;
                        $rowClass = 'bg-blue-100';

                    // ❌ EXPIRED
                    } elseif ($today->gt($endDate)) {
                        $status = 'Target Expired';
                        $pendingValue = null;
                        $rowClass = 'bg-gray-100';

                    // 🔥 ACTIVE
                    } else {

                        $pendingValue = max($target->target_value - $totalSales, 0);

                        $executiveCount = $target->executives_count ?? 1;
                        $perExecutiveTarget = $target->target_value / max($executiveCount,1);

                        $executivesMetTarget = $validSales
                            ->groupBy('executive_id')
                            ->filter(fn($sales) => $sales->sum($valueColumn) >= $perExecutiveTarget)
                            ->count();

                        if ($totalSales >= $target->target_value) {
                            if ($executivesMetTarget == $executiveCount) {
                                $status = 'Target Achieved (Full)';
                                $rowClass = 'bg-green-100';
                            } else {
                                $status = 'Target Achieved (Partial)';
                                $rowClass = 'bg-yellow-100';
                            }
                        } else {
                            $status = 'Target Not Achieved';
                            $rowClass = 'bg-red-100';
                        }
                    }
                @endphp

                <tr class="{{ $rowClass }}">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $target->product->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $target->executive->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                            {{ ucfirst($target->target_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ $target->target_value }}</td>

                    <!-- ✅ PENDING VALUE -->
                    <td class="px-4 py-3 font-semibold">
                        {{ is_null($pendingValue) ? '-' : $pendingValue }}
                    </td>

                    <td class="px-4 py-3">{{ $target->start_date }}</td>
                    <td class="px-4 py-3">{{ $target->end_date }}</td>

                    <td class="px-4 py-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold">
                            {{ $status }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.products.details', $target->product->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-xs">
                            View
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="10" class="text-center py-6 text-gray-500">
                        No targets found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
