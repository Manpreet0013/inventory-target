@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        📊 Admin Overview Report
    </h1>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <a href="{{ route('admin.products') }}" class="rounded-xl bg-green-500 text-white p-5 shadow hover:shadow-lg transition block">
            <p class="text-sm opacity-90">Total Products</p>
            <h2 class="text-3xl font-bold mt-2">{{ $totalProducts }}</h2>
        </a>

        <a href="{{ route('admin.users.index') }}" class="rounded-xl bg-yellow-500 text-white p-5 shadow hover:shadow-lg transition block">
            <p class="text-sm opacity-90">Total Executives</p>
            <h2 class="text-3xl font-bold mt-2">{{ $totalExecutives }}</h2>
        </a>

        <a href="{{ route('admin.list') }}" class="rounded-xl bg-blue-500 text-white p-5 shadow hover:shadow-lg transition block">
            <p class="text-sm opacity-90">Main Targets</p>
            <h2 class="text-3xl font-bold mt-2">{{ $totalTargets }}</h2>
        </a>

    </div>

    <!-- LATEST TARGETS TABLE -->
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        🕒 Latest Main Targets
    </h2>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <h2 class="text-xl font-semibold mb-4"> Target Progress</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($latestTargets as $target)

            @php
                $targetValue   = $target->target_value;
                $achievedValue = $target->children->sum('target_value') ?? 0;
                $remaining     = max($targetValue - $achievedValue, 0);
                $progress = $targetValue > 0 ? round(($achievedValue / $targetValue) * 100) : 0;
                $progress = min($progress, 100);

                $productType = $target->product?->type ?? '-';
                $expiryDate  = $target->product?->expiry_date?->format('d M Y') ?? '-';
            @endphp

            <a href="{{ route('admin.sales.details', ['product' => $target->product->id, 'target' => $target->id]) }}"
               class="bg-white rounded-lg shadow p-4 border hover:shadow-lg transition block">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $target->product->name ?? '-' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            Executive: {{ $target->executive->name ?? 'N/A' }}
                        </p>
                    </div>

                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $progress >= 100 ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $progress }}%
                    </span>
                </div>

                <!-- PRODUCT TYPE & EXPIRY -->
                <div class="flex justify-between text-xs text-gray-600 mb-3">
                    <span class="bg-gray-100 px-2 py-1 rounded">Type: {{ $productType }}</span>
                    <span class="bg-gray-100 px-2 py-1 rounded">Expiry: {{ $expiryDate }}</span>
                </div>

                <!-- PROGRESS BAR -->
                <div class="w-full bg-gray-200 h-2 rounded-full mb-3">
                    <div
                        class="h-2 rounded-full transition-all duration-500
                        {{ $progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                        style="width: {{ $progress }}%">
                    </div>
                </div>

                <!-- STATS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <div>
                        <p class="text-gray-500">Target</p>
                        <p class="font-semibold">{{ $targetValue }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Achieved</p>
                        <p class="font-semibold text-green-600">{{ $achievedValue }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Remaining</p>
                        <p class="font-semibold text-red-500">{{ $remaining }}</p>
                    </div>
                </div>

            </a>

            @endforeach
            </div>

    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(statusChart, {
        type: 'doughnut',
        data: {
            labels: ['Accepted','Pending','Rejected'],
            datasets: [{
                data: [
                    {{ $chartStatusData['active'] }},
                    {{ $chartStatusData['pending'] }},
                    {{ $chartStatusData['expired'] }}
                ],
                backgroundColor: ['#22c55e','#facc15','#ef4444']
            }]
        }
    });

    new Chart(productChart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($targetsByProduct->keys()) !!},
            datasets: [{
                data: {!! json_encode($targetsByProduct->values()) !!},
                backgroundColor: '#3b82f6'
            }]
        }
    });

    new Chart(executiveChart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($targetsByExecutive->keys()) !!},
            datasets: [{
                data: {!! json_encode($targetsByExecutive->values()) !!},
                backgroundColor: '#fb923c'
            }]
        }
    });
</script>
@endsection
