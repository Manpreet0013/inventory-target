@extends('layouts.admin')

@section('title','Product Details')

@section('content')
<div class="p-4 space-y-6">

    {{-- BACK BUTTON --}}
    <a href="{{ url()->previous() }}"
       class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
        ← Back
    </a>

    {{-- PRODUCT INFO --}}
    <div class="bg-white rounded shadow p-6 flex gap-6 items-center">
        <div class="w-40 h-40 flex-shrink-0">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover rounded shadow">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded text-gray-500 font-semibold">
                    No Image
                </div>
            @endif
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-bold mb-2 text-blue-700">{{ $product->name }}</h1>
            <div class="flex flex-wrap gap-4 text-gray-700 text-sm">
                <p><b>Composition:</b> {{ $product->composition ?? '-' }}</p>
                <p><b>Type:</b> <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded">{{ ucfirst($product->type) }}</span></p>
                <p><b>Expiry:</b> 
                    @if($product->expiry_date && \Carbon\Carbon::parse($product->expiry_date)->isPast())
                        <span class="px-2 py-1 bg-red-200 text-red-800 rounded">Expired</span>
                    @else
                        <span class="px-2 py-1 bg-green-200 text-green-800 rounded">{{ $product->expiry_date ?? '-' }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- TARGETS --}}
    <h2 class="text-2xl font-semibold text-gray-800">Targets & Sales</h2>

    @forelse($product->targets->whereNull('parent_id') as $target)
        @php
            $achieved = $target->target_type === 'box' ? $target->sales->sum('boxes_sold') : $target->sales->sum('amount');
            $assignedToTeam = $target->children->sum('target_value');
            $adminRemaining = max(($target->target_value - $assignedToTeam) - $achieved, 0);
            $percentage = $target->target_value > 0 ? round(($achieved / $target->target_value) * 100) : 0;

            // Parent target achieved
            $adminCompleted = $achieved >= ($target->target_value - $assignedToTeam);

            // Parent target accepted status (use your field, e.g., $target->status)
            $adminAccepted = $target->status ?? 'pending'; // accepted / pending / rejected

            // Classes for badges
            $statusClasses = [
                'pending' => 'bg-yellow-100 text-yellow-800 font-semibold',
                'accepted' => 'bg-green-100 text-green-800 font-semibold',
                'rejected' => 'bg-red-100 text-red-800 font-semibold',
            ];
        @endphp

        {{-- PARENT TARGET CARD --}}
        <div class="bg-white rounded shadow p-5 mb-5 border-l-4 border-blue-500 hover:shadow-lg transition">
            <div class="flex justify-between flex-wrap gap-4 items-start">
                <div>
                    <p><b>Product Admin:</b> <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-semibold">{{ $target->creator->name ?? 'Admin' }}</span></p>
                    <p><b>Total Target:</b> {{ $target->target_value }} ({{ ucfirst($target->target_type) }})</p>
                    <p><b>Achieved:</b> {{ $achieved }} | <b>Remaining:</b> {{ $adminRemaining }}</p>
                    <p><b>Status:</b> 
                        <span class="px-3 py-1 rounded-full text-xs {{ $adminCompleted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            Target Achieved: {{ $adminCompleted ? 'Yes' : 'No' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs {{ $statusClasses[$adminAccepted] ?? 'bg-gray-200 text-gray-800' }}">
                            Target Accepted: {{ ucfirst($adminAccepted) }}
                        </span>
                    </p>
                    <p><b>Duration:</b> {{ $target->start_date }} → {{ $target->end_date }}</p>
                </div>

                <div class="w-64">
                    <div class="text-xs mb-1 font-medium">{{ $achieved }} achieved / {{ $adminRemaining }} remaining</div>
                    <div class="w-full bg-gray-200 rounded h-3 overflow-hidden">
                        <div class="h-3 rounded {{ $adminCompleted ? 'bg-green-600' : 'bg-blue-500' }}" 
                             style="width: {{ min($percentage,100) }}%"></div>
                    </div>
                    <div class="text-xs mt-1 {{ $adminCompleted ? 'text-green-700' : 'text-gray-700' }}">
                        {{ $percentage }}%
                    </div>
                </div>
            </div>

            {{-- PARENT SALES --}}
            @if($target->sales->count())
            <div class="mt-5 overflow-x-auto">
                <h4 class="text-sm font-semibold mb-2 text-gray-700">Product Admin Sales</h4>
                <table class="w-full text-xs border table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">#</th>
                            <th class="border px-2 py-1">Party</th>
                            <th class="border px-2 py-1">Value</th>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Product Admin Status</th>
                            <th class="border px-2 py-1">Accountant Status</th>
                            <th class="border px-2 py-1">Overall Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($target->sales as $pIndex => $sale)
                            @php
                                // Determine overall status
                                $overallStatus = ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'approved' : 'pending';

                                // Badge classes
                                $badgeClasses = [
                                    'approved' => 'bg-green-600 text-white font-semibold',
                                    'pending' => 'bg-yellow-500 text-white font-semibold',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="border px-2 py-1">{{ $pIndex + 1 }}</td>
                                <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                <td class="border px-2 py-1">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                <td class="border px-2 py-1">
                                    <span class="px-3 py-1 rounded-full text-xs {{ $badgeClasses[$sale->status] ?? 'bg-gray-200 text-gray-800' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="border px-2 py-1">
                                    <span class="px-3 py-1 rounded-full text-xs {{ $badgeClasses[$sale->accountant_status] ?? 'bg-gray-200 text-gray-800' }}">
                                        {{ ucfirst($sale->accountant_status) }}
                                    </span>
                                </td>
                                <td class="border px-2 py-1">
                                    <span class="px-3 py-1 rounded-full text-xs {{ $badgeClasses[$overallStatus] }}">
                                        {{ ucfirst($overallStatus) }}
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


            {{-- CHILD TARGETS --}}
            @if($target->children->count())
                <div class="ml-6 mt-5 border-l-2 border-gray-300 pl-4 space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700">Assigned Targets</h4>

                    @foreach($target->children as $child)
                        @php
                            $childAchieved = $child->target_type === 'box' ? $child->sales->sum('boxes_sold') : $child->sales->sum('amount');
                            $childRemaining = max($child->target_value - $childAchieved, 0);
                            $childPercentage = $child->target_value > 0 ? round(($childAchieved / $child->target_value) * 100) : 0;
                            $childCompleted = $childAchieved >= $child->target_value;
                        @endphp

                        <div class="bg-gray-50 rounded p-4 border-l-4 border-purple-500 hover:shadow-md transition">
                            <div class="flex justify-between flex-wrap gap-4 items-start text-sm">
                                <div>
                                    <p><b>User:</b> <span class="px-2 py-1 rounded bg-blue-500 text-white text-xs">{{ $child->executive->name ?? '-' }}</span></p>
                                    <p><b>Target:</b> {{ $child->target_value }} ({{ ucfirst($child->target_type) }})</p>
                                    <p><b>Achieved / Remaining:</b> {{ $childAchieved }} / {{ $childRemaining }}</p>
                                </div>

                                <div class="w-48">
                                    <div class="w-full bg-gray-200 rounded h-2 overflow-hidden">
                                        <div class="h-2 rounded {{ $childCompleted ? 'bg-green-600' : 'bg-purple-500' }}" 
                                             style="width: {{ min($childPercentage,100) }}%"></div>
                                    </div>
                                    <div class="text-xs mt-1 {{ $childCompleted ? 'text-green-700' : 'text-gray-700' }}">
                                        {{ $childPercentage }}%
                                    </div>
                                </div>
                            </div>

                            {{-- SALES --}}
                            @if($child->sales->count())
                                <div class="mt-3 overflow-x-auto">
                                    <h5 class="text-xs font-semibold mb-1 text-gray-700">Sales</h5>
                                    <table class="w-full text-xs border table-auto">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border px-2 py-1">#</th>
                                                <th class="border px-2 py-1">Party</th>
                                                <th class="border px-2 py-1">Value</th>
                                                <th class="border px-2 py-1">Date</th>
                                                <th class="border px-2 py-1">Product Admin Status</th>
                                                <th class="border px-2 py-1">Accountant Status</th>
                                                <th class="border px-2 py-1">Overall Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($child->sales as $cIndex => $sale)
                                                @php
                                                    // Determine overall status
                                                    $overallStatus = ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'approved' : 'pending';

                                                    // Badge classes
                                                    $badgeClasses = [
                                                        'approved' => 'bg-green-600 text-white font-semibold',
                                                        'pending' => 'bg-yellow-500 text-white font-semibold',
                                                    ];
                                                @endphp
                                                <tr class="hover:bg-gray-50">
                                                    <td class="border px-2 py-1">{{ $cIndex + 1 }}</td>
                                                    <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                                                    <td class="border px-2 py-1">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                                    <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                                                    <td class="border px-2 py-1">
                                                        <span class="px-3 py-1 rounded-full text-xs {{ $badgeClasses[$sale->status] ?? 'bg-gray-200 text-gray-800' }}">
                                                            {{ ucfirst($sale->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="border px-2 py-1">
                                                        <span class="px-3 py-1 rounded-full text-xs {{ $badgeClasses[$sale->accountant_status] ?? 'bg-gray-200 text-gray-800' }}">
                                                            {{ ucfirst($sale->accountant_status) }}
                                                        </span>
                                                    </td>
                                                    <td class="border px-2 py-1">
                                                        <span class="px-3 py-1 rounded-full text-xs {{ $badgeClasses[$overallStatus] }}">
                                                            {{ ucfirst($overallStatus) }}
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
        </div>
    @empty
        <p class="text-gray-500">No targets available.</p>
    @endforelse

</div>
@endsection
