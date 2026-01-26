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

<div x-data="{ 
            tab: 'assignedByAdmin', 
            modalOpen: false, 
            teamModal: false,
            selectedTarget: null,
            selectedTeamTarget: null
        }">

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
                <div class="flex gap-2">
                    <button @click="modalOpen = true; selectedTarget = {{ $target->id }}" 
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-indigo-600">
                        View Sales
                    </button>

                    <button @click="teamModal = true; selectedTeamTarget = {{ $target->id }}" 
                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                        View Team
                    </button>
                </div>

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
                <div class="flex gap-2">
                    <button @click="modalOpen = true; selectedTarget = {{ $target->id }}" 
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-indigo-600">
                        View Sales
                    </button>

                    <button @click="teamModal = true; selectedTeamTarget = {{ $target->id }}" 
                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                        View Team
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- VIEW SALES MODAL --}}
    <div x-show="modalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">

        <div x-transition.scale
             class="bg-white rounded-2xl shadow-2xl w-11/12 md:w-5/6 max-h-[90vh] overflow-y-auto p-6 relative">

            {{-- CLOSE --}}
            <button @click="modalOpen=false; selectedTarget=null"
                    class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-3xl font-bold">
                &times;
            </button>

            {{-- HEADER --}}
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800 text-center">
                    Target & Sales Breakdown
                </h2>
                <p class="text-sm text-gray-500 text-center">
                    Target progress, team performance & sales approval status
                </p>
            </div>

            @php
                $allTargets = $targetsAssignedByAdmin->merge($targetsAssignedToExecutive);
            @endphp

            @foreach($allTargets as $target)
            <div x-show="selectedTarget === {{ $target->id }}" class="space-y-8">

                @php
                    // ADMIN CALCULATIONS
                    $assignedToTeam = $target->children->sum('target_value');

                    $adminAchieved = $target->sales->sum(
                        $target->target_type === 'box' ? 'boxes_sold' : 'amount'
                    );

                    $adminRemaining = max(
                        ($target->target_value - $assignedToTeam) - $adminAchieved,
                        0
                    );

                    // ADMIN TARGET STATUS
                    $adminCompleted = $adminAchieved >= ($target->target_value - $assignedToTeam);
                    $adminTargetText = $adminCompleted ? 'Target Completed' : 'Target Pending';
                    $adminTargetClass = $adminCompleted
                        ? 'bg-green-100 text-green-700'
                        : 'bg-yellow-100 text-yellow-700';
                @endphp

                <p class="text-sm text-gray-600">Target Status 
                    <span class="inline-block mt-2 px-4 py-1 rounded-full text-sm font-semibold {{ $adminTargetClass }}">
                        {{ $adminTargetText }}
                    </span>
                </p>


                {{-- SUMMARY CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                        <p class="text-sm text-blue-600">Total Target</p>
                        <p class="text-2xl font-bold text-blue-800">
                            {{ $target->target_value }}
                        </p>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                        <p class="text-sm text-green-600">Achieved</p>
                        <p class="text-2xl font-bold text-green-800">
                            {{ $adminAchieved }}
                        </p>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-sm text-red-600">Remaining</p>
                        <p class="text-2xl font-bold text-red-800">
                            {{ $adminRemaining }}
                        </p>
                    </div>

                </div>

                {{-- TEAM PERFORMANCE TABLE --}}
                <div class="overflow-x-auto border rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2 text-center">Assigned</th>
                                <th class="px-3 py-2 text-center">Achieved</th>
                                <th class="px-3 py-2 text-center">Remaining</th>
                                <th class="px-3 py-2 text-center">Target Achieved</th>
                                <th class="px-3 py-2 text-center">Target Accepted</th>
                                <th class="px-3 py-2 text-center">Sales</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            {{-- ADMIN ROW --}}
                            <tr class="bg-white font-medium">
                                {{-- USER --}}
                                <td class="px-3 py-2">
                                    {{ $user->name }}
                                    <span class="block text-xs text-blue-600">(Product Admin)</span>
                                </td>

                                {{-- ASSIGNED --}}
                                <td class="px-3 py-2 text-center">
                                    {{ $target->target_value - $assignedToTeam }}
                                </td>

                                {{-- ACHIEVED --}}
                                <td class="px-3 py-2 text-center text-green-700 font-semibold">
                                    {{ $adminAchieved }}
                                </td>

                                {{-- REMAINING --}}
                                <td class="px-3 py-2 text-center text-red-600">
                                    {{ $adminRemaining }}
                                </td>

                                {{-- TARGET ACHIEVED --}}
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-1 rounded text-xs {{ $adminCompleted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $adminCompleted ? 'Yes' : 'No' }}
                                    </span>
                                </td>

                                {{-- TARGET ACCEPTED --}}
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">
                                        Accepted
                                    </span>
                                </td>

                                {{-- SALES --}}
                                <td class="px-3 py-2 space-y-1">
                                    @forelse($target->sales as $sale)
                                        <div class="text-xs bg-white border rounded px-2 py-1 flex flex-col space-y-1 mb-2">
                                            <div class="flex justify-between items-center">
                                                <span>
                                                    {{ $target->target_type === 'box' ? $sale->boxes_sold : '₹'.$sale->amount }}
                                                </span>
                                                <span class="px-2 py-1 rounded text-white text-[10px] font-semibold 
                                                             {{ ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'bg-green-600' : 'bg-yellow-500' }}">
                                                    {{ ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'Approved' : 'Pending' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center space-x-1">
                                                Product Admin Status: <span class="px-2 py-1 rounded text-white text-[10px] 
                                                             {{ $sale->status === 'approved' ? 'bg-green-600' : 'bg-yellow-500' }}">
                                                    {{ $sale->status }}
                                                </span>
                                                Accountant: <span class="px-2 py-1 rounded text-white text-[10px] 
                                                             {{ $sale->accountant_status === 'approved' ? 'bg-green-600' : 'bg-yellow-500' }}">
                                                    {{ $sale->accountant_status }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-xs text-gray-400">No sales</span>
                                    @endforelse
                                </td>
                            </tr>

                            {{-- EXECUTIVE ROWS --}}
                            @foreach($target->children as $child)

                                @php
                                    $childAchieved = $child->sales->sum(
                                        $child->target_type === 'box' ? 'boxes_sold' : 'amount'
                                    );

                                    $childRemaining = max($child->target_value - $childAchieved, 0);

                                    $childCompleted = $childAchieved >= $child->target_value;
                                    $childTargetText = $childCompleted ? 'Target Completed' : 'Target Pending';
                                    $childTargetClass = $childCompleted
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700';
                                @endphp

                                <tr class="bg-gray-50">
                                    {{-- USER --}}
                                    <td class="px-3 py-2">
                                        {{ $child->executive?->name ?? '-' }}
                                    </td>

                                    {{-- ASSIGNED --}}
                                    <td class="px-3 py-2 text-center">
                                        {{ $child->target_value }}
                                    </td>

                                    {{-- ACHIEVED --}}
                                    <td class="px-3 py-2 text-center text-green-700 font-semibold">
                                        {{ $childAchieved }}
                                    </td>

                                    {{-- REMAINING --}}
                                    <td class="px-3 py-2 text-center text-red-600">
                                        {{ $childRemaining }}
                                    </td>

                                    {{-- TARGET ACHIEVED --}}
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-2 py-1 rounded text-xs {{ $childCompleted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $childCompleted ? 'Yes' : 'No' }}
                                        </span>
                                    </td>

                                    {{-- TARGET ACCEPTED --}}
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">
                                            {{ ucfirst($child->status ?? 'pending') }}
                                        </span>
                                    </td>

                                    {{-- SALES --}}
                                    <td class="px-3 py-2 space-y-1">
                                        @forelse($child->sales as $sale)
                                            <div class="text-xs bg-white border rounded px-2 py-1 flex flex-col space-y-1 mb-2">
                                                <div class="flex justify-between items-center">
                                                    <span>
                                                        {{ $child->target_type === 'box' ? $sale->boxes_sold : '₹'.$sale->amount }}
                                                    </span>
                                                    <span class="px-2 py-1 rounded text-white text-[10px] font-semibold 
                                                                 {{ ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'bg-green-600' : 'bg-yellow-500' }}">
                                                        {{ ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'Approved' : 'Pending' }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center space-x-1">
                                                    Product Admin Status: <span class="px-2 py-1 rounded text-white text-[10px] 
                                                                 {{ $sale->status === 'approved' ? 'bg-green-600' : 'bg-yellow-500' }}">
                                                        {{ $sale->status }}
                                                    </span>
                                                    Accountant: <span class="px-2 py-1 rounded text-white text-[10px] 
                                                                 {{ $sale->accountant_status === 'approved' ? 'bg-green-600' : 'bg-yellow-500' }}">
                                                        {{ $sale->accountant_status }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-xs text-gray-400">No sales</span>
                                        @endforelse
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

    {{-- TEAM LIST MODAL --}}
    <div x-show="teamModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">

        <div x-transition.scale
             class="bg-white rounded-xl shadow-xl w-11/12 md:w-1/2 max-h-[80vh] overflow-y-auto p-6 relative">

            {{-- Close --}}
            <button @click="teamModal=false; selectedTeamTarget=null"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-center text-gray-800">
                Team Members
            </h2>

            @foreach($allTargets as $target)
            <div x-show="selectedTeamTarget === {{ $target->id }}">

                @if($target->children->count())
                    <table class="min-w-full text-sm border rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Users</th>
                                <th class="px-3 py-2 text-center">Assigned</th>
                                <th class="px-3 py-2 text-center">Achieved</th>
                                <th class="px-3 py-2 text-center">Pending</th>
                                <th class="px-3 py-2 text-center">Request Status</th>
                                <th class="px-3 py-2 text-center">Target Status</th>
                            </tr>
                        </thead>


                        <tbody class="divide-y">
                            @foreach($target->children as $child)

                                @php
                                    // ACHIEVED
                                    $childAchieved = $child->sales->sum(
                                        $child->target_type === 'box' ? 'boxes_sold' : 'amount'
                                    );

                                    // PENDING / REMAINING
                                    $childPending = max($child->target_value - $childAchieved, 0);

                                    // TARGET COMPLETION STATUS
                                    $isCompleted = $childAchieved >= $child->target_value;

                                    $statusText  = $isCompleted ? 'Target Completed' : 'Target Pending';
                                    $statusClass = $isCompleted
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700';
                                @endphp

                                @php
                                    // REQUEST STATUS (Approval)
                                    $requestStatus = $child->status ?? 'pending';

                                    $requestStatusClass = match($requestStatus) {
                                        'accepted' => 'bg-blue-100 text-blue-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp


                                <tr>
                                    <td class="px-3 py-2">
                                        {{ $child->executive?->name ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        {{ $child->target_value }}
                                    </td>

                                    <td class="px-3 py-2 text-center text-green-700 font-semibold">
                                        {{ $childAchieved }}
                                    </td>

                                    <td class="px-3 py-2 text-center text-red-600 font-semibold">
                                        {{ $childPending }}
                                    </td>

                                    {{-- REQUEST STATUS --}}
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $requestStatusClass }}">
                                            {{ ucfirst($requestStatus) }}
                                        </span>
                                    </td>

                                    {{-- TARGET STATUS --}}
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                @else
                    <p class="text-center text-gray-500">No team assigned.</p>
                @endif

            </div>
            @endforeach
        </div>
    </div>

</div>

<script src="//unpkg.com/alpinejs" defer></script>

@endsection
