@extends('layouts.admin')

@section('title','Create Product')

@section('content')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Card -->
            <div class="card shadow-lg border-0 rounded-4">

                <!-- Card Header -->
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0">Create Product</h5>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">

                    <!-- Message Box -->
                    <div id="formMessage" class="alert d-none"></div>

                    <form id="productForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control"
                                   placeholder="Enter product name"
                                   required>
                        </div>

                        <!-- Composition -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Composition
                            </label>
                            <textarea name="composition"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Enter composition"></textarea>
                        </div>

                        <!-- Product Type -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Product Type
                            </label>
                            <select name="type"
                                    id="productType"
                                    class="form-select">
                                <option value="expiry">Expiry Product</option>
                                <option value="new">New Launch</option>
                            </select>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Product Image
                            </label>
                            <input type="file"
                                   name="image"
                                   accept="image/*"
                                   class="form-control">
                        </div>

                        <!-- Expiry Date -->
                        <div class="mb-3" id="expiryWrapper">
                            <label class="form-label fw-semibold">
                                Expiry Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="expiry_date"
                                   id="expiryDate"
                                   class="form-control">
                        </div>

                        <!-- Loader -->
                        <div id="loader"
                             class="d-none text-primary fw-semibold mb-3">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            Saving...
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary px-4">
                                Save
                            </button>

                            <a href="{{ route('admin.products') }}"
                               class="btn btn-secondary px-4">
                                Back
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- JS --}}
<script>

const typeSelect     = document.getElementById('productType');
const expiryWrapper = document.getElementById('expiryWrapper');
const expiryInput   = document.getElementById('expiryDate');
const form          = document.getElementById('productForm');
const messageBox    = document.getElementById('formMessage');
const loader        = document.getElementById('loader');


// Toggle Expiry Field
typeSelect.addEventListener('change', function () {

    if (this.value === 'new') {
        expiryWrapper.classList.add('d-none');
        expiryInput.value = '';
        expiryInput.removeAttribute('required');
    } else {
        expiryWrapper.classList.remove('d-none');
        expiryInput.setAttribute('required','required');
    }

});


// AJAX Submit
form.addEventListener('submit', function(e){

    e.preventDefault();

    messageBox.classList.add('d-none');
    loader.classList.remove('d-none');

    fetch('/admin/products/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })

    .then(async response => {

        const data = await response.json();
        loader.classList.add('d-none');

        if (!response.ok) {

            let msg = '';

            if (response.status === 422) {
                Object.values(data.errors).forEach(err => {
                    msg += err[0] + '<br>';
                });
            } else {
                msg = data.message || 'Something went wrong';
            }

            messageBox.innerHTML = msg;
            messageBox.className = 'alert alert-danger';
            messageBox.classList.remove('d-none');
            return;
        }

        messageBox.innerHTML = '✅ ' + data.message;
        messageBox.className = 'alert alert-success';
        messageBox.classList.remove('d-none');

        setTimeout(() => {
            window.location.href = '/admin/product-listing';
        }, 1500);

        form.reset();
        expiryWrapper.classList.remove('d-none');

    })

    .catch(() => {

        loader.classList.add('d-none');

        messageBox.innerHTML = 'Server error';
        messageBox.className = 'alert alert-danger';
        messageBox.classList.remove('d-none');

    });

});
</script>

@endsection
