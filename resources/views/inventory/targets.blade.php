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
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Product</th>
                <th class="border px-2 py-1">Executive</th>
                <th class="border px-2 py-1">Type</th>
                <th class="border px-2 py-1">Value</th>
                <th class="border px-2 py-1">Start Date</th>
                <th class="border px-2 py-1">End Date</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">View</th>
            </tr>
        </thead>
        <tbody>
            @forelse($targets as $index => $target)
            <tr>
                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                <td class="border px-2 py-1">{{ $target->product->name ?? '-' }}</td>
                <td class="border px-2 py-1">{{ $target->executive->name ?? '-' }}</td>
                <td class="border px-2 py-1">{{ ucfirst($target->target_type) }}</td>
                <td class="border px-2 py-1">{{ $target->target_value }}</td>
                <td class="border px-2 py-1">{{ $target->start_date }}</td>
                <td class="border px-2 py-1">{{ $target->end_date }}</td>
                <td class="border px-2 py-1">
                    @php
                        $today = \Carbon\Carbon::today();
                        $status = 'Active';
                        if($today->gt(\Carbon\Carbon::parse($target->end_date))) $status = 'Expired';
                        elseif($today->lt(\Carbon\Carbon::parse($target->start_date))) $status = 'Pending';
                    @endphp
                    <span class="px-2 py-1 rounded
                        @if($status=='Active') text-green-600
                        @elseif($status=='Pending') text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $status }}
                    </span>
                </td>
                <td class="border px-2 py-1">
                    <a href="{{ route('inventory.products', $target->product->id) }}" 
                       class="bg-blue-600 text-white px-2 py-1 rounded">View Targets</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="border px-2 py-1 text-center">No targets found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
