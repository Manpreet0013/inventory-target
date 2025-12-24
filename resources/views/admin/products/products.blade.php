@extends('layouts.admin')

@section('title','Products Listing')

@section('content')

<h1 class="text-2xl font-bold mb-4">Products</h1>

<a href="{{ route('admin.add-product') }}" 
   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
    Create New Product
</a>

<table class="border w-full text-left">
    <thead>
        <tr class="bg-gray-200">
            <th class="border px-2 py-1">#</th>
            <th class="border px-2 py-1">Product Name</th>
            <th class="border px-2 py-1">Image</th>
            <th class="border px-2 py-1">Composition</th>
            <th class="border px-2 py-1">Type</th>
            <th class="border px-2 py-1">Expiry</th>
            <th class="border px-2 py-1">Target</th>
            <th class="border px-2 py-1">Targets Status</th>
            <th class="border px-2 py-1">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr>
            <td class="border px-2 py-1">{{ $index + 1 }}</td>
            <td class="border px-2 py-1">{{ $product->name }}</td>
            <td class="border px-2 py-1">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="w-16 h-16 object-cover">
                @else
                    <span class="text-gray-500">No Image</span>
                @endif
            </td>
            <td class="border px-2 py-1">{{ $product->composition ?? '-' }}</td>
            <td class="border px-2 py-1">{{ $product->type }}</td>
            <td class="border px-2 py-1">{{ $product->expiry_date ?? '-' }}</td>
            <td class="border px-2 py-1">{{ $product->targets()->count() }}</td>
            <td class="border px-2 py-1">
                @if($product->targets->count() > 0)
                    <span class="px-2 py-1 rounded text-white 
                        {{ $product->isTargetCompleted() ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $product->isTargetCompleted() ? 'Complete' : 'Incomplete' }}
                    </span>
                @else
                    <span class="text-gray-500">No Targets</span>
                @endif
            </td>
            <td class="border px-2 py-1">
                <a href="{{ route('admin.products.details', $product->id) }}" 
                   class="bg-blue-600 text-white px-2 py-1 rounded">View Targets</a>
                <a href="{{ route('admin.targets') }}" 
                   class="bg-green-600 text-white px-2 py-1 rounded">Add Targets</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
