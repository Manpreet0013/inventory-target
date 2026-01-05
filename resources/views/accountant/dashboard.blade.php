@extends('layouts.accountant')

@section('title','Accountant Dashboard')

@section('content')
<h2 class="text-2xl font-bold mb-6">Pending Sales for Accountant Approval</h2>

@if($sales->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($sales as $sale)
    <div class="bg-white rounded-xl shadow-lg p-5 hover:shadow-2xl transition">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-500 text-sm">Product</p>
                <p class="text-lg font-semibold text-gray-800">{{ $sale->target->product->name }}</p>
            </div>

            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $sale->accountant_status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                   ($sale->accountant_status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                {{ ucfirst($sale->accountant_status) }}
            </span>
        </div>

        <div class="text-sm text-gray-700 mb-4">
            <p><b>Invoice:</b> {{ $sale->invoice_number }}</p>
            <p><b>Party:</b> {{ $sale->party_name }}</p>
            <p><b>Boxes:</b> {{ $sale->boxes_sold ?? '-' }}</p>
            <p><b>Amount:</b> {{ $sale->amount ?? '-' }}</p>
            <p><b>Date:</b> {{ $sale->sale_date }}</p>
        </div>

        <div class="flex gap-2 mt-3">
            @if($sale->accountant_status == 'pending')
                <button onclick="approve({{ $sale->id }})"
                    class="flex-1 text-white bg-green-600 hover:bg-green-700 px-3 py-2 rounded font-medium transition">
                    Approve
                </button>

                <button onclick="reject({{ $sale->id }})"
                    class="flex-1 text-white bg-red-600 hover:bg-red-700 px-3 py-2 rounded font-medium transition">
                    Reject
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-yellow-100 p-5 rounded text-yellow-800 text-center">
    No pending sales for accountant approval.
</div>
@endif

{{-- AJAX --}}
<script>
function approve(id){
    fetch(`/accountant/sale/${id}/approve`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
    })
    .then(res => res.json())
    .then(() => location.reload());
}

function reject(id){
    fetch(`/accountant/sale/${id}/reject`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
    })
    .then(res => res.json())
    .then(() => location.reload());
}
</script>
@endsection
