<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One:wght@400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --kids-blue: #4ECDC4;
            --kids-pink: #FF6B9D;
            --kids-yellow: #FFE66D;
            --kids-orange: #FF8C42;
            --kids-purple: #A8E6CF;
            --kids-red: #FF6B6B;
            --text-color: #2F2F2F;
            --bg-color: #FFFFFF;
            --border-color: #E0E0E0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Nunito', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Playful Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(78, 205, 196, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 107, 157, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 230, 109, 0.1) 0%, transparent 50%);
            z-index: -1;
        }
        
        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, var(--kids-blue), var(--kids-purple));
            color: white;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }
        .top-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .kids-info span {
            margin-right: 2rem;
        }
        .kids-info i {
            color: var(--kids-yellow);
        }
        .kids-actions a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }
        .kids-actions a:hover {
            color: var(--kids-yellow);
        }
        
        /* Main Header */
        .main-header { 
            background: linear-gradient(135deg, var(--kids-pink), var(--kids-orange));
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="bubbles" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="8" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23bubbles)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .main-header h1 { 
            font-family: 'Fredoka One', cursive; 
            margin: 0;
            font-size: 4rem;
            text-shadow: 3px 3px 0 var(--kids-blue), 6px 6px 0 var(--kids-yellow);
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        .header-subtitle {
            font-size: 1.3rem;
            margin-top: 0.5rem;
            color: var(--kids-yellow);
            font-weight: 700;
        }
        .kids-badges {
            margin-top: 1rem;
        }
        .kids-badge {
            display: inline-block;
            background: rgba(255,255,255,0.9);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            margin: 0 0.5rem;
            font-size: 0.9rem;
            color: var(--kids-pink);
            font-weight: 700;
            backdrop-filter: blur(10px);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Search Section */
        .search-section {
            background: var(--kids-purple);
            padding: 1.5rem 0;
            border-bottom: 3px solid var(--kids-blue);
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
            border: 3px solid var(--kids-blue);
            border-radius: 25px;
            background: white;
            color: var(--text-color);
            font-size: 1rem;
            font-family: 'Nunito', sans-serif;
        }
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--kids-blue);
            font-size: 1.2rem;
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--kids-blue);
            text-decoration: none;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--kids-pink);
            transform: translateY(-3px) scale(1.1);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--kids-red);
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
            animation: bounce 1s infinite;
        }
        
        /* Navigation */
        .category-nav { 
            background: var(--kids-blue); 
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
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.75rem 1.5rem;
            border: 3px solid transparent;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            font-family: 'Fredoka One', cursive;
        }
        .category-nav a:hover {
            color: var(--kids-blue);
            border-color: white;
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--kids-yellow);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }
        .category-nav a:hover::before {
            width: 80%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: linear-gradient(135deg, var(--kids-pink), var(--kids-orange));
            border: none;
            color: white;
            font-weight: 700;
            font-family: 'Fredoka One', cursive;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 2rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(255, 107, 157, 0.3);
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--kids-orange), var(--kids-yellow));
            box-shadow: 0 8px 25px rgba(255, 140, 66, 0.4);
            transform: translateY(-3px);
        }
        
        /* Footer */
        .footer { 
            background: linear-gradient(135deg, var(--kids-purple), var(--kids-blue)); 
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
            color: var(--kids-yellow);
            margin-bottom: 1rem;
            font-family: 'Fredoka One', cursive;
            font-size: 1.5rem;
            border-bottom: 3px solid var(--kids-yellow);
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
            font-weight: 600;
        }
        .footer-section a:hover {
            color: var(--kids-yellow);
            transform: translateX(5px);
        }
        .fun-fact {
            background: rgba(255,255,255,0.2);
            padding: 1rem;
            border-radius: 15px;
            margin-top: 1rem;
            border-left: 4px solid var(--kids-yellow);
        }
        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .newsletter-form input {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid var(--kids-yellow);
            border-radius: 25px;
            background: white;
            color: var(--text-color);
            font-family: 'Nunito', sans-serif;
        }
        .newsletter-form input::placeholder {
            color: var(--kids-blue);
            opacity: 0.7;
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--kids-yellow);
            border: 2px solid var(--kids-yellow);
            color: var(--kids-blue);
            border-radius: 25px;
            cursor: pointer;
            font-weight: 700;
            font-family: 'Fredoka One', cursive;
            transition: all 0.3s ease;
        }
        .newsletter-form button:hover {
            background: white;
            border-color: white;
            transform: translateY(-2px);
        }
        .footer-bottom {
            border-top: 2px solid var(--kids-yellow);
            padding-top: 1rem;
            text-align: center;
            font-family: 'Fredoka One', cursive;
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
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="kids-info">
                <span><i class="fas fa-star"></i> Free Gift with Every Order!</span>
                <span><i class="fas fa-shipping-fast"></i> Fast & Fun Shipping</span>
            </div>
            <div class="kids-actions">
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
                <h1>Kids World</h1>
                <div class="header-subtitle">Where Fun Meets Imagination!</div>
                <div class="kids-badges">
                    <span class="kids-badge"><i class="fas fa-heart"></i> Safe for Kids</span>
                    <span class="kids-badge"><i class="fas fa-smile"></i> Educational</span>
                    <span class="kids-badge"><i class="fas fa-gift"></i> Fun Toys</span>
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
                    <input type="text" placeholder="Search for fun toys and games...">
                </div>
                <div class="user-actions">
                    <a href="/account"><i class="fas fa-user"></i></a>
                    <a href="/wishlist"><i class="fas fa-heart"></i></a>
                    <div class="cart-icon">
                        <a href="/cart"><i class="fas fa-shopping-cart"></i></a>
                        <span class="cart-badge">5</span>
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
                <a href="/toys">Toys</a>
                <a href="/games">Games</a>
                <a href="/books">Books</a>
                <a href="/clothing">Clothing</a>
                <a href="/educational">Educational</a>
                <a href="/arts">Arts & Crafts</a>
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
                    <h3>About Kids World</h3>
                    <p>We believe every child deserves to have fun while learning! Our collection of toys, games, and educational materials is designed to spark imagination and creativity.</p>
                    <div class="fun-fact">
                        <strong>Fun Fact:</strong> Playing with toys helps develop problem-solving skills and creativity in children!
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Shop Categories</h3>
                    <a href="/toys">Fun Toys</a>
                    <a href="/games">Board Games</a>
                    <a href="/books">Children's Books</a>
                    <a href="/clothing">Kids Fashion</a>
                    <a href="/educational">Learning Toys</a>
                </div>
                <div class="footer-section">
                    <h3>For Parents</h3>
                    <a href="/safety">Safety Information</a>
                    <a href="/age-groups">Age Groups</a>
                    <a href="/educational-tips">Learning Tips</a>
                    <a href="/reviews">Parent Reviews</a>
                    <a href="/gift-guides">Gift Guides</a>
                </div>
                <div class="footer-section">
                    <h3>Stay Fun</h3>
                    <p>Subscribe for fun activities, new toy alerts, and special offers for kids!</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter parent's email">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Kids World &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
