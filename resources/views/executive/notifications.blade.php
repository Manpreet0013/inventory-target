@extends('layouts.executive')

@section('title','Notifications')

@section('content')

<h2 class="text-2xl font-bold mb-6">🔔 Notifications</h2>

<div class="space-y-3">
    @forelse($notifications as $note)
        <div class="p-4 bg-white rounded-lg shadow">
            <p class="text-gray-700">
                {{ $note->data['message'] ?? 'Notification' }}
            </p>
            <small class="text-gray-400">
                {{ $note->created_at->diffForHumans() }}
            </small>
        </div>
    @empty
        <p class="text-gray-500">No notifications found.</p>
    @endforelse
</div>

@endsection
