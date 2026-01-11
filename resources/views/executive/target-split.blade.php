@extends('layouts.executive')

@section('title','Team Management')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Team Management – {{ $target->product->name }}
</h2>

{{-- Target Summary --}}
<div class="max-w-md bg-white border rounded p-4 mb-6">
    <div class="mb-3 text-sm text-gray-700">
        <p><strong>Target Type:</strong> {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}</p>
        <p><strong>Total Target:</strong> {{ $target->target_value }}</p>
        <p class="text-red-600 font-semibold">Remaining: {{ $target->remainingValue() }}</p>
    </div>

    {{-- Assign to Executive --}}
    <form id="splitForm">
        @csrf
        <label class="block font-medium mb-1">Assign To Executive</label>
        <select name="executive_id" class="border w-full px-2 py-1 mb-3 rounded" required>
            <option value="">Select Executive</option>
            @foreach($executives as $exec)
                <option value="{{ $exec->id }}">{{ $exec->name }}</option>
            @endforeach
        </select>

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

{{-- ================= TEAM LIST ================= --}}
@if($target->children->count())
<div class="max-w-4xl bg-white border rounded p-4">

    <h3 class="text-lg font-semibold mb-3 text-gray-800">
        Team Members Assigned
    </h3>

    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">#</th>
                <th class="border px-2 py-1">Executive</th>
                <th class="border px-2 py-1 text-right">Assigned</th>
                <th class="border px-2 py-1 text-right">Achieved</th>
                <th class="border px-2 py-1 text-right">Remaining</th>
                <th class="border px-2 py-1 text-center">Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($team as $index => $member)
            <tr class="hover:bg-gray-50 text-center">
                <td class="border px-2 py-1">{{ $loop->iteration }}</td>

                <td class="border px-2 py-1 text-left">
                    {{ $member['executive']->name }}
                </td>

                <td class="border px-2 py-1 text-right font-semibold">
                    {{ $member['assigned'] }}
                </td>

                <td class="border px-2 py-1 text-right font-semibold text-green-600">
                    {{ $member['achieved'] }}
                </td>

                <td class="border px-2 py-1 text-right font-semibold text-red-600">
                    {{ $member['remaining'] }}
                </td>

                <td class="border px-2 py-1">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $member['status'] === 'accepted'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($member['status']) }}
                    </span>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@else
<div class="max-w-md mt-6 text-gray-500 text-sm">
    No team members assigned yet.
</div>
@endif

{{-- AJAX for splitting target --}}
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
        btn.innerText = 'Add Target';

        if (!data.success) {
            errorBox.textContent = data.message || 'Error occurred';
            errorBox.classList.remove('hidden');
        } else {
            successBox.textContent = data.message;
            successBox.classList.remove('hidden');

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Add Target';
        errorBox.textContent = 'Something went wrong';
        errorBox.classList.remove('hidden');
    });
});
</script>

@endsection
