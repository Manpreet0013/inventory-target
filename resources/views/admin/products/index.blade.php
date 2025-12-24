@extends('layouts.admin')

@section('title','Create Product')

@section('content')
<div class="container mx-auto mt-6 ">
    <div class="bg-white shadow-md rounded px-6 py-6">

        <!-- Message Box -->
        <div id="formMessage" class="p-3 rounded text-white hidden mb-4"></div>

        <form id="productForm" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-semibold mb-1">Product Name</label>
                <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" placeholder="Product Name" required>
            </div>

            <div>
                <label for="composition" class="block font-semibold mb-1">Composition</label>
                <textarea name="composition" id="composition" class="w-full border rounded px-3 py-2" placeholder="Composition"></textarea>
            </div>

            <div>
                <label for="type" class="block font-semibold mb-1">Product Type</label>
                <select name="type" id="productType" class="w-full border rounded px-3 py-2">
                    <option value="expiry">Expiry Product</option>
                    <option value="new">New Launch</option>
                </select>
            </div>

            <div>
                <label for="image" class="block font-semibold mb-1">Product Image</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>

            <div id="expiryWrapper">
                <label for="expiryDate" class="block font-semibold mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiryDate" class="w-full border rounded px-3 py-2">
            </div>

            <div id="loader" class="hidden font-semibold">Saving...</div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Save
                </button>
                <a href="{{ route('admin.products') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const typeSelect = document.getElementById('productType');
const expiryWrapper = document.getElementById('expiryWrapper');
const expiryInput = document.getElementById('expiryDate');
const form = document.getElementById('productForm');
const messageBox = document.getElementById('formMessage');
const loader = document.getElementById('loader');

// Toggle expiry field
typeSelect.addEventListener('change', function () {
    if (this.value === 'new') {
        expiryWrapper.style.display = 'none';
        expiryInput.value = '';
        expiryInput.removeAttribute('required');
    } else {
        expiryWrapper.style.display = 'block';
        expiryInput.setAttribute('required', 'required');
    }
});

// AJAX submit
form.addEventListener('submit', function (e) {
    e.preventDefault();
    messageBox.classList.add('hidden');
    loader.classList.remove('hidden');

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
        loader.classList.add('hidden');

        if (!response.ok) {
            let msg = '';
            if (response.status === 422) {
                Object.values(data.errors).forEach(err => { msg += err[0] + '<br>'; });
            } else {
                msg = data.message || 'Something went wrong';
            }
            messageBox.innerHTML = msg;
            messageBox.classList.remove('hidden', 'bg-green-500');
            messageBox.classList.add('bg-red-500');
            return;
        }

        messageBox.innerHTML = data.message;
        messageBox.classList.remove('hidden', 'bg-red-500');
        messageBox.classList.add('bg-green-500');

        setTimeout(() => { window.location.href = '/admin/product-listing'; }, 1500);
        form.reset();
        expiryWrapper.style.display = 'block';
    })
    .catch(() => {
        loader.classList.add('hidden');
        messageBox.innerHTML = 'Server error';
        messageBox.classList.remove('hidden', 'bg-green-500');
        messageBox.classList.add('bg-red-500');
    });
});
</script>

<style>
.hidden { display: none; }
#loader { margin: 5px 0; }
.bg-red-500 { background-color: #f56565; }
.bg-green-500 { background-color: #48bb78; }
#formMessage { transition: all 0.3s; }
</style>
@endsection
