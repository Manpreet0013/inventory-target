@extends('layouts.admin')

@section('title','Targets Listing')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            🎯 Targets
        </h1>

        <a href="{{ route('admin.targets') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow">
            + Assign New Target
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE CARD -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Executive</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Value</th>
                    <th class="px-4 py-3">Start Date</th>
                    <th class="px-4 py-3">End Date</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">View</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($targets as $index => $target)
                @php
                    $today = \Carbon\Carbon::today();
                    $status = 'Active';

                    if ($today->gt(\Carbon\Carbon::parse($target->end_date))) {
                        $status = 'Expired';
                    } elseif ($today->lt(\Carbon\Carbon::parse($target->start_date))) {
                        $status = 'Pending';
                    }
                @endphp

                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $target->product->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->executive->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 capitalize">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">
                            {{ $target->target_type }}
                        </span>
                    </td>

                    <td class="px-4 py-3 font-semibold">
                        {{ $target->target_value }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->start_date }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $target->end_date }}
                    </td>

                    <td class="px-4 py-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($status === 'Active') bg-green-100 text-green-700
                            @elseif($status === 'Pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $status }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.products.details', $target->product->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-xs">
                            View Targets
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-gray-500">
                        No targets found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
