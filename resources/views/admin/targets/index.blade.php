@extends('layouts.admin')

@section('title','Assign Target')

@section('content')
<div class="container mx-auto mt-6">
    <div class="bg-white shadow-md rounded px-6 py-6">

        <!-- Message Box -->
        <div id="formMessage" class="p-3 rounded text-white hidden mb-4"></div>

        <form id="targetForm" class="space-y-4">
            @csrf

            <!-- Product -->
            <div>
                <label for="product_id" class="block font-semibold mb-1">Product</label>
                <select name="product_id" id="product_id" class="w-full border rounded px-3 py-2">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-start="{{ $product->created_at->format('Y-m-d') }}"
                                data-end="{{ $product->expiry_date }}"
                                data-type="{{ $product->type }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Executive -->
            <div>
                <label for="executive_id" class="block font-semibold mb-1">Executive</label>
                <select name="executive_id" id="executive_id" class="w-full border rounded px-3 py-2">
                    @foreach($executives as $exe)
                        <option value="{{ $exe->id }}">{{ $exe->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Target Type -->
            <div>
                <label for="target_type" class="block font-semibold mb-1">Target Type</label>
                <select name="target_type" id="target_type" class="w-full border rounded px-3 py-2">
                    <option value="box">Box</option>
                    <option value="amount">Amount</option>
                </select>
            </div>

            <!-- Target Value -->
            <div>
                <label for="target_value" class="block font-semibold mb-1">Target Value</label>
                <input type="text" name="target_value" id="target_value" class="w-full border rounded px-3 py-2" placeholder="Target Value">
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block font-semibold mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="w-full border rounded px-3 py-2">
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block font-semibold mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" class="w-full border rounded px-3 py-2">
            </div>

            <!-- Loader -->
            <div id="loader" class="hidden font-semibold">Saving...</div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Assign Target
                </button>
                <a href="{{ route('admin.list') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const targetForm = document.getElementById('targetForm');
const messageBox = document.getElementById('formMessage');
const loader = document.getElementById('loader');
const productSelect = document.getElementById('product_id');
const startDateInput = document.getElementById('start_date');
const endDateInput = document.getElementById('end_date');
const submitBtn = targetForm.querySelector('button[type="submit"]');

function updateDates() {
    const selected = productSelect.options[productSelect.selectedIndex];
    const start = selected.dataset.start;
    const end = selected.dataset.end;
    const type = selected.dataset.type;
    const today = new Date().toISOString().split('T')[0];

    startDateInput.min = start;
    startDateInput.max = end;
    endDateInput.min = start;
    endDateInput.max = end;

    startDateInput.value = start;
    endDateInput.value = end;

    if (type === 'expiry' && end < today) {
        messageBox.innerHTML = '❌ This product is expired. Target cannot be assigned.';
        messageBox.classList.remove('hidden', 'bg-green-500');
        messageBox.classList.add('bg-red-500');
        submitBtn.disabled = true;
    } else {
        messageBox.classList.add('hidden');
        submitBtn.disabled = false;
    }
}

updateDates();
productSelect.addEventListener('change', updateDates);

targetForm.addEventListener('submit', function(e){
    e.preventDefault();
    messageBox.classList.add('hidden');
    loader.classList.remove('hidden');

    fetch('/admin/targets/store', {
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
                if (data.errors) {
                    Object.values(data.errors).forEach(err => {
                        msg += err[0] + '<br>';
                    });
                } else if (data.message) {
                    msg = data.message;
                } else {
                    msg = 'Validation error';
                }
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

        setTimeout(() => {
            window.location.href = `/admin/product-listing/${data.product_id}`;
        }, 1000);

        targetForm.reset();
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
