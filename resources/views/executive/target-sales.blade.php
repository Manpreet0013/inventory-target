@extends('layouts.executive')
@section('title','Sales')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Sales – {{ $target->product->name }}
</h2>

{{-- Target Summary --}}
<div class="mb-4 text-sm text-gray-700 space-y-1 bg-white p-4 rounded-xl shadow">
    <p><strong>Target Type:</strong> {{ ucfirst($target->target_type) }}</p>
    <p><strong>Total Target:</strong> {{ $target->target_value }}</p>
    <p><strong>Achieved:</strong> {{ $target->achievedValue() }}</p>
    <p><strong>Remaining:</strong> {{ $target->remainingValue() }}</p>
    <p><strong>Accepted:</strong>
        <span class="px-2 py-1 rounded text-xs
            {{ ($target->status == 'accepted') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ ($target->status == 'accepted') ? 'Yes' : 'No' }}
        </span>
    </p>
</div>

{{-- Sales Table --}}
<div class="overflow-x-auto bg-white rounded-xl shadow">
    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-3 py-2 border">#</th>
                <th class="px-3 py-2 border">Party</th>
                <th class="px-3 py-2 border text-right">{{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}</th>
                <th class="px-3 py-2 border">Date</th>
                <th class="px-3 py-2 border">Product Admin Status</th>
                <th class="px-3 py-2 border">Accountant Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($target->sales as $index => $sale)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-3 py-2 border text-center font-semibold">{{ $index + 1 }}</td>
                <td class="px-3 py-2 border">{{ $sale->party_name }}</td>
                <td class="px-3 py-2 border text-right font-semibold">
                    {{ $target->target_type === 'box' ? $sale->boxes_sold : $sale->amount }}
                </td>
                <td class="px-3 py-2 border">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                <td class="px-3 py-2 border text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $sale->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $sale->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $sale->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </td>
                <td class="px-3 py-2 border text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $sale->accountant_status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $sale->accountant_status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $sale->accountant_status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($sale->accountant_status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-3 py-2 text-center border text-gray-500">
                    No sales recorded for this target yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<br>
<a href="{{ url()->previous() }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded
          bg-blue-600 text-white hover:bg-blue-700 transition text-sm">
    ← Back
</a>

@endsection
