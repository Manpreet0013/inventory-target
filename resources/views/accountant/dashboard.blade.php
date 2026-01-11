@extends('layouts.accountant')

@section('title','Accountant Dashboard')

@section('content')
<h2 class="text-2xl font-bold mb-6">Pending Sales for Accountant Approval</h2>

@if($sales->count())

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 text-gray-700 text-sm">
            <tr>
                <th class="px-4 py-3 border">#</th>
                <th class="px-4 py-3 border text-left">Product</th>
                <th class="px-4 py-3 border text-left">Invoice</th>
                <th class="px-4 py-3 border text-left">Party</th>
                <th class="px-4 py-3 border text-right">Boxes</th>
                <th class="px-4 py-3 border text-right">Amount</th>
                <th class="px-4 py-3 border">Date</th>
                <th class="px-4 py-3 border">Status</th>
                <th class="px-4 py-3 border">Action</th>
            </tr>
        </thead>

        <tbody class="text-sm">
            @foreach($sales as $sale)
            <tr class="hover:bg-gray-50 transition text-center">
                {{-- Pagination-aware numbering --}}
                <td class="px-4 py-2 border">
                    {{ $sales->firstItem() + $loop->index }}
                </td>

                <td class="px-4 py-2 border text-left font-medium">
                    {{ $sale->target->product->name }}
                </td>

                <td class="px-4 py-2 border text-left">
                    {{ $sale->invoice_number }}
                </td>

                <td class="px-4 py-2 border text-left">
                    {{ $sale->party_name }}
                </td>

                <td class="px-4 py-2 border text-right">
                    {{ $sale->boxes_sold ?? '-' }}
                </td>

                <td class="px-4 py-2 border text-right font-semibold">
                    {{ $sale->amount ?? '-' }}
                </td>

                <td class="px-4 py-2 border">
                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                </td>

                <td class="px-4 py-2 border">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $sale->accountant_status === 'pending'
                            ? 'bg-yellow-100 text-yellow-700'
                            : ($sale->accountant_status === 'approved'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($sale->accountant_status) }}
                    </span>
                </td>

                <td class="px-4 py-2 border">
                    @if($sale->accountant_status === 'pending')
                        <div class="flex justify-center gap-2">
                            <button onclick="approve({{ $sale->id }})"
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs">
                                Approve
                            </button>

                            <button onclick="reject({{ $sale->id }})"
                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
                                Reject
                            </button>
                        </div>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $sales->links() }}
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
        headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
    }).then(() => location.reload());
}

function reject(id){
    fetch(`/accountant/sale/${id}/reject`, {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
    }).then(() => location.reload());
}
</script>
@endsection
