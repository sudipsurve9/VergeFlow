<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&family=Oswald:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --urban-black: #000000;
            --urban-gray: #333333;
            --urban-light-gray: #666666;
            --urban-white: #FFFFFF;
            --urban-red: #FF0000;
            --urban-yellow: #FFFF00;
            --urban-blue: #0066CC;
            --urban-green: #00FF00;
            --text-color: #FFFFFF;
            --bg-color: #000000;
            --border-color: #333333;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Urban Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, rgba(255,0,0,0.02) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(0,255,0,0.02) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(0,0,255,0.02) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(255,255,0,0.02) 75%);
            background-size: 20px 20px;
            z-index: -1;
        }
        
        /* Top Bar */
        .top-bar {
            background: var(--urban-gray);
            color: var(--urban-white);
            padding: 0.5rem 0;
            font-size: 0.9rem;
            border-bottom: 2px solid var(--urban-red);
        }
        .top-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .urban-info span {
            margin-right: 2rem;
        }
        .urban-info i {
            color: var(--urban-yellow);
        }
        .urban-actions a {
            color: var(--urban-white);
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }
        .urban-actions a:hover {
            color: var(--urban-yellow);
        }
        
        /* Main Header */
        .main-header { 
            background: var(--urban-black);
            color: var(--urban-white);
            padding: 2rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid var(--urban-red);
        }
        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="graffiti" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M10 10 L40 10 L40 40 L10 40 Z" fill="none" stroke="rgba(255,0,0,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23graffiti)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .main-header h1 { 
            font-family: 'Oswald', sans-serif; 
            margin: 0;
            font-size: 4rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 2px 2px 0 var(--urban-red), 4px 4px 0 var(--urban-yellow);
        }
        .header-subtitle {
            font-size: 1.2rem;
            margin-top: 0.5rem;
            color: var(--urban-yellow);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Search Section */
        .search-section {
            background: var(--urban-gray);
            padding: 1.5rem 0;
            border-bottom: 2px solid var(--urban-blue);
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
            border: 2px solid var(--urban-blue);
            border-radius: 0;
            background: var(--urban-black);
            color: var(--urban-white);
            font-size: 1rem;
        }
        .search-box input::placeholder {
            color: var(--urban-light-gray);
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--urban-blue);
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--urban-white);
            text-decoration: none;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--urban-yellow);
            transform: translateY(-2px);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--urban-red);
            color: var(--urban-white);
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
            background: var(--urban-red); 
            padding: 1.5rem 0;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .category-nav a {
            color: var(--urban-white);
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 0.5rem 1rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            font-family: 'Oswald', sans-serif;
        }
        .category-nav a:hover {
            color: var(--urban-black);
            border-color: var(--urban-black);
            background: var(--urban-yellow);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--urban-black);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .category-nav a:hover::before {
            width: 80%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: var(--urban-red);
            border: 2px solid var(--urban-red);
            color: var(--urban-white);
            font-weight: 700;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 1rem 2rem;
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
            background: linear-gradient(90deg, transparent, rgba(255,255,0,0.3), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            background: var(--urban-yellow);
            border-color: var(--urban-yellow);
            color: var(--urban-black);
            box-shadow: 0 5px 20px rgba(255,0,0,0.3);
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer { 
            background: var(--urban-gray); 
            color: var(--urban-white);
            padding: 3rem 0 1rem;
            margin-top: 3rem;
            border-top: 3px solid var(--urban-red);
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .footer-section h3 {
            color: var(--urban-yellow);
            margin-bottom: 1rem;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid var(--urban-yellow);
            padding-bottom: 0.5rem;
        }
        .footer-section p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .footer-section a {
            color: var(--urban-white);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer-section a:hover {
            color: var(--urban-yellow);
            transform: translateX(5px);
        }
        .urban-culture {
            background: rgba(255,0,0,0.1);
            padding: 1rem;
            border-left: 3px solid var(--urban-red);
            margin-top: 1rem;
        }
        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .newsletter-form input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--urban-blue);
            background: var(--urban-black);
            color: var(--urban-white);
        }
        .newsletter-form input::placeholder {
            color: var(--urban-light-gray);
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--urban-blue);
            border: 1px solid var(--urban-blue);
            color: var(--urban-white);
            cursor: pointer;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .newsletter-form button:hover {
            background: var(--urban-yellow);
            border-color: var(--urban-yellow);
            color: var(--urban-black);
        }
        .footer-bottom {
            border-top: 1px solid var(--urban-red);
            padding-top: 1rem;
            text-align: center;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
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
            .main-header h1 {
                font-size: 2.5rem;
            }
            .search-container {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-container {
                gap: 1rem;
            }
            .category-nav a {
                font-size: 0.9rem;
                padding: 0.3rem 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="urban-info">
                <span><i class="fas fa-fire"></i> Limited Edition Drops</span>
                <span><i class="fas fa-shipping-fast"></i> Free Shipping on Orders Over $100</span>
            </div>
            <div class="urban-actions">
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
                <h1>Urban Street</h1>
                <div class="header-subtitle">Street Culture. Street Style.</div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for streetwear...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav">
        <div class="container">
            <div class="nav-container">
                <a href="/">Home</a>
                <a href="/clothing">Clothing</a>
                <a href="/sneakers">Sneakers</a>
                <a href="/accessories">Accessories</a>
                <a href="/caps">Caps & Hats</a>
                <a href="/street-art">Street Art</a>
                <a href="/limited">Limited Edition</a>
                <a href="/sale">Sale</a>
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
                    <h3>About Urban Street</h3>
                    <p>We represent the authentic voice of street culture. From urban fashion to street art, we bring you the latest trends and timeless classics that define the streets.</p>
                    <div class="urban-culture">
                        <strong>Street Culture:</strong> Where fashion meets attitude and style meets substance.
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Shop Categories</h3>
                    <a href="/clothing">Urban Clothing</a>
                    <a href="/sneakers">Sneakers & Kicks</a>
                    <a href="/accessories">Street Accessories</a>
                    <a href="/caps">Caps & Hats</a>
                    <a href="/street-art">Street Art Collection</a>
                </div>
                <div class="footer-section">
                    <h3>Street Culture</h3>
                    <a href="/events">Street Events</a>
                    <a href="/artists">Featured Artists</a>
                    <a href="/culture">Street Culture</a>
                    <a href="/community">Community</a>
                    <a href="/collaborations">Collaborations</a>
                </div>
                <div class="footer-section">
                    <h3>Stay Street</h3>
                    <p>Subscribe for the latest drops, street culture news, and exclusive releases.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Urban Street &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
