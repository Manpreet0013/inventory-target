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
                            <th>Product</th>
                            <th>Expiry Date</th>
                            <th>Days Left</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($products as $product)

                        @php
                            $expiry = \Carbon\Carbon::parse($product->expiry_date);
                            $daysLeft = $expiry->isPast() ? 0 : $expiry->diffInDays(now());
                            $isExpiring = !$expiry->isPast() && $daysLeft <= 180;
                        @endphp

                        <tr>

                            <td class="fw-semibold">
                                {{ $product->name }}
                            </td>

                            <td>
                                {{ $expiry->format('d M Y') }}
                            </td>

                            <td>
                                @if($expiry->isPast())
                                    <span class="badge bg-danger">
                                        Expired
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        {{ $daysLeft }} days left
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">

                                @if(!$product->notified_at && $isExpiring)

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
                                        Not expiring soon
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No expiring products found 🎉
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

    fetch(`/inventory/notify-admin/${productId}`, {
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
