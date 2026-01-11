<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans" x-data="{ sidebarCollapsed: false }">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'"
           class="bg-slate-900 text-white transition-all duration-300 relative">

        <!-- Sidebar header -->
        <div class="flex items-center justify-between p-5 border-b border-slate-700">
            <span x-show="!sidebarCollapsed" class="text-xl font-bold">Inventory Pro</span>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="text-white focus:outline-none">
                <span class="material-icons">menu</span>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2 text-sm">
            @php $activeClass = 'bg-slate-700 text-white'; @endphp
            @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
            @php $roleSlug = strtolower(str_replace(' ', '-', auth()->user()->roles->first()->name ?? '')); @endphp

            <a href="{{ route('executive.report') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('executive/report') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">assessment</span>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>My Report</span>
            </a>

            <a href="{{ route('executive.targets.managed') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('executive/targets/managed') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">admin_panel_settings</span>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Products Admin</span>
            </a>

            <a href="{{ route('executive.targets.assigned') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('executive/targets/assigned') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">groups</span>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Products Executive</span>
            </a>

            <a href="{{ route('executive.notifications') }}"
               class="flex items-center justify-between gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('executive/notifications*') ? $activeClass : 'hover:bg-slate-700' }}">
                <div class="flex items-center gap-3">
                    <span class="material-icons">notifications</span>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Notifications</span>
                </div>
                @if($unreadCount > 0)
                    <span x-show="!sidebarCollapsed" class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-600 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('role.profile', $roleSlug) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('executive/profile') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">person</span>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-4 px-4">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 py-2 px-3 bg-red-600 text-white rounded hover:bg-red-700">
                    <span class="material-icons">logout</span>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Logout</span>
                </button>
            </form>
        </nav>

    </aside>

    <!-- Main content -->
    <main class="flex-1 transition-all duration-300">
        <!-- Top bar -->
        <header class="bg-white px-6 py-4 shadow flex justify-between items-center">
            <h1 class="text-xl font-semibold text-slate-800">@yield('title')</h1>
            <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
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
