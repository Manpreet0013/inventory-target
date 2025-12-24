@extends('layouts.executive')

@section('title','Executive Report')

@section('content')

<h2 class="text-2xl font-bold mb-6">📊 Executive Sales Report</h2>

@php
    // Prepare backend-safe data
    $labels = $targets->map(fn($t) => $t->product->name ?? 'N/A')->toArray();
    $targetValues = $targets->pluck('target_value')->toArray();
    $completedSales = $targets->map(function ($t) {
        return $t->sales->sum(fn($s) => $s->boxes_sold ?? $s->amount ?? 0);
    })->toArray();
@endphp

{{-- =================== SUMMARY CARDS =================== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <p class="text-gray-500 text-sm">Total Targets</p>
        <p class="text-2xl font-bold">{{ count($targets) }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-gray-500 text-sm">Total Target Boxes</p>
        <p class="text-2xl font-bold">{{ array_sum($targetValues) }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-gray-500 text-sm">Total Sales Completed</p>
        <p class="text-2xl font-bold">{{ array_sum($completedSales) }}</p>
    </div>
</div>

{{-- =================== CHARTS =================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Target vs Sales --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">🎯 Target vs Completed Sales</h3>
        <canvas id="targetSalesChart" height="200"></canvas>
    </div>

    {{-- Assigned By --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">👤 Assigned By</h3>
        <canvas id="assignedByChart" height="200"></canvas>
    </div>

</div>

{{-- =================== SALES TREND =================== --}}
<div class="bg-white p-4 rounded shadow mb-8">
    <h3 class="font-semibold mb-3">📈 Sales Trend (Date Wise)</h3>
    <canvas id="salesTrendChart" height="150"></canvas>
</div>

{{-- =================== TARGET DETAILS TABLE =================== --}}
<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Product</th>
                <th class="px-4 py-2 border">Target</th>
                <th class="px-4 py-2 border">Completed</th>
                <th class="px-4 py-2 border">Remaining</th>
                <th class="px-4 py-2 border">Assigned By</th>
                <th class="px-4 py-2 border">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($targets as $target)
                @php
                    $completed = $target->sales->sum(fn($s) => $s->boxes_sold ?? $s->amount ?? 0);
                    $remaining = max($target->target_value - $completed, 0);
                @endphp
                <tr class="text-center">
                    <td class="px-4 py-2 border">{{ $target->product->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $target->target_value }}</td>
                    <td class="px-4 py-2 border text-green-600 font-semibold">{{ $completed }}</td>
                    <td class="px-4 py-2 border text-red-600 font-semibold">{{ $remaining }}</td>
                    <td class="px-4 py-2 border">
                        {{ $target->creator?->name ?? 'Admin' }}
                    </td>
                    <td class="px-4 py-2 border">
                        @if($remaining == 0)
                            <span class="text-green-600 font-semibold">Completed</span>
                        @else
                            <span class="text-yellow-600 font-semibold">In Progress</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-6 text-gray-500 text-center">
                        No targets assigned yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- =================== CHART.JS =================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const targets = @json($targets);

/* ---------- Target vs Sales ---------- */
new Chart(document.getElementById('targetSalesChart'), {
    type: 'bar',
    data: {
        labels: targets.map(t => t.product?.name ?? 'N/A'),
        datasets: [
            {
                label: 'Target',
                data: targets.map(t => t.target_value),
            },
            {
                label: 'Completed Sales',
                data: targets.map(t =>
                    t.sales.reduce((sum, s) => sum + Number(s.boxes_sold ?? s.amount ?? 0), 0)
                ),
            }
        ]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

/* ---------- Assigned By ---------- */
const adminCount = targets.filter(t => !t.creator).length;
const executiveCount = targets.filter(t => t.creator).length;

new Chart(document.getElementById('assignedByChart'), {
    type: 'doughnut',
    data: {
        labels: ['Admin', 'Executive'],
        datasets: [{
            data: [adminCount, executiveCount],
        }]
    },
    options: { responsive: true }
});

/* ---------- Sales Trend ---------- */
const salesPerDate = {};

targets.forEach(t => {
    t.sales.forEach(s => {
        if (!s.created_at) return;
        const date = s.created_at.split('T')[0];
        if (!salesPerDate[date]) salesPerDate[date] = 0;
        salesPerDate[date] += Number(s.boxes_sold ?? s.amount ?? 0);
    });
});

const dates = Object.keys(salesPerDate).sort();
const salesValues = dates.map(d => salesPerDate[d]);

new Chart(document.getElementById('salesTrendChart'), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Sales',
            data: salesValues,
            tension: 0.3,
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>

@endsection
