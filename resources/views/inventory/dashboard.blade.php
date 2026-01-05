@extends('layouts.inventory')

@section('title','Inventory Dashboard')

@section('content')

<div class="justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Expiring Products</h1>
    <br>
    {{-- Add Target Button --}}
    <button
        onclick="openTargetModal()"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
        + Add Target
    </button>
    <a
        href="{{ route('target.list') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-green-700 transition">
        View Targets
    </a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Product</th>
                <th class="px-4 py-2">Expiry Date</th>
                <th class="px-4 py-2">Days Left</th>
                <th class="px-4 py-2 text-right">Action</th>
            </tr>
        </thead>

        <tbody>
        @forelse($products as $product)
            @php
                $expiry = \Carbon\Carbon::parse($product->expiry_date);
                $daysLeft = $expiry->isPast() ? 0 : $expiry->diffInDays(now());
                $isExpiring = !$expiry->isPast() && $daysLeft <= 180;
            @endphp

            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2 font-medium">{{ $product->name }}</td>
                <td class="px-4 py-2">{{ $expiry->format('d M Y') }}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded
                        {{ $expiry->isPast() ? 'bg-red-200 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $expiry->isPast() ? 'Expired' : $daysLeft . ' days left' }}
                    </span>
                </td>
                <td class="px-4 py-2 text-right">
                    @if(!$product->notified_at && $isExpiring)
                        <button
                            onclick="notifyAdmin({{ $product->id }})"
                            class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition">
                            Notify Admin
                        </button>
                    @elseif($product->notified_at)
                        <span class="text-xs text-green-600 font-semibold">Notified</span>
                    @else
                        <span class="text-xs text-gray-500">Not expiring soon</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-6 text-gray-500">
                    No expiring products found 🎉
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links('pagination::tailwind') }}
</div>

{{-- Add Target Modal --}}
<div id="targetModal" class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center z-50">
    <div class="bg-white rounded shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">Add New Product Target</h2>

        <form id="targetForm">
            @csrf

            <label class="block mb-1 text-sm font-medium">Product Name</label>
            <input type="text" name="product_name" placeholder="Enter product name"
                   class="w-full border px-3 py-2 rounded mb-3" required>

            <label class="block mb-1 text-sm font-medium">Assign Executive</label>
            <select name="executive_id" class="border w-full px-3 py-2 mb-3 rounded" required>
                <option value="">Select Executive</option>
                @foreach($executives as $exec)
                    <option value="{{ $exec->id }}">{{ $exec->name }}</option>
                @endforeach
            </select>

            <label class="block mb-1 text-sm">Target Type</label>
            <select name="target_type" class="border w-full px-3 py-2 mb-3 rounded" required>
                <option value="">Select Type</option>
                <option value="box">Box</option>
                <option value="amount">Amount</option>
            </select>

            <label class="block mb-1 text-sm">Target Value</label>
            <input type="number" name="target_value" class="border w-full px-3 py-2 mb-3 rounded" min="1" required>

            <label class="block mb-1 text-sm">Start Date</label>
            <input type="date" name="start_date" class="border w-full px-3 py-2 mb-3 rounded" required>

            <label class="block mb-1 text-sm">End Date</label>
            <input type="date" name="end_date" class="border w-full px-3 py-2 mb-3 rounded" required>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeTargetModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save Target</button>
            </div>

            <p id="modalMessage" class="text-xs mt-2"></p>
        </form>

    </div>
</div>

{{-- JS --}}
<script>
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

// Modal open/close
function openTargetModal() {
    document.getElementById('targetModal').classList.remove('hidden');
    document.getElementById('targetModal').classList.add('flex');
}
function closeTargetModal() {
    document.getElementById('targetModal').classList.remove('flex');
    document.getElementById('targetModal').classList.add('hidden');
}

// Submit target via AJAX
document.getElementById('targetForm').addEventListener('submit', function(e){
    e.preventDefault();

    const form = this;
    const message = document.getElementById('modalMessage');
    message.textContent = '';

    fetch("{{ route('inventory.target.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_name: form.product_name.value,
            target_type: form.target_type.value,
             executive_id: form.executive_id.value,
            target_value: form.target_value.value,
            start_date: form.start_date.value,
            end_date: form.end_date.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            message.textContent = data.message;
            message.className = 'text-green-600 mt-2';
            setTimeout(() => location.reload(), 1200);
        } else {
            message.textContent = data.message || 'Error occurred';
            message.className = 'text-red-600 mt-2';
        }
    })
    .catch(() => {
        message.textContent = 'Something went wrong!';
        message.className = 'text-red-600 mt-2';
    });
});
</script>

@endsection
