<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --tech-blue: #007ACC;
            --tech-cyan: #00D4FF;
            --tech-green: #00FF88;
            --tech-orange: #FF6B35;
            --tech-purple: #8A2BE2;
            --dark-bg: #0A0A0A;
            --darker-bg: #000000;
            --text-color: #FFFFFF;
            --border-color: #333333;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Rajdhani', sans-serif; 
            background: var(--dark-bg);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(138, 43, 226, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(0, 255, 136, 0.05) 0%, transparent 50%);
            z-index: -1;
        }
        
        /* Top Bar */
        .top-bar {
            background: var(--darker-bg);
            color: var(--tech-cyan);
            padding: 0.5rem 0;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--tech-blue);
        }
        .top-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tech-info span {
            margin-right: 2rem;
        }
        .tech-info i {
            color: var(--tech-green);
        }
        .tech-actions a {
            color: var(--tech-cyan);
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }
        .tech-actions a:hover {
            color: var(--tech-green);
        }
        
        /* Main Header */
        .main-header { 
            background: linear-gradient(135deg, var(--tech-blue), var(--tech-purple));
            color: white;
            padding: 2rem 0;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="circuit" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M0 10h20M10 0v20" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23circuit)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .main-header h1 { 
            font-family: 'Orbitron', monospace; 
            margin: 0;
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 0 0 20px var(--tech-cyan);
        }
        .header-subtitle {
            font-size: 1.2rem;
            margin-top: 0.5rem;
            color: var(--tech-green);
            font-weight: 600;
        }
        
        /* Search Section */
        .search-section {
            background: var(--darker-bg);
            padding: 1rem 0;
            border-bottom: 2px solid var(--tech-blue);
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
            border: 2px solid var(--tech-blue);
            border-radius: 0;
            background: var(--dark-bg);
            color: var(--text-color);
            font-size: 1rem;
        }
        .search-box input::placeholder {
            color: var(--tech-cyan);
            opacity: 0.7;
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--tech-blue);
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--tech-cyan);
            text-decoration: none;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--tech-green);
            transform: translateY(-2px);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--tech-orange);
            color: var(--text-color);
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
            background: var(--tech-blue); 
            padding: 1.5rem 0;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .category-nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.5rem 1rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        .category-nav a:hover {
            color: var(--tech-green);
            border-color: var(--tech-green);
            box-shadow: 0 0 15px var(--tech-green);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--tech-green);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .category-nav a:hover::before {
            width: 80%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: linear-gradient(135deg, var(--tech-blue), var(--tech-cyan));
            border: none;
            color: white;
            font-weight: 600;
            font-family: 'Rajdhani', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 2rem;
            border-radius: 5px;
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
            background: linear-gradient(135deg, var(--tech-green), var(--tech-cyan));
            box-shadow: 0 0 20px var(--tech-green);
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer { 
            background: var(--darker-bg); 
            color: var(--tech-cyan);
            padding: 3rem 0 1rem;
            margin-top: 3rem;
            border-top: 2px solid var(--tech-blue);
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .footer-section h3 {
            color: var(--tech-green);
            margin-bottom: 1rem;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid var(--tech-blue);
            padding-bottom: 0.5rem;
        }
        .footer-section p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .footer-section a {
            color: var(--tech-cyan);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .footer-section a:hover {
            color: var(--tech-green);
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
            border: 1px solid var(--tech-blue);
            border-radius: 5px;
            background: var(--dark-bg);
            color: var(--text-color);
        }
        .newsletter-form input::placeholder {
            color: var(--tech-cyan);
            opacity: 0.7;
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--tech-blue);
            border: 1px solid var(--tech-blue);
            color: var(--text-color);
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .newsletter-form button:hover {
            background: var(--tech-green);
            border-color: var(--tech-green);
        }
        .footer-bottom {
            border-top: 1px solid var(--tech-blue);
            padding-top: 1rem;
            text-align: center;
            font-family: 'Orbitron', monospace;
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
            <div class="tech-info">
                <span><i class="fas fa-wifi"></i> Free Shipping on Orders Over $99</span>
                <span><i class="fas fa-shield-alt"></i> 2-Year Warranty</span>
            </div>
            <div class="tech-actions">
                <a href="/account">My Account</a>
                <a href="/wishlist">Wishlist</a>
                <a href="/support">Support</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
        <div class="container">
            <div class="header-content">
                <h1>GadgetPro</h1>
                <div class="header-subtitle">Next-Gen Technology at Your Fingertips</div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for gadgets, tech, and more...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">8</span>
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
                <a href="/smartphones">Smartphones</a>
                <a href="/laptops">Laptops</a>
                <a href="/gaming">Gaming</a>
                <a href="/audio">Audio</a>
                <a href="/wearables">Wearables</a>
                <a href="/accessories">Accessories</a>
                <a href="/deals">Deals</a>
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
                    <h3>About GadgetPro</h3>
                    <p>Your premier destination for cutting-edge technology and innovative gadgets. We bring you the latest in tech with unbeatable prices and exceptional service.</p>
                </div>
                <div class="footer-section">
                    <h3>Shop Categories</h3>
                    <a href="/smartphones">Smartphones & Phones</a>
                    <a href="/laptops">Laptops & Computers</a>
                    <a href="/gaming">Gaming & VR</a>
                    <a href="/audio">Audio & Speakers</a>
                    <a href="/wearables">Wearables & Smartwatches</a>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <a href="/contact">Contact Support</a>
                    <a href="/shipping">Shipping Info</a>
                    <a href="/returns">Returns & Exchanges</a>
                    <a href="/warranty">Warranty</a>
                    <a href="/repairs">Repair Services</a>
                </div>
                <div class="footer-section">
                    <h3>Stay Updated</h3>
                    <p>Subscribe for the latest tech news and exclusive deals.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} GadgetPro &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html> 