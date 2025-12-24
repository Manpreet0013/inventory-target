@extends('layouts.executive')

@section('title','Target Details')

@section('content')

<h1 class="text-2xl font-bold mb-4">{{ $target->product->name }}</h1>

<div class="bg-white p-4 rounded shadow mb-6">
    <p><b>Target Type:</b> {{ ucfirst($target->target_type) }}</p>
    <p><b>Target Value:</b> {{ $target->target_value }}</p>
    <p><b>Remaining:</b> {{ $target->remainingValue() }}</p>
    <p><b>Status:</b> {{ ucfirst($target->status) }}</p>
    <p><b>Period:</b> {{ $target->start_date }} → {{ $target->end_date }}</p>
</div>

<h2 class="text-xl font-bold mb-2">Sales</h2>

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">Date</th>
            <th class="p-3">Party</th>
            <th class="p-3">Boxes / Amount</th>
            <th class="p-3">Status</th>
        </tr>
    </thead>

    <tbody>
        @forelse($target->sales as $sale)
        <tr class="border-t">
            <td class="p-3">{{ $sale->sale_date }}</td>
            <td class="p-3">{{ $sale->party_name }}</td>
            <td class="p-3">
                {{ $target->target_type === 'box' ? $sale->boxes_sold : $sale->amount }}
            </td>
            <td class="p-3">{{ ucfirst($sale->status) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="p-3 text-center text-gray-500">
                No sales yet
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<br>
<div class="mb-4">
    <a href="{{ route('executive.dashboard') }}" class="inline-block mt-4 bg-gray-600 text-white px-3 py-1 rounded">Back to Target</a>
</div>

@endsection
