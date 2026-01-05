@extends('layouts.admin')

@section('title','User Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            👥 Users
        </h1>

        <a href="{{ route('admin.users.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
            + Create New User
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
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($users as $index => $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $user->name }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $user->email }}
                    </td>

                    <td class="px-4 py-3">
                        @foreach($user->roles as $role)
                            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full mr-1">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>

                    <td class="px-4 py-3 text-center space-x-1">

                        <!-- EDIT -->
                        <a href="{{ route('admin.users.edit',$user->id) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md text-xs">
                            Edit
                        </a>

                        <!-- PROFILE -->
                        <a href="{{ route('admin.users.profile',$user->id) }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">
                            Profile
                        </a>

                        <!-- REPORT (EXECUTIVE ONLY) -->
                        @if($user->roles->pluck('name')->contains('Executive'))
                            <a href="{{ route('admin.users.report',$user->id) }}"
                               class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs">
                                Report
                            </a>
                        @endif

                        <!-- DELETE -->
                        @if($user->id != 1)
                        <form action="{{ route('admin.users.destroy',$user->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs">
                                Delete
                            </button>
                        </form>
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
