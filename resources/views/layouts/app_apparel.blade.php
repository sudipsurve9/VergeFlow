<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vault64') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --apparel-primary: #2c3e50;
            --apparel-secondary: #e74c3c;
            --apparel-accent: #f39c12;
            --apparel-light: #ecf0f1;
            --apparel-dark: #34495e;
            --apparel-text: #2c3e50;
            --apparel-bg: #ffffff;
            --apparel-border: #bdc3c7;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--apparel-bg); 
            color: var(--apparel-text);
            line-height: 1.6;
        }
        
        /* Top Header */
        .top-header {
            background: var(--apparel-dark);
            color: white;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }
        .top-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .contact-info a {
            color: var(--apparel-light);
            text-decoration: none;
            margin-right: 1rem;
        }
        .social-links a {
            color: var(--apparel-light);
            margin-left: 1rem;
            font-size: 1.1rem;
        }
        
        /* Main Header */
        .main-header { 
            background: var(--apparel-primary); 
            color: white; 
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo h1 { 
            font-family: 'Playfair Display', serif; 
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .search-bar {
            position: relative;
        }
        .search-bar input {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 25px;
            width: 300px;
            font-size: 0.9rem;
        }
        .search-bar button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--apparel-primary);
            cursor: pointer;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-menu a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--apparel-secondary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        /* Category Navigation */
        .category-nav { 
            background: var(--apparel-secondary); 
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        .category-nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
        }
        .category-nav a:hover {
            color: var(--apparel-accent);
            transform: translateY(-2px);
        }
        .category-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--apparel-accent);
            transition: width 0.3s ease;
        }
        .category-nav a:hover::after {
            width: 100%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: var(--apparel-accent); 
            border-color: var(--apparel-accent);
            color: var(--apparel-primary);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: var(--apparel-secondary);
            border-color: var(--apparel-secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        
        /* Footer */
        .footer { 
            background: var(--apparel-dark); 
            color: white; 
            padding: 3rem 0 1rem;
            margin-top: 3rem;
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .footer-section h3 {
            color: var(--apparel-accent);
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
        }
        .footer-section a {
            color: var(--apparel-light);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }
        .footer-section a:hover {
            color: var(--apparel-accent);
        }
        .footer-bottom {
            border-top: 1px solid var(--apparel-border);
            padding-top: 1rem;
            text-align: center;
            color: var(--apparel-light);
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .py-4 {
            padding: 2rem 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            .search-bar input {
                width: 250px;
            }
            .nav-links {
                flex-wrap: wrap;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="contact-info">
                <a href="tel:+1234567890"><i class="fas fa-phone"></i> +1 (234) 567-890</a>
                <a href="mailto:info@vault64.com"><i class="fas fa-envelope"></i> info@vault64.com</a>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Vault64 Apparel</h1>
                </div>
                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" placeholder="Search for products...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    <div class="user-menu">
                        <a href="/profile"><i class="fas fa-user"></i></a>
                        <a href="/wishlist"><i class="fas fa-heart"></i></a>
                        <div class="cart-icon">
                            <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                            <span class="cart-badge">3</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav">
        <div class="container">
            <div class="nav-links">
                <a href="/">Home</a>
                <a href="/products">All Products</a>
                <a href="/categories">Categories</a>
                <a href="/new-arrivals">New Arrivals</a>
                <a href="/sale">Sale</a>
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Vault64</h3>
                    <p>Your premier destination for fashion and style. Discover the latest trends and timeless classics.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="/products">Shop All</a>
                    <a href="/new-arrivals">New Arrivals</a>
                    <a href="/sale">Sale Items</a>
                    <a href="/size-guide">Size Guide</a>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <a href="/contact">Contact Us</a>
                    <a href="/shipping">Shipping Info</a>
                    <a href="/returns">Returns</a>
                    <a href="/faq">FAQ</a>
                </div>
                <div class="footer-section">
                    <h3>Newsletter</h3>
                    <p>Subscribe for the latest updates and exclusive offers.</p>
                    <input type="email" placeholder="Enter your email" style="padding: 0.5rem; border-radius: 5px; border: none; width: 100%; margin-top: 0.5rem;">
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Vault64 Apparel &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html> 