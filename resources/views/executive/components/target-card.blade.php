<div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                {{ $target->product->name }}
            </h3>
            <p class="text-sm text-gray-500">
                {{ ucfirst($target->target_type) }} Target
            </p>
        </div>

        <span class="px-3 py-1 rounded-full text-xs font-semibold
            {{ $target->status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ ucfirst($target->status) }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm text-gray-700 mb-4">
        <div>
            <p class="text-gray-500">Target</p>
            <p class="font-semibold">{{ $target->target_value }}</p>
        </div>

        <div>
            <p class="text-gray-500">Remaining</p>
            <p class="font-semibold text-red-600">
                {{ $target->remainingValue() }}
            </p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        {{-- View Sales --}}
        <a href="{{ route('executive.target.sales', $target->id) }}"
           class="px-3 py-2 text-sm rounded bg-gray-800 text-white hover:bg-gray-900">
            View Sales
        </a>

        {{-- Add Sale --}}
        <a href="{{ route('executive.sale.create', $target->id) }}"
           class="px-3 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700">
            Add Sale
        </a>

        {{-- Split --}}
        @if($target->parent_id === null && $target->status === 'accepted')
            <a href="{{ route('executive.target.split.view', $target->id) }}"
               class="px-3 py-2 text-sm rounded bg-purple-600 text-white hover:bg-purple-700">
                Split
            </a>
        @endif
    </div>
</div>
