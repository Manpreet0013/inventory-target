@extends('layouts.inventory')

@section('title','Inventory Dashboard')

@section('content')

<div class="p-6">

    {{-- 4 Summary Cards --}}
   <div class="grid grid-cols-4 gap-6 mb-8 overflow-x-auto">

        {{-- Total Targets --}}
        <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col hover:shadow-xl transition min-w-[220px]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Targets</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalTarget }}</p>
                </div>
                <div class="text-blue-500 text-3xl">
                    <i class="fas fa-bullseye"></i>
                </div>
            </div>
            <div class="mt-4 w-full bg-gray-200 h-2 rounded-full">
                <div class="h-2 bg-blue-500 rounded-full" style="width: 100%"></div>
            </div>
            <p class="text-xs mt-2 text-gray-500">All product targets combined</p>
        </div>

        {{-- Achieved --}}
        <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col hover:shadow-xl transition min-w-[220px]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Achieved</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $achieved }}</p>
                </div>
                <div class="text-green-500 text-3xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="mt-4 w-full bg-gray-200 h-2 rounded-full">
                <div class="h-2 bg-green-500 rounded-full" style="width: {{ $totalTarget ? round(($achieved/$totalTarget)*100) : 0 }}%"></div>
            </div>
            <p class="text-xs mt-2 text-gray-500">{{ $totalTarget ? round(($achieved/$totalTarget)*100) : 0 }}% completed</p>
        </div>

        {{-- Remaining --}}
        <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col hover:shadow-xl transition min-w-[220px]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Remaining</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $remaining }}</p>
                </div>
                <div class="text-yellow-500 text-3xl">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-4 w-full bg-gray-200 h-2 rounded-full">
                <div class="h-2 bg-yellow-500 rounded-full" style="width: {{ $totalTarget ? round(($remaining/$totalTarget)*100) : 0 }}%"></div>
            </div>
            <p class="text-xs mt-2 text-gray-500">{{ $totalTarget ? round(($remaining/$totalTarget)*100) : 0 }}% remaining</p>
        </div>

        {{-- Total Sales --}}
        <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col hover:shadow-xl transition min-w-[220px]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSales }}</p>
                </div>
                <div class="text-purple-500 text-3xl">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="mt-4 w-full bg-gray-200 h-2 rounded-full">
                <div class="h-2 bg-purple-500 rounded-full" style="width: {{ $totalTarget ? round(($totalSales/$totalTarget)*100) : 0 }}%"></div>
            </div>
            <p class="text-xs mt-2 text-gray-500">{{ $totalTarget ? round(($totalSales/$totalTarget)*100) : 0 }}% of targets sold</p>
        </div>

    </div>


    {{-- Products & Targets Tree --}}
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-lg p-4 border hover:shadow-xl transition">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
                    <span class="text-sm text-gray-500">{{ $product->type }}</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $product->composition ?? '-' }}</p>

                @forelse($product->targets as $target)
                    @php
                        $achievedTarget = $target->target_type === 'box' 
                            ? $target->sales->sum('boxes_sold') 
                            : $target->sales->sum('amount');
                        $remainingTarget = max($target->target_value - $achievedTarget, 0);
                        $percent = $target->target_value > 0 
                            ? round(($achievedTarget / $target->target_value) * 100) 
                            : 0;
                    @endphp

                    <div class="bg-gray-50 p-3 rounded mb-2 border-l-4 {{ $percent>=100 ? 'border-green-600' : 'border-blue-500' }}">
                        <div class="flex justify-between items-center mb-1 text-sm">
                            <span>{{ $target->executive->name ?? '-' }}</span>
                            <span>{{ $achievedTarget }}/{{ $target->target_value }}</span>
                        </div>
                        <div class="w-full bg-gray-200 h-2 rounded">
                            <div class="h-2 rounded {{ $percent>=100 ? 'bg-green-600' : 'bg-blue-500' }}" style="width: {{ min($percent,100) }}%"></div>
                        </div>
                    </div>

                    {{-- Child Targets --}}
                    @foreach($target->children as $child)
                        @php
                            $achievedChild = $child->target_type === 'box' ? $child->sales->sum('boxes_sold') : $child->sales->sum('amount');
                            $remainingChild = max($child->target_value - $achievedChild, 0);
                            $percentChild = $child->target_value > 0 ? round(($achievedChild / $child->target_value) * 100) : 0;
                        @endphp
                        <div class="ml-6 bg-gray-100 p-2 rounded mb-1 border-l-4 {{ $percentChild>=100 ? 'border-green-500' : 'border-blue-400' }}">
                            <div class="flex justify-between text-xs mb-1">
                                <span>{{ $child->executive->name ?? '-' }} (Child)</span>
                                <span>{{ $achievedChild }}/{{ $child->target_value }}</span>
                            </div>
                            <div class="w-full bg-gray-300 h-1 rounded">
                                <div class="h-1 rounded {{ $percentChild>=100 ? 'bg-green-500' : 'bg-blue-400' }}" style="width: {{ min($percentChild,100) }}%"></div>
                            </div>
                        </div>
                    @endforeach

                @empty
                    <p class="text-xs text-gray-400">No targets assigned</p>
                @endforelse
            </div>
        @empty
            <p class="text-gray-500">No products found.</p>
        @endforelse
    </div>

</div>

@endsection
