<div class="overflow-x-auto">
<table class="min-w-full border border-gray-200 rounded-lg shadow overflow-hidden">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 text-left text-gray-700">Product</th>
            <th class="px-4 py-3 text-left text-gray-700">Type</th>
            <th class="px-4 py-3 text-left text-gray-700">Target</th>
            <th class="px-4 py-3 text-left text-gray-700">Achieved</th>
            <th class="px-4 py-3 text-left text-gray-700">Remaining</th>
            <th class="px-4 py-3 text-left text-gray-700">Start Date</th>
            <th class="px-4 py-3 text-left text-gray-700">End Date</th>
            <th class="px-4 py-3 text-left text-gray-700">Status</th>
        </tr>
    </thead>
    <tbody class="bg-white">
        @forelse($targets as $target)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">{{ $target->product?->name ?? '-' }}</td>
            <td class="px-4 py-3 capitalize">{{ $target->target_type }}</td>
            <td class="px-4 py-3">{{ $target->target_value }}</td>
            <td class="px-4 py-3">{{ $target->achieved_amount ?? 0 }}</td>
            <td class="px-4 py-3">{{ $target->target_value - ($target->achieved_amount ?? 0) }}</td>
            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($target->start_date)->format('d-m-Y') }}</td>
            <td class="px-4 py-3">{{ $target->end_date ? \Carbon\Carbon::parse($target->end_date)->format('d-m-Y') : '-' }}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                    {{ $target->status === 'accepted' ? 'bg-green-100 text-green-700' : ($target->status==='pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ ucfirst($target->status) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-4 text-gray-500">No targets found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
