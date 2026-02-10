@extends('layouts.admin')

@section('title', 'Tracking IP Whitelist')

@section('content')

<div class="container py-4">

    <!-- PAGE TITLE -->
    <div class="mb-4">
        <h4 class="fw-bold">
            🔐 Tracking IP Whitelist
        </h4>
        <p class="text-muted small mb-0">
            Manage IP addresses allowed to access tracking system
        </p>
    </div>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- CARD -->
    <div class="card shadow-sm border-0">

        <div class="card-body">

            <form method="POST" action="{{ route('admin.tracking.ips.update') }}">
                @csrf

                <!-- TEXTAREA -->
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Allowed IP Addresses
                    </label>

                    <textarea
                        name="allowed_ips"
                        rows="8"
                        class="form-control"
                        placeholder="Example:
127.0.0.1
192.168.1.1
103.21.244.0"
                    >{{ old('allowed_ips', $allowedIps) }}</textarea>

                    <div class="form-text">
                        Enter one IP address per line.
                    </div>

                    @error('allowed_ips')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- BUTTON -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        💾 Save IPs
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection
