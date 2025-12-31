@extends('layouts.admin')

@section('title','Targets Listing')

@section('content')
<div class="container mt-6">
    <h1 class="text-2xl font-bold mb-4">Targets</h1>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.targets') }}" 
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">
        Assign New Target
    </a>

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
                    <span class="px-2 py-1 rounded text-white
                        @if($status=='Active') bg-green-500
                        @elseif($status=='Pending') bg-yellow-500
                        @else bg-red-500
                        @endif">
                        {{ $status }}
                    </span>
                </td>
                <td class="border px-2 py-1">
                    <a href="{{ route('admin.products.details', $target->product->id) }}" 
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
