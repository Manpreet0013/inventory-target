@extends('layouts.executive')

@section('title','Executive Report')

@section('content')

<h2 class="text-2xl font-bold mb-4">📊 Executive Sales Report</h2>

@php
    $totalTargets = $targets->sum('target_value');
    $totalCompleted = $targets->sum(fn($t) => $t->sales->sum(fn($s) => $s->boxes_sold ?? $s->amount ?? 0));
    $totalRemaining = max($totalTargets - $totalCompleted, 0);
@endphp

{{-- ===== SUMMARY ===== --}}
<div class="flex flex-wrap gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow text-center flex-1">
        <p class="text-gray-500 text-sm">Total Targets</p>
        <p class="text-xl font-bold">{{ count($targets) }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow text-center flex-1">
        <p class="text-gray-500 text-sm">Target Boxes</p>
        <p class="text-xl font-bold">{{ $totalTargets }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow text-center flex-1">
        <p class="text-gray-500 text-sm">Completed</p>
        <p class="text-xl font-bold text-green-600">{{ $totalCompleted }}</p>
        <p class="text-red-600 text-sm">Remaining: {{ $totalRemaining }}</p>
    </div>
</div>

{{-- ===== TARGET DETAILS TABLE ===== --}}
<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full border-collapse text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-3 py-2 border">Product</th>
                <th class="px-3 py-2 border">Target</th>
                <th class="px-3 py-2 border">Completed</th>
                <th class="px-3 py-2 border">Remaining</th>
                <th class="px-3 py-2 border">Assigned By</th>
                <th class="px-3 py-2 border">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($targets as $target)
                @php
                    $completed = $target->sales->sum(fn($s) => $s->boxes_sold ?? $s->amount ?? 0);
                    $remaining = max($target->target_value - $completed, 0);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 border">{{ $target->product->name ?? 'N/A' }}</td>
                    <td class="px-3 py-2 border">{{ $target->target_value }}</td>
                    <td class="px-3 py-2 border text-green-600 font-semibold">{{ $completed }}</td>
                    <td class="px-3 py-2 border text-red-600 font-semibold">{{ $remaining }}</td>
                    <td class="px-3 py-2 border">{{ $target->creator?->name ?? 'Admin' }}</td>
                    <td class="px-3 py-2 border">
                        <span class="{{ $remaining == 0 ? 'text-green-600' : 'text-yellow-600' }} font-semibold">
                            {{ $remaining == 0 ? 'Completed' : 'In Progress' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-gray-500">No targets assigned yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== OPTIONAL SMALL CHART ===== --}}
@if(count($targets))
<div class="bg-white p-4 rounded shadow mt-6">
    <h3 class="font-semibold mb-2">🎯 Target vs Completed Sales</h3>
    <canvas id="targetSalesChart" height="150"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const targets = @json($targets);
new Chart(document.getElementById('targetSalesChart'), {
    type: 'bar',
    data: {
        labels: targets.map(t => t.product?.name ?? 'N/A'),
        datasets: [
            { label: 'Target', data: targets.map(t => t.target_value), backgroundColor: '#3b82f6' },
            { label: 'Completed', data: targets.map(t => t.sales.reduce((sum,s)=>sum + Number(s.boxes_sold ?? s.amount ?? 0),0)), backgroundColor: '#10b981' }
        ]
    },
    options: { responsive:true, scales: { y: { beginAtZero: true } } }
});
</script>
@endif

@endsection
