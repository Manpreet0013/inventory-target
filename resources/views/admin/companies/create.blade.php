@extends('layouts.admin')

@section('title', 'Create Company')

@section('content')
<div class="container mx-auto mt-6">
    <div class="bg-white shadow-md rounded px-6 py-6">

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.companies.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block font-semibold mb-1">Company Name</label>
                <input type="text" name="name" id="name" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="{{ old('name') }}" required>
            </div>

            <div class="flex gap-3">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Create
                </button>
                <a href="{{ route('admin.companies.index') }}" 
                   class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
