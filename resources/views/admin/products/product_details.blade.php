@extends('layouts.admin')

@section('title','Product Details')

@section('content')
<div class="container-fluid py-4">

    <!-- BACK BUTTON -->
    <a href="{{ url()->previous() }}"
       class="btn btn-secondary mb-4">
        ← Back
    </a>

    @forelse($product->targets->whereNull('parent_id') as $target)

        @php
            $achieved = $target->target_type === 'box'
                ? $target->sales->sum('boxes_sold')
                : $target->sales->sum('amount');

            $assignedToTeam = $target->children->sum('target_value');
            $pending = max(($target->target_value - $assignedToTeam) - $achieved, 0);

            $percentage = $target->target_value > 0
                ? round(($achieved / $target->target_value) * 100)
                : 0;

            $completed = $pending == 0;
        @endphp

        <!-- PRODUCT + TARGET CARD -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">

                <div class="row g-4">

                    <!-- LEFT : PRODUCT -->
                    <div class="col-md-6 d-flex gap-4">

                        <!-- IMAGE -->
                        <div style="width:140px;height:140px;"
                             class="rounded-3 overflow-hidden border">

                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}"
                                     class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-light">
                                    No Image
                                </div>
                            @endif
                        </div>

                        <!-- INFO -->
                        <div>

                            <h4 class="fw-bold text-primary">
                                {{ $product->name }}
                            </h4>

                            <p class="mb-1">
                                <strong>Composition:</strong>
                                {{ $product->composition ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>Type:</strong>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($product->type) }}
                                </span>
                            </p>

                            <p class="mb-0">
                                <strong>Expiry:</strong>

                                @if($product->expiry_date && \Carbon\Carbon::parse($product->expiry_date)->isPast())
                                    <span class="badge bg-danger">
                                        Expired
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        {{ $product->expiry_date ?? '-' }}
                                    </span>
                                @endif
                            </p>

                        </div>
                    </div>

                    <!-- RIGHT : TARGET -->
                    <div class="col-md-6">

                        <div class="d-flex flex-wrap gap-2 mb-3">

                            <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                Admin: {{ $target->creator->name ?? 'Admin' }}
                            </span>

                            <span class="badge bg-secondary-subtle text-dark px-3 py-2">
                                {{ $target->start_date }} → {{ $target->end_date }}
                            </span>

                        </div>

                        <!-- TARGET STATS -->
                        <div class="row text-sm mb-3">

                            <div class="col-6 mb-2">
                                <small class="text-muted">Target</small>
                                <div class="fw-bold">{{ $target->target_value }}</div>
                            </div>

                            <div class="col-6 mb-2">
                                <small class="text-muted">Achieved</small>
                                <div class="fw-bold text-success">{{ $achieved }}</div>
                            </div>

                            <div class="col-6">
                                <small class="text-muted">Pending</small>
                                <div class="fw-bold text-danger">{{ $pending }}</div>
                            </div>

                            <div class="col-6">
                                <small class="text-muted">Status</small>
                                <div class="fw-bold {{ $completed ? 'text-success' : 'text-warning' }}">
                                    {{ $completed ? 'Completed' : 'In Progress' }}
                                </div>
                            </div>

                        </div>

                        <!-- PROGRESS BAR -->
                        <div>

                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span>{{ $percentage }}%</span>
                                <span>{{ $pending }} remaining</span>
                            </div>

                            <div class="progress" style="height:10px;">
                                <div class="progress-bar {{ $completed ? 'bg-success' : 'bg-primary' }}"
                                     role="progressbar"
                                     style="width: {{ min($percentage,100) }}%">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- MEMBERS TABLE -->
        <div class="card shadow-sm border-0 rounded-4 mb-5">
            <div class="card-body">

                <h4 class="fw-bold text-primary mb-3">
                    Members
                </h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Member Name</th>
                                <th>Parent Name</th>
                                <th>Target Value</th>
                                <th>Total Sales</th>
                                <th>Pending Sales</th>
                                <th>Status</th>
                                <th>Verified</th>
                            </tr>
                        </thead>

                        <tbody>

                        @forelse($product->targets as $target)

                            @php
                                $today = \Carbon\Carbon::today();
                                $valueColumn = $target->target_type === 'amount' ? 'amount' : 'boxes_sold';

                                $validSales = $target->sales->filter(fn($sale) =>
                                    $sale->status === 'approved' &&
                                    $sale->accountant_status === 'approved'
                                );

                                $totalSales = $validSales->sum($valueColumn);
                                $pendingSales = max($target->target_value - $totalSales, 0);

                                $status = $today->gt(\Carbon\Carbon::parse($target->end_date))
                                    ? 'Expired'
                                    : ($totalSales >= $target->target_value ? 'Achieved' : 'Not Achieved');

                                $badge =
                                    $status == 'Achieved' ? 'success' :
                                    ($status == 'Expired' ? 'secondary' : 'danger');

                                $verified =
                                    $target->sales->count() > 0 &&
                                    $target->sales->count() === $validSales->count();
                            @endphp

                            <tr>

                                <td>
                                    {{ $target->executive->name ?? 'Product Admin' }}
                                </td>

                                <td>
                                    {{ $target->parent?->executive->name ?? 'Admin' }}
                                </td>

                                <td>{{ $target->target_value }}</td>

                                <td>{{ $totalSales }}</td>

                                <td>{{ $pendingSales }}</td>

                                <td>
                                    <span class="badge bg-{{ $badge }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td>
                                    @if($verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Partial / Pending</span>
                                    @endif
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No targets assigned.
                                </td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    @empty
        <div class="alert alert-warning">
            No targets available.
        </div>
    @endforelse

</div>
@endsection
