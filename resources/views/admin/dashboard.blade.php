@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- PAGE TITLE -->
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        📊 Admin Overview Report
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        <a href="{{ route('admin.list') }}"
           class="block bg-blue-600 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Total Target</p>
            <h2 class="text-3xl font-bold">{{ $totalTargets }}</h2>
        </a>

        <a href="{{ route('admin.list') }}"
           class="block bg-green-500 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Current</p>
            <h2 class="text-3xl font-bold">{{ $currentTargets }}</h2>
        </a>

        <a href="{{ route('admin.list', ['status' => 'expired']) }}"
           class="block bg-red-500 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Expired</p>
            <h2 class="text-3xl font-bold">{{ $expiredTargets }}</h2>
        </a>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        <a href="{{ route('admin.list', ['status' => 'achieved_full']) }}"
           class="block bg-blue-600 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Achieved Fully</p>
            <h2 class="text-3xl font-bold">{{ $achievedFully }}</h2>
        </a>

        <a href="{{ route('admin.list', ['status' => 'achieved_partial']) }}"
           class="block bg-yellow-500 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Achieved Partially</p>
            <h2 class="text-3xl font-bold">{{ $achievedPartial }}</h2>
        </a>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <a href="{{ route('admin.products') }}"
           class="block bg-blue-500 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Total Products</p>
            <h2 class="text-3xl font-bold">{{ $totalProducts }}</h2>
        </a>

        <a href="{{ route('admin.products', ['target' => 'set']) }}"
           class="block bg-green-600 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Target Set</p>
            <h2 class="text-3xl font-bold">{{ $targetSetProducts }}</h2>
        </a>

        <a href="{{ route('admin.products', ['target' => 'not_set']) }}"
           class="block bg-red-500 text-white p-4 rounded-xl hover:shadow-lg hover:scale-105 transition cursor-pointer">
            <p>Target Not Set</p>
            <h2 class="text-3xl font-bold">{{ $targetNotSetProducts }}</h2>
        </a>

    </div>


</div>
@endsection
