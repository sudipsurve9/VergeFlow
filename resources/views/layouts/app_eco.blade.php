<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600;700&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --eco-green: #2E8B57;
            --eco-light-green: #90EE90;
            --eco-dark-green: #006400;
            --eco-brown: #8B4513;
            --eco-beige: #F5F5DC;
            --eco-cream: #FFF8DC;
            --text-color: #2F2F2F;
            --bg-color: #FFFFFF;
            --border-color: #90EE90;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Nature Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(46, 139, 87, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(144, 238, 144, 0.05) 0%, transparent 50%);
            z-index: -1;
        }
        
        /* Top Bar */
        .top-bar {
            background: var(--eco-green);
            color: white;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }
        .top-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .eco-info span {
            margin-right: 2rem;
        }
        .eco-info i {
            color: var(--eco-light-green);
        }
        .eco-actions a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }
        .eco-actions a:hover {
            color: var(--eco-light-green);
        }
        
        /* Main Header */
        .main-header { 
            background: linear-gradient(135deg, var(--eco-green), var(--eco-light-green));
            color: white;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="leaves" x="0" y="0" width="30" height="30" patternUnits="userSpaceOnUse"><path d="M15 5 Q20 10 15 15 Q10 10 15 5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23leaves)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .main-header h1 { 
            font-family: 'Montserrat', sans-serif; 
            margin: 0;
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header-subtitle {
            font-size: 1.2rem;
            margin-top: 0.5rem;
            opacity: 0.9;
        }
        .eco-badges {
            margin-top: 1rem;
        }
        .eco-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin: 0 0.5rem;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }
        
        /* Search Section */
        .search-section {
            background: var(--eco-cream);
            padding: 1.5rem 0;
            border-bottom: 2px solid var(--eco-light-green);
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
            border: 2px solid var(--eco-green);
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
            color: var(--eco-green);
            font-size: 1.2rem;
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--eco-green);
            text-decoration: none;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--eco-dark-green);
            transform: translateY(-2px);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--eco-brown);
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
            background: var(--eco-dark-green); 
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
            color: var(--eco-light-green);
            border-color: var(--eco-light-green);
            background: rgba(255,255,255,0.1);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--eco-light-green);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .category-nav a:hover::before {
            width: 80%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: linear-gradient(135deg, var(--eco-green), var(--eco-light-green));
            border: none;
            color: white;
            font-weight: 600;
            font-family: 'Quicksand', sans-serif;
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
            background: linear-gradient(135deg, var(--eco-dark-green), var(--eco-green));
            box-shadow: 0 5px 20px rgba(46, 139, 87, 0.3);
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer { 
            background: var(--eco-dark-green); 
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
            color: var(--eco-light-green);
            margin-bottom: 1rem;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
            border-bottom: 2px solid var(--eco-light-green);
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
            color: var(--eco-light-green);
            transform: translateX(5px);
        }
        .sustainability-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--eco-light-green);
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .newsletter-form input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--eco-light-green);
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
            background: var(--eco-light-green);
            border: 1px solid var(--eco-light-green);
            color: var(--eco-dark-green);
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
            border-top: 1px solid var(--eco-light-green);
            padding-top: 1rem;
            text-align: center;
            font-family: 'Montserrat', sans-serif;
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
            .sustainability-stats {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="eco-info">
                <span><i class="fas fa-leaf"></i> 100% Eco-Friendly Products</span>
                <span><i class="fas fa-shipping-fast"></i> Carbon-Neutral Shipping</span>
            </div>
            <div class="eco-actions">
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
                <h1>EcoMarket</h1>
                <div class="header-subtitle">Sustainable Living Starts Here</div>
                <div class="eco-badges">
                    <span class="eco-badge"><i class="fas fa-recycle"></i> Recycled Materials</span>
                    <span class="eco-badge"><i class="fas fa-seedling"></i> Organic</span>
                    <span class="eco-badge"><i class="fas fa-hand-holding-heart"></i> Fair Trade</span>
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
                    <input type="text" placeholder="Search for eco-friendly products...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">4</span>
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
                <a href="/organic">Organic</a>
                <a href="/recycled">Recycled</a>
                <a href="/natural">Natural</a>
                <a href="/zero-waste">Zero Waste</a>
                <a href="/fair-trade">Fair Trade</a>
                <a href="/local">Local</a>
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
                    <h3>About EcoMarket</h3>
                    <p>We're committed to providing sustainable, eco-friendly products that help you live a greener lifestyle while supporting ethical practices and environmental conservation.</p>
                    <div class="sustainability-stats">
                        <div class="stat-item">
                            <div class="stat-number">15K+</div>
                            <div class="stat-label">Trees Planted</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Carbon Neutral</div>
                        </div>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Shop Categories</h3>
                    <a href="/organic">Organic Products</a>
                    <a href="/recycled">Recycled Materials</a>
                    <a href="/natural">Natural & Chemical-Free</a>
                    <a href="/zero-waste">Zero Waste Solutions</a>
                    <a href="/fair-trade">Fair Trade Items</a>
                </div>
                <div class="footer-section">
                    <h3>Eco Initiatives</h3>
                    <a href="/sustainability">Our Sustainability</a>
                    <a href="/carbon-offset">Carbon Offset Program</a>
                    <a href="/recycling">Recycling Program</a>
                    <a href="/partners">Eco Partners</a>
                    <a href="/education">Environmental Education</a>
                </div>
                <div class="footer-section">
                    <h3>Stay Green</h3>
                    <p>Subscribe for eco-living tips and sustainable product updates.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} EcoMarket &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html> 