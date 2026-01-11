@extends('layouts.inventory')

@section('title','Targets Listing')

@section('content')
<div class="container mt-6">
    <h1 class="text-2xl font-bold mb-4">Targets</h1>

    <a href="{{ url()->previous() }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        ← Back
    </a>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <table class="border w-full text-left">
        <thead class="bg-gray-200 text-xs">
            <tr>
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Product</th>
                <th class="border px-2 py-1">Executive</th>
                <th class="border px-2 py-1">Type</th>
                <th class="border px-2 py-1">Target Value</th>
                <th class="border px-2 py-1">Achieved</th>
                <th class="border px-2 py-1">Remaining</th>
                <th class="border px-2 py-1">Accepted</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($targets as $index => $target)
            @php
                $today = \Carbon\Carbon::today();
                $status = 'Active';
                if($today->gt(\Carbon\Carbon::parse($target->end_date))) $status = 'Expired';
                elseif($today->lt(\Carbon\Carbon::parse($target->start_date))) $status = 'Pending';

                $achievedValue = $target->sales->sum(function($sale) use ($target) {
                    return $target->target_type === 'box' ? $sale->boxes_sold : $sale->amount;
                });

                $remainingValue = $target->target_value - $achievedValue;
            @endphp
            <tr class="bg-gray-50">
                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                <td class="border px-2 py-1">{{ $target->product->name ?? '-' }}</td>
                <td class="border px-2 py-1">{{ $target->executive->name ?? '-' }}</td>
                <td class="border px-2 py-1">{{ ucfirst($target->target_type) }}</td>
                <td class="border px-2 py-1">{{ $target->target_value }}</td>
                <td class="border px-2 py-1">{{ $achievedValue }}</td>
                <td class="border px-2 py-1">{{ $remainingValue > 0 ? $remainingValue : 0 }}</td>
                <td class="border px-2 py-1">
                    @if($target->accepted)
                        <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">Accepted</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs bg-yellow-500 text-white">Pending</span>
                    @endif
                </td>
                <td class="border px-2 py-1">
                    <span class="px-2 py-1 rounded text-xs 
                        @if($status=='Active') text-green-600
                        @elseif($status=='Pending') text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $status }}
                    </span>
                </td>
                <td class="border px-2 py-1">
                    @if($target->sales->count())
                        <table class="w-full text-xs border mt-1">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-1 py-1">#</th>
                                    <th class="border px-1 py-1">Party</th>
                                    <th class="border px-1 py-1">Value</th>
                                    <th class="border px-1 py-1">Date</th>
                                    <th class="border px-1 py-1">Admin Status</th>
                                    <th class="border px-1 py-1">Accountant Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($target->sales as $sIndex => $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-1 py-1">{{ $sIndex + 1 }}</td>
                                    <td class="border px-1 py-1">{{ $sale->party_name }}</td>
                                    <td class="border px-1 py-1">{{ $target->target_type === 'box' ? $sale->boxes_sold : '₹'.$sale->amount }}</td>
                                    <td class="border px-1 py-1">{{ $sale->sale_date }}</td>
                                    <td class="border px-1 py-1">
                                        <span class="px-2 py-1 rounded-full text-white text-[10px] 
                                            {{ $sale->status === 'approved' ? 'bg-green-600' : 'bg-yellow-500' }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td class="border px-1 py-1">
                                        <span class="px-2 py-1 rounded-full text-white text-[10px] 
                                            {{ $sale->accountant_status === 'approved' ? 'bg-green-600' : 'bg-yellow-500' }}">
                                            {{ ucfirst($sale->accountant_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <span class="text-xs text-gray-400">No sales yet</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="border px-2 py-1 text-center">No targets found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $targets->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection
