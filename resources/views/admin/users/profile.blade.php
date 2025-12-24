@extends('layouts.admin')

@section('title', 'User Profile')

@section('content')
<div class="container mx-auto mt-6">
    <div class="bg-white shadow-md rounded px-6 py-6">
        <h1 class="text-2xl font-bold mb-4">User Profile</h1>

        <div class="mb-4">
            <p><span class="font-semibold">Name:</span> {{ $user->name }}</p>
            <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
            <p><span class="font-semibold">Role(s):</span> {{ $user->roles->pluck('name')->join(', ') }}</p>
            <p><span class="font-semibold">Company:</span> {{ $user->company?->name ?? '-' }}</p>
            <p><span class="font-semibold">Created At:</span> {{ $user->created_at->format('d M, Y H:i') }}</p>
            @if($user->updated_at)
                <p><span class="font-semibold">Last Updated:</span> {{ $user->updated_at->format('d M, Y H:i') }}</p>
            @endif
        </div>

        <div class="flex gap-3 mt-4">
            <a href="{{ route('admin.users.index') }}" 
               class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                Back to Users
            </a>
            <a href="{{ route('admin.users.edit', $user->id) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Edit User
            </a>
        </div>
    </div>
</div>
@endsection
