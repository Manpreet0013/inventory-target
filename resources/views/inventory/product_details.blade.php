@extends('layouts.inventory')

@section('title','Product Details')

@section('content')

<div class="container py-4">

    <!-- BACK BUTTON -->
    <a href="{{ url()->previous() }}"
       class="btn btn-secondary mb-3">
        ← Back
    </a>


    <!-- PRODUCT INFO CARD -->
    <div class="card shadow-sm mb-4 border-0">

        <div class="card-body d-flex flex-wrap gap-4">

            <!-- IMAGE -->
            <div style="width:160px;height:160px;">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}"
                         class="img-fluid rounded h-100 w-100 object-fit-cover">
                @else
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center h-100">
                        <small class="text-muted">No Image</small>
                    </div>
                @endif
            </div>

            <!-- DETAILS -->
            <div>
                <h4 class="fw-bold mb-2">{{ $product->name }}</h4>

                <p class="mb-1">
                    <strong>Composition:</strong>
                    {{ $product->composition ?? '-' }}
                </p>

                <p class="mb-1">
                    <strong>Type:</strong>
                    {{ ucfirst($product->type) }}
                </p>

                <p class="mb-0">
                    <strong>Expiry Date:</strong>
                    {{ $product->expiry_date ?? '-' }}
                </p>
            </div>

        </div>
    </div>



    <!-- TARGET HEADING -->
    <h5 class="fw-bold mb-3">Targets & Sales</h5>


@forelse($product->targets->whereNull('parent_id') as $target)

@php
    $achieved = $target->target_type === 'box'
        ? $target->sales->sum('boxes_sold')
        : $target->sales->sum('amount');

    $remaining  = max($target->target_value - $achieved, 0);

    $percentage = $target->target_value > 0
        ? round(($achieved / $target->target_value) * 100)
        : 0;
@endphp


<!-- ================= PARENT TARGET CARD ================= -->

<div class="card shadow-sm border-0 mb-4">

    <div class="card-body">

        <div class="row g-3 align-items-center">

            <!-- TARGET INFO -->
            <div class="col-md-7">

                <p class="mb-1">
                    <strong>Executive:</strong>
                    <span class="badge bg-warning text-dark">
                        {{ $target->executive->name ?? '-' }}
                    </span>
                </p>

                <p class="mb-1">
                    <strong>Target:</strong>
                    {{ $target->target_value }}
                    ({{ ucfirst($target->target_type) }})
                </p>

                <p class="mb-0">
                    <strong>Duration:</strong>
                    {{ $target->start_date }} → {{ $target->end_date }}
                </p>

            </div>


            <!-- PROGRESS -->
            <div class="col-md-5">

                <small>
                    {{ $achieved }} achieved /
                    {{ $remaining }} remaining
                </small>

                <div class="progress mt-1" style="height:8px;">
                    <div class="progress-bar
                        {{ $percentage >= 100 ? 'bg-success' : 'bg-primary' }}"
                        style="width: {{ min($percentage,100) }}%">
                    </div>
                </div>

                <small class="fw-semibold">
                    {{ $percentage }}%
                </small>

            </div>

        </div>



        <!-- ================= CHILD TARGETS ================= -->

        @if($target->children->count())

        <div class="mt-4 ps-3 border-start">

            <h6 class="fw-bold mb-3">Child Targets</h6>

            @foreach($target->children as $child)

            @php
                $childAchieved = $child->target_type === 'box'
                    ? $child->sales->sum('boxes_sold')
                    : $child->sales->sum('amount');

                $childRemaining  = max($child->target_value - $childAchieved, 0);

                $childPercentage = $child->target_value > 0
                    ? round(($childAchieved / $child->target_value) * 100)
                    : 0;
            @endphp


            <div class="card bg-light border-0 mb-3">

                <div class="card-body p-3">

                    <div class="row g-3">

                        <div class="col-md-7 small">

                            <p class="mb-1">
                                <strong>Executive:</strong>
                                <span class="badge bg-info">
                                    {{ $child->executive->name ?? '-' }}
                                </span>
                            </p>

                            <p class="mb-1">
                                <strong>Target:</strong>
                                {{ $child->target_value }}
                                ({{ ucfirst($child->target_type) }})
                            </p>

                            <p class="mb-0">
                                <strong>Status:</strong>
                                {{ ucfirst($child->status) }}
                            </p>

                        </div>


                        <!-- CHILD PROGRESS -->
                        <div class="col-md-5">

                            <small>
                                {{ $childAchieved }} achieved /
                                {{ $childRemaining }} remaining
                            </small>

                            <div class="progress mt-1" style="height:6px;">
                                <div class="progress-bar
                                    {{ $childPercentage >= 100 ? 'bg-success' : 'bg-purple' }}"
                                    style="width: {{ min($childPercentage,100) }}%">
                                </div>
                            </div>

                            <small>{{ $childPercentage }}%</small>

                        </div>

                    </div>



                    <!-- CHILD SALES TABLE -->
                    @if($child->sales->count())

                    <div class="table-responsive mt-3">

                        <table class="table table-sm table-bordered align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Party</th>
                                    <th>Value</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($child->sales as $i => $sale)

                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $sale->party_name }}</td>
                                    <td>{{ $sale->boxes_sold ?? $sale->amount }}</td>
                                    <td>{{ $sale->sale_date }}</td>
                                    <td>

                                        <span class="badge
                                            {{ $sale->status=='accepted' ? 'bg-success' :
                                               ($sale->status=='pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>

                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>

                    </div>

                    @else
                        <div class="alert alert-warning py-2 mt-2 small">
                            No sales added yet.
                        </div>
                    @endif

                </div>
            </div>

            @endforeach

        </div>
        @endif



        <!-- ================= PARENT SALES ================= -->

        @if($target->sales->count())

        <div class="mt-4">

            <h6 class="fw-bold mb-2">Parent Sales</h6>

            <div class="table-responsive">

                <table class="table table-sm table-bordered">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Party</th>
                            <th>Value</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($target->sales as $i => $sale)

                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $sale->party_name }}</td>
                            <td>{{ $sale->boxes_sold ?? $sale->amount }}</td>
                            <td>{{ $sale->sale_date }}</td>
                            <td>

                                <span class="badge
                                    {{ $sale->status=='accepted' ? 'bg-success' :
                                       ($sale->status=='pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>

                            </td>
                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>
        </div>

        @endif

    </div>
</div>

@empty
    <div class="alert alert-info">
        No targets available.
    </div>
@endforelse

</div>

@endsection
