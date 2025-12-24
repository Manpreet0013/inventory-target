@extends('layouts.app') <!-- or your admin/executive layout -->

@section('title', 'Access Denied')

@section('content')
<div class="flex flex-col items-center justify-center h-screen text-center p-4 bg-gray-50">
    <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
    <h2 class="text-2xl font-semibold mb-2">Access Denied</h2>
    <p class="text-gray-700 mb-4">
        You do not have permission to access this page.
    </p>
    <a href="{{ url()->previous() ?? '/' }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Go Back
    </a>
</div>
@endsection
