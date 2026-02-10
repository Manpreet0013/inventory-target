@extends('layouts.admin')

@section('title','Products Listing')

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">

        <h4 class="mb-0 fw-bold text-dark">
            📦 Products Listing
        </h4>

        <a href="{{ route('admin.add-product') }}"
           class="btn btn-primary shadow-sm">
            + Create New Product
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <!-- TABLE HEAD -->
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Composition</th>
                            <th>Type</th>
                            <th>Expiry</th>
                            <th class="text-center">Targets</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <!-- TABLE BODY -->
                    <tbody>
                        @forelse($products as $index => $product)
                        <tr>

                            <!-- Index -->
                            <td class="fw-semibold">
                                {{ $index + 1 }}
                            </td>

                            <!-- Product Name -->
                            <td class="fw-bold text-dark">
                                {{ $product->name }}
                            </td>

                            <!-- Image -->
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         width="50"
                                         height="50"
                                         class="rounded border object-fit-cover">
                                @else
                                    <span class="text-muted fst-italic">
                                        No Image
                                    </span>
                                @endif
                            </td>

                            <!-- Composition -->
                            <td>
                                {{ $product->composition ?? '-' }}
                            </td>

                            <!-- Type -->
                            <td>
                                @if($product->type === 'new')
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                        New Launch
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        Expiry
                                    </span>
                                @endif
                            </td>

                            <!-- Expiry -->
                            <td class="text-muted">
                                {{ $product->expiry_date ?? '-' }}
                            </td>

                            <!-- Targets Count -->
                            <td class="text-center fw-semibold">
                                {{ $product->targets()->whereNull('parent_id')->count() }}
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                @if($product->targets->count())

                                    @if($product->isTargetCompleted())
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            Completed
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            Incomplete
                                        </span>
                                    @endif

                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                        No Targets
                                    </span>
                                @endif
                            </td>

                            @php
                                $parentTarget = $product->targets->whereNull('parent_id')->first();
                                $parentTargetCount = $parentTarget ? 1 : 0;
                            @endphp

                            <!-- Actions -->
                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-2 flex-wrap">

                                    <!-- View -->
                                    <a href="{{ route('admin.products.details', $product->id) }}"
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>

                                    <!-- Add Target -->
                                    @if ($parentTargetCount === 0)
                                        <a href="{{ route('admin.targets', ['product_id' => $product->id]) }}"
                                           class="btn btn-sm btn-success">
                                            Add
                                        </a>
                                    @endif

                                    <!-- Delete -->
                                    @if ($parentTargetCount === 1)
                                        <form action="{{ route('admin.product.destroy', $product->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>
                        @empty

                        <!-- No Data -->
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No products found
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
