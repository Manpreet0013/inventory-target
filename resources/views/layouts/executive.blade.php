<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white">
        <div class="p-5 text-xl font-bold border-b border-slate-700">
            Inventory Pro
        </div>
        @php
            $activeClass = 'bg-slate-700 text-white';
        @endphp
        <nav class="p-4 space-y-2 text-sm">

            <a href="{{ route('executive.report') }}"
               class="block px-4 py-2 rounded hover:bg-slate-700 {{ request()->is('executive/report') ? $activeClass : 'hover:bg-slate-700' }}">
               My Report
            </a>

            <a href="{{ route('executive.targets.managed') }}"
               class="block px-4 py-2 rounded hover:bg-slate-700 {{ request()->is('executive/targets/managed') ? $activeClass : 'hover:bg-slate-700' }}">
               📌 Products Admin
            </a>

            <a href="{{ route('executive.targets.assigned') }}"
               class="block px-4 py-2 rounded hover:bg-slate-700 {{ request()->is('executive/targets/assigned') ? $activeClass : 'hover:bg-slate-700' }}">
               📌 Products executive
            </a>

            @php
                $activeClass = 'bg-slate-700 text-white';
                $unreadCount = auth()->user()->unreadNotifications()->count();
            @endphp

            <a href="{{ route('executive.notifications') }}"
               class="flex items-center justify-between px-4 py-2 rounded-lg transition
               {{ request()->is('executive/notifications*') ? $activeClass : 'hover:bg-slate-700' }}">
                
                <span>Notifications</span>

                @if($unreadCount > 0)
                    <span class="ml-2 inline-flex items-center justify-center
                        w-6 h-6 text-xs font-bold text-white bg-red-600 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>


            @php use App\Helpers\RoleHelper; @endphp
            <a href="{{ route('role.profile', RoleHelper::slug(auth()->user()->roles->first()->name)) }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('executive/profile') || request()->is('executive/profile') ? $activeClass : 'hover:bg-slate-700' }}">
                Profile
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-3 bg-red-600 text-white rounded hover:bg-red-700">
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1">

        <!-- Top bar -->
        <header class="bg-white px-6 py-4 shadow flex justify-between items-center">
            <h1 class="text-xl font-semibold text-slate-800">
                @yield('title')
            </h1>

            <div class="text-sm font-medium">
                {{ auth()->user()->name }}
            </div>
        </header>

        <section class="p-6">
            @yield('content')
        </section>

    </main>

</div>
{{-- Global Target Modal --}}
@include('executive.components.partial-accept-modal')

{{-- Global Target JS --}}
@include('executive.components.target-actions-js')

</body>
</html>
