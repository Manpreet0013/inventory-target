@extends('layouts.admin')

@section('title', 'Target - Sale Details')

@section('content')
<div class="p-4">

    {{-- BACK BUTTON --}}
    <a href="{{ route('admin.products.details', $product->id) }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        ← Back
    </a>

    {{-- MAIN TARGET --}}
    <div class="mb-4 bg-white p-4 rounded shadow border-l-4 border-green-600">
        <h2 class="text-lg font-bold mb-3">Main Target</h2>
        <p><b>Executive:</b> {{ $target->executive->name ?? '-' }}</p>
        <p><b>Target Value:</b> {{ $target->target_value }}</p>
        <p><b>Status:</b> {{ ucfirst($target->status) }}</p>
        <p><b>Duration:</b> {{ $target->start_date }} → {{ $target->end_date }}</p>

        {{-- SALES OF MAIN TARGET --}}
        @if($target->sales->count())
            <div class="ml-6 mt-2">
                <h3 class="font-semibold">Sales of Main Target</h3>
                <table class="border w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">#</th>
                            <th class="border px-2 py-1">Executive</th>
                            <th class="border px-2 py-1">Party</th>
                            <th class="border px-2 py-1">Value</th>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($target->sales as $index => $sale)
                            <tr>
                                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                <td class="border px-2 py-1">{{ $sale->target->executive->name ?? '-' }}</td>
                                <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                <td class="border px-2 py-1">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                <td class="border px-2 py-1">{{ ucfirst($sale->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ml-6 bg-yellow-100 p-2 rounded mt-2">
                No sales for this target yet.
            </div>
        @endif
    </div>

    {{-- CHILD TARGETS --}}
    @if($childTargets->count())
        <div class="ml-6">
            <h3 class="font-semibold mb-2">Child Targets</h3>
            @foreach($childTargets as $cIndex => $child)
                <div class="mb-4 bg-gray-50 p-3 rounded border-l-4 border-blue-500">
                    <p><b>#{{ $cIndex + 1 }} Executive:</b> {{ $child->executive->name ?? '-' }}</p>
                    <p><b>Target Value:</b> {{ $child->target_value }}</p>
                    <p><b>Status:</b> {{ ucfirst($child->status) }}</p>
                    <p><b>Duration:</b> {{ $child->start_date }} → {{ $child->end_date }}</p>

                    {{-- SALES OF CHILD TARGET --}}
                    @if($child->sales->count())
                        <div class="ml-6 mt-2">
                            <h4 class="font-medium">Sales of Child Target</h4>
                            <table class="border w-full text-left">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-2 py-1">#</th>
                                        <th class="border px-2 py-1">Executive</th>
                                        <th class="border px-2 py-1">Party</th>
                                        <th class="border px-2 py-1">Value</th>
                                        <th class="border px-2 py-1">Date</th>
                                        <th class="border px-2 py-1">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($child->sales as $sIndex => $sale)
                                        <tr>
                                            <td class="border px-2 py-1">{{ $sIndex + 1 }}</td>
                                            <td class="border px-2 py-1">{{ $sale->target->executive->name ?? '-' }}</td>
                                            <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                            <td class="border px-2 py-1">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                            <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                            <td class="border px-2 py-1">{{ ucfirst($sale->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="ml-6 bg-yellow-100 p-2 rounded mt-2">
                            No sales for this child target yet.
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
