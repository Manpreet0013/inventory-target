@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')

<div class="container-fluid py-4">

    <!-- TARGETS -->
    <h5 class="mb-3 fw-semibold">🎯 Targets</h5>

    <div class="row g-4 mb-4">


        <!-- Total -->
        <div class="col-md-4">
            <a href="{{ route('admin.list') }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <span class="material-icons">flag</span>
                    </div>

        <a href="{{ route('admin.list') }}"
           class="block bg-green-500 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Current</p>
            <h2 class="text-3xl font-bold">{{ $currentTargets }}</h2>
        </a>

                    <div>
                        <p>Total Targets</p>
                        <h3>{{ $totalTargets }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Current -->
        <div class="col-md-4">
            <a href="{{ route('admin.list',['status'=>'current']) }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <span class="material-icons">trending_up</span>
                    </div>

                    <div>
                        <p>Current Targets</p>
                        <h3>{{ $currentTargets }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Expired -->
        <div class="col-md-4">
            <a href="{{ route('admin.list',['status'=>'expired']) }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-danger">
                        <span class="material-icons">event_busy</span>
                    </div>

                    <div>
                        <p>Expired Targets</p>
                        <h3>{{ $expiredTargets }}</h3>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- ACHIEVEMENTS -->
    <h5 class="mb-3 fw-semibold">🏆 Achievements</h5>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <a href="{{ route('admin.list',['status'=>'achieved_full']) }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <span class="material-icons">emoji_events</span>
                    </div>

                    <div>
                        <p>Achieved Fully</p>
                        <h3>{{ $achievedFully }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6">
            <!-- ✅ FIXED TAG -->
            <a href="{{ route('admin.list',['status'=>'achieved_partial']) }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <span class="material-icons">military_tech</span>
                    </div>

                    <div>
                        <p>Achieved Partially</p>
                        <h3>{{ $achievedPartial }}</h3>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- PRODUCTS -->
    <h5 class="mb-3 fw-semibold">📦 Products</h5>

    <div class="row g-4">

        <div class="col-md-4">
            <a href="{{ route('admin.products') }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <span class="material-icons">inventory_2</span>
                    </div>

                    <div>
                        <p>Total Products</p>
                        <h3>{{ $totalProducts }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('admin.products',['target'=>'set']) }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <span class="material-icons">check_circle</span>
                    </div>

                    <div>
                        <p>Target Set</p>
                        <h3>{{ $targetSetProducts }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('admin.products',['target'=>'not_set']) }}" class="card-link">
                <div class="stat-card">
                    <div class="stat-icon bg-danger">
                        <span class="material-icons">cancel</span>
                    </div>

                    <div>
                        <p>Target Not Set</p>
                        <h3>{{ $targetNotSetProducts }}</h3>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>

@endsection
