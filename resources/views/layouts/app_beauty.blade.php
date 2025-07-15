<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vault64') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --beauty-pink: #FF69B4;
            --beauty-rose: #FF1493;
            --beauty-purple: #9370DB;
            --beauty-lavender: #E6E6FA;
            --beauty-peach: #FFDAB9;
            --beauty-gold: #FFD700;
            --text-color: #2F2F2F;
            --bg-color: #FFFFFF;
            --border-color: #FFB6C1;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Elegant Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 105, 180, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(147, 112, 219, 0.05) 0%, transparent 50%);
            z-index: -1;
        }
        
        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, var(--beauty-pink), var(--beauty-purple));
            color: white;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }
        .top-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .beauty-info span {
            margin-right: 2rem;
        }
        .beauty-info i {
            color: var(--beauty-gold);
        }
        .beauty-actions a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }
        .beauty-actions a:hover {
            color: var(--beauty-gold);
        }
        
        /* Main Header */
        .main-header { 
            background: linear-gradient(135deg, var(--beauty-lavender), var(--beauty-peach));
            color: var(--text-color);
            padding: 2.5rem 0;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="flowers" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="3" fill="rgba(255,105,180,0.1)"/><circle cx="20" cy="20" r="1" fill="rgba(147,112,219,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23flowers)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .main-header h1 { 
            font-family: 'Dancing Script', cursive; 
            margin: 0;
            font-size: 4rem;
            font-weight: 700;
            color: var(--beauty-rose);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .header-subtitle {
            font-size: 1.2rem;
            margin-top: 0.5rem;
            color: var(--beauty-purple);
            font-weight: 600;
        }
        .beauty-badges {
            margin-top: 1rem;
        }
        .beauty-badge {
            display: inline-block;
            background: rgba(255,255,255,0.8);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin: 0 0.5rem;
            font-size: 0.9rem;
            color: var(--beauty-rose);
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        /* Search Section */
        .search-section {
            background: white;
            padding: 1.5rem 0;
            border-bottom: 2px solid var(--beauty-lavender);
            box-shadow: 0 2px 10px rgba(255,105,180,0.1);
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
            border: 2px solid var(--beauty-pink);
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
            color: var(--beauty-pink);
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--beauty-pink);
            text-decoration: none;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--beauty-rose);
            transform: translateY(-2px);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--beauty-purple);
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
            background: linear-gradient(135deg, var(--beauty-pink), var(--beauty-rose)); 
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
            color: var(--beauty-gold);
            border-color: var(--beauty-gold);
            background: rgba(255,255,255,0.1);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--beauty-gold);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .category-nav a:hover::before {
            width: 80%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: linear-gradient(135deg, var(--beauty-pink), var(--beauty-rose));
            border: none;
            color: white;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 2rem;
            border-radius: 25px;
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
            background: linear-gradient(135deg, var(--beauty-rose), var(--beauty-purple));
            box-shadow: 0 5px 20px rgba(255,105,180,0.3);
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer { 
            background: linear-gradient(135deg, var(--beauty-purple), var(--beauty-pink)); 
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
            color: var(--beauty-gold);
            margin-bottom: 1rem;
            font-family: 'Dancing Script', cursive;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--beauty-gold);
            padding-bottom: 0.5rem;
        }
        .footer-section p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .footer-section a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .footer-section a:hover {
            color: var(--beauty-gold);
            transform: translateX(5px);
        }
        .beauty-tips {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 10px;
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
            border: 1px solid var(--beauty-gold);
            border-radius: 25px;
            background: transparent;
            color: white;
        }
        .newsletter-form input::placeholder {
            color: white;
            opacity: 0.7;
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--beauty-gold);
            border: 1px solid var(--beauty-gold);
            color: var(--beauty-purple);
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .newsletter-form button:hover {
            background: white;
            border-color: white;
        }
        .footer-bottom {
            border-top: 1px solid var(--beauty-gold);
            padding-top: 1rem;
            text-align: center;
            font-family: 'Dancing Script', cursive;
            font-size: 1.1rem;
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
            <div class="beauty-info">
                <span><i class="fas fa-star"></i> Free Samples with Every Order</span>
                <span><i class="fas fa-shipping-fast"></i> Free Shipping Over $50</span>
            </div>
            <div class="beauty-actions">
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
                <h1>Beauty Bliss</h1>
                <div class="header-subtitle">Discover Your Natural Radiance</div>
                <div class="beauty-badges">
                    <span class="beauty-badge"><i class="fas fa-leaf"></i> Natural</span>
                    <span class="beauty-badge"><i class="fas fa-heart"></i> Cruelty-Free</span>
                    <span class="beauty-badge"><i class="fas fa-certificate"></i> Organic</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for beauty products...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">6</span>
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
                <a href="/skincare">Skincare</a>
                <a href="/makeup">Makeup</a>
                <a href="/haircare">Hair Care</a>
                <a href="/fragrances">Fragrances</a>
                <a href="/tools">Beauty Tools</a>
                <a href="/bath">Bath & Body</a>
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
                    <h3>About Beauty Bliss</h3>
                    <p>We believe everyone deserves to feel beautiful and confident. Our curated collection of premium beauty products helps you discover your natural radiance.</p>
                    <div class="beauty-tips">
                        <strong>Beauty Tip:</strong> Always remove makeup before bed and moisturize for healthy, glowing skin!
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Shop Categories</h3>
                    <a href="/skincare">Skincare Essentials</a>
                    <a href="/makeup">Makeup Collection</a>
                    <a href="/haircare">Hair Care Products</a>
                    <a href="/fragrances">Perfumes & Fragrances</a>
                    <a href="/tools">Beauty Tools & Brushes</a>
                </div>
                <div class="footer-section">
                    <h3>Beauty Services</h3>
                    <a href="/consultation">Beauty Consultation</a>
                    <a href="/tutorials">Beauty Tutorials</a>
                    <a href="/reviews">Product Reviews</a>
                    <a href="/samples">Free Samples</a>
                    <a href="/loyalty">Loyalty Program</a>
                </div>
                <div class="footer-section">
                    <h3>Stay Beautiful</h3>
                    <p>Subscribe for beauty tips, new product alerts, and exclusive offers.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Beauty Bliss &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
