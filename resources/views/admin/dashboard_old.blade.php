@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- PAGE TITLE -->
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        📊 Admin Overview Report
    </h1>

    <!-- ===============================
        SUMMARY COUNTS
    =============================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <a href="{{ route('admin.products') }}" class="block">
            <div class="bg-green-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p class="text-sm opacity-90">Total Products</p>
                <h2 class="text-3xl font-bold">{{ $totalProducts }}</h2>
            </div>
        </a>

        <a href="{{ route('admin.list') }}" class="block">
            <div class="bg-blue-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p class="text-sm opacity-90">Total Targets</p>
                <h2 class="text-3xl font-bold">{{ $totalTargets }}</h2>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="block">
            <div class="bg-yellow-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p class="text-sm opacity-90">Total Executives</p>
                <h2 class="text-3xl font-bold">{{ $totalExecutives }}</h2>
            </div>
        </a>

    </div>

    <!-- ===============================
        TARGET & SALES SUMMARY
    =============================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <a href="{{ route('admin.list') }}" class="block">
            <div class="bg-red-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p class="text-sm opacity-90">Expired Targets</p>
                <h2 class="text-3xl font-bold">{{ $expiredTargets }}</h2>
            </div>
        </a>

        <a href="{{ route('admin.list') }}" class="block">
            <div class="bg-blue-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p>Total Target Amount & Boxes</p>
                <h2 class="text-2xl font-bold">₹ {{ number_format($totalTargetAmount) }}</h2>
                <p class="text-sm mt-1">Boxes: {{ number_format($totalTargetBoxes) }}</p>
            </div>
        </a>

        <a href="{{ route('admin.sales.index') }}" class="block">
            <div class="bg-green-600 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p>Achieved (Fully Approved)</p>
                <h2 class="text-2xl font-bold">₹ {{ number_format($approvedAmount) }}</h2>
                <p class="text-sm mt-1">Boxes: {{ number_format($approvedBoxes) }}</p>
            </div>
        </a>

    </div>

    <!-- ===============================
        SALES STATUS / PENDING
    =============================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">

        <a href="{{ route('admin.list') }}" class="block">
            <div class="bg-yellow-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p>Pending</p>
                <h2 class="text-2xl font-bold">₹ {{ number_format($pendingAmount) }}</h2>
                <p class="text-sm mt-1">Boxes: {{ number_format($pendingBoxes) }}</p>
            </div>
        </a>

        <a href="{{ route('admin.sales.index') }}" class="block">
            <div class="bg-red-500 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p>Approved Sales Count</p>
                <h2 class="text-3xl font-bold">{{ $approvedSales }}</h2>
            </div>
        </a>

        <a href="{{ route('admin.sales.index') }}" class="block">
            <div class="bg-blue-600 text-white p-5 rounded-xl shadow hover:shadow-lg transition">
                <p>Total Sales Amount</p>
                <h2 class="text-3xl font-bold">₹ {{ number_format($approvedAmount) }}</h2>
            </div>
        </a>

    </div>

</div>
@endsection
