@extends('layouts.admin')

@section('title','Product Details')

@section('content')

<h1 class="text-2xl font-bold mb-4">{{ $product->name }} Details</h1>

<div class="flex mb-4">
    <div class="w-40 h-40 mr-4">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="w-full h-full object-cover rounded">
        @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded text-gray-500">
                No Image
            </div>
        @endif
    </div>
    <div>
        <p><b>Composition:</b> {{ $product->composition ?? '-' }}</p>
        <p><b>Type:</b> {{ $product->type }}</p>
        <p><b>Expiry Date:</b> {{ $product->expiry_date ?? '-' }}</p>
    </div>
</div>

<h2 class="font-bold mt-4 mb-2">Targets</h2>

@if($product->targets->count() > 0)
<table class="border w-full text-left">
    <thead>
        <tr class="bg-gray-200">
            <th class="border px-2 py-1">#</th>
            <th class="border px-2 py-1">Executive</th>
            <th class="border px-2 py-1">Type</th>
            <th class="border px-2 py-1">Target Value</th>
            <th class="border px-2 py-1">Remaining</th>
            <th class="border px-2 py-1">Start / End Date</th>
            <th class="border px-2 py-1">Status</th>
            <th class="border px-2 py-1">Current Status</th>
            <th class="border px-2 py-1">View Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($product->targets as $index => $target)
        @php
            $remaining = $target->target_type === 'box' 
                         ? $target->target_value - $target->sales->sum('boxes_sold')
                         : $target->target_value - $target->sales->sum('amount');
        @endphp
        <tr>
            <td class="border px-2 py-1">{{ $index + 1 }}</td>
            <td class="border px-2 py-1"><a class="px-2 py-1 rounded text-white bg-yellow-500" href="{{route('admin.users.profile', $target->executive->id)}}">{{ $target->executive->name ?? '-' }}</a></td>
            <td class="border px-2 py-1">{{ ucfirst($target->target_type) }}</td>
            <td class="border px-2 py-1">{{ $target->target_value }}</td>
            <td class="border px-2 py-1">{{ $remaining }}</td>
            <td class="border px-2 py-1">{{ $target->start_date }} / {{ $target->end_date }}</td>
            <td class="border px-2 py-1">{{ ucfirst($target->status) }}</td>
            <td class="border px-2 py-1">
                <span class="px-2 py-1 rounded  {{ $target->isComplete() ? 'text-green-700' : 'text-red-900' }}">
                    {{ $target->isComplete() ? 'Complete' : 'Incomplete' }}
                </span>
            </td>
            <td class="border px-2 py-1">
                <a class="px-2 py-1 rounded text-white bg-green-500" href="{{ route('admin.sales.details', [
                        $target->product_id,
                        $target->id
                    ]) }}">Sales</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-gray-500">No targets assigned for this product.</p>
@endif

<a href="{{ route('admin.products') }}" class="inline-block mt-4 bg-gray-600 text-white px-3 py-1 rounded">Back to Products</a>

@endsection
