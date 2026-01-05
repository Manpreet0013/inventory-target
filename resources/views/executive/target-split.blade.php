@extends('layouts.executive')

@section('title','Split Target')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Split Target – {{ $target->product->name }}
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
            Split {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}
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
            Split Target
        </button>

        <a href="{{ url()->previous() }}"
           class="ml-2 bg-gray-400 text-white px-4 py-2 rounded">
            Back
        </a>

        <p id="errorBox" class="text-red-600 mt-2 hidden"></p>
        <p id="successBox" class="text-green-600 mt-2 hidden"></p>
    </form>
</div>

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
