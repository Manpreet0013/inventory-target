@extends('layouts.admin')

@section('title', 'Companies Management')

@section('content')

    
    <h1 class="text-2xl font-bold mb-4">Companies</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.companies.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Create New Company
    </a>
    <div class="overflow-x-auto bg-white shadow-md rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Name</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($companies as $company)
                    <tr>
                        <td class="px-4 py-2">{{ $company->id }}</td>
                        <td class="px-4 py-2">{{ $company->name }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('admin.companies.edit', $company->id) }}" 
                               class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-sm">
                               Edit
                            </a>

                            <form action="{{ route('admin.companies.destroy', $company->id) }}" 
                                  method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-500">No companies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
