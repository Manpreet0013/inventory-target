@extends('layouts.admin')

@section('title','👥 User Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- HEADER -->
 <div class="px-6 py-1 mb-6">

    <!-- TITLE -->

    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2
              bg-emerald-600 hover:bg-emerald-700
              text-white px-6 py-2.5 rounded-xl
              shadow-md hover:shadow-lg transition">
        <span class="flex items-center justify-center w-8 h-8 rounded-full
                     bg-white/20 text-lg font-bold">
            +
        </span>
        <span class="font-semibold">Create User</span>
    </a>

</div>



    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-700 px-5 py-3 rounded-xl">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- TABLE CARD -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-700">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">
                                {{ $user->name }}
                            </div>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $user->email }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full
                                               text-xs font-semibold
                                               bg-indigo-100 text-indigo-700">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-wrap justify-center gap-2">

                                <!-- EDIT -->
                                <a href="{{ route('admin.users.edit',$user->id) }}"
                                   class="px-3 py-1.5 rounded-md text-xs font-semibold
                                          bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                                    ✏️ Edit
                                </a>

                                <!-- PROFILE -->
                                <a href="{{ route('admin.users.profile',$user->id) }}"
                                   class="px-3 py-1.5 rounded-md text-xs font-semibold
                                          bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                    👤 Profile
                                </a>

                                <!-- REPORT -->
                                @if($user->roles->pluck('name')->contains('Executive'))
                                    <a href="{{ route('admin.users.report',$user->id) }}"
                                       class="px-3 py-1.5 rounded-md text-xs font-semibold
                                              bg-green-100 text-green-700 hover:bg-green-200 transition">
                                        📊 Report
                                    </a>
                                @endif

                                <!-- DELETE -->
                                @if($user->id != 1)
                                <form action="{{ route('admin.users.destroy',$user->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold
                                                   bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        🗑 Delete
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-500">
                            No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
