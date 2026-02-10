@extends('layouts.admin')

@section('title','Notifications')

@section('content')

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">📌 Notifications</h5>

        <span class="badge bg-secondary">
            {{ $notifications->count() }} Total
        </span>
    </div>

    <!-- GRID ROW -->
    <div class="row g-3">

        @forelse($notifications as $note)

        <!-- CARD -->
        <div class="col-md-4">

            <div class="card shadow-sm h-100 border-0">

                <!-- CARD BODY -->
                <div class="card-body p-3">

                    <!-- STATUS DOT -->
                    @if(!$note->read_at)
                        <span class="badge bg-primary mb-2">New</span>
                    @else
                        <span class="badge bg-success mb-2">Read</span>
                    @endif

                    <!-- MESSAGE -->
                    <p class="small text-dark mb-2">
                        {{ $note->data['message'] }}
                    </p>

                    <!-- TIME -->
                    <small class="text-muted d-block mb-3">
                        {{ $note->created_at->diffForHumans() }}
                    </small>

                    <!-- ACTION -->
                    @if(!$note->read_at)
                        <a href="{{ route('admin.notifications.read', $note->id) }}"
                           class="btn btn-sm btn-outline-primary w-100">
                            Mark as Read
                        </a>
                    @else
                        <span class="btn btn-sm btn-success w-100 disabled">
                            Already Read
                        </span>
                    @endif

                </div>

            </div>

        </div>

        @empty

        <div class="col-12 text-center text-muted py-5">
            No notifications found
        </div>

        @endforelse

    </div>

</div>

@endsection
