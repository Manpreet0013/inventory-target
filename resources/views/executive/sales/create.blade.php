@extends('layouts.executive')

@section('title', 'Add Sale')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Add Sale – {{ $target->product->name }}
</h2>

<div class="mb-3 text-sm text-gray-700 space-y-1">
    <p><strong>Product:</strong> {{ $target->product->name }}</p>
    <p><strong>Target Type:</strong> {{ ucfirst($target->target_type) }}</p>
    <p><strong>Total Target:</strong> {{ $target->target_value }}</p>
    <p><strong>Achieved:</strong> {{ $target->achievedValue() }}</p>
    <p><strong>Remaining:</strong> {{ $target->remainingValue() }}</p>
    <p><strong>Accepted:</strong>
        <span class="px-2 py-1 rounded text-xs
            {{ ($target->status == 'accepted') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ ($target->status == 'accepted') ? 'Yes' : 'No' }}
        </span>
    </p>
    <p><strong>Start Date:</strong> {{ $target->start_date }}</p>
    <p><strong>End Date:</strong> {{ $target->end_date }}</p>
</div>

{{-- SALE ADD FORM --}}
<div class="max-w-md bg-white p-4 border rounded mb-6">
    <form id="saleForm">
        @csrf
        <input type="hidden" name="target_id" value="{{ $target->id }}">

        <label class="block mb-1 font-medium">Invoice Number</label>
        <input type="text" name="invoice_number" class="border w-full px-2 py-1 mb-3 rounded">

        <label class="block mb-1 font-medium">Party Name</label>
        <input type="text" name="party_name" class="border w-full px-2 py-1 mb-3 rounded" required>

        <label class="block mb-1 font-medium">Sale Date</label>
        <input type="date" name="sale_date" min="{{ $target->start_date }}" max="{{ $target->end_date }}"
               class="border w-full px-2 py-1 mb-3 rounded" required>
        <p class="text-sm text-gray-500">Allowed: {{ $target->start_date }} to {{ $target->end_date }}</p>

        @if($target->target_type === 'box')
            <label class="block mb-1 font-medium">Boxes Sold</label>
            <input type="number" name="boxes_sold" min="1" max="{{ $target->remainingValue() }}"
                   placeholder="Boxes Sold" class="border w-full px-2 py-1 rounded mb-3" required>
        @else
            <label class="block mb-1 font-medium">Amount</label>
            <input type="number" name="amount" min="1" max="{{ $target->remainingValue() }}"
                   placeholder="Amount" class="border w-full px-2 py-1 rounded mb-3" required>
        @endif

        <button type="submit" id="saveBtn" class="bg-green-600 text-white px-4 py-2 rounded">Save Sale</button>
        <a href="{{ url()->previous() }}" class="bg-gray-400 text-white px-4 py-2 rounded ml-2">Back</a>

        <span id="savingMsg" class="ml-2 text-gray-700 hidden">Saving...</span>
        <p id="errorBox" class="text-red-600 mt-2 hidden"></p>
        <p id="successBox" class="text-green-600 mt-2 hidden"></p>
    </form>
</div>

{{-- SALES LIST --}}
@if($target->sales->count())
<div class="mt-10 max-w-4xl bg-white border rounded-xl p-5">

    <h3 class="text-xl font-bold mb-4">Sales List ({{ $target->sales->count() }})</h3>

    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">Invoice</th>
                <th class="border px-3 py-2">Party</th>
                <th class="border px-3 py-2 text-center">Date</th>
                <th class="border px-3 py-2 text-right">{{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}</th>
                <th class="border px-3 py-2 text-center">Product Admin Status</th>
                <th class="border px-3 py-2 text-center">Accountant Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($target->sales as $sale)
            <tr class="hover:bg-gray-50">
                <td class="border px-3 py-2">{{ $sale->invoice_number ?? '—' }}</td>
                <td class="border px-3 py-2">{{ $sale->party_name }}</td>
                <td class="border px-3 py-2 text-center">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                <td class="border px-3 py-2 text-right font-semibold">
                    {{ $target->target_type === 'box' ? $sale->boxes_sold : $sale->amount }}
                </td>
                <td class="border px-3 py-2 text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $sale->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $sale->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $sale->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </td>
                <td class="border px-3 py-2 text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $sale->accountant_status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $sale->accountant_status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $sale->accountant_status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($sale->accountant_status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="mt-10 text-gray-500 text-sm">No sales recorded for this target yet.</div>
@endif

{{-- AJAX for adding sale --}}
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

            setTimeout(() => {
                window.location.reload(); // reload page to show updated sales
            }, 1000);
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
