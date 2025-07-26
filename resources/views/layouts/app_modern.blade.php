<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #181818;
            --secondary-bg: #232323;
            --card-bg: #232323;
            --text-primary: #fff;
            --text-secondary: #bbb;
            --accent-color: #ffb300;
            --accent-glow: #ff6a00;
            --border-color: #333;
        }

        [data-theme="light"] {
            --primary-bg: #fff;
            --secondary-bg: #f8f9fa;
            --card-bg: #fff;
            --text-primary: #181818;
            --text-secondary: #444;
            --accent-color: #ff6a00;
            --accent-glow: #ffb300;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: radial-gradient(ellipse at 50% 20%, #ff9900 0%, #2d1a06 80%, #18120a 100%);
            min-height: 100vh;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        [data-theme="light"] body {
            background: radial-gradient(ellipse at 50% 20%, #ffb347 0%, #ffe7c2 80%, #fff 100%);
            min-height: 100vh;
        }

        .navbar {
            background: var(--secondary-bg) !important;
            border-bottom: 2px solid var(--accent-color);
        }

        .navbar-brand {
            color: var(--accent-color) !important;
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            text-shadow: 0 0 10px var(--accent-glow);
            text-decoration: none;
        }

        .brand-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: var(--accent-color);
            text-shadow: 0 0 8px var(--accent-glow);
        }

        .nav-link, .nav-link:visited {
            color: var(--text-primary) !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.15);
            transition: color 0.3s ease;
        }

        .nav-link:hover, .nav-link:focus {
            color: var(--accent-color) !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-accent, .btn-accent:hover, .btn-accent:focus {
            color: #fff !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .theme-switcher {
            background: var(--card-bg);
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            border-radius: 50px;
            padding: 8px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .theme-switcher:hover {
            background: var(--accent-color);
            color: white;
            box-shadow: 0 0 15px var(--accent-glow);
            transform: translateY(-2px);
        }

        .theme-switcher i {
            margin-right: 5px;
        }

        .neon-glow {
            text-shadow: 0 0 4px var(--accent-color), 0 0 8px var(--accent-glow);
            animation: neonPulse 3s infinite alternate;
        }

        @keyframes neonPulse {
            from { text-shadow: 0 0 4px var(--accent-color), 0 0 8px var(--accent-glow); }
            to { text-shadow: 0 0 8px var(--accent-color), 0 0 16px var(--accent-glow); }
        }

        .checkout-glow {
            box-shadow: 0 0 16px rgba(255, 179, 0, 0.6);
            transition: box-shadow 0.2s;
        }

        .checkout-glow:hover {
            box-shadow: 0 0 32px var(--accent-color), 0 0 64px var(--accent-glow);
        }

        .icon-btn-glow i {
            transition: text-shadow 0.2s, color 0.2s;
        }

        .icon-btn-glow:hover i {
            color: var(--accent-color) !important;
            text-shadow: 0 0 8px var(--accent-color), 0 0 16px var(--accent-glow);
        }

        input, textarea, select {
            background: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary) !important;
        }

        input::placeholder, textarea::placeholder {
            color: var(--text-secondary) !important;
        }

        .form-label, label {
            color: var(--text-primary) !important;
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .table {
            color: var(--text-primary);
        }

        .table th {
            background: var(--secondary-bg);
            color: var(--accent-color);
            border-color: var(--border-color);
        }

        .table td {
            border-color: var(--border-color);
        }

        .footer {
            background: var(--secondary-bg);
            border-top: 2px solid var(--accent-color);
            color: var(--text-primary);
            padding: 3rem 0 1rem;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,179,0,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,179,0,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,179,0,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,179,0,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,179,0,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .footer .container {
            position: relative;
            z-index: 1;
        }

        .footer h5 {
            color: var(--accent-color);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            text-shadow: 0 0 8px var(--accent-glow);
        }

        .footer p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .footer ul {
            margin: 0;
            padding: 0;
        }

        .footer ul li {
            margin-bottom: 0.75rem;
        }

        .footer ul li i {
            color: var(--accent-color);
            width: 20px;
            text-align: center;
        }

        /* Footer links */
        .footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .footer a:hover {
            color: var(--accent-color);
            text-shadow: 0 0 8px var(--accent-glow);
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 50%;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .social-links a:hover {
            background: var(--accent-color);
            color: var(--text-primary);
            border-color: var(--accent-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 179, 0, 0.3);
        }

        .footer .input-group {
            margin-top: 1rem;
        }

        .footer .form-control {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 25px 0 0 25px;
            padding: 0.75rem 1rem;
        }

        .footer .form-control:focus {
            background: var(--card-bg);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 179, 0, 0.25);
            color: var(--text-primary);
        }

        .footer .form-control::placeholder {
            color: var(--text-muted);
        }

        .footer .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
            border: none;
            color: var(--text-primary);
            border-radius: 0 25px 25px 0;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 179, 0, 0.3);
            color: var(--text-primary);
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            margin-top: 2rem;
            padding-top: 1.5rem;
            text-align: center;
        }

        .footer-bottom p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
        }

        .product-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 179, 0, 0.2);
            border-color: var(--accent-color);
        }

        .product-image {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }

        .product-info {
            padding: 20px;
        }

        .product-title {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .product-category {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .product-price {
            color: var(--accent-color);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .sale-price {
            color: #ff4757;
        }

        .original-price {
            text-decoration: line-through;
            color: var(--text-secondary);
        }

        .badge {
            background: var(--accent-color) !important;
            color: white !important;
        }

        .alert {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .modal-content {
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .dropdown-menu {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .dropdown-item {
            color: var(--text-primary);
        }

        .dropdown-item:hover {
            background: var(--secondary-bg);
            color: var(--accent-color);
        }

        .pagination .page-link {
            background: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .pagination .page-link:hover {
            background: var(--accent-color);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Update existing styles for better theme compatibility */
        .top-header {
            background: var(--secondary-bg);
            color: var(--text-primary);
            padding: 8px 0;
            font-size: 14px;
        }

        .top-header a {
            color: var(--text-primary);
            text-decoration: none;
            margin-right: 20px;
            transition: color 0.3s;
        }

        .top-header a:hover {
            color: var(--accent-color);
        }

        .main-header {
            background: var(--secondary-bg);
            box-shadow: 0 2px 10px rgba(255, 106, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .search-form {
            position: relative;
            max-width: 400px;
        }

        .search-form input {
            border-radius: 25px;
            border: 2px solid var(--border-color);
            padding: 10px 45px 10px 20px;
            width: 100%;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .search-form button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--accent-color);
            border: none;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-icon {
            position: relative;
            color: var(--text-primary);
            font-size: 20px;
            text-decoration: none;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-nav {
            background: var(--secondary-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 0;
        }

        .category-nav .nav-link {
            color: var(--text-primary);
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 20px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .category-nav .nav-link:hover,
        .category-nav .nav-link.active {
            background: var(--accent-color);
            color: white;
        }

        .logo-icon {
            color: var(--accent-color);
        }

        /* Button styles for theme compatibility */
        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-glow);
            border-color: var(--accent-glow);
            color: white;
        }

        .btn-outline-primary {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .btn-outline-primary:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }

        /* Form elements */
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 179, 0, 0.25);
        }

        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 179, 0, 0.25);
        }

        /* Newsletter section */
        .newsletter-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px;
        }

        /* Social links */
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
            color: var(--text-primary);
        }

        .social-links a:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Footer links */
        .footer a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--accent-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .theme-switcher {
                padding: 6px 12px;
                font-size: 12px;
            }
            
            .theme-switcher span {
                display: none;
            }
            
            .navbar-brand span {
                font-size: 1.5rem !important;
            }
        }

        /* Remove nuclear override and add targeted light mode fixes */
        html[data-theme="light"] body {
            background: radial-gradient(ellipse at 50% 20%, #ffb347 0%, #ffe7c2 80%, #fff 100%);
            min-height: 100vh;
        }
        html[data-theme="light"] .main-header,
        html[data-theme="light"] .category-nav,
        html[data-theme="light"] .footer,
        html[data-theme="light"] main,
        html[data-theme="light"] .container.py-5,
        html[data-theme="light"] #products-container {
            background: #fff !important;
            color: #181818 !important;
        }
        html[data-theme="light"] .card,
        html[data-theme="light"] .product-card,
        html[data-theme="light"] .theme-card {
            background: #f8f9fa !important;
            color: #181818 !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border-color: #eee !important;
        }
        html[data-theme="light"] .neon-glow,
        html[data-theme="light"] .product-title-glow,
        html[data-theme="light"] .price-glow,
        html[data-theme="light"] .section-title {
            color: #FF6A00 !important;
            text-shadow: 0 1px 4px #FFB30033, 0 0 0 #fff;
        }
        html[data-theme="light"] .btn-accent {
            background: linear-gradient(45deg, #FFB300, #FF6A00);
            color: #fff !important;
        }
        html[data-theme="light"] .btn-accent:hover {
            background: linear-gradient(45deg, #FF6A00, #FFB300);
            color: #fff !important;
        }
        html[data-theme="light"] .hero-section, [data-theme="light"] .welcome-section {
            background: none !important;
        }

        /* Visually hidden but focusable skip link */
        .visually-hidden-focusable {
            position: absolute;
            left: -9999px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
        .visually-hidden-focusable:focus {
            position: static;
            width: auto;
            height: auto;
            left: auto;
            background: var(--accent-color, #FFB300);
            color: #181818;
            padding: 8px 16px;
            z-index: 10000;
            border-radius: 4px;
            outline: 2px solid var(--accent-glow, #FF6A00);
        }
        /* Improved color contrast for .text-muted */
        .text-muted {
            color: #b3b3b3 !important;
        }
        [data-theme="light"] .text-muted {
            color: #555 !important;
        }
        @media (max-width: 991.98px) {
            .main-header .row.align-items-center {
                flex-direction: column;
                text-align: center;
            }
            .main-header .col-md-3,
            .main-header .col-md-6 {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            }
            .main-header .col-md-3.text-end {
                justify-content: center !important;
                margin-top: 10px;
            }
            .search-form {
                margin: 10px 0;
                max-width: 100%;
            }
            .navbar-collapse {
                background: var(--secondary-bg);
                padding: 1rem;
            }
            .category-nav .d-flex.flex-wrap {
                flex-direction: column;
                align-items: stretch;
            }
            .category-nav .nav-link {
                margin-bottom: 8px;
                padding: 12px 0;
                border-radius: 10px;
            }
            .product-card {
                margin-bottom: 20px;
            }
        }
        @media (max-width: 575.98px) {
            .brand-text {
                font-size: 1.2rem !important;
            }
            .main-header .row.align-items-center {
                padding: 10px 0 !important;
            }
            .product-image {
                height: 160px;
            }
            .banner-btn, .btn, .form-control {
                font-size: 1rem;
                min-height: 44px;
            }
        }
    </style>
</head>
<body>
    <!-- Skip to Content Link for Accessibility -->
    <a href="#main-content" class="visually-hidden-focusable skip-link" tabindex="0">Skip to main content</a>
    
    <div id="app">
        <!-- Top Header -->
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span><i class="fa-solid fa-phone me-2"></i>+1 (555) 123-4567</span>
                        <span class="ms-3"><i class="fa-solid fa-envelope me-2"></i>info@valult64.com</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header with Hamburger -->
        <header class="main-header">
            <div class="container">
                <div class="row align-items-center py-3">
                    <div class="col-md-3">
                        <a class="navbar-brand d-flex align-items-center" href="/">
                            <img src="{{ asset('logo.png') }}" alt="Vault64 Logo" style="width:200px;height:auto;max-width:100%;object-fit:contain;margin-right:10px;">
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form class="search-form" role="search" aria-label="Product search">
                            <input type="text" placeholder="Search for products..." aria-label="Search for products">
                            <button type="submit" aria-label="Submit search"><i class="fa-solid fa-search"></i></button>
                        </form>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <button class="theme-switcher me-3" onclick="toggleTheme()" aria-label="Toggle dark and light mode">
                                <i class="fa-solid fa-moon" id="theme-icon"></i>
                                <span id="theme-text">Dark</span>
                            </button>
                            @auth
                                <div class="dropdown me-3">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="User menu for {{ Auth::user()->name }}">
                                        <i class="fa-solid fa-user-circle me-1"></i>{{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="fa-solid fa-user"></i> Profile
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">My Orders</a></li>
                                        @if(auth()->user()->role === 'admin')
                                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endauth
                            
                            <a href="{{ route('cart.index') }}" class="cart-icon ms-3" aria-label="View cart">
                                <i class="fa-solid fa-shopping-cart"></i>
                                <span class="cart-badge" id="cart-count">0</span>
                            </a>
                            <!-- Hamburger for mobile -->
                            <button class="navbar-toggler d-lg-none ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Mobile Navigation Collapse -->
        <nav class="category-nav d-lg-none" role="navigation" aria-label="Mobile category navigation">
            <div class="container">
                <div class="collapse" id="mobileNav">
                    <div class="d-flex flex-column">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">All Products</a>
                        @foreach($categories ?? [] as $category)
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                               class="nav-link {{ request('category') == $category->slug ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                        @auth
                            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">My Orders</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin*') ? 'active' : '' }}">Admin</a>
                            @endif
                        @endauth
                        <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">Profile</a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Desktop Navigation -->
        <nav class="category-nav d-none d-lg-block" role="navigation" aria-label="Category navigation">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">All Products</a>
                            @foreach($categories ?? [] as $category)
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                                   class="nav-link {{ request('category') == $category->slug ? 'active' : '' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                            @auth
                                <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">My Orders</a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin*') ? 'active' : '' }}">Admin</a>
                                @endif
                            @endauth
                            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-4" id="main-content" role="main" tabindex="-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5>
                            <i class="fa-solid fa-warehouse me-2"></i>About Vault64
                        </h5>
                        <p>Your premier destination for authentic racing collectibles and premium motorsport merchandise. We specialize in curating the finest selection of racing memorabilia for true enthusiasts.</p>
                        <div class="social-links mt-3">
                            <a href="#" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                            <a href="#" title="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h5>
                            <i class="fa-solid fa-link me-2"></i>Quick Links
                        </h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}"><i class="fa-solid fa-home me-2"></i>Home</a></li>
                            <li><a href="{{ route('products.index') }}"><i class="fa-solid fa-box me-2"></i>Products</a></li>
                            <li><a href="#"><i class="fa-solid fa-info-circle me-2"></i>About Us</a></li>
                            <li><a href="#"><i class="fa-solid fa-envelope me-2"></i>Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h5>
                            <i class="fa-solid fa-headset me-2"></i>Customer Support
                        </h5>
                        <ul class="list-unstyled">
                            <li><i class="fa-solid fa-phone me-2"></i>+91 (555) 123-4567</li>
                            <li><i class="fa-solid fa-envelope me-2"></i>support@vault64.com</li>
                            <li><i class="fa-solid fa-clock me-2"></i>Mon-Fri: 9AM-6PM IST</li>
                            <li><i class="fa-solid fa-shipping-fast me-2"></i>Free Shipping on Orders ₹999+</li>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h5>
                            <i class="fa-solid fa-bell me-2"></i>Newsletter
                        </h5>
                        <p>Subscribe for latest racing collectibles and exclusive offers</p>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email">
                            <button class="btn btn-primary" type="button">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} Vault64. All rights reserved. | Premium Racing Collectibles</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Cart Count Update Script -->
    <script>
        // Dark mode toggle logic
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            const icon = document.getElementById('theme-icon');
            const text = document.getElementById('theme-text');
            if (theme === 'dark') {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                text.textContent = 'Dark';
            } else {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                text.textContent = 'Light';
            }
        }
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            setTheme(current === 'dark' ? 'light' : 'dark');
        }
        // On page load, set theme from localStorage or default
        (function() {
            const saved = localStorage.getItem('theme');
            if (saved) setTheme(saved);
        })();

        // Update cart count via AJAX
        function updateCartCount() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                });
        }

        // Update cart count every 5 seconds
        setInterval(updateCartCount, 5000);
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</body>
</html>
