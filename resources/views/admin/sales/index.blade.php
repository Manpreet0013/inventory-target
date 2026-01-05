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

    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <form class="flex flex-wrap gap-3 items-center">

            {{-- STATUS --}}
            <select name="status" onchange="this.form.submit()"
                    class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All Status</option>
                @foreach(['pending','approved','rejected'] as $st)
                    <option value="{{ $st }}" @selected(request('status')==$st)>
                        {{ ucfirst($st) }}
                    </option>
                @endforeach
            </select>

            {{-- TARGET --}}
            <select name="target_id" onchange="this.form.submit()"
                    class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All Targets</option>
                @foreach($targets as $target)
                    <option value="{{ $target->id }}"
                        @selected(request('target_id')==$target->id)>
                        {{ $target->product?->name }}
                        ({{ ucfirst($target->target_type) }})
                    </option>
                @endforeach
            </select>

            {{-- USER --}}
            <select name="executive_id" onchange="this.form.submit()"
                    class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All Users</option>
                @foreach($executives as $exe)
                    <option value="{{ $exe->id }}"
                        @selected(request('executive_id')==$exe->id)>
                        {{ $exe->name }}
                    </option>
                @endforeach
            </select>

        </form>
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
                    <th class="p-3 text-left">Invoice</th>
                    <th class="p-3 text-left">Party</th>
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Target Type</th>
                    <th class="p-3 text-left">Target</th>
                    <th class="p-3 text-left">Sale Value</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">User</th>
                    <th class="p-3 text-left">Admin Status</th>
                    <th class="p-3 text-left">Accountant</th>
                </tr>
                </thead>


            <tbody>
                @forelse($sales as $sale)
                <tr class="border-t hover:bg-gray-50">

                    <td class="p-3 font-semibold">
                        {{ $sale->invoice_number }}
                    </td>

                    <td class="p-3">{{ $sale->party_name }}</td>

                    <td class="p-3">
                        {{ $sale->target->product->name ?? '-' }}
                    </td>

                    <td class="p-3">
                        {{ ucfirst($sale->target->target_type ?? '-') }}
                    </td>

                    <td class="p-3 font-semibold">
                        {{ $sale->target->target_value ?? '-' }}
                    </td>

                   <td class="p-3 font-semibold">
                        @if($sale->target->target_type === 'box')
                            {{ $sale->boxes_sold ?? 0 }} Box
                        @elseif($sale->target->target_type === 'amount')
                            ₹ {{ number_format($sale->amount ?? 0, 2) }}
                        @else
                            -
                        @endif
                    </td>

                    <td class="p-3">
                        {{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}
                    </td>

                    <td class="p-3">
                        {{ $sale->executive->name ?? '-' }}
                    </td>

                    {{-- ADMIN STATUS (READ ONLY) --}}
                    <td class="p-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $sale->status=='approved' ? 'bg-green-100 text-green-700' :
                           ($sale->status=='rejected' ? 'bg-red-100 text-red-700' :
                           'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($sale->status) }}
                        </span>
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
                @empty
                <tr>
                    <td colspan="11" class="p-6 text-center text-gray-500">
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