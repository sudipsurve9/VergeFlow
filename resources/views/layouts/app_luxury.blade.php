<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Cinzel:wght@400;600;700;900&family=Cormorant+Garamond:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --gold-primary: #B8860B;
            --gold-secondary: #DAA520;
            --gold-accent: #FFD700;
            --gold-light: #FFF8DC;
            --gold-dark: #8B6914;
            --dark-color: #2F2F2F;
            --text-color: #1A1A1A;
            --bg-color: #FFFFFF;
            --border-color: #E5E5E5;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Cormorant Garamond', serif; 
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.8;
        }
        
        /* Elegant Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--gold-light);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gold-primary);
            border-radius: 4px;
        }
        
        /* Top Header */
        .top-header {
            background: var(--dark-color);
            color: var(--gold-accent);
            padding: 0.75rem 0;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--gold-primary);
        }
        .top-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .announcement {
            font-style: italic;
        }
        .top-links a {
            color: var(--gold-accent);
            text-decoration: none;
            margin-left: 2rem;
            transition: color 0.3s ease;
        }
        .top-links a:hover {
            color: var(--gold-secondary);
        }
        
        /* Main Header */
        .main-header { 
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary), var(--gold-accent));
            color: white;
            padding: 3rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="luxury" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23luxury)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .main-header h1 { 
            font-family: 'Cinzel', serif; 
            margin: 0;
            font-size: 4rem;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 3px;
        }
        .header-subtitle {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            margin-top: 0.5rem;
            font-style: italic;
            opacity: 0.9;
        }
        
        /* Search Section */
        .search-section {
            background: var(--gold-light);
            padding: 1.5rem 0;
            border-bottom: 2px solid var(--gold-primary);
        }
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }
        .search-box {
            flex: 1;
            max-width: 500px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--gold-primary);
            border-radius: 25px;
            background: white;
            color: var(--text-color);
            font-size: 1rem;
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold-primary);
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--gold-primary);
            text-decoration: none;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--gold-secondary);
            transform: translateY(-2px);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gold-secondary);
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
        
        /* Navigation */
        .category-nav { 
            background: var(--dark-color); 
            padding: 1.5rem 0;
            text-align: center;
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
        }
        .category-nav a {
            color: var(--gold-accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0;
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gold-secondary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .category-nav a:hover {
            color: var(--gold-secondary);
            transform: translateY(-2px);
        }
        .category-nav a:hover::before {
            width: 100%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
            border: 2px solid var(--gold-primary);
            color: white;
            font-weight: 600;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 2.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gold-secondary), var(--gold-accent));
            border-color: var(--gold-accent);
            box-shadow: 0 5px 20px rgba(184, 134, 11, 0.4);
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer { 
            background: var(--dark-color); 
            color: var(--gold-accent);
            padding: 4rem 0 2rem;
            margin-top: 4rem;
            border-top: 3px solid var(--gold-primary);
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }
        .footer-section h3 {
            color: var(--gold-secondary);
            margin-bottom: 1.5rem;
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid var(--gold-primary);
            padding-bottom: 0.5rem;
        }
        .footer-section p {
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .footer-section a {
            color: var(--gold-accent);
            text-decoration: none;
            display: block;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            font-style: italic;
        }
        .footer-section a:hover {
            color: var(--gold-secondary);
            transform: translateX(5px);
        }
        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .newsletter-form input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--gold-primary);
            background: transparent;
            color: var(--gold-accent);
            font-family: inherit;
        }
        .newsletter-form input::placeholder {
            color: var(--gold-accent);
            opacity: 0.7;
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--gold-primary);
            border: 1px solid var(--gold-primary);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .newsletter-form button:hover {
            background: var(--gold-secondary);
            border-color: var(--gold-secondary);
        }
        .footer-bottom {
            border-top: 1px solid var(--gold-primary);
            padding-top: 2rem;
            text-align: center;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .py-4 {
            padding: 3rem 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-header h1 {
                font-size: 2.5rem;
            }
            .search-container {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                gap: 1.5rem;
            }
            .category-nav a {
                font-size: 1rem;
            }
            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="announcement">
                <i class="fas fa-crown"></i> Free Shipping on Orders Over $500
            </div>
            <div class="top-links">
                <a href="/account">My Account</a>
                <a href="/wishlist">Wishlist</a>
                <a href="/contact">Contact</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
        <div class="container">
            <div class="header-content">
                <h1>Vault64 Luxury</h1>
                <div class="header-subtitle">Where Elegance Meets Excellence</div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for luxury products...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">2</span>
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
                <a href="/collections">Collections</a>
                <a href="/new-arrivals">New Arrivals</a>
                <a href="/exclusive">Exclusive</a>
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
                    <p>For over a decade, Vault64 has been the epitome of luxury and sophistication. We curate the finest collections for discerning customers who appreciate quality and elegance.</p>
                </div>
                <div class="footer-section">
                    <h3>Collections</h3>
                    <a href="/collections/premium">Premium Collection</a>
                    <a href="/collections/exclusive">Exclusive Items</a>
                    <a href="/collections/limited">Limited Edition</a>
                    <a href="/collections/seasonal">Seasonal Pieces</a>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <a href="/contact">Personal Concierge</a>
                    <a href="/shipping">Worldwide Shipping</a>
                    <a href="/returns">Returns & Exchanges</a>
                    <a href="/appointments">Private Appointments</a>
                </div>
                <div class="footer-section">
                    <h3>Newsletter</h3>
                    <p>Subscribe to receive exclusive offers and updates about our latest collections.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter your email address">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Vault64 Luxury &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
