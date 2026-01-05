@extends('layouts.executive')

@section('title','Team Management')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Team Management – {{ $target->product->name }}
</h2>

<div class="max-w-md bg-white border rounded p-4">

    <div class="mb-3 text-sm text-gray-700">
        <p><strong>Target Type:</strong>
            {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}
        </p>
        <p><strong>Total Target:</strong> {{ $target->target_value }}</p>
        <p class="text-red-600 font-semibold">
            Remaining: {{ $target->remainingValue() }}
        </p>
    </div>

    <form id="splitForm">
        @csrf

        {{-- Select Executive --}}
        <label class="block font-medium mb-1">Assign To Executive</label>
        <select name="executive_id" class="border w-full px-2 py-1 mb-3 rounded" required>
            <option value="">Select Executive</option>
            @foreach($executives as $exec)
                <option value="{{ $exec->id }}">{{ $exec->name }}</option>
            @endforeach
        </select>

        {{-- Split Value --}}
        <label class="block font-medium mb-1">
            Team {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}
        </label>
        <input type="number"
               name="value"
               min="1"
               max="{{ $target->remainingValue() }}"
               class="border w-full px-2 py-1 mb-3 rounded"
               placeholder="Enter value"
               required>

        <button type="submit"
                id="splitBtn"
                class="bg-purple-600 text-white px-4 py-2 rounded">
            Add Target
        </button>

        <a href="{{ url()->previous() }}"
           class="ml-2 bg-gray-400 text-white px-4 py-2 rounded">
            Back
        </a>

        <p id="errorBox" class="text-red-600 mt-2 hidden"></p>
        <p id="successBox" class="text-green-600 mt-2 hidden"></p>
    </form>
</div>
{{-- ================= TARGET SALES LIST ================= --}}
@if($target->sales->count())

<div class="max-w-3xl bg-white border rounded p-4 mt-6">

    <h3 class="text-lg font-semibold mb-3 text-gray-800">
        Sales for this Target
    </h3>

    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">
                    {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}
                </th>
                <th class="border px-2 py-1">Date</th>
                <th class="border px-2 py-1">Added By</th>
                <th class="border px-2 py-1">Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($target->sales as $index => $sale)
                <tr class="text-center hover:bg-gray-50">
                    <td class="border px-2 py-1">{{ $index + 1 }}</td>

                    <td class="border px-2 py-1 font-semibold text-green-600">
                        {{ $sale->boxes_sold ?? $sale->amount }}
                    </td>

                    <td class="border px-2 py-1">
                        {{ $sale->sale_date ?? $sale->created_at->format('d M Y') }}
                    </td>

                    <td class="border px-2 py-1">
                        {{ $sale->user->name ?? 'Executive' }}
                    </td>

                    <td class="border px-2 py-1">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $sale->status === 'approved'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($sale->status ?? 'pending') }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@else
<div class="max-w-md mt-6 text-gray-500 text-sm">
    No sales added for this target yet.
</div>
@endif

{{-- AJAX --}}
<script>
document.getElementById('splitForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const btn = document.getElementById('splitBtn');
    const errorBox = document.getElementById('errorBox');
    const successBox = document.getElementById('successBox');

    errorBox.classList.add('hidden');
    successBox.classList.add('hidden');
    btn.disabled = true;
    btn.innerText = 'Processing...';

    fetch("{{ route('executive.target.split', $target->id) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            executive_id: form.executive_id.value,
            value: form.value.value
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = 'Split Target';

        if (!data.success) {
            errorBox.textContent = data.message || 'Error occurred';
            errorBox.classList.remove('hidden');
        } else {
            successBox.textContent = data.message;
            successBox.classList.remove('hidden');

            setTimeout(() => {
                window.location.href = "{{ url()->previous() }}";
            }, 1500);

        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Split Target';
        errorBox.textContent = 'Something went wrong';
        errorBox.classList.remove('hidden');
    });
});
</script>

@endsection
