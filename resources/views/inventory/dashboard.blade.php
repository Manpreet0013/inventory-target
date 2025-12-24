<x-app-layout>
<h2 class="text-xl font-bold mb-4">Expiring Products (Next 6 Months)</h2>

<table class="w-full border">
<tr class="bg-gray-200">
    <th class="p-2">Product</th>
    <th>Expiry Date</th>
    <th>Action</th>
</tr>

@foreach($products as $product)
<tr>
    <td class="p-2">{{ $product->name }}</td>
    <td>{{ $product->expiry_date }}</td>
    <td>
        <button onclick="notifyAdmin({{ $product->id }})"
            class="bg-red-600 text-white px-3 py-1 rounded">
            Notify Admin
        </button>
    </td>
</tr>
@endforeach
</table>

<script>
function notifyAdmin(id){
    fetch('/inventory/notify/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(() => alert('Admin notified'));
}
</script>
</x-app-layout>
