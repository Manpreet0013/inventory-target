@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<div class="container mt-4">

    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-blue-500 text-white rounded shadow">
            <h2 class="text-lg font-semibold">Total Products</h2>
            <p class="text-2xl">{{ $totalProducts }}</p>
        </div>

        <div class="p-4 bg-green-500 text-white rounded shadow">
            <h2 class="text-lg font-semibold">Total Executives</h2>
            <p class="text-2xl">{{ $totalExecutives }}</p>
        </div>

        <div class="p-4 bg-yellow-500 text-white rounded shadow">
            <h2 class="text-lg font-semibold">Main Targets</h2>
            <p class="text-2xl">{{ $totalTargets }}</p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold mb-2">Target Status</h2>
            <canvas id="statusChart"></canvas>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold mb-2">Targets by Product</h2>
            <canvas id="productChart"></canvas>
        </div>

        <div class="bg-white p-4 rounded shadow col-span-1 md:col-span-2">
            <h2 class="text-lg font-semibold mb-2">Targets by Executive</h2>
            <canvas id="executiveChart"></canvas>
        </div>
    </div>

    <!-- LATEST TARGETS -->
    <h2 class="text-xl font-bold mb-3">Latest Main Targets</h2>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="table-auto w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">Product</th>
                    <th class="p-2 border">Executive</th>
                    <th class="p-2 border">Created By</th>
                    <th class="p-2 border">Type</th>
                    <th class="p-2 border">Value</th>
                    <th class="p-2 border">Start</th>
                    <th class="p-2 border">End</th>
                    <th class="p-2 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestTargets as $index => $target)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border">{{ $index + 1 }}</td>
                        <td class="p-2 border">{{ $target->product->name ?? '-' }}</td>
                        <td class="p-2 border">{{ $target->executive->name ?? '-' }}</td>

                        <!-- CREATED BY -->
                        <td class="p-2 border">
                            @if($target->creator?->hasRole('Admin'))
                                <span class="text-blue-600 font-semibold">Admin</span>
                            @else
                                <span class="text-orange-600 font-semibold">Executive</span>
                            @endif
                        </td>

                        <td class="p-2 border">{{ ucfirst($target->target_type) }}</td>
                        <td class="p-2 border">{{ $target->target_value }}</td>
                        <td class="p-2 border">{{ $target->start_date }}</td>
                        <td class="p-2 border">{{ $target->end_date }}</td>

                        <!-- STATUS -->
                        <td class="p-2 border">
                            @php
                                $statusClass = match($target->status) {
                                    'accepted' => 'bg-green-500',
                                    'pending'  => 'bg-yellow-500',
                                    'rejected' => 'bg-red-500',
                                    default    => 'bg-gray-500'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-white {{ $statusClass }}">
                                {{ ucfirst($target->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-4 text-center text-gray-500">
                            No main targets found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // STATUS CHART
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Accepted', 'Pending', 'Rejected'],
            datasets: [{
                data: [
                    {{ $chartStatusData['active'] }},
                    {{ $chartStatusData['pending'] }},
                    {{ $chartStatusData['expired'] }}
                ],
                backgroundColor: ['#48bb78','#ecc94b','#f56565']
            }]
        }
    });

    // PRODUCT CHART
    new Chart(document.getElementById('productChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($targetsByProduct->keys()) !!},
            datasets: [{
                label: 'Main Targets',
                data: {!! json_encode($targetsByProduct->values()) !!},
                backgroundColor: '#4299e1'
            }]
        }
    });

    // EXECUTIVE CHART
    new Chart(document.getElementById('executiveChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($targetsByExecutive->keys()) !!},
            datasets: [{
                label: 'Main Targets',
                data: {!! json_encode($targetsByExecutive->values()) !!},
                backgroundColor: '#ed8936'
            }]
        }
    });
</script>
@endsection
