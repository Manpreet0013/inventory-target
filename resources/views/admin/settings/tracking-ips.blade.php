@extends('layouts.admin')

@section('title', 'Tracking IP Whitelist')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6">

    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        🔐 Tracking IP Whitelist
    </h1>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tracking.ips.update') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Allowed IP Addresses
            </label>

            <textarea
                name="allowed_ips"
                rows="8"
                class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-indigo-200"
                placeholder="Example:
127.0.0.1
192.168.1.1
103.21.244.0"
            >{{ old('allowed_ips', $allowedIps) }}</textarea>

            <p class="mt-2 text-sm text-gray-500">
                Enter one IP address per line.
            </p>

            @error('allowed_ips')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700"
        >
            Save IPs
        </button>
    </form>

</div>
@endsection
