@extends('layouts.admin')

@section('title','Notifications')

@section('content')
<h2 class="text-2xl font-bold mb-4">📌 Notifications</h2>

<div class="space-y-3">
    @foreach($notifications as $note)
    <div class="p-3 bg-white rounded shadow flex justify-between items-center">
        <div>
            <p class="text-gray-700">{{ $note->data['message'] }}</p>
            <small class="text-gray-400">{{ $note->created_at->diffForHumans() }}</small>
        </div>
        <div class="flex gap-2">
            @if(!$note->read_at)
                <a href="{{ route('admin.notifications.read', $note->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">Mark Read</a>
            @else
                <span class="text-green-600 text-xs">Read</span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
