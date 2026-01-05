@extends('layouts.admin')

@section('title','Sales Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            📊 Sales Management
        </h1>

        <a href="{{ route('admin.sales.export') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            ⬇ Export Excel
        </a>
    </div>

    {{-- STATS (ONE ROW) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

        <div class="min-w-[220px] bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-500">Total Sales</p>
            <h2 class="text-2xl font-bold">{{ $stats['total'] }}</h2>
        </div>

        <div class="min-w-[220px] bg-green-500 text-white rounded-xl shadow p-4">
            <p class="text-sm opacity-90">Approved Amount</p>
            <h2 class="text-2xl font-bold">
                ₹ {{ number_format($stats['approved'],2) }}
            </h2>
        </div>

        <div class="min-w-[220px] bg-yellow-400 rounded-xl shadow p-4">
            <p class="text-sm">Pending</p>
            <h2 class="text-2xl font-bold">{{ $stats['pending'] }}</h2>
        </div>

        <div class="min-w-[220px] bg-red-500 text-white rounded-xl shadow p-4">
            <p class="text-sm opacity-90">Rejected</p>
            <h2 class="text-2xl font-bold">{{ $stats['rejected'] }}</h2>
        </div>

    </div>


    {{-- FILTER --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4 flex justify-between items-center">
        <form>
            <select name="status"
                    onchange="this.form.submit()"
                    class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All Status</option>
                @foreach(['pending','approved','rejected'] as $st)
                    <option value="{{ $st }}" @selected($status==$st)>
                        {{ ucfirst($st) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- SALES TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3"></th>
                    <th class="p-3 text-left">Invoice</th>
                    <th class="p-3 text-left">Party</th>
                    <th class="p-3 text-left">Boxes</th>
                    <th class="p-3 text-left">Amount</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Executive</th>
                    <th class="p-3 text-left">Admin Status</th>
                    <th class="p-3 text-left">Accountant</th>
                </tr>
            </thead>

            <tbody>
            @forelse($sales as $sale)

                {{-- MAIN ROW --}}
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">
                        <button onclick="toggleRow({{ $sale->id }})"
                                class="text-blue-600 text-xs hover:underline">
                            View
                        </button>
                    </td>

                    <td class="p-3 font-semibold">
                        {{ $sale->invoice_number }}
                    </td>

                    <td class="p-3">{{ $sale->party_name }}</td>
                    <td class="p-3">{{ $sale->boxes_sold }}</td>

                    <td class="p-3 font-bold">
                        ₹ {{ number_format($sale->amount,2) }}
                    </td>

                    <td class="p-3">{{ $sale->sale_date }}</td>
                    <td class="p-3">{{ $sale->executive->name ?? '-' }}</td>

                    {{-- ADMIN STATUS --}}
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <span id="status-badge-{{ $sale->id }}"
                                  class="px-3 py-1 rounded-full text-xs font-semibold
                                  {{ $sale->status=='approved' ? 'bg-green-100 text-green-700' :
                                     ($sale->status=='rejected' ? 'bg-red-100 text-red-700' :
                                     'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($sale->status) }}
                            </span>

                            @if($sale->status == 'pending')
                                <button onclick="updateStatus({{ $sale->id }}, 'approved')"
                                        class="text-green-600 text-xs hover:underline">
                                    Approve
                                </button>

                                <button onclick="updateStatus({{ $sale->id }}, 'rejected')"
                                        class="text-red-600 text-xs hover:underline">
                                    Reject
                                </button>
                            @endif
                        </div>
                    </td>

                    {{-- ACCOUNTANT STATUS --}}
                    <td class="p-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $sale->accountant_status=='approved' ? 'bg-green-100 text-green-700' :
                           ($sale->accountant_status=='rejected' ? 'bg-red-100 text-red-700' :
                           'bg-gray-200 text-gray-700') }}">
                            {{ ucfirst($sale->accountant_status) }}
                        </span>
                    </td>
                </tr>

                {{-- EXPANDED ROW --}}
                <tr id="expand-{{ $sale->id }}" class="hidden bg-gray-50">
                    <td colspan="9" class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">

                            <div>
                                <p class="text-gray-500 text-xs">Product</p>
                                <p class="font-semibold">
                                    {{ $sale->target->product->name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Target Type</p>
                                <p class="font-semibold">
                                    {{ ucfirst($sale->target->target_type ?? '-') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Target Value</p>
                                <p class="font-semibold">
                                    {{ $sale->target->target_value ?? '-' }}
                                </p>
                            </div>

                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="9" class="p-6 text-center text-gray-500">
                        No sales found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $sales->links() }}
    </div>

</div>

<script>
function toggleRow(id) {
    document.getElementById(`expand-${id}`).classList.toggle('hidden');
}

function updateStatus(id, status) {
    fetch(`/admin/sales/${id}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById(`status-badge-${id}`);
            badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            badge.className =
                'px-3 py-1 rounded-full text-xs font-semibold ' +
                (status === 'approved'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700');
        }
    });
}
</script>
@endsection