@extends('layouts.admin')

@section('title', 'User Profile')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">User Profile</h3>
            <small class="text-muted">User details overview</small>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Profile Card -->
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">

            <div class="row">

                <!-- Left: Avatar -->
                <!-- Left: Avatar -->
<div class="col-md-3 d-flex flex-column align-items-center justify-content-center text-center border-end py-4">

    <!-- Avatar -->
    <div class="mb-3">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=120"
             class="rounded-circle shadow"
             alt="User Avatar">
    </div>

    <!-- Name -->
    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>

    <!-- Role -->
    <small class="text-muted">
        {{ $user->roles->pluck('name')->join(', ') }}
    </small>

</div>


                <!-- Right: Details -->
                <div class="col-md-9">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <small class="text-muted">Email</small>
                                <div class="fw-semibold">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <small class="text-muted">Company</small>
                                <div class="fw-semibold">
                                    {{ $user->company?->name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <small class="text-muted">Created At</small>
                                <div class="fw-semibold">
                                    {{ $user->created_at->format('d M, Y H:i') }}
                                </div>
                            </div>
                        </div>

                        @if($user->updated_at)
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <small class="text-muted">Last Updated</small>
                                <div class="fw-semibold">
                                    {{ $user->updated_at->format('d M, Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 d-flex gap-2">

                        <a href="{{ route('admin.users.edit', $user->id) }}"
                           class="btn btn-primary shadow-sm">
                            <i class="bi bi-pencil-square"></i> Edit User
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                           class="btn btn-outline-secondary">
                            Back to Users
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>
@endsection
