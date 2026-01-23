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
    <div class="bg-white rounded-2xl shadow-lg p-6 flex gap-6 items-start hover:shadow-xl transition-shadow duration-300">
    
        <!-- Product Image -->
        <div class="w-40 h-40 flex-shrink-0 rounded-xl overflow-hidden border border-gray-200">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
            @else
                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400 font-medium">
                    No Image
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="flex-1 flex flex-col justify-between">
            <!-- Name -->
            <h1 class="text-2xl md:text-3xl font-extrabold text-blue-600 mb-3 line-clamp-2">{{ $product->name }}</h1>

            <!-- Info Badges -->
            <div class="text-md">
                <p class="text-gray-700"><span class="font-semibold">Composition:</span> {{ $product->composition ?? '-' }}</p>

                <p>
                    <span class="font-semibold">Type:</span>
                    <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 font-medium">{{ ucfirst($product->type) }}</span>
                </p>

                <p>
                    <span class="font-semibold">Expiry:</span>
                    @if($product->expiry_date && \Carbon\Carbon::parse($product->expiry_date)->isPast())
                        <span class="px-3 py-1 rounded-full bg-red-200 text-red-800 font-medium">Expired</span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-green-200 text-green-800 font-medium">{{ $product->expiry_date ?? '-' }}</span>
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
        <div class="bg-white rounded-2xl shadow-lg p-6  justify-between gap-6 hover:shadow-xl transition-shadow duration-300">

            <!-- Target Info -->
            <div class="flex-1 flex flex-col gap-2">
                <p>
                    <b>Product Admin:</b>
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">
                        {{ $target->creator->name ?? 'Admin' }}
                    </span>
                </p>
                <p><b>Total Target:</b> {{ $target->target_value }} ({{ ucfirst($target->target_type) }})</p>
                <p><b>Achieved:</b> {{ $achieved }} | <b>Remaining:</b> {{ $adminRemaining }}</p>
                
                <p class="flex flex-wrap gap-2 items-center">
                    <b>Status:</b>
                    <span class="px-3 py-1 rounded-full text-xs {{ $adminCompleted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        Target Achieved: {{ $adminCompleted ? 'Yes' : 'No' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs {{ $statusClasses[$adminAccepted] ?? 'bg-gray-200 text-gray-800' }}">
                        Target Accepted: {{ ucfirst($adminAccepted) }}
                    </span>
                </p>

                <p><b>Duration:</b> {{ $target->start_date }} → {{ $target->end_date }}</p>
            </div>

            <!-- Progress Bar -->
            <div class="w-full md:w-64 flex flex-col gap-2">
                <div class="flex justify-between items-center text-xs font-medium">
                    <span>{{ $achieved }} achieved</span>
                    <span>{{ $adminRemaining }} remaining</span>
                </div>

                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                    <div class="h-4 rounded-full {{ $adminCompleted ? 'bg-green-500' : 'bg-gradient-to-r from-blue-500 to-indigo-400' }}"
                         style="width: {{ min($percentage,100) }}%"></div>
                </div>

                <div class="text-right text-xs font-semibold {{ $adminCompleted ? 'text-green-700' : 'text-gray-700' }}">
                    {{ $percentage }}%
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">

            <h1 class="text-2xl font-bold text-blue-700 mb-4">Members</h1>

            <div class="overflow-x-auto rounded-lg shadow border mt-4">
                <table class="min-w-full divide-y divide-gray-200 table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Member Name</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Parent Name</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Target Value</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Total Sales</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Pending Sales</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($product->targets as $target)
                            @php
                                $today = \Carbon\Carbon::today();
                                $trBg = '';
                                $trHover = '';
                                $status = '';

                                if ($today->gt(\Carbon\Carbon::parse($target->end_date))) {
                                    $status = 'Target Expired';
                                    $trBg = 'bg-gray-100 font-bold';
                                    $trHover = 'hover:bg-gray-200';
                                } else {
                                    $valueColumn = $target->target_type === 'amount' ? 'amount' : 'boxes_sold';

                                    $validSales = $target->sales->filter(fn($sale) => $sale->status === 'approved' && $sale->accountant_status === 'approved');
                                    $totalSales = $validSales->sum($valueColumn);

                                    $executiveCount = $target->executives_count ?? 1;
                                    $perExecutiveTarget = $target->target_value / max($executiveCount, 1);

                                    $executivesMetTarget = $validSales->groupBy('executive_id')->filter(fn($sales) => $sales->sum($valueColumn) >= $perExecutiveTarget)->count();

                                    if ($totalSales >= $target->target_value) {
                                        $status = $executivesMetTarget == $executiveCount 
                                            ? 'Target Achieved - (Full participation)' 
                                            : 'Target Achieved - (Partial participation)';
                                        $trBg = $executivesMetTarget == $executiveCount ? 'bg-green-100 font-bold' : 'bg-yellow-100 font-bold';
                                        $trHover = 'hover:bg-green-200';
                                    } else {
                                        $status = 'Target Not Achieved';
                                        $trBg = 'bg-red-100 font-bold';
                                        $trHover = 'hover:bg-red-200';
                                    }
                                }
                            @endphp

                            {{-- Parent Target --}}
                            @if(is_null($target->parent_id))
                                <tr class="{{ $trBg }} {{ $trHover }}">
                                    <td class="px-4 py-2 font-bold">{{ $target->executive->name ?? 'Product Admin' }} (Product Admin)</td>
                                    <td class="px-4 py-2 font-bold">Admin</td>
                                    <td class="px-4 py-2 font-bold">{{ $target->target_value }}</td>
                                    <td class="px-4 py-2 font-bold">{{ $totalSales }}</td>
                                    <td class="px-4 py-2 font-bold">{{ max($target->target_value - $totalSales, 0) }}</td>
                                    <td class="px-4 py-2 font-bold">{{ $status }}</td>
                                </tr>
                            @endif

                            {{-- Child Targets --}}
                            @foreach($target->children as $child)
                                @php
                                    $childValueColumn = $child->target_type === 'amount' ? 'amount' : 'boxes_sold';
                                    $childValidSales = $child->sales->filter(fn($sale) => $sale->status === 'approved' && $sale->accountant_status === 'approved');
                                    $childTotalSales = $childValidSales->sum($childValueColumn);
                                    $childPending = max($child->target_value - $childTotalSales, 0);

                                    if ($today->gt(\Carbon\Carbon::parse($child->end_date))) {
                                        $childStatus = 'Target Expired';
                                        $childBg = 'bg-gray-50';
                                        $childHover = 'hover:bg-gray-100';
                                    } else {
                                        $childStatus = $childTotalSales >= $child->target_value ? 'Target Achieved' : 'Target Not Achieved';
                                        $childBg = $childTotalSales >= $child->target_value ? 'bg-green-50' : 'bg-red-50';
                                        $childHover = $childTotalSales >= $child->target_value ? 'hover:bg-green-100' : 'hover:bg-red-100';
                                    }
                                @endphp
                                <tr class="{{ $childBg }} {{ $childHover }}">
                                    <td class="px-4 py-2 pl-8">{{ $child->executive->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-2">{{ $target->executive->name ?? 'Product Admin' }}</td>
                                    <td class="px-4 py-2">{{ $child->target_value }}</td>
                                    <td class="px-4 py-2">{{ $childTotalSales }}</td>
                                    <td class="px-4 py-2">{{ $childPending }}</td>
                                    <td class="px-4 py-2">{{ $childStatus }}</td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">No targets assigned.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- PARENT SALES --}}
        @if($target->sales->count())
            <div class="mt-5">
                <h4 class="text-sm font-semibold mb-3 text-gray-700">Product Admin Sales</h4>
                <div class="overflow-x-auto rounded-lg shadow border">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Party</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Value</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Product Admin Status</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Accountant Status</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Overall Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($target->sales as $pIndex => $sale)
                                @php
                                    $overallStatus = ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'approved' : 'pending';
                                    $badgeClasses = [
                                        'approved' => 'bg-green-500 text-white font-semibold',
                                        'pending' => 'bg-yellow-400 text-white font-semibold',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $pIndex + 1 }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $sale->party_name }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $sale->sale_date }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $badgeClasses[$sale->status] ?? 'bg-gray-200 text-gray-800' }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $badgeClasses[$sale->accountant_status] ?? 'bg-gray-200 text-gray-800' }}">
                                            {{ ucfirst($sale->accountant_status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $badgeClasses[$overallStatus] }}">
                                            {{ ucfirst($overallStatus) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
        <div class="mt-2 text-xs bg-yellow-100 p-3 rounded shadow text-gray-700">
            No sales added yet.
        </div>
        @endif


        {{-- CHILD TARGETS --}}
        @if($target->children->count())
        <div class="ml-6 mt-5 space-y-6 border-l-2 border-gray-300 pl-4">
            <h4 class="text-sm font-semibold text-gray-700">Assigned Targets</h4>

            @foreach($target->children as $child)
                @php
                    $childAchieved = $child->target_type === 'box' ? $child->sales->sum('boxes_sold') : $child->sales->sum('amount');
                    $childRemaining = max($child->target_value - $childAchieved, 0);
                    $childPercentage = $child->target_value > 0 ? round(($childAchieved / $child->target_value) * 100) : 0;
                    $childCompleted = $childAchieved >= $child->target_value;
                @endphp

                <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                    <div class="flex justify-between flex-wrap gap-4 items-start text-sm">
                        {{-- Target Info --}}
                        <div class="space-y-1">
                            <p>
                                <b>User:</b>
                                <span class="px-2 py-1 rounded-full bg-blue-500 text-white text-xs font-medium">
                                    {{ $child->executive->name ?? '-' }}
                                </span>
                            </p>
                            <p><b>Target:</b> {{ $child->target_value }} ({{ ucfirst($child->target_type) }})</p>
                            <p><b>Achieved / Remaining:</b> {{ $childAchieved }} / {{ $childRemaining }}</p>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="w-48">
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="h-3 rounded-full {{ $childCompleted ? 'bg-green-500' : 'bg-purple-500' }}" style="width: {{ min($childPercentage,100) }}%"></div>
                            </div>
                            <div class="text-xs mt-1 font-medium {{ $childCompleted ? 'text-green-700' : 'text-gray-700' }}">
                                {{ $childPercentage }}%
                            </div>
                        </div>
                    </div>

                    {{-- Sales Table --}}
                    @if($child->sales->count())
                        <div class="mt-4 overflow-x-auto rounded-lg shadow border">
                            <h5 class="text-xs font-semibold mb-2 text-gray-700">Sales</h5>
                            <table class="min-w-full divide-y divide-gray-200 table-auto text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">#</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Party</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Value</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Date</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Product Admin Status</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Accountant Status</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Overall Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($child->sales as $cIndex => $sale)
                                        @php
                                            $overallStatus = ($sale->status === 'approved' && $sale->accountant_status === 'approved') ? 'approved' : 'pending';
                                            $badgeClasses = [
                                                'approved' => 'bg-green-500 text-white font-semibold',
                                                'pending' => 'bg-yellow-400 text-white font-semibold',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 py-2 whitespace-nowrap">{{ $cIndex + 1 }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap">{{ $sale->party_name }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap">{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap">{{ $sale->sale_date }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="px-2 py-1 rounded-full text-xs {{ $badgeClasses[$sale->status] ?? 'bg-gray-200 text-gray-800' }}">
                                                    {{ ucfirst($sale->status) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="px-2 py-1 rounded-full text-xs {{ $badgeClasses[$sale->accountant_status] ?? 'bg-gray-200 text-gray-800' }}">
                                                    {{ ucfirst($sale->accountant_status) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="px-2 py-1 rounded-full text-xs {{ $badgeClasses[$overallStatus] }}">
                                                    {{ ucfirst($overallStatus) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="mt-2 text-xs bg-yellow-100 p-3 rounded shadow text-gray-700">
                            No sales added yet.
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    @empty
        <p class="text-gray-500">No targets available.</p>
    @endforelse

</div>
@endsection
