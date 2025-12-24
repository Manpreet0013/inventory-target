@extends('layouts.executive')

@section('title','My Targets')

@section('content')

@if($targets->count())
<div class="overflow-x-auto">
<table class="w-full border-collapse border">
    <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2 border">Product</th>
            <th class="px-4 py-2 border">Target</th>
            <th class="px-4 py-2 border">Remaining</th>
            <th class="px-4 py-2 border">Assigned By</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Actions</th>
            <th class="px-4 py-2 border">Add Sale</th>
            <th class="px-4 py-2 border">Sales</th>
        </tr>
    </thead>
    <tbody>
    @foreach($targets as $target)
        @php
            $assignedBy = $target->creator ? $target->creator->name : 'Admin';
            $companyExecutives = \App\Models\User::role('Executive')
                ->where('company_id', $executive->company_id)
                ->where('id', '!=', $executive->id)
                ->whereDoesntHave('targets', function ($q) use ($target) {
                    $q->where('product_id', $target->product_id)
                      ->whereIn('status', ['pending','accepted']);
                })
                ->get();
        @endphp

        {{-- Parent Target Row --}}
        <tr class="hover:bg-gray-50">
            <td class="border px-4 py-2">{{ $target->product->name ?? '-' }}</td>
            <td class="border px-4 py-2">
                {{ $target->target_value }} {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}
            </td>
            <td class="border px-4 py-2 font-semibold text-red-600">{{ $target->remainingValue() }}</td>
            <td class="border px-4 py-2">{{ $assignedBy }}</td>
            <td class="border px-4 py-2 capitalize">{{ $target->status }}</td>
            <td class="border px-4 py-2 space-y-1">
                @if($target->status === 'pending')
                    <div class="flex gap-1 items-center flex-wrap">
                        <button onclick="acceptTarget({{ $target->id }}, this)"
                                class="bg-green-600 text-white px-2 py-1 rounded">
                            Accept All
                        </button>
                        <button onclick="rejectTarget({{ $target->id }}, this)"
                                class="bg-red-600 text-white px-2 py-1 rounded">
                            Reject
                        </button>
                    </div>
                @elseif($target->status === 'accepted')
                    @if($target->remainingValue() > 0 && $target->parent_id === null)
                        <div class="flex gap-1 items-center flex-wrap">
                            <select class="border px-1 py-1 exec-{{ $target->id }}">
                                @foreach($companyExecutives as $exec)
                                    <option value="{{ $exec->id }}">{{ $exec->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" min="1" placeholder="Value" class="border px-1 py-1 w-20 val-{{ $target->id }}">
                            <button onclick="splitTarget({{ $target->id }}, this)"
                                    class="bg-purple-600 text-white px-2 py-1 rounded">
                                Split
                            </button>
                        </div>
                    @endif
                @else
                    <span class="text-red-600 font-semibold">Rejected</span>
                @endif
            </td>
            <td class="border px-4 py-2">
                @if($target->status === 'accepted')
                    <a href="{{ route('executive.sale.create', $target->id) }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                        Add Sale
                    </a>
                @endif
            </td>
            <td class="border px-4 py-2">
                <button onclick="toggleSales('target-{{ $target->id }}')"
                        class="bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-700 transition">
                    View Sales
                </button>
            </td>
        </tr>

        {{-- Collapsible Sales Row --}}
        <tr id="sales-target-{{ $target->id }}" class="hidden bg-gray-100">
            <td colspan="8" class="px-4 py-2">
                <table class="w-full border-collapse border text-sm">
                    <thead>
                        <tr class="bg-gray-300">
                            <th class="px-2 py-1 border">Sale ID</th>
                            <th class="px-2 py-1 border">Executive</th>
                            <th class="px-2 py-1 border">Party</th>
                            <th class="px-2 py-1 border">Value</th>
                            <th class="px-2 py-1 border">Date</th>
                            <th class="px-2 py-1 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($target->sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-1">{{ $sale->id }}</td>
                            <td class="border px-2 py-1">{{ $sale->executive->name ?? '-' }}</td>
                            <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                            <td class="border px-2 py-1">
                                {{ $sale->boxes_sold ?? $sale->amount }}
                                {{ $target->target_type === 'box' ? 'Boxes' : 'Amount' }}
                            </td>
                            <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                            <td class="border px-2 py-1 capitalize">{{ $sale->status }}</td>
                        </tr>
                        @endforeach
                        @if($target->sales->count() === 0)
                        <tr>
                            <td colspan="6" class="text-center py-2">No sales yet.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>

        {{-- Child Targets --}}
        @foreach($target->children as $child)
        @php
            $childAssignedBy = $child->creator ? $child->creator->name : 'Admin';
        @endphp
        <tr class="bg-gray-50">
            <td class="border px-4 py-2 ml-6">↳ {{ $child->product->name ?? '-' }}</td>
            <td class="border px-4 py-2">
                {{ $child->target_value }} {{ $child->target_type === 'box' ? 'Boxes' : 'Amount' }}
            </td>
            <td class="border px-4 py-2 font-semibold text-red-600">{{ $child->remainingValue() }}</td>
            <td class="border px-4 py-2">{{ $childAssignedBy }}</td>
            <td class="border px-4 py-2 capitalize">{{ $child->status }}</td>
            <td class="border px-4 py-2">
                <!-- Optional child actions -->
            </td>
            <td class="border px-4 py-2">
                @if($child->status === 'accepted')
                    <a href="{{ route('executive.sale.create', $child->id) }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                       Add Sale
                    </a>
                @endif
            </td>
            <td class="border px-4 py-2">
                <button onclick="toggleSales('target-{{ $child->id }}')"
                        class="bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-700 transition">
                    View Sales
                </button>
            </td>
        </tr>

        {{-- Child Sales Row --}}
        <tr id="sales-target-{{ $child->id }}" class="hidden bg-gray-100">
            <td colspan="8" class="px-4 py-2">
                <table class="w-full border-collapse border text-sm">
                    <thead>
                        <tr class="bg-gray-300">
                            <th class="px-2 py-1 border">Sale ID</th>
                            <th class="px-2 py-1 border">Executive</th>
                            <th class="px-2 py-1 border">Party</th>
                            <th class="px-2 py-1 border">Value</th>
                            <th class="px-2 py-1 border">Date</th>
                            <th class="px-2 py-1 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($child->sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-1">{{ $sale->id }}</td>
                            <td class="border px-2 py-1">{{ $sale->executive->name ?? '-' }}</td>
                            <td class="border px-2 py-1">{{ $sale->party_name }}</td>
                            <td class="border px-2 py-1">
                                {{ $sale->boxes_sold ?? $sale->amount }}
                                {{ $child->target_type === 'box' ? 'Boxes' : 'Amount' }}
                            </td>
                            <td class="border px-2 py-1">{{ $sale->sale_date }}</td>
                            <td class="border px-2 py-1 capitalize">{{ $sale->status }}</td>
                        </tr>
                        @endforeach
                        @if($child->sales->count() === 0)
                        <tr>
                            <td colspan="6" class="text-center py-2">No sales yet.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        @endforeach

    @endforeach
    </tbody>
</table>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $targets->links() }}
</div>

