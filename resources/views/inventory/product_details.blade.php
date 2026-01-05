@extends('layouts.inventory')

@section('title','Product Details')

@section('content')

<div class="p-4">

    {{-- BACK --}}
    <a href="{{ url()->previous() }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        ← Back
    </a>

    {{-- PRODUCT INFO --}}
    <div class="bg-white rounded shadow p-4 mb-6 flex gap-4">
        <div class="w-40 h-40">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}"
                     class="w-full h-full object-cover rounded">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded text-gray-500">
                    No Image
                </div>
            @endif
        </div>

        <div>
            <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>
            <p><b>Composition:</b> {{ $product->composition ?? '-' }}</p>
            <p><b>Type:</b> {{ ucfirst($product->type) }}</p>
            <p><b>Expiry Date:</b> {{ $product->expiry_date ?? '-' }}</p>
        </div>
    </div>

    {{-- TARGETS --}}
    <h2 class="text-xl font-semibold mb-3">Targets & Sales</h2>

    @forelse($product->targets->whereNull('parent_id') as $target)

        @php
            $achieved = $target->target_type === 'box'
                ? $target->sales->sum('boxes_sold')
                : $target->sales->sum('amount');
            $remaining  = max($target->target_value - $achieved, 0);
            $percentage = $target->target_value > 0
                ? round(($achieved / $target->target_value) * 100)
                : 0;
        @endphp

        {{-- PARENT TARGET CARD --}}
        <div class="bg-white rounded shadow p-4 mb-4 border-l-4 border-blue-500">
            <div class="flex justify-between flex-wrap gap-4">
                <div>
                    <p><b>Executive:</b>
                        <span class="px-2 py-1 rounded bg-yellow-500 text-white text-xs">
                            {{ $target->executive->name ?? '-' }}
                        </span>
                    </p>
                    <p><b>Target:</b> {{ $target->target_value }} ({{ ucfirst($target->target_type) }})</p>
                    <p><b>Duration:</b> {{ $target->start_date }} → {{ $target->end_date }}</p>
                </div>

                <div class="w-64">
                    <div class="text-xs mb-1">
                        {{ $achieved }} achieved / {{ $remaining }} remaining
                    </div>
                    <div class="w-full bg-gray-200 rounded h-2">
                        <div
                            class="h-2 rounded {{ $percentage >= 100 ? 'bg-green-600' : 'bg-blue-500' }}"
                            style="width: {{ min($percentage,100) }}%">
                        </div>
                    </div>
                    <div class="text-xs mt-1 {{ $percentage >= 100 ? 'text-green-700' : 'text-gray-700' }}">
                        {{ $percentage }}%
                    </div>
                </div>
            </div>

            {{-- CHILD TARGETS --}}
            @if($target->children->count())
                <div class="ml-6 mt-4 border-l-2 border-gray-300 pl-4">
                    <h4 class="text-sm font-semibold mb-2">Child Targets</h4>

                    @foreach($target->children as $child)
                        @php
                            $childAchieved = $child->target_type === 'box'
                                ? $child->sales->sum('boxes_sold')
                                : $child->sales->sum('amount');
                            $childRemaining  = max($child->target_value - $childAchieved, 0);
                            $childPercentage = $child->target_value > 0
                                ? round(($childAchieved / $child->target_value) * 100)
                                : 0;
                        @endphp

                        <div class="bg-gray-50 rounded p-3 mb-3 border-l-4 border-purple-500">
                            <div class="flex justify-between flex-wrap gap-4 text-sm">
                                <div>
                                    <p><b>Executive:</b>
                                        <span class="px-2 py-1 rounded bg-indigo-500 text-white text-xs">
                                            {{ $child->executive->name ?? '-' }}
                                        </span>
                                    </p>
                                    <p><b>Target:</b> {{ $child->target_value }} ({{ ucfirst($child->target_type) }})</p>
                                    <p><b>Status:</b> {{ ucfirst($child->status) }}</p>
                                </div>

                                <div class="w-48">
                                    <div class="text-xs mb-1">
                                        {{ $childAchieved }} achieved / {{ $childRemaining }} remaining
                                    </div>
                                    <div class="w-full bg-gray-200 rounded h-2">
                                        <div
                                            class="h-2 rounded {{ $childPercentage >= 100 ? 'bg-green-600' : 'bg-purple-500' }}"
                                            style="width: {{ min($childPercentage,100) }}%">
                                        </div>
                                    </div>
                                    <div class="text-xs mt-1 {{ $childPercentage >= 100 ? 'text-green-700' : 'text-gray-700' }}">
                                        {{ $childPercentage }}%
                                    </div>
                                </div>
                            </div>

                            {{-- SALES --}}
                            @if($child->sales->count())
                                <div class="mt-3">
                                    <h5 class="text-xs font-semibold mb-1">Sales</h5>
                                    <table class="w-full text-xs border">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border px-2 py-1">#</th>
                                                <th class="border px-2 py-1">Party</th>
                                                <th class="border px-2 py-1">Value</th>
                                                <th class="border px-2 py-1">Date</th>
                                                <th class="border px-2 py-1">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($child->sales as $cIndex => $sale)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="border px-2 py-1">{{ $cIndex + 1 }}</td>
                                                    <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                                    <td class="border px-2 py-1">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                                    <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                                    <td class="border px-2 py-1">
                                                        <span class="px-2 py-0.5 rounded text-xs
                                                            {{ $sale->status === 'accepted' ? 'bg-green-200 text-green-800' :
                                                               ($sale->status === 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                                            {{ ucfirst($sale->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="mt-2 text-xs bg-yellow-100 p-2 rounded">
                                    No sales added yet.
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif

            {{-- PARENT SALES --}}
            @if($target->sales->count())
                <div class="mt-4">
                    <h4 class="text-sm font-semibold mb-2">Parent Sales</h4>
                    <table class="w-full text-xs border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">#</th>
                                <th class="border px-2 py-1">Party</th>
                                <th class="border px-2 py-1">Value</th>
                                <th class="border px-2 py-1">Date</th>
                                <th class="border px-2 py-1">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($target->sales as $pIndex => $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-2 py-1">{{ $pIndex + 1 }}</td>
                                    <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                    <td class="border px-2 py-1">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                    <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                    <td class="border px-2 py-1">
                                        <span class="px-2 py-0.5 rounded text-xs
                                            {{ $sale->status === 'accepted' ? 'bg-green-200 text-green-800' :
                                               ($sale->status === 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

    @empty
        <p class="text-gray-500">No targets available.</p>
    @endforelse

</div>

@endsection
