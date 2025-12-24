<x-app-layout>
<h2 class="text-xl font-bold mb-4">Pending Sales</h2>

@foreach($sales as $sale)
<div class="border p-4 mb-2">
    <p><b>Product:</b> {{ $sale->target->product->name }}</p>
    <p>Boxes: {{ $sale->boxes_sold }}</p>
    <p>Party: {{ $sale->party_name }}</p>

    <button onclick="approve({{ $sale->id }})"
        class="bg-green-600 text-white px-2">Approve</button>

    <button onclick="reject({{ $sale->id }})"
        class="bg-red-600 text-white px-2">Reject</button>
</div>
@endforeach

<script>
function approve(id){
 fetch(`/accountant/sale/${id}/approve`,{
   method:'POST',
   headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
 }).then(()=>location.reload());
}

function reject(id){
 fetch(`/accountant/sale/${id}/reject`,{
   method:'POST',
   headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
 }).then(()=>location.reload());
}
</script>
</x-app-layout>