@else
<p>No targets assigned.</p>
@endif

<script>
function toggleSales(id) {
    let el = document.getElementById('sales-' + id);
    if(el.classList.contains('hidden')) el.classList.remove('hidden');
    else el.classList.add('hidden');
}
function acceptTarget(id, btn) {
    btn.disabled=true; btn.innerHTML='Processing...';
    fetch(`/executive/target/${id}/accept`, {
        method:'POST', 
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
    })
    .then(res=>res.json())
    .then(data=>{ alert(data.message); location.reload(); });
}
function rejectTarget(id, btn) {
    btn.disabled=true; btn.innerHTML='Processing...';
    fetch(`/executive/target/${id}/reject`, {
        method:'POST', 
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
    })
    .then(res=>res.json())
    .then(data=>{ alert(data.message); location.reload(); });
}
function splitTarget(id, btn) {
    btn.disabled=true; btn.innerHTML='Processing...';
    const exec=document.querySelector('.exec-'+id).value; 
    const val=document.querySelector('.val-'+id).value;
    if(!val || val<=0){ alert('Enter valid value'); btn.disabled=false; btn.innerHTML='Split'; return;}
    fetch(`/executive/target/${id}/split`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'},
        body:JSON.stringify({executive_id:exec,value:val})
    })
    .then(res=>res.json()).then(data=>{ alert(data.message); location.reload(); });
}
</script>

@endsection
