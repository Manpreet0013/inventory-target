@extends('layouts.admin')

@section('title','Executive Report Dashboard')

@section('content')

<h1 class="text-2xl font-bold mb-6">Executive Report: {{ $user->name }}</h1>

{{-- Date Filter --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="date" name="from" value="{{ request('from') }}" class="border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
    <input type="date" name="to" value="{{ request('to') }}" class="border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
</form>

<div x-data="{ tab: 'assignedByAdmin', modalOpen: false, selectedTarget: null }">

    {{-- Tabs --}}
    <div class="flex border-b-2 border-gray-200 mb-4">
        <button @click="tab='assignedByAdmin'" 
                :class="tab==='assignedByAdmin' ? 'border-b-4 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600'"
                class="px-6 py-2 transition-colors">
            Product Admin
        </button>
        <button @click="tab='assignedToExecutive'" 
                :class="tab==='assignedToExecutive' ? 'border-b-4 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600'"
                class="px-6 py-2 transition-colors">
            Product Executive
        </button>
    </div>

    {{-- Tab 1: Assigned by Admin --}}
    <div x-show="tab==='assignedByAdmin'">
        @foreach($targetsAssignedByAdmin as $target)
        <div class="bg-white rounded shadow p-4 mb-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-lg">{{ $target->product?->name ?? '-' }} ({{ ucfirst($target->target_type) }})</h2>
                    <div class="text-gray-500 text-sm">
                        Assigned by: {{ $target->creator?->role?->name === 'Admin' ? 'Product Admin' : $target->creator?->name ?? 'Admin' }}
                    </div>
                    <div class="flex gap-2 mt-2 text-xs">
                        <span class="bg-gray-100 px-2 py-1 rounded">Type: {{ $target->product?->type ?? '-' }}</span>
                        @if($target->product?->expiry_date && now()->greaterThan($target->product->expiry_date))
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded">Expired: {{ $target->product->expiry_date->format('d M Y') }}</span>
                        @elseif($target->product?->expiry_date)
                            <span class="bg-gray-100 px-2 py-1 rounded">Expiry: {{ $target->product->expiry_date->format('d M Y') }}</span>
                        @endif
                    </div>
                    <div class="flex gap-4 mt-2">
                        <div class="bg-blue-100 p-2 rounded text-center">Total Target: {{ $target->target_value }}({{ ucfirst($target->target_type) }})</div>
                        <div class="bg-green-100 p-2 rounded text-center">Achieved: {{ $target->achievedValue() }}({{ ucfirst($target->target_type) }})</div>
                        <div class="bg-red-100 p-2 rounded text-center">Remaining: {{ $target->remainingValue() }}({{ ucfirst($target->target_type) }})</div>
                    </div>
                </div>
                <button @click="modalOpen = true; selectedTarget = {{ $target->id }}" 
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-indigo-600">View Sales</button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tab 2: Assigned to Executive --}}
    <div x-show="tab==='assignedToExecutive'">
        @foreach($targetsAssignedToExecutive as $target)
        <div class="bg-white rounded shadow p-4 mb-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-lg">{{ $target->product?->name ?? '-' }} ({{ ucfirst($target->target_type) }})</h2>
                    <div class="text-gray-500 text-sm">
                        Assigned by: {{ $target->parent?->creator?->role?->name === 'Admin' ? 'Product Admin' : $target->parent?->creator?->name ?? 'Admin' }}
                    </div>
                    <div class="flex gap-2 mt-2 text-xs">
                        <span class="bg-gray-100 px-2 py-1 rounded">Type: {{ $target->product?->type ?? '-' }}</span>
                        @if($target->product?->expiry_date && now()->greaterThan($target->product->expiry_date))
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded">Expired: {{ $target->product->expiry_date->format('d M Y') }}</span>
                        @elseif($target->product?->expiry_date)
                            <span class="bg-gray-100 px-2 py-1 rounded">Expiry: {{ $target->product->expiry_date->format('d M Y') }}</span>
                        @endif
                    </div>
                    <div class="flex gap-4 mt-2">
                        <div class="bg-blue-100 p-2 rounded text-center">Total Target: {{ $target->target_value }}({{ ucfirst($target->target_type) }})</div>
                        <div class="bg-green-100 p-2 rounded text-center">Achieved: {{ $target->achievedValue() }}({{ ucfirst($target->target_type) }})</div>
                        <div class="bg-red-100 p-2 rounded text-center">Remaining: {{ $target->remainingValue() }}({{ ucfirst($target->target_type) }})</div>
                    </div>
                </div>
                <button @click="modalOpen = true; selectedTarget = {{ $target->id }}" 
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-indigo-600">View Sales</button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Modal for Sales --}}
    <div x-show="modalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">

        <div x-show="modalOpen"
             x-transition.scale
             class="bg-white rounded-xl shadow-xl w-11/12 md:w-4/5 max-h-[90vh] overflow-y-auto p-6 relative">

            {{-- Close --}}
            <button @click="modalOpen=false; selectedTarget=null"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">
                Target & Sales Breakdown
            </h2>

            @php
                $allTargets = $targetsAssignedByAdmin->merge($targetsAssignedToExecutive);
            @endphp

            @foreach($allTargets as $target)
            <div x-show="selectedTarget === {{ $target->id }}" class="space-y-6">

                @php
                    // ADMIN CALCULATIONS
                    $adminAssignedToExecutives = $target->children->sum('target_value');

                    $adminSales = $target->sales->sum(
                        $target->target_type === 'quantity' ? 'quantity' : 'amount'
                    );

                    $adminRemaining = $target->target_value
                                        - $adminAssignedToExecutives
                                        - $adminSales;
                @endphp

                {{-- SUMMARY BOX --}}
               <div class="flex flex-wrap justify-between items-center gap-6">

                    <div class="flex items-center gap-2 bg-blue-100 px-4 py-2 rounded-lg">
                        <span class="font-medium text-blue-800">Total Target:</span>
                        <span class="font-bold text-lg text-blue-900">
                            {{ $target->target_value }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 bg-green-100 px-4 py-2 rounded-lg">
                        <span class="font-medium text-green-800">Achieved:</span>
                        <span class="font-bold text-lg text-green-900">
                            {{ $target->achievedValue() }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 bg-red-100 px-4 py-2 rounded-lg">
                        <span class="font-medium text-red-800">Product Admin Remaining:</span>
                        <span class="font-bold text-lg text-red-900">
                            {{ $adminRemaining }}
                        </span>
                    </div>

                </div>

                {{-- SALES TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border rounded-lg">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2">Assigned Target</th>
                                <th class="px-3 py-2">Achieved</th>
                                <th class="px-3 py-2">Remaining</th>
                                <th class="px-3 py-2">Target Status</th>
                                <th class="px-3 py-2">All Sales</th>
                                <th class="px-3 py-2">Sale Dates</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            {{-- PRODUCT ADMIN ROW --}}
                            <tr class="bg-white font-semibold">
                                <td class="px-3 py-2">
                                    {{ $user->name }}
                                    <span class="text-xs text-blue-600">(Product Admin)</span>
                                </td>

                                <td class="px-3 py-2">
                                    {{ $target->target_value - $adminAssignedToExecutives }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $adminSales }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $adminRemaining }}
                                </td>

                                @php
                                    $adminStatus = $target->status ?? 'pending';
                                    $adminStatusClass = match($adminStatus) {
                                        'accepted' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-yellow-100 text-yellow-700'
                                    };
                                @endphp

                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $adminStatusClass }}">
                                        {{ ucfirst($adminStatus) }}
                                    </span>
                                </td>


                                <td class="px-3 py-2">
                                    @foreach($target->sales as $sale)
                                        <div>
                                            {{ $sale->boxes_sold ?? $sale->amount }}
                                            ({{ ucfirst($sale->status ?? 'pending') }})
                                        </div>
                                    @endforeach
                                </td>

                                <td class="px-3 py-2">
                                    @foreach($target->sales as $sale)
                                        <div>{{ $sale->created_at->format('d-m-Y') }}</div>
                                    @endforeach
                                </td>
                            </tr>

                            @foreach($target->children as $child)

                                @php
                                    $childAchieved = $child->sales->sum(
                                        $child->target_type === 'box' ? 'boxes_sold' : 'amount'
                                    );

                                    $childRemaining = max($child->target_value - $childAchieved, 0);

                                    $childStatus = $child->status ?? 'pending';
                                    $childStatusClass = match($childStatus) {
                                        'accepted' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-yellow-100 text-yellow-700'
                                    };
                                @endphp

                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2">
                                        {{ $child->executive?->name ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2">{{ $child->target_value }}</td>

                                    <td class="px-3 py-2">{{ $childAchieved }}</td>

                                    <td class="px-3 py-2">{{ $childRemaining }}</td>

                                    {{-- TARGET STATUS --}}
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $childStatusClass }}">
                                            {{ ucfirst($childStatus) }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2">
                                        @foreach($child->sales as $sale)
                                            <div>
                                                {{ $child->target_type === 'box' ? $sale->boxes_sold : $sale->amount }}
                                                <span class="text-xs text-gray-500">
                                                    ({{ ucfirst($sale->status ?? 'pending') }})
                                                </span>
                                            </div>
                                        @endforeach
                                    </td>

                                    <td class="px-3 py-2">
                                        @foreach($child->sales as $sale)
                                            <div>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}</div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>


</div>

<script src="//unpkg.com/alpinejs" defer></script>

@endsection
