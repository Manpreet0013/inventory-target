<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Material Icons (Optional) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #0f172a;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .nav-link {
            color: #cbd5f1;
            border-radius: 8px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            transition: 0.2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #1e293b;
            color: #fff;
        }

        .sidebar.collapsed .link-text {
            display: none;
        }

        .topbar {
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>

<div class="d-flex">

    <!-- ================= SIDEBAR ================= -->
    <div id="sidebar" class="sidebar p-3">

        <!-- Logo / Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-white mb-0 link-text">Inventory Pro</h5>
            <button class="btn btn-sm btn-light" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
        </div>

        @php $activeClass = 'active'; @endphp
        @php $roleSlug = strtolower(str_replace(' ', '-', auth()->user()->roles->first()->name ?? '')); @endphp

        <!-- Nav -->
        <nav class="nav flex-column text-sm">

            <a href="{{ route('inventory.dashboard') }}"
               class="nav-link {{ request()->is('inventory/dashboard') ? $activeClass : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span class="link-text">Dashboard</span>
            </a>

        

            <a href="{{ route('role.profile', $roleSlug) }}"
               class="nav-link {{ request()->is('inventory/profile') ? $activeClass : '' }}">
                <i class="bi bi-person"></i>
                <span class="link-text">Profile</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-danger w-100 d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="link-text">Logout</span>
                </button>
            </form>

        </nav>

    </div>


    <!-- ================= MAIN ================= -->
    <div class="flex-grow-1">

        <!-- Topbar -->
        <div class="topbar px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">@yield('title')</h5>
            <div class="fw-medium text-muted">
                <i class="bi bi-person-circle me-1"></i>
                {{ auth()->user()->name }}
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-4">
            @yield('content')
        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    }
</script>

</body>
</html>
