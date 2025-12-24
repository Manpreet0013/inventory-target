@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<h1 class="text-2xl font-bold mb-4">All Notifications</h1>

@if($notifications->count())
    <ul class="space-y-2">
        @foreach($notifications as $notification)
            <li class="p-2 border rounded {{ $notification->read_at ? 'bg-gray-100' : 'bg-white' }}">
                {{ $notification->data['message'] }}
                <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
            </li>
        @endforeach
    </ul>
@else
    <p>No notifications found.</p>
@endif
@endsection
