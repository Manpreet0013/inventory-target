<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-blue-100 p-5 rounded shadow text-center">
        <div class="text-3xl font-bold text-blue-600">{{ $targets->sum('target_value') }}</div>
        <div class="text-gray-700 mt-2 font-medium">Total Target</div>
    </div>
    <div class="bg-green-100 p-5 rounded shadow text-center">
        <div class="text-3xl font-bold text-green-600">{{ $targets->sum(fn($t) => $t->achievedValue()) }}</div>
        <div class="text-gray-700 mt-2 font-medium">Achieved</div>
    </div>
    <div class="bg-red-100 p-5 rounded shadow text-center">
        <div class="text-3xl font-bold text-red-600">{{ $targets->sum(fn($t) => $t->remainingValue()) }}</div>
        <div class="text-gray-700 mt-2 font-medium">Remaining</div>
    </div>
</div>
