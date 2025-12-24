<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans">

<div class="flex min-h-screen">
    @php
        $activeClass = 'bg-slate-700 text-white';
    @endphp
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white">
        <div class="p-5 text-xl font-bold border-b border-slate-700">
            Inventory Pro
        </div>

        <nav class="p-4 space-y-2 text-sm">

            <a href="/admin/dashboard"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('admin/dashboard') ? $activeClass : 'hover:bg-slate-700' }}">
                Dashboard
            </a>

            <a href="/admin/users"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('admin/users*') ? $activeClass : 'hover:bg-slate-700' }}">
                User Management
            </a>

            <a href="/admin/companies"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('admin/companies*') ? $activeClass : 'hover:bg-slate-700' }}">
                Companies Management
            </a>

            <a href="/admin/product-listing"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('admin/product-listing*') ? $activeClass : 'hover:bg-slate-700' }}">
                Product Management
            </a>

            <a href="/admin/target-listing"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('admin/target-listing*') || request()->is('admin/targets*') ? $activeClass : 'hover:bg-slate-700' }}">
                Targets Management
            </a>

            @php use App\Helpers\RoleHelper; @endphp
            <a href="{{ route('role.profile', RoleHelper::slug(auth()->user()->roles->first()->name)) }}"
               class="block px-4 py-2 rounded-lg transition
               {{ request()->is('admin/profile') || request()->is('admin/profile') ? $activeClass : 'hover:bg-slate-700' }}">
                Profile
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full text-left py-2 px-3 bg-red-600 text-white rounded hover:bg-red-700">
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

            <div class="relative ml-4 group">
                <!-- Bell Icon -->
                <button class="relative focus:outline-none">
                    🔔
                    @if(auth()->user()->unreadNotifications->count())
                        <span id="notif-count" class="absolute top-0 right-0 bg-red-500 text-white rounded-full px-1 text-xs">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown -->
                <div
                    class="absolute mt-2 right-0 w-80 bg-white border rounded shadow-lg max-h-96 overflow-auto hidden group-hover:block z-50"
                    id="notif-dropdown">
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <a href="#" 
                           class="block px-4 py-2 hover:bg-gray-100 notif-item" 
                           data-id="{{ $notification->id }}">
                            {{ $notification->data['message'] }}
                            <span class="text-xs text-gray-400 float-right">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <p class="px-4 py-2 text-gray-500">No new notifications</p>
                    @endforelse
                </div>
            </div>

        </header>

        <section class="p-6">
            @yield('content')
        </section>

    </main>

</div>
<script>
document.querySelectorAll('.notif-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        let id = this.dataset.id;
        let el = this;

        fetch(`/notifications/mark-as-read/${id}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                el.remove(); // Remove notification from dropdown

                // Update counter
                let countElem = document.getElementById('notif-count');
                let count = parseInt(countElem.textContent);
                count--;
                if(count <= 0) {
                    countElem.remove();
                    document.getElementById('notif-dropdown').innerHTML = '<p class="px-4 py-2 text-gray-500">No new notifications</p>';
                } else {
                    countElem.textContent = count;
                }
            }
        })
        .catch(err => console.error(err));
    });
});
</script>

</body>
</html>
