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

        <div class="flex items-center justify-between p-5 border-b border-slate-700">
            <span x-show="!sidebarCollapsed" class="text-xl font-bold">Inventory Pro</span>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="text-white focus:outline-none">
                <svg :class="sidebarCollapsed ? 'rotate-180' : ''" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-2 text-sm">
            @php $activeClass = 'bg-slate-700 text-white'; @endphp

            <a href="/admin/dashboard"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/dashboard') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">dashboard</span>
                <span x-show="!sidebarCollapsed">Dashboard</span>
            </a>

            <a href="/admin/users"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/users*') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">people</span>
                <span x-show="!sidebarCollapsed">User Management</span>
            </a>

            <a href="/admin/product-listing"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/product-listing*') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">inventory_2</span>
                <span x-show="!sidebarCollapsed">Product Management</span>
            </a>

            <a href="/admin/target-listing"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/target-listing*') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">track_changes</span>
                <span x-show="!sidebarCollapsed">Targets Management</span>
            </a>

            <a href="/admin/sales"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/sales*') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">payments</span>
                <span x-show="!sidebarCollapsed">Total Sales</span>
            </a>

            <a href="{{ route('admin.notifications') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/notifications*') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">notifications</span>
                <span x-show="!sidebarCollapsed">Notifications</span>
                @if($adminUnreadCount > 0)
                    <span x-show="!sidebarCollapsed" class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ $adminUnreadCount }}
                    </span>
                @endif
            </a>

            @php $roleSlug = strtolower(str_replace(' ', '-', auth()->user()->roles->first()->name ?? '')); @endphp

            <a href="{{ route('role.profile', $roleSlug) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->is('admin/profile') ? $activeClass : 'hover:bg-slate-700' }}">
                <span class="material-icons">person</span>
                <span x-show="!sidebarCollapsed">Profile</span>
            </a>

            <a href="{{ route('admin.tracking.ips') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('admin.tracking.ips*') ? $activeClass : 'hover:bg-slate-700' }}">
                
                <span class="material-icons">security</span>
                <span x-show="!sidebarCollapsed">Tracking IPs</span>
            </a>



            <form method="POST" action="{{ route('logout') }}" class="mt-4 px-4">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 py-2 px-3 bg-red-600 text-white rounded hover:bg-red-700">
                    <span class="material-icons">logout</span>
                    <span x-show="!sidebarCollapsed">Logout</span>
                </button>
            </form>
        </nav>

    </aside>

    <!-- Main content -->
    <main class="flex-1 transition-all duration-300">
        <header class="bg-white px-6 py-4 shadow flex justify-between items-center">
            <h1 class="text-xl font-semibold text-slate-800">@yield('title')</h1>
            <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
        </header>

        <section class="p-6">
            @yield('content')
        </section>
    </main>

</div>
</body>
</html>
