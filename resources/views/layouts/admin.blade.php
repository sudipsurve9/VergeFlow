<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name', 'VergeFlow') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div id="app">
        <div class="d-flex">
            <!-- Sidebar -->
            <nav class="sidebar admin-sidebar">
                <div class="sidebar-header">
                    <span class="brand">Admin Panel</span>
                </div>
                @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="sidebar-profile text-center py-4 border-bottom mb-3 position-relative">
                    <div class="dropdown w-100">
                        <a href="#" class="d-flex flex-column align-items-center text-decoration-none" id="sidebarProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4fc3f7&color=fff&size=64" alt="Avatar" class="rounded-circle mb-2" width="64" height="64">
                            <div class="fw-bold" style="font-size:1.1rem; color:#fff;">{{ Auth::user()->name }}</div>
                            <div class="text-muted small mb-1" style="color:#b0bec5!important;">Admin</div>
                            <span class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle" style="top: 56px; left: 56%;"></span>
                        </a>
                        <ul class="dropdown-menu shadow border-0 p-0 mt-2 w-100 sidebar-profile-dropdown" aria-labelledby="sidebarProfileDropdown" style="border-radius: 1rem; min-width: 220px; left: 0; right: 0; margin: 0 auto; background: #263043;">
                            <li><a class="dropdown-item d-flex align-items-center py-3 sidebar-profile-link" href="{{ route('profile.edit') }}"><i class="fas fa-user me-3"></i> My Profile</a></li>
                            <li><a class="dropdown-item d-flex align-items-center py-3 sidebar-profile-link" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-3"></i> Settings</a></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-3 sidebar-profile-link" href="#">
                                    <span><i class="fas fa-file-invoice-dollar me-3"></i> Billing</span>
                                    <span class="badge bg-danger rounded-pill">4</span>
                                </a>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center py-3 sidebar-profile-link" href="#"><i class="fas fa-dollar-sign me-3"></i> Pricing</a></li>
                            <li><a class="dropdown-item d-flex align-items-center py-3 sidebar-profile-link" href="#"><i class="fas fa-question-circle me-3"></i> FAQ</a></li>
                            <li><hr class="dropdown-divider my-0" style="border-color:#374151;"></li>
                            <li class="p-3">
                                <form method="POST" action="{{ route('logout') }}" class="w-100">
                                    @csrf
                                    <button class="btn w-100 d-flex align-items-center justify-content-center fw-bold sidebar-logout-btn" type="submit" style="border-radius: 0.5rem; font-size: 1rem;">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                @elseif(!Auth::check())
                <div class="sidebar-profile text-center py-4 border-bottom mb-3">
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Login</a>
                </div>
                @endif
                <ul class="sidebar-menu nav flex-column">
                    <!-- Dashboard -->
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <!-- Catalog Section -->
                    <li class="sidebar-section">CATALOG</li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="fas fa-box"></i> Products</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.categories.index') }}"><i class="fas fa-tags"></i> Categories</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.reviews.index') }}"><i class="fas fa-star"></i> Reviews</a>
                    </li>
                    <!-- Sales Section -->
                    <li class="sidebar-section">SALES</li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.orders.index') }}"><i class="fas fa-shopping-cart"></i> Orders</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="fas fa-users"></i> Customers</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.coupons.index') }}"><i class="fas fa-ticket-alt"></i> Coupons</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.payments.index') }}"><i class="fas fa-credit-card"></i> Payments</a>
                    </li>
                    <!-- Content Section -->
                    <li class="sidebar-section">CONTENT</li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.pages.index') }}"><i class="fas fa-file-alt"></i> Pages</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.banners.index') }}"><i class="fas fa-images"></i> Banners</a>
                    </li>
                    <!-- Users Section -->
                    <li class="sidebar-section">USERS</li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-user-cog"></i> Users</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.wishlists.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.wishlists.index') }}"><i class="fas fa-heart"></i> Wishlists</a>
                    </li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.notifications.index') }}"><i class="fas fa-bell"></i> Notifications</a>
                    </li>
                    <!-- Profile Section -->
                    <li class="sidebar-section">PROFILE</li>
                    <li class="sidebar-item nav-item mb-2 {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> Profile</a>
                    </li>
                    <!-- System Section -->
                    <li class="sidebar-section">SYSTEM</li>
                    <li class="sidebar-item nav-item mb-2 dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="apiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-plug"></i> API
                        </a>
                        <ul class="dropdown-menu bg-dark" aria-labelledby="apiDropdown">
                            <li><a class="dropdown-item text-white" href="{{ route('admin.api-integrations.index') }}"><i class="fas fa-plug me-2"></i> API Integrations</a></li>
                            <li><a class="dropdown-item text-white" href="{{ route('admin.api-types.index') }}"><i class="fas fa-code me-2"></i> API Types</a></li>
                            <li><a class="dropdown-item text-white" href="{{ route('admin.api-logs.index') }}"><i class="fas fa-list-alt me-2"></i> API Logs</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-item nav-item mt-4">
                        <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-external-link-alt"></i> View Site</a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="py-4" style="margin-left:250px;width:100%;">
                <div class="container-fluid">
                @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        {{ session('error') }}
                    </div>
                @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('info') }}
                        </div>
                    @endif

                @yield('content')
            </div>
        </main>
    </div>
    </div>
    
    <style>
        .admin-sidebar {
            background: #1a2232;
            color: #fff;
            min-width: 220px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .admin-sidebar .sidebar-header .brand {
            color: #4fc3f7;
            font-weight: bold;
            font-size: 1.3rem;
            padding: 1.5rem 1rem;
            display: block;
        }
        .admin-sidebar .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .admin-sidebar .sidebar-item {
            padding: 0.75rem 1.5rem;
            color: #b0bec5;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .admin-sidebar .sidebar-item.active,
        .admin-sidebar .sidebar-item:hover {
            background: #263043;
            color: #4fc3f7;
        }
        .admin-sidebar .sidebar-item .nav-link {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
        }
        .admin-sidebar .sidebar-section {
            padding: 0.5rem 1.5rem 0.25rem 1.5rem;
            color: #90a4ae;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .sidebar-profile {
            background: #263043;
            border-radius: 0.5rem;
        }
        .sidebar-profile .btn {
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }
        .position-absolute.bg-success {
            width: 10px;
            height: 10px;
            border: 2px solid #fff;
        }
        .dot-online {
            width: 10px;
            height: 10px;
            background: #4fc3f7;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #fff;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-menu {
            box-shadow: 0 8px 24px rgba(60,72,88,.15);
            border-radius: 1rem;
            min-width: 220px;
            padding: 0;
        }
        .dropdown-item {
            font-size: 1rem;
            color: #222;
            transition: background 0.2s, color 0.2s;
        }
        .dropdown-item:active, .dropdown-item:focus, .dropdown-item:hover {
            background: #f5f7fa;
            color: #007bff;
        }
        .btn-danger[disabled], .btn-danger:disabled {
            background: #e0e0e0;
            color: #b0b0b0;
            border: none;
        }
        .sidebar-profile-dropdown {
            background: #263043 !important;
            color: #fff !important;
        }
        .sidebar-profile-link {
            color: #b0bec5 !important;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-profile-link:active, .sidebar-profile-link:focus, .sidebar-profile-link:hover {
            background: #1a2232 !important;
            color: #4fc3f7 !important;
        }
        .sidebar-logout-btn {
            background: #e53935;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .sidebar-logout-btn:hover, .sidebar-logout-btn:focus {
            background: #b71c1c;
            color: #fff;
        }
        .dropdown-divider {
            border-color: #374151 !important;
        }
        .sidebar-profile-settings {
            color: #4fc3f7 !important;
            border-color: #4fc3f7 !important;
            background: transparent !important;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .sidebar-profile-settings:hover, .sidebar-profile-settings:focus {
            background: #1a2232 !important;
            color: #fff !important;
            border-color: #4fc3f7 !important;
        }
    </style>
    
    @stack('scripts')
</body>
</html> 