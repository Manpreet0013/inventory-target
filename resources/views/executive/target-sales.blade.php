@extends('layouts.executive')
@section('title','Sales')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Sales – {{ $target->product->name }}
</h2>

<div class="overflow-x-auto bg-white rounded-xl shadow">
<table class="w-full text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-3 py-2">Party</th>
            <th class="px-3 py-2">Value</th>
            <th class="px-3 py-2">Date</th>
            <th class="px-3 py-2">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($target->sales as $sale)
        <tr class="border-t">
            <td class="px-3 py-2">{{ $sale->party_name }}</td>
            <td class="px-3 py-2 font-semibold">
                {{ $sale->boxes_sold ?? $sale->amount }}
            </td>
            <td class="px-3 py-2">{{ $sale->sale_date }}</td>
            <td class="px-3 py-2 capitalize">{{ $sale->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
<br>
<a href="{{ url()->previous() }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded
          bg-blue-600 text-gray-800 hover:bg-gray-300 transition text-sm">
    ← Back
</a>

@endsection
