@extends('layouts.admin')

@section('title','Sales Management')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">
            📊 Sales Management
        </h3>

        <a href="{{ route('admin.sales.export') }}"
           class="btn btn-primary shadow-sm">
            ⬇ Export Excel
        </a>
    </div>


    {{-- FILTER BAR --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3">

                {{-- STATUS --}}
                <div class="col-md-3">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="form-select">
                        <option value="">All Status</option>
                        @foreach(['pending','approved','rejected'] as $st)
                            <option value="{{ $st }}" @selected(request('status')==$st)>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TARGET --}}
                <div class="col-md-4">
                    <select name="target_id"
                            onchange="this.form.submit()"
                            class="form-select">
                        <option value="">All Targets</option>
                        @foreach($targets as $target)
                            <option value="{{ $target->id }}"
                                @selected(request('target_id')==$target->id)>
                                {{ $target->product?->name }}
                                ({{ ucfirst($target->target_type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- USER --}}
                <div class="col-md-3">
                    <select name="executive_id"
                            onchange="this.form.submit()"
                            class="form-select">
                        <option value="">All Users</option>
                        @foreach($executives as $exe)
                            <option value="{{ $exe->id }}"
                                @selected(request('executive_id')==$exe->id)>
                                {{ $exe->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </form>
        </div>
    </div>


    {{-- STATS CARDS --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Sales</p>
                    <h4 class="fw-bold">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-white bg-success border-0">
                <div class="card-body">
                    <p class="mb-1">Approved Amount</p>
                    <h4 class="fw-bold">
                        ₹ {{ number_format($stats['approved'],2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-warning border-0">
                <div class="card-body">
                    <p class="mb-1">Pending</p>
                    <h4 class="fw-bold">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>

    </div>


    {{-- SALES TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Party</th>
                            <th>Product</th>
                            <th>Target Type</th>
                            <th>Target</th>
                            <th>Sale Value</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Admin Status</th>
                            <th>Accountant</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sales as $sale)
                        <tr>

                            <td class="fw-semibold">
                                {{ $sale->invoice_number ?? 'New Launch' }}
                            </td>

                            <td>{{ $sale->party_name }}</td>

                            <td>
                                {{ $sale->target->product->name ?? '-' }}
                            </td>

                            <td>
                                {{ ucfirst($sale->target->target_type ?? '-') }}
                            </td>

                            <td class="fw-semibold">
                                {{ $sale->target->target_value ?? '-' }}
                            </td>

                            <td class="fw-semibold">
                                @if($sale->target->target_type === 'box')
                                    {{ $sale->boxes_sold ?? 0 }} Box
                                @elseif($sale->target->target_type === 'amount')
                                    ₹ {{ number_format($sale->amount ?? 0, 2) }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $sale->executive->name ?? '-' }}
                            </td>

                            {{-- ADMIN STATUS --}}
                            <td>
                                <span class="badge
                                    @if($sale->status=='approved') bg-success
                                    @elseif($sale->status=='rejected') bg-danger
                                    @else bg-warning text-dark
                                    @endif">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>

                            {{-- ACCOUNTANT STATUS --}}
                            <td>
                                <span class="badge
                                    @if($sale->accountant_status=='approved') bg-success
                                    @elseif($sale->accountant_status=='rejected') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($sale->accountant_status) }}
                                </span>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                No sales found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>


    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $sales->links() }}
    </div>

</div>
@endsection
