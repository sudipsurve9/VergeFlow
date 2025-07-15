<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vault64') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --furniture-primary: #8B4513;
            --furniture-secondary: #D2691E;
            --furniture-accent: #CD853F;
            --furniture-light: #F5F5DC;
            --furniture-dark: #654321;
            --text-color: #2F2F2F;
            --bg-color: #FFFFFF;
            --border-color: #DEB887;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Source Sans Pro', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Top Bar */
        .top-bar {
            background: var(--furniture-dark);
            color: var(--furniture-light);
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }
        .top-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .contact-info span {
            margin-right: 2rem;
        }
        .contact-info i {
            color: var(--furniture-accent);
        }
        .top-actions a {
            color: var(--furniture-light);
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }
        .top-actions a:hover {
            color: var(--furniture-accent);
        }
        
        /* Main Header */
        .main-header { 
            background: var(--furniture-light);
            color: var(--furniture-primary);
            padding: 2rem 0;
            border-bottom: 3px solid var(--furniture-primary);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo h1 { 
            font-family: 'Merriweather', serif; 
            margin: 0;
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--furniture-primary);
        }
        .logo-subtitle {
            font-style: italic;
            color: var(--furniture-secondary);
            margin-top: 0.25rem;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .search-container {
            position: relative;
        }
        .search-container input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--furniture-primary);
            border-radius: 25px;
            width: 300px;
            font-size: 1rem;
            background: white;
        }
        .search-container button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--furniture-primary);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .search-container button:hover {
            background: var(--furniture-secondary);
        }
        .user-actions {
            display: flex;
            gap: 1.5rem;
        }
        .user-actions a {
            color: var(--furniture-primary);
            text-decoration: none;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }
        .user-actions a:hover {
            color: var(--furniture-secondary);
        }
        .cart-icon {
            position: relative;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--furniture-secondary);
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
            background: var(--furniture-primary); 
            padding: 1.5rem 0;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
        }
        .category-nav a {
            color: var(--furniture-light);
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
            color: var(--furniture-accent);
            border-color: var(--furniture-accent);
            background: rgba(255,255,255,0.1);
        }
        .category-nav a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--furniture-accent);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .category-nav a:hover::before {
            width: 80%;
        }
        
        /* Buttons */
        .btn-primary { 
            background: var(--furniture-primary); 
            border-color: var(--furniture-primary);
            color: white;
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: var(--furniture-secondary);
            border-color: var(--furniture-secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        /* Footer */
        .footer { 
            background: var(--furniture-dark); 
            color: var(--furniture-light);
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
            color: var(--furniture-accent);
            margin-bottom: 1rem;
            font-family: 'Merriweather', serif;
            font-size: 1.3rem;
            border-bottom: 2px solid var(--furniture-accent);
            padding-bottom: 0.5rem;
        }
        .footer-section p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .footer-section a {
            color: var(--furniture-light);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .footer-section a:hover {
            color: var(--furniture-accent);
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
            border: 1px solid var(--furniture-accent);
            border-radius: 5px;
            background: transparent;
            color: var(--furniture-light);
        }
        .newsletter-form input::placeholder {
            color: var(--furniture-light);
            opacity: 0.7;
        }
        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--furniture-accent);
            border: 1px solid var(--furniture-accent);
            color: var(--furniture-dark);
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .newsletter-form button:hover {
            background: var(--furniture-secondary);
            border-color: var(--furniture-secondary);
        }
        .footer-bottom {
            border-top: 1px solid var(--furniture-accent);
            padding-top: 1rem;
            text-align: center;
            font-family: 'Merriweather', serif;
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
            .search-container input {
                width: 250px;
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
            <div class="contact-info">
                <span><i class="fas fa-phone"></i> +1 (555) 123-4567</span>
                <span><i class="fas fa-envelope"></i> info@vault64furniture.com</span>
            </div>
            <div class="top-actions">
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
                <div class="logo">
                    <h1>Vault64 Furniture</h1>
                    <div class="logo-subtitle">Crafting Comfort, Creating Homes</div>
                </div>
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" placeholder="Search for furniture...">
                        <button><i class="fas fa-search"></i></button>
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
    </div>

    <!-- Category Navigation -->
    <div class="category-nav">
        <div class="container">
            <div class="nav-container">
                <a href="/">Home</a>
                <a href="/living-room">Living Room</a>
                <a href="/bedroom">Bedroom</a>
                <a href="/dining">Dining</a>
                <a href="/office">Office</a>
                <a href="/outdoor">Outdoor</a>
                <a href="/decor">Decor</a>
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
                    <h3>About Vault64</h3>
                    <p>We specialize in creating beautiful, functional furniture that transforms your space into a home. Our pieces are crafted with care and designed for comfort.</p>
                </div>
                <div class="footer-section">
                    <h3>Shop by Room</h3>
                    <a href="/living-room">Living Room Furniture</a>
                    <a href="/bedroom">Bedroom Sets</a>
                    <a href="/dining">Dining Room</a>
                    <a href="/office">Office Furniture</a>
                    <a href="/outdoor">Outdoor Living</a>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <a href="/contact">Contact Us</a>
                    <a href="/shipping">Shipping & Delivery</a>
                    <a href="/returns">Returns & Exchanges</a>
                    <a href="/warranty">Warranty</a>
                    <a href="/assembly">Assembly Services</a>
                </div>
                <div class="footer-section">
                    <h3>Stay Connected</h3>
                    <p>Subscribe to our newsletter for design tips and exclusive offers.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Vault64 Furniture &mdash; All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
