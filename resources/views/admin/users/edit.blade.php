@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto mt-6">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded px-6 py-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label for="name" class="block font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div class="mb-4">
                <label for="email" class="block font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div class="mb-4">
                <label for="role" class="block font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <!-- <select name="role" id="role"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" 
                                {{ $user->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select> -->
                <select id="role"
                        class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
                        disabled>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ $user->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Hidden field so value is submitted --}}
                <input type="hidden" name="role" value="{{ $user->roles->first()->name }}">

            </div>

            <div class="mb-6">
                <!-- <label for="company_id" class="block font-medium text-gray-700 mb-1">Company</label>
                <select name="company_id" id="company_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" 
                                {{ $user->company_id == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select> -->
                @php
                    $companyId = $user->company_id ?? $companies->first()?->id;
                @endphp

                @if($companyId)
                    <input type="hidden" name="company_id" value="{{ $companyId }}">
                @endif

            </div>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                    Update
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-400 text-white px-5 py-2 rounded hover:bg-gray-500 transition">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
