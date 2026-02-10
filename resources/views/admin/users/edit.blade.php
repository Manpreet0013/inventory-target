@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Edit User</h3>
            <small class="text-muted">Update user details</small>
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

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row">

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="form-control rounded-3"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control rounded-3"
                               required>
                    </div>

                    <!-- Role (Disabled UI) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Role
                        </label>

                        <select class="form-select bg-light rounded-3" disabled>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ $user->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Hidden Role Value -->
                        <input type="hidden"
                               name="role"
                               value="{{ $user->roles->first()->name }}">
                    </div>

                    <!-- Company Hidden -->
                    @php
                        $companyId = $user->company_id ?? $companies->first()?->id;
                    @endphp

                    @if($companyId)
                        <input type="hidden"
                               name="company_id"
                               value="{{ $companyId }}">
                    @endif

                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2 mt-3">

                    <button type="submit"
                            class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-check-circle"></i> Update User
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
