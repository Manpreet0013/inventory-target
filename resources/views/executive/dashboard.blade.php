@extends('layouts.executive')

@section('title','Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name }}</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500">Total Targets</p>
        <h2 class="text-3xl font-bold">{{ auth()->user()->targets()->count() }}</h2>
    </div>

    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500">Accepted</p>
        <h2 class="text-3xl font-bold text-green-600">
            {{ auth()->user()->targets()->where('status','accepted')->count() }}
        </h2>
    </div>

    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500">Pending</p>
        <h2 class="text-3xl font-bold text-yellow-600">
            {{ auth()->user()->targets()->where('status','pending')->count() }}
        </h2>
    </div>

    <div class="bg-white p-5 rounded-xl shadow">
        <p class="text-gray-500">Total Sales</p>
        <h2 class="text-3xl font-bold text-blue-600">
            {{ \App\Models\Sale::where('executive_id',auth()->id())->count() }}
        </h2>
    </div>
</div>
@endsection
