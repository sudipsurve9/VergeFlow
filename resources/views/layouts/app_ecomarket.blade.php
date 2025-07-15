<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --eco-primary: #2d5a27;
            --eco-secondary: #4a7c59;
            --eco-accent: #7fb069;
            --eco-light: #a7c957;
            --eco-bright: #b8e6b8;
            --eco-dark: #1b4332;
            --text-color: #2d5a27;
            --bg-color: #f8faf8;
        }
        body { 
            font-family: 'Open Sans', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        .main-header { 
            background: linear-gradient(135deg, var(--eco-primary), var(--eco-secondary));
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            pointer-events: none;
        }
        .main-header h1 { 
            font-family: 'Quicksand', sans-serif; 
            margin: 0;
            font-size: 2.8rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .category-nav { 
            background: var(--eco-accent); 
            padding: 1rem 0;
            text-align: center;
        }
        .category-nav a {
            color: white;
            text-decoration: none;
            margin: 0 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .category-nav a:hover {
            color: var(--eco-bright);
            transform: translateY(-2px);
        }
        .btn-primary { 
            background: var(--eco-primary); 
            border-color: var(--eco-primary);
            color: white;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--eco-secondary);
            border-color: var(--eco-secondary);
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.3);
        }
        .footer { 
            background: var(--eco-dark); 
            color: var(--eco-bright);
            text-align: center;
            padding: 2rem 0;
            margin-top: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .py-4 {
            padding: 2rem 0;
        }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <h1>VergeFlow EcoMarket</h1>
        </div>
    </div>
    <div class="category-nav">
        <div class="container">
            <a href="/">Home</a>
            <a href="/products">Products</a>
            <a href="/profile">Profile</a>
            <a href="/cart">Cart</a>
        </div>
    </div>
    <main class="container py-4">
        @yield('content')
    </main>
    <div class="footer">
        <div class="container">
            &copy; {{ date('Y') }} VergeFlow &mdash; EcoMarket Theme
        </div>
    </div>
</body>
</html>
