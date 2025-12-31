@extends('layouts.executive')

@section('title', 'Add Sale')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Add Sale – {{ $target->product->name }}
</h2>
<div class="mb-3 text-sm text-gray-700">
    <p><strong>Product:</strong> {{ $target->product->name }}</p>
    <p><strong>Target Type:</strong> {{ ucfirst($target->target_type) }}</p>
    <p><strong>Remaining:</strong> {{ $target->remainingValue() }}</p>
</div>

<div class="max-w-md bg-white p-4 border rounded">

<form id="saleForm">
    @csrf

    <input type="hidden" name="target_id" value="{{ $target->id }}">

    {{-- Party Name --}}
    <label class="block mb-1 font-medium">Party Name</label>
    <input type="text" name="party_name"
           class="border w-full px-2 py-1 mb-3 rounded" required>

    {{-- Sale Date --}}
    <label class="block mb-1 font-medium">Sale Date</label>
    <input type="date"
       name="sale_date"
       min="{{ $target->start_date }}"
       max="{{ $target->end_date }}"
       class="border w-full px-2 py-1 mb-3 rounded"
       required>
    <p class="text-sm text-gray-500">
        Allowed: {{ $target->start_date }} to {{ $target->end_date }}
    </p>


    {{-- BOX / AMOUNT --}}
    @if($target->target_type === 'box')
        <label class="block mb-1 font-medium">Boxes Sold</label>
        <input type="number"
               name="boxes_sold"
               min="1"
               max="{{ $target->remainingValue() }}"
               placeholder="Boxes Sold"
               class="border w-full px-2 py-1 rounded mb-3"
               required>

    @else
        <label class="block mb-1 font-medium">Amount</label>
        <input type="number"
               name="amount"
               min="1"
               max="{{ $target->remainingValue() }}"
               placeholder="Boxes Sold"
               class="border w-full px-2 py-1 rounded mb-3"
               required>

    @endif
    <button type="submit"
            id="saveBtn"
            class="bg-green-600 text-white px-4 py-2 rounded">
        Save Sale
    </button>

    <a href="{{ route('executive.targets.assigned') }}" class="bg-gray-400 text-white px-4 py-2 rounded">
                    Back
                </a>

    <span id="savingMsg" class="ml-2 text-gray-700 hidden">Saving...</span>

    <p id="errorBox" class="text-red-600 mt-2 hidden"></p>
    <p id="successBox" class="text-green-600 mt-2 hidden"></p>

</form>

</div>

{{-- AJAX --}}
<script>
document.getElementById('saleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);

    let errorBox = document.getElementById('errorBox');
    let successBox = document.getElementById('successBox');
    let saveBtn = document.getElementById('saveBtn');
    let savingMsg = document.getElementById('savingMsg');

    errorBox.classList.add('hidden');
    successBox.classList.add('hidden');

    // Disable button and show "Saving..."
    saveBtn.disabled = true;
    savingMsg.classList.remove('hidden');

    fetch("{{ route('executive.sale.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        // Re-enable button and hide "Saving..."
        saveBtn.disabled = false;
        savingMsg.classList.add('hidden');

        if (!data.success) {
            let msg = Object.values(data.errors)[0][0];
            errorBox.textContent = msg;
            errorBox.classList.remove('hidden');
        } else {
            successBox.textContent = data.message;
            successBox.classList.remove('hidden');
            form.reset();

            setTimeout(() => { window.location.href = '/executive/dashboard'; }, 1500);
        }
    })
    .catch(() => {
        saveBtn.disabled = false;
        savingMsg.classList.add('hidden');

        errorBox.textContent = 'Something went wrong';
        errorBox.classList.remove('hidden');
    });
});

</script>

@endsection
