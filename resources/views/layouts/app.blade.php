<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') - MarketLink</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>MarketLink</h2>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="icon">🏠</span>
                            <span class="text">الرئيسية</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <span class="icon">👥</span>
                            <span class="text">العملاء</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'active' : '' }}">
                            <span class="icon">📁</span>
                            <span class="text">المشاريع</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                            <span class="icon">👨‍💼</span>
                            <span class="text">الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('subscriptions.index') }}" class="{{ request()->routeIs('subscriptions.*') ? 'active' : '' }}">
                            <span class="icon">🔔</span>
                            <span class="text">الاشتراكات</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">
                            <span class="icon">📊</span>
                            <span class="text">تقرير الحسابات</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.payment-status', ['month' => '2026-01']) }}" class="{{ request()->routeIs('reports.payment-status') ? 'active' : '' }}">
                            <span class="icon">💳</span>
                            <span class="text">حالة الدفع</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                            <span class="icon">📋</span>
                            <span class="text">لوحة المهام</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link">
                            <span class="icon">⚙️</span>
                            <span class="text">الإعدادات</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="main-wrapper">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'لوحة التحكم')</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-logout">تسجيل الخروج</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="main-content">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="main-footer">
                <div class="footer-content">
                    <p>&copy; {{ date('Y') }} MarketLink. جميع الحقوق محفوظة.</p>
                    <p>نظام إدارة العملاء</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('modals')

    <script>
        // Toggle Sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.querySelector('.main-wrapper');

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('sidebar-collapsed');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', toggleSidebar);
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                    sidebar.classList.remove('collapsed');
                    mainWrapper.classList.remove('sidebar-collapsed');
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

