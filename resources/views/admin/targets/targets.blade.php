@extends('layouts.admin')

@section('title','Targets Listing')

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">🎯 Targets Listing</h2>

        <a href="{{ route('admin.targets') }}" class="btn btn-success shadow-sm">
            + Assign New Target
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- FILTER BAR -->
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap gap-2">

            <a href="{{ route('admin.list') }}"
               class="btn btn-sm {{ empty($statusFilter) ? 'btn-primary' : 'btn-outline-secondary' }}">
                All
            </a>

            <a href="{{ route('admin.list',['status'=>'achieved_full']) }}"
               class="btn btn-sm {{ $statusFilter==='achieved_full' ? 'btn-success' : 'btn-outline-success' }}">
                Achieved (Full)
            </a>

            <a href="{{ route('admin.list',['status'=>'achieved_partial']) }}"
               class="btn btn-sm {{ $statusFilter==='achieved_partial' ? 'btn-warning text-white' : 'btn-outline-warning' }}">
                Achieved (Partial)
            </a>

            <a href="{{ route('admin.list',['status'=>'not_achieved']) }}"
               class="btn btn-sm {{ $statusFilter==='not_achieved' ? 'btn-danger' : 'btn-outline-danger' }}">
                Not Achieved
            </a>

            <a href="{{ route('admin.list',['status'=>'expired']) }}"
               class="btn btn-sm {{ $statusFilter==='expired' ? 'btn-dark' : 'btn-outline-dark' }}">
                Expired
            </a>

        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-lg border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <!-- TABLE HEAD -->
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Product Admin</th>
                            <th>Type</th>
                            <th>Target</th>
                            <th width="220">Achieved</th>
                            <th>Pending</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th class="text-center">View</th>
                        </tr>
                    </thead>

                    <!-- TABLE BODY -->
                    <tbody>
                    @forelse($targets as $index => $target)

                        @php
                            $today = \Carbon\Carbon::today();
                            $startDate = \Carbon\Carbon::parse($target->start_date);
                            $endDate   = \Carbon\Carbon::parse($target->end_date);

                            $valueColumn = $target->target_type === 'amount' ? 'amount' : 'boxes_sold';

                            $validSales = $target->sales->filter(fn($sale) =>
                                $sale->status === 'approved' &&
                                $sale->accountant_status === 'approved'
                            );

                            $totalSales = $validSales->sum($valueColumn);
                            $pendingValue = max($target->target_value - $totalSales, 0);

                            $status = '';
                            $badge = '';
                            $rowClass = '';

                            if ($today->lt($startDate)) {
                                $status = 'Target Pending';
                                $badge = 'info';
                                $rowClass = 'table-primary';

                            } elseif ($today->gt($endDate)) {
                                $status = 'Target Expired';
                                $badge = 'dark';
                                $rowClass = 'table-secondary';

                            } else {

                                if ($totalSales >= $target->target_value) {
                                    $status = 'Target Achieved';
                                    $badge = 'success';
                                    $rowClass = 'table-success';
                                } else {
                                    $status = 'Target Not Achieved';
                                    $badge = 'danger';
                                    $rowClass = 'table-danger';
                                }
                            }

                            $percent = $target->target_value > 0
                                ? min(round(($totalSales / $target->target_value) * 100),100)
                                : 0;
                        @endphp

                        <tr class="{{ $rowClass }}">

                            <td>{{ $index + 1 }}</td>

                            <td class="fw-semibold">
                                {{ $target->product->name ?? '-' }}
                            </td>

                            <td>
                                {{ $target->executive->name ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-primary text-uppercase">
                                    {{ ucfirst($target->target_type) }}
                                </span>
                            </td>

                            <td class="fw-bold">
                                {{ $target->target_value }}
                            </td>

                            <!-- ACHIEVED WITH PROGRESS -->
                            <td>
                                <div class="fw-semibold text-success">
                                    {{ $totalSales }}
                                </div>

                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-success"
                                         style="width: {{ $percent }}%">
                                    </div>
                                </div>

                                <small class="text-muted">{{ $percent }}%</small>
                            </td>

                            <!-- PENDING -->
                            <td class="fw-semibold">
                                {{ $pendingValue }}
                            </td>

                            <!-- DATES -->
                            <td>
                                {{ $startDate->format('d M Y') }}
                            </td>

                            <td>
                                {{ $endDate->format('d M Y') }}
                            </td>

                            <!-- STATUS -->
                            <td>
                                <span class="badge bg-{{ $badge }} px-3 py-2">
                                    {{ $status }}
                                </span>
                            </td>

                            <!-- VIEW -->
                            <td class="text-center">
                                <a href="{{ route('admin.products.details', $target->product->id) }}"
                                   class="btn btn-sm btn-primary">
                                    View
                                </a>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                No targets found
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection
