@extends('layouts.admin')

@section('title','Targets Listing')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            🎯 Targets
        </h1>

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


    <!-- TABLE CARD -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Executive</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Value</th>
                    <th class="px-4 py-3">Start Date</th>
                    <th class="px-4 py-3">End Date</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">View</th>
                </tr>
            </thead>

            <!-- <tbody class="divide-y">
                @forelse($targets as $index => $target)
                @php
                    $today = \Carbon\Carbon::today();
                    $status = 'Active';

                    if ($today->gt(\Carbon\Carbon::parse($target->end_date))) {
                        $status = 'Expired';
                    } elseif ($today->lt(\Carbon\Carbon::parse($target->start_date))) {
                        $status = 'Pending';
                    }
                @endphp

                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $target->product->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->executive->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 capitalize">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">
                            {{ $target->target_type }}
                        </span>
                    </td>

                    <td class="px-4 py-3 font-semibold">
                        {{ $target->target_value }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->start_date }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->end_date }}
                    </td>

                    <td class="px-4 py-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($status === 'Active') bg-green-100 text-green-700
                            @elseif($status === 'Pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $status }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.products.details', $target->product->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-xs">
                            View Targets
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-gray-500">
                        No targets found
                    </td>
                </tr>
                @endforelse
            </tbody> -->
            <tbody class="divide-y">
                @forelse($targets as $index => $target)
                @php
                    
                    $today = \Carbon\Carbon::today();
                    $status = '';
                    $trBg = '';
                    $trHover = '';

                    if ($today->gt(\Carbon\Carbon::parse($target->end_date))) {

                        $status = 'Target Expired';
                        $trBg = 'bg-gray-100';
                        $trHover = 'hover:bg-gray-200';

                    } else {

                        // Column based on target type
                        $valueColumn = $target->target_type === 'amount' ? 'amount' : 'boxes_sold';

                        
                        $validSales = $target->sales->filter(function ($sale) {
                            return $sale->status === 'approved'
                                && $sale->accountant_status === 'approved';
                        });

                        $totalSales = $validSales->sum($valueColumn);

                        $executiveCount = $target->executives_count ?? 1;
                        $perExecutiveTarget = $target->target_value / max($executiveCount, 1);

                        $executivesMetTarget = $validSales
                            ->groupBy('executive_id')
                            ->filter(fn ($sales) => $sales->sum($valueColumn) >= $perExecutiveTarget)
                            ->count();

                        if ($totalSales >= $target->target_value) {

                            if ($executivesMetTarget == $executiveCount) {
                                $status = 'Target Achieved - (Full participation)';
                                $trBg = 'bg-green-100';
                                $trHover = 'hover:bg-green-100';
                            } else {
                                $status = 'Target Achieved - (Partial participation)';
                                $trBg = 'bg-yellow-100';
                                $trHover = 'hover:bg-yellow-100';
                            }

                        } else {
                            $status = 'Target Not Achieved';
                            $trBg = 'bg-red-100';
                            $trHover = 'hover:bg-red-100';
                        }
                    }
                @endphp


                <tr class="transition {{ $trBg }} {{ $trHover }}">
                    <td class="px-4 py-3 font-medium">{{ $index + 1 }}</td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $target->product->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->executive->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 capitalize">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">
                            {{ $target->target_type }}
                        </span>
                    </td>

                    <td class="px-4 py-3 font-semibold">{{ $target->target_value }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $target->start_date }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $target->end_date ? $target->end_date : '-' }}</td>

                    <td class="px-4 py-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($status === 'Target Achieved - (Full participation)') bg-green-100 text-green-700
                        @elseif($status === 'Target Achieved - (Partial participation)') bg-yellow-100 text-yellow-700
                        @elseif($status === 'Target Not Achieved') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                            {{ $status }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.products.details', $target->product->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-xs">
                            View Targets
                        </a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-gray-500">
                        No targets found
                    </td>
                </tr>
                @endforelse
                </tbody>

        </table>
    </div>

</div>
@endsection
