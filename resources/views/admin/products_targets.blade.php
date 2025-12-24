@extends('layouts.admin')

@section('title','Products & Targets')

@section('content')

<h1 class="text-2xl font-bold mb-4">Products</h1>

<table class="border w-full text-left">
    <thead>
        <tr class="bg-gray-200">
            <th class="border px-2 py-1">#</th>
            <th class="border px-2 py-1">Product Name</th>
            <th class="border px-2 py-1">Composition</th>
            <th class="border px-2 py-1">Type</th>
            <th class="border px-2 py-1">Targets</th>
            <th class="border px-2 py-1">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr>
            <td class="border px-2 py-1">{{ $index + 1 }}</td>
            <td class="border px-2 py-1">{{ $product->name }}</td>
            <td class="border px-2 py-1">{{ $product->composition ?? '-' }}</td>
            <td class="border px-2 py-1">{{ $product->type }}</td>
            <td class="border px-2 py-1">{{ $product->targets->count() }}</td>
            <td class="border px-2 py-1">
                <button onclick="toggleDetails({{ $product->id }})" 
                    class="bg-blue-600 text-white px-2 py-1 rounded">
                    View Details
                </button>
            </td>
        </tr>

        <!-- Hidden Details Row -->
        <tr id="details-{{ $product->id }}" class="hidden">
            <td colspan="6" class="p-2">
                @if($product->targets->count() > 0)
                <table class="border w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">#</th>
                            <th class="border px-2 py-1">Executive</th>
                            <th class="border px-2 py-1">Target Type</th>
                            <th class="border px-2 py-1">Target Value</th>
                            <th class="border px-2 py-1">Remaining</th>
                            <th class="border px-2 py-1">Start / End Date</th>
                            <th class="border px-2 py-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->targets as $tIndex => $target)
                        @php
                            $remaining = $target->target_type === 'box' 
                                        ? $target->target_value - $target->sales->sum('boxes_sold')
                                        : $target->target_value - $target->sales->sum('amount');
                        @endphp
                        <tr>
                            <td class="border px-2 py-1">{{ $tIndex + 1 }}</td>
                            <td class="border px-2 py-1">{{ $target->executive->name ?? '-' }}</td>
                            <td class="border px-2 py-1">{{ ucfirst($target->target_type) }}</td>
                            <td class="border px-2 py-1">{{ $target->target_value }}</td>
                            <td class="border px-2 py-1">{{ $remaining }}</td>
                            <td class="border px-2 py-1">{{ $target->start_date }} / {{ $target->end_date }}</td>
                            <td class="border px-2 py-1">{{ ucfirst($target->status) }}</td>
                        </tr>

                        @if($target->sales->count() > 0)
                        <tr>
                            <td colspan="7" class="p-2">
                                <b>Sales:</b>
                                <table class="border w-full text-left mt-1">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border px-2 py-1">#</th>
                                            <th class="border px-2 py-1">Party</th>
                                            <th class="border px-2 py-1">Value</th>
                                            <th class="border px-2 py-1">Date</th>
                                            <th class="border px-2 py-1">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($target->sales as $sIndex => $sale)
                                        <tr>
                                            <td class="border px-2 py-1">{{ $sIndex + 1 }}</td>
                                            <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                            <td class="border px-2 py-1">
                                                {{ $target->target_type === 'box' ? $sale->boxes_sold : $sale->amount }}
                                            </td>
                                            <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                            <td class="border px-2 py-1">{{ ucfirst($sale->status) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endif

                        @endforeach
                    </tbody>
                </table>
                @else
                    <p class="text-gray-500">No targets assigned.</p>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
function toggleDetails(id) {
    const row = document.getElementById(`details-${id}`);
    row.classList.toggle('hidden');
}
</script>

@endsection
