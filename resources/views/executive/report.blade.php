@extends('layouts.executive')

@section('title','Executive Dashboard')

@section('content')

<h2 class="text-2xl font-bold mb-6">📊 Executive Dashboard</h2>

{{-- ===== SUMMARY CARDS ===== --}}
<div class="grid grid-cols-2 gap-4 mb-6">

    <a href="{{ route('executive.targets.managed') }}">
        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-gray-500 text-sm">Products Added by Admin</p>
            <p class="text-xl font-bold">{{ $adminProductCount }}</p>
        </div>
    </a>

    <a href="{{ route('executive.targets.assigned') }}">
            <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-gray-500 text-sm">Products Added by You</p>
            <p class="text-xl font-bold">{{ $executiveProductCount }}</p>
        </div>
    </a>
</div>


{{-- ===== NOTIFICATION SECTION ===== --}}
<div class="bg-white rounded-xl shadow p-5">

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">🔔 Notifications</h3>
        <a href="{{ route('executive.notifications') }}"
           class="text-sm text-blue-600 hover:underline">
            View All
        </a>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $note)
            <div class="p-3 rounded-lg border
                {{ $note->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                
                <p class="text-gray-700">
                    {{ $note->data['message'] ?? 'New notification' }}
                </p>

                <small class="text-gray-400">
                    {{ $note->created_at->diffForHumans() }}
                </small>
            </div>
        @empty
            <p class="text-gray-500 text-sm">No notifications available.</p>
        @endforelse
    </div>

</div>

@endsection
