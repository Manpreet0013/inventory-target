@extends('layouts.inventory')

@section('title','Inventory Dashboard')

@section('content')

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Expiring Products</h4>

        <div class="d-flex gap-2">
           <button onclick="openProductModal()"
                class="btn btn-success">
                + Add Product
            </button>     
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm border-0">

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Composition</th>
                            <th>Type</th>
                            <th>Stock</th>
                            <th>Expiry Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($products as $product)

                        @php
                            $expiry = $product->expiry_date 
                                ? \Carbon\Carbon::parse($product->expiry_date) 
                                : null;

                            $daysLeft = $expiry && !$expiry->isPast()
                                ? $expiry->diffInDays(now())
                                : 0;

                            $isExpiring = $expiry && !$expiry->isPast() && $daysLeft <= 180;
                        @endphp

                        <tr class="{{ $expiry && $expiry->isPast() ? 'table-danger' : '' }}">

                            {{-- Image --}}
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" 
                                         width="50" height="50"
                                         class="rounded shadow-sm">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>

                            {{-- Name --}}
                            <td class="fw-semibold">
                                {{ $product->name }}
                            </td>

                            {{-- Composition --}}
                            <td>
                                {{ $product->composition ?? '-' }}
                            </td>

                            {{-- Type --}}
                            <td>
                                @if($product->type == 'expiry')
                                    <span class="badge bg-warning text-dark">
                                        Expiry Product
                                    </span>
                                @else
                                    <span class="badge bg-info text-dark">
                                        New Product
                                    </span>
                                @endif
                            </td>

                            {{-- Stock --}}
                            <td>
                                @if($product->stock <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->stock <= 10)
                                    <span class="badge bg-warning text-dark">
                                        Low ({{ $product->stock }})
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        {{ $product->stock }}
                                    </span>
                                @endif
                            </td>

                            {{-- Expiry Date --}}
                            <td>
                                @if($expiry)
                                    {{ $expiry->format('d M Y') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                
                            {{-- Action --}}
                            <td class="text-end">

                                @if($isExpiring && !$product->notified_at)
                                    <button
                                        onclick="notifyAdmin({{ $product->id }})"
                                        class="btn btn-sm btn-danger">
                                        Notify Admin
                                    </button>

                                @elseif($product->notified_at)
                                    <span class="badge bg-success">
                                        Notified
                                    </span>

                                @else
                                    <span class="text-muted small">
                                        No Action
                                    </span>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No products found 🎉
                            </td>
                        </tr>

                    @endforelse
                    </tbody>

                </table>

            </div>

        </div>
    </div>

    <!-- PAGINATION -->
    <div class="mt-3">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>

</div>


<!-- ================= MODAL ================= -->

<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" onclick="closeProductModal()"></button>
            </div>

            <div class="modal-body">

                <form id="productForm" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name"
                               class="form-control"
                               placeholder="Enter product name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Stock</label>
                        <input type="number" name="stock"
                               class="form-control"
                               placeholder="Enter product stock" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image"
                               class="form-control"
                               accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Composition</label>
                        <input type="text" name="composition"
                               class="form-control"
                               placeholder="Enter composition">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="expiry">Expiry</option>
                            <option value="new">New</option>
                        </select>
                    </div>

                    <p id="modalMessage" class="small"></p>

                    <div class="text-end">
                        <button type="button"
                                class="btn btn-secondary"
                                onclick="closeProductModal()">
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn btn-primary">
                            Add Product
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ================= JS ================= -->

<script>

// Bootstrap modal instance
let productModal = new bootstrap.Modal(document.getElementById('productModal'));

function openProductModal() {
    productModal.show();
}

function closeProductModal() {
    productModal.hide();
}


// Notify Admin
function notifyAdmin(productId) {

    fetch(`/inventory/notify/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Action completed');
        if (data.success) location.reload();
    })
    .catch(() => {
        alert('Something went wrong!');
    });

}


// Submit Product AJAX
document.getElementById('productForm')
.addEventListener('submit', function(e){

    e.preventDefault();

    const form = this;
    const message = document.getElementById('modalMessage');
    message.textContent = '';

    let formData = new FormData(form);

    fetch("{{ route('inventory.target.store') }}", {
        method: 'POST',
        body: formData
    })
    .then(async res => {

        let data = await res.json();

        if(!res.ok){
            message.textContent = data.message || 'Server error!';
            message.className = 'text-danger small';
            return;
        }

        if(data.success){
            message.textContent = data.message;
            message.className = 'text-success small';
            setTimeout(()=>location.reload(),1200);
        }
        else{
            message.textContent = data.message || 'Error!';
            message.className = 'text-danger small';
        }

    })
    .catch(()=>{
        message.textContent = 'Something went wrong!';
        message.className = 'text-danger small';
    });

});
</script>



@endsection
