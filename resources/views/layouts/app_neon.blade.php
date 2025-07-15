<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Exo+2:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --neon-pink: #ff00ff;
            --neon-cyan: #00ffff;
            --neon-yellow: #ffff00;
            --neon-green: #00ff00;
            --neon-purple: #8a2be2;
            --dark-bg: #0a0a0a;
            --darker-bg: #000000;
            --text-color: #ffffff;
            --grid-color: rgba(0, 255, 255, 0.1);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Exo 2', sans-serif; 
            background: var(--dark-bg);
            color: var(--text-color);
            line-height: 1.6;
            position: relative;
            overflow-x: hidden;
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
                linear-gradient(90deg, var(--grid-color) 1px, transparent 1px),
                linear-gradient(0deg, var(--grid-color) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            z-index: -1;
        }
        
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        /* Glow Effects */
        .glow {
            text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
        }
        
        /* Main Header */
        .main-header { 
            background: var(--darker-bg);
            color: var(--neon-pink);
            padding: 2rem 0;
            text-align: center;
            border-bottom: 3px solid var(--neon-pink);
            box-shadow: 0 0 30px var(--neon-pink);
            position: relative;
            overflow: hidden;
        }
        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 0, 255, 0.3), transparent);
            animation: scan 3s linear infinite;
        }
        @keyframes scan {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        .main-header h1 { 
            font-family: 'Orbitron', monospace; 
            margin: 0;
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            animation: neonPulse 2s ease-in-out infinite alternate;
        }
        @keyframes neonPulse {
            from { text-shadow: 0 0 10px var(--neon-pink), 0 0 20px var(--neon-pink), 0 0 30px var(--neon-pink); }
            to { text-shadow: 0 0 5px var(--neon-pink), 0 0 10px var(--neon-pink), 0 0 15px var(--neon-pink); }
        }
        
        /* Search Section */
        .search-section {
            background: var(--darker-bg);
            padding: 1rem 0;
            border-bottom: 2px solid var(--neon-cyan);
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
            border: 2px solid var(--neon-cyan);
            border-radius: 0;
            background: var(--dark-bg);
            color: var(--text-color);
            font-size: 1rem;
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--neon-cyan);
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--neon-cyan);
            text-decoration: none;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--neon-yellow);
            transform: translateY(-2px);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--neon-green);
            color: var(--darker-bg);
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
            background: var(--neon-pink); 
            padding: 1.5rem 0;
            text-align: center;
            box-shadow: 0 0 20px var(--neon-pink);
            position: relative;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .category-nav a {
            color: var(--darker-bg);
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Orbitron', monospace;
            padding: 0.5rem 1rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        .category-nav a:hover {
            color: var(--neon-yellow);
            border-color: var(--neon-yellow);
            box-shadow: 0 0 15px var(--neon-yellow);
            transform: translateY(-3px);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--neon-yellow);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        .category-nav a:hover::before {
            opacity: 0.2;
        }
        
        /* Buttons */
        .btn-primary { 
            background: var(--neon-cyan); 
            border-color: var(--neon-cyan);
            color: var(--darker-bg);
            font-weight: 700;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            background: var(--neon-yellow);
            border-color: var(--neon-yellow);
            box-shadow: 0 0 20px var(--neon-yellow);
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer { 
            background: var(--darker-bg); 
            color: var(--neon-green);
            text-align: center;
            padding: 3rem 0 1rem;
            margin-top: 3rem;
            border-top: 3px solid var(--neon-green);
            box-shadow: 0 0 30px var(--neon-green);
            position: relative;
        }
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neon-green), transparent);
            animation: scan 2s linear infinite;
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .footer-section h3 {
            color: var(--neon-cyan);
            margin-bottom: 1rem;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .footer-section p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .footer-section a {
            color: var(--text-color);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .footer-section a:hover {
            color: var(--neon-cyan);
            text-shadow: 0 0 5px var(--neon-cyan);
        }
        .footer-bottom {
            border-top: 1px solid var(--neon-green);
            padding-top: 1rem;
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
    <!-- Main Header -->
    <div class="main-header">
        <div class="container">
            <h1 class="glow">VAULT64 NEON</h1>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for products...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">3</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav">
        <div class="container">
            <div class="nav-container">
                <a href="/">HOME</a>
                <a href="/products">PRODUCTS</a>
                <a href="/new">NEW ARRIVALS</a>
                <a href="/featured">FEATURED</a>
                <a href="/sale">SALE</a>
                <a href="/about">ABOUT</a>
                <a href="/contact">CONTACT</a>
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
                    <h3>System Status</h3>
                    <p>All systems operational. Welcome to the future of shopping.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Access</h3>
                    <a href="/products">Browse Products</a>
                    <a href="/new">New Arrivals</a>
                    <a href="/featured">Featured Items</a>
                    <a href="/sale">Sale Items</a>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <a href="/contact">Contact Support</a>
                    <a href="/shipping">Shipping Info</a>
                    <a href="/returns">Returns</a>
                    <a href="/faq">FAQ</a>
                </div>
                <div class="footer-section">
                    <h3>Connect</h3>
                    <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                    <a href="#"><i class="fab fa-discord"></i> Discord</a>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} VAULT64 NEON &mdash; FUTURE OF SHOPPING
            </div>
        </div>
    </div>
</body>
</html> 