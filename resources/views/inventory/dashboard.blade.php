@extends('layouts.inventory')

@section('title','Inventory Dashboard')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Expiring Products</h1>
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
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2 font-medium">{{ $product->name }}</td>
                <td class="px-4 py-2">
                    {{ $product->expiry_date->format('d M Y') }}
                </td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                        {{ now()->diffInDays($product->expiry_date) }} days
                    </span>
                </td>
                <td class="px-4 py-2 text-right">
                    @if(!$product->notified_at)
                        <button
                            onclick="notifyAdmin({{ $product->id }})"
                            class="px-3 py-1 bg-red-600 text-white rounded text-xs">
                            Notify Admin
                        </button>
                    @else
                        <span class="text-xs text-green-600 font-semibold">
                            Notified
                        </span>
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
    {{ $products->links() }}
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
        alert(data.message);
        if (data.success) location.reload();
    });
}
</script>

@endsection
