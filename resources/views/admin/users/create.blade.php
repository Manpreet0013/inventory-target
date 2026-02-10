@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Create User</h3>
            <small class="text-muted">Add new executive user</small>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card -->
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="row">

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control rounded-3"
                               placeholder="Enter full name"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control rounded-3"
                               placeholder="Enter email address"
                               required>
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               name="password"
                               class="form-control rounded-3"
                               placeholder="Enter password"
                               required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control rounded-3"
                               placeholder="Confirm password"
                               required>
                    </div>

                    <!-- Role -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Role
                        </label>

                        <!-- Disabled Select UI -->
                        <select class="form-select rounded-3 bg-light" disabled>
                            <option selected>Executive</option>
                        </select>

                        <!-- Hidden value -->
                        <input type="hidden" name="role" value="Executive">
                    </div>

                    <!-- Company Hidden -->
                    @if($companies->isNotEmpty())
                        <input type="hidden"
                               name="company_id"
                               value="{{ $companies->first()->id }}">
                    @endif

                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2 mt-3">

                    <button type="submit"
                            class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-check-circle"></i> Create User
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection
