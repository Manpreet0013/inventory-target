<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>

@vite(['resources/css/app.css','resources/js/app.js'])
<link href="{{ asset('style.css') }}" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://target.crmmanch.com/style.css" rel="stylesheet">
<style>

/* ========== SIDEBAR ========== */

.sidebar {
    background: #0f172a;
    min-height: 100vh;
    transition: 0.3s;
}

/* UL spacing */
.sidebar ul {
    padding-left: 0;
}

/* Links */
.sidebar-link {
    color: #cbd5e1;
    text-decoration: none;
    position: relative;
    transition: all .25s ease;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Hover */
.sidebar-link:hover {
    background: rgba(255,255,255,0.08);
    color: #fff;
    transform: translateX(4px);
}

/* Active */
.active-link {
    background: rgba(59,130,246,0.15);
    color: #fff !important;
    border-left: 4px solid #3b82f6;
}

/* Icon circle */
.icon-wrap {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.08);
    border-radius: 10px;
    transition: .3s;
}

/* Icon hover */
.sidebar-link:hover .icon-wrap {
    background: #3b82f6;
    color: #fff;
}

/* Active icon */
.active-link .icon-wrap {
    background: #3b82f6;
    color: #fff;
}

/* Notification badge */
.notify-badge {
    background: red;
    font-size: 10px;
    padding: 3px 6px;
    border-radius: 50px;
    margin-left: auto;
}

/* Header */
.topbar {
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
}

</style>

</head>

<body class="bg-slate-100" x-data="{ sidebarCollapsed:false }">

<div class="d-flex">

<!-- ================= SIDEBAR ================= -->

<aside :class="sidebarCollapsed ? 'w-20' : 'w-64'"
       class="sidebar text-white p-3">

    <!-- Logo / Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h5 x-show="!sidebarCollapsed" class="m-0 fw-bold">
            Inventory Pro
        </h5>

        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="btn btn-sm btn-light">
            ☰
        </button>

    </div>

    <!-- ===== UL MENU ===== -->

    <ul class="list-unstyled space-y-1">

        <!-- Dashboard -->
        <li>
            <a href="/admin/dashboard"
               class="sidebar-link px-3 py-2 rounded
               {{ request()->is('admin/dashboard') ? 'active-link' : '' }}">

                <span class="icon-wrap">
                    <span class="material-icons">dashboard</span>
                </span>

                <span x-show="!sidebarCollapsed">Dashboard</span>
            </a>
        </li>

        <!-- Users -->
        <li>
            <a href="/admin/users"
               class="sidebar-link px-3 py-2 rounded
               {{ request()->is('admin/users*') ? 'active-link' : '' }}">

                <span class="icon-wrap">
                    <span class="material-icons">people</span>
                </span>

                <span x-show="!sidebarCollapsed">User Management</span>
            </a>
        </li>

        <!-- Products -->
        <li>
            <a href="/admin/product-listing"
               class="sidebar-link px-3 py-2 rounded
               {{ request()->is('admin/product-listing*') ? 'active-link' : '' }}">

                <span class="icon-wrap">
                    <span class="material-icons">inventory_2</span>
                </span>

                <span x-show="!sidebarCollapsed">Products</span>
            </a>
        </li>

        <!-- Targets -->
        <li>
            <a href="/admin/target-listing"
               class="sidebar-link px-3 py-2 rounded
               {{ request()->is('admin/target-listing*') ? 'active-link' : '' }}">

                <span class="icon-wrap">
                    <span class="material-icons">track_changes</span>
                </span>

                <span x-show="!sidebarCollapsed">Targets</span>
            </a>
        </li>

        <!-- Sales -->
        <li>
            <a href="/admin/sales"
               class="sidebar-link px-3 py-2 rounded
               {{ request()->is('admin/sales*') ? 'active-link' : '' }}">

                <span class="icon-wrap">
                    <span class="material-icons">payments</span>
                </span>

                <span x-show="!sidebarCollapsed">Sales</span>
            </a>
        </li>

        <!-- Notifications -->
        <li>
            <a href="{{ route('admin.notifications') }}"
               class="sidebar-link px-3 py-2 rounded
               {{ request()->is('admin/notifications*') ? 'active-link' : '' }}">

                <span class="icon-wrap">
                    <span class="material-icons">notifications</span>
                </span>

                <span x-show="!sidebarCollapsed">Notifications</span>

                @if($adminUnreadCount > 0)
                    <span class="notify-badge">
                        {{ $adminUnreadCount }}
                    </span>
                @endif
            </a>
        </li>

        <!-- Profile -->
        <li>
            <a href="{{ route('role.profile','admin') }}"
               class="sidebar-link px-3 py-2 rounded">

                <span class="icon-wrap">
                    <span class="material-icons">person</span>
                </span>

                <span x-show="!sidebarCollapsed">Profile</span>
            </a>
        </li>

        <!-- Tracking IP -->
        <li>
            <a href="{{ route('admin.tracking.ips') }}"
               class="sidebar-link px-3 py-2 rounded">

                <span class="icon-wrap">
                    <span class="material-icons">security</span>
                </span>

                <span x-show="!sidebarCollapsed">Tracking IPs</span>
            </a>
        </li>

    </ul>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button class="btn btn-danger w-100 d-flex align-items-center gap-2">
            <span class="material-icons">logout</span>
            <span x-show="!sidebarCollapsed">Logout</span>
        </button>
    </form>

</aside>

<!-- ================= MAIN ================= -->

<div class="flex-grow-1">

    <!-- Topbar -->
    <div class="topbar px-4 py-3 d-flex justify-content-between">

        <h5 class="m-0">@yield('title')</h5>

        <div>{{ auth()->user()->name }}</div>

    </div>

    <!-- Content -->
    <div class="p-4">
        @yield('content')
    </div>

</div>

</div>

</body>
</html>
