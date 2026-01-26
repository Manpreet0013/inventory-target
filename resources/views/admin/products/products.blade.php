@extends('layouts.admin')

@section('title','Products Listing')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            📦 Products
        </h1>

        <a href="{{ route('admin.add-product') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
            + Create New Product
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Image</th>
                    <th class="px-4 py-3">Composition</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Expiry</th>
                    <th class="px-4 py-3 text-center">Targets</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($products as $index => $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $product->name }}
                    </td>

                    <td class="px-4 py-3">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 class="w-12 h-12 rounded object-cover" width="100px">
                        @else
                            <span class="text-gray-400 italic">No Image</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        {{ $product->composition ?? '-' }}
                    </td>

                    <td class="px-4 py-3 capitalize">
                        {{ $product->type }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $product->expiry_date ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-center font-semibold">
                        {{ $product->targets()->whereNull('parent_id')->count() }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($product->targets->count())
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $product->isTargetCompleted()
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700' }}">
                                {{ $product->isTargetCompleted() ? 'Completed' : 'Incomplete' }}
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs">
                                No Targets
                            </span>
                        @endif
                    </td>

                    @php
                        $parentTarget = $product->targets->whereNull('parent_id')->first();
                        $parentTargetCount = $parentTarget ? 1 : 0;
                    @endphp
                    
                    <td class="px-4 py-3 text-center space-x-2">

                        <!-- View Button -->
                        <a href="{{ route('admin.products.details', $product->id) }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">
                            View
                        </a>

                        @if ($parentTargetCount === 0)
                            <!-- Add Button -->
                            <a href="{{ route('admin.targets', ['product_id' => $product->id]) }}"
                               class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs">
                                Add
                            </a>
                        @endif

                        @if ($parentTargetCount === 1)
                            <!-- Delete Target -->
                            <form action="{{ route('admin.product.destroy', $product->id) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this target?')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs">
                                    Delete
                                </button>
                            </form>
                        @endif

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-gray-500">
                        No products found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
