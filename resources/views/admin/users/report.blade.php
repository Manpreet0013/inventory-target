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
            Assigned by Admin
        </button>
        <button @click="tab='assignedToExecutive'" 
                :class="tab==='assignedToExecutive' ? 'border-b-4 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600'"
                class="px-6 py-2 transition-colors">
            Assigned to Executive
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
                        Assigned by: {{ $target->creator?->name ?? 'Admin' }}
                    </div>
                    <div class="flex gap-4 mt-2">
                        <div class="bg-blue-100 p-2 rounded text-center">Total: {{ $target->target_value }}</div>
                        <div class="bg-green-100 p-2 rounded text-center">Achieved: {{ $target->achievedValue() }}</div>
                        <div class="bg-red-100 p-2 rounded text-center">Remaining: {{ $target->remainingValue() }}</div>
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
                        Assigned by: {{ $target->parent?->creator?->name ?? 'Admin' }}
                    </div>
                    <div class="flex gap-4 mt-2">
                        <div class="bg-blue-100 p-2 rounded text-center">Total: {{ $target->target_value }}</div>
                        <div class="bg-green-100 p-2 rounded text-center">Achieved: {{ $target->achievedValue() }}</div>
                        <div class="bg-red-100 p-2 rounded text-center">Remaining: {{ $target->remainingValue() }}</div>
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
             class="bg-white rounded-xl shadow-xl w-11/12 md:w-3/4 max-h-[90vh] overflow-y-auto p-6 relative">

            {{-- Close button --}}
            <button @click="modalOpen=false; selectedTarget=null" 
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold">&times;</button>

            <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">Sales Details</h2>

            @php
                $allTargets = $targetsAssignedByAdmin->merge($targetsAssignedToExecutive);
            @endphp

            @foreach($allTargets as $target)
            <div x-show="selectedTarget === {{ $target->id }}" class="space-y-4">
                <div class="bg-gray-50 p-4 rounded shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-lg font-semibold">{{ $target->product?->name ?? '-' }} ({{ ucfirst($target->target_type) }})</div>
                        <div class="text-sm text-gray-500">Assigned by: {{ $target->creator?->name ?? 'Admin' }}</div>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="bg-blue-100 p-2 rounded text-center w-1/3">
                            <div class="text-xl font-bold text-blue-600">{{ $target->target_value }}</div>
                            <div class="text-gray-700 text-sm">Total</div>
                        </div>
                        <div class="bg-green-100 p-2 rounded text-center w-1/3">
                            <div class="text-xl font-bold text-green-600">{{ $target->achievedValue() }}</div>
                            <div class="text-gray-700 text-sm">Achieved</div>
                        </div>
                        <div class="bg-red-100 p-2 rounded text-center w-1/3">
                            <div class="text-xl font-bold text-red-600">{{ $target->remainingValue() }}</div>
                            <div class="text-gray-700 text-sm">Remaining</div>
                        </div>
                    </div>

                    {{-- Sales Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border rounded-lg">
                            <thead class="bg-blue-100 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2">Executive</th>
                                    <th class="px-3 py-2">Target</th>
                                    <th class="px-3 py-2">Achieved</th>
                                    <th class="px-3 py-2">Remaining</th>
                                    <th class="px-3 py-2">Sale Qty / Amount</th>
                                    <th class="px-3 py-2">Sale Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                {{-- User's own sales --}}
                                <tr class="bg-white">
                                    <td class="px-3 py-2">{{ $user->name }}</td>
                                    <td class="px-3 py-2">{{ $target->target_value - $target->children->sum('target_value') }}</td>
                                    <td class="px-3 py-2">{{ $target->sales->sum('quantity') ?? $target->sales->sum('amount') }}</td>
                                    <td class="px-3 py-2">{{ ($target->target_value - $target->children->sum('target_value')) - ($target->sales->sum('quantity') ?? $target->sales->sum('amount')) }}</td>
                                    <td class="px-3 py-2">
                                        @foreach($target->sales as $sale)
                                            <span class="block">{{ $sale->quantity ?? $sale->amount }} - {{ $sale->executive?->name ?? $user->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-3 py-2">
                                        @foreach($target->sales as $sale)
                                            <span class="block">{{ $sale->created_at->format('d-m-Y') }}</span>
                                        @endforeach
                                    </td>
                                </tr>

                                {{-- Child targets --}}
                                @foreach($target->children as $child)
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2">{{ $child->executive?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $child->target_value }}</td>
                                    <td class="px-3 py-2">{{ $child->sales->sum('quantity') ?? $child->sales->sum('amount') }}</td>
                                    <td class="px-3 py-2">{{ $child->target_value - ($child->sales->sum('quantity') ?? $child->sales->sum('amount')) }}</td>
                                    <td class="px-3 py-2">
                                        @foreach($child->sales as $sale)
                                            <span class="block">{{ $sale->quantity ?? $sale->amount }} - {{ $sale->executive?->name ?? '-' }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-3 py-2">
                                        @foreach($child->sales as $sale)
                                            <span class="block">{{ $sale->created_at->format('d-m-Y') }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            @endforeach

        </div>
    </div>


</div>

<script src="//unpkg.com/alpinejs" defer></script>

@endsection
