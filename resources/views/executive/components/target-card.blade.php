<div class="bg-white border rounded-xl p-5 shadow-sm hover:shadow-md transition">

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                {{ $target->product->name }}
            </h3>
            <p class="text-sm text-gray-500">
                {{ ucfirst($target->target_type) }} target
            </p>
        </div>

        <span class="px-3 py-1 rounded-full text-xs font-semibold
            @if($target->status === 'accepted') bg-green-100 text-green-700
            @elseif($target->status === 'rejected') bg-red-100 text-red-700
            @else bg-yellow-100 text-yellow-700 @endif">
            {{ ucfirst($target->status) }}
        </span>
    </div>
    {{-- EXTRA PRODUCT & TARGET INFO --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs text-gray-600 mb-4">

        {{-- Product Type --}}
        <div>
            <p class="text-gray-400">Product Type</p>
            <p class="font-medium capitalize">
                {{ $target->product->type ?? 'N/A' }}
            </p>
        </div>

        {{-- Product Expiry --}}
        <div>
            <p class="text-gray-400">Product Expiry</p>
            <p class="font-medium">
                {{ $target->product->expiry_date
                    ? \Carbon\Carbon::parse($target->product->expiry_date)->format('d M Y')
                    : 'No Expiry' }}
            </p>
        </div>

        {{-- Target Type --}}
        <div>
            <p class="text-gray-400">Target Type</p>
            <p class="font-medium uppercase">
                {{ $target->target_type === 'box' ? 'Box' : 'Amount' }}
            </p>
        </div>

        {{-- Assigned By --}}
        <div>
            <p class="text-gray-400">Assigned By</p>
            <p class="font-medium">
                {{ $target->creator?->name ?? 'Admin' }}
            </p>
        </div>

        {{-- Assigned Date --}}
        <div>
            <p class="text-gray-400">Assigned On</p>
            <p class="font-medium">
                {{ $target->created_at->format('d M Y') }}
            </p>
        </div>

        {{-- Product Expiry Status --}}
        @if($target->product->expiry_date)
            @php
                $daysLeft = now()->diffInDays($target->product->expiry_date, false);
            @endphp
            <div>
                <p class="text-gray-400">Expiry Status</p>
                <p class="font-medium
                    {{ $daysLeft < 0 ? 'text-red-600' : ($daysLeft <= 7 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $daysLeft < 0 ? 'Expired' : ($daysLeft <= 7 ? 'Expiring Soon' : 'Active') }}
                </p>
            </div>
        @endif

    </div>

    {{-- TARGET STATS --}}
    @php
        $total = $target->target_value;
        $remaining = $target->remainingValue();
        $achieved = $total - $remaining;
        $percent = $total > 0 ? round(($achieved / $total) * 100) : 0;
    @endphp

    <div class="grid grid-cols-3 text-sm text-gray-700 mb-3">
        <div>
            <p class="text-gray-500">Target</p>
            <p class="font-semibold">{{ $total }}</p>
        </div>

        <div>
            <p class="text-gray-500">Achieved</p>
            <p class="font-semibold text-green-600">{{ $achieved }}</p>
        </div>

        <div>
            <p class="text-gray-500">Remaining</p>
            <p class="font-semibold text-red-600">{{ $remaining }}</p>
        </div>
    </div>

    {{-- PROGRESS BAR --}}
    <div class="mb-4">
        <div class="flex justify-between text-xs mb-1">
            <span class="text-gray-500">Progress</span>
            <span class="font-semibold">{{ $percent }}%</span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2">
            <div
                class="h-2 rounded-full transition-all
                    {{ $percent >= 100 ? 'bg-green-600' : 'bg-blue-600' }}"
                style="width: {{ min($percent,100) }}%">
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex flex-wrap gap-2">

        @role('Executive')

            {{-- PENDING --}}
            @if($target->status === 'pending')

                <button
                    onclick="acceptTarget({{ $target->id }})"
                    class="px-3 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
                    Accept
                </button>

                <button
                    onclick="openPartialModal({{ $target->id }}, {{ $remaining }})"
                    class="px-3 py-2 text-sm rounded bg-yellow-500 text-white hover:bg-yellow-600">
                    Accept Partial
                </button>

                <button
                    onclick="rejectTarget({{ $target->id }})"
                    class="px-3 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700">
                    Reject
                </button>

            @endif

            {{-- ACCEPTED --}}
            @if($target->status === 'accepted')

                <a href="{{ route('executive.sale.create', $target->id) }}"
                   class="px-3 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
                    Add Sale
                </a>

                <a href="{{ route('executive.target.sales', $target->id) }}"
                   class="px-3 py-2 text-sm rounded bg-gray-800 text-white hover:bg-gray-900">
                    View Sales
                </a>

                @if($target->parent_id === null)
                    <a href="{{ route('executive.target.split.view', $target->id) }}"
                       class="px-3 py-2 text-sm rounded bg-purple-600 text-white hover:bg-purple-700">
                        Team
                    </a>
                @endif

            @endif

        @endrole

    </div>

</div>
