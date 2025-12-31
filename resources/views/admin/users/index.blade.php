@extends('layouts.admin')

@section('title','User Management')

@section('content')
    
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.users.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Create New User
    </a>

    <table class="table-auto border-collapse border w-full text-left">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Name</th>
                <th class="border px-2 py-1">Email</th>
                <!-- <th class="border px-2 py-1">Company</th> -->
                <th class="border px-2 py-1">Role</th>
                <th class="border px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                <td class="border px-2 py-1">{{ $user->name }}</td>
                <td class="border px-2 py-1">{{ $user->email }}</td>
                <!-- <td class="border px-2 py-1">{{ $user->company?->name ?? '-' }}</td> -->
                <td class="border px-2 py-1">{{ $user->roles->pluck('name')->join(', ') }}</td>
                <td class="border px-2 py-1">
                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                       class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500">Edit</a>
                    <a href="{{ route('admin.users.profile', $user->id) }}" 
                       class="bg-blue-400 text-white px-2 py-1 rounded hover:bg-blue-500">Profile</a>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" 
                          method="POST" class="inline-block" 
                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        @if($user->id != 1)
                            <button type="submit" 
                                    class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                Delete
                            </button>
                        @endif
                    </form>
                    @if($user->roles->pluck('name')->contains('Executive'))
                        <a href="{{ route('admin.users.report', $user->id) }}"
                           class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 ml-1">
                            View Report
                        </a>
                    @endif

                </td>
            </tr>
            @empty
            <tr>
                <td class="border px-2 py-1 text-center" colspan="6">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
