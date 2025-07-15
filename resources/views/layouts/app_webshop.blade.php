<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --webshop-primary: #3498db;
            --webshop-secondary: #2ecc71;
            --webshop-accent: #e67e22;
            --webshop-light: #f8f9fa;
            --webshop-dark: #2c3e50;
            --text-color: #2c3e50;
            --bg-color: #ffffff;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        .main-header { 
            background: linear-gradient(135deg, var(--webshop-primary), var(--webshop-secondary));
            color: white;
            padding: 1.5rem 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(52, 152, 219, 0.2);
        }
        .main-header h1 { 
            font-family: 'Poppins', sans-serif; 
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .category-nav { 
            background: var(--webshop-dark); 
            padding: 1rem 0;
            text-align: center;
        }
        .category-nav a {
            color: white;
            text-decoration: none;
            margin: 0 1.5rem;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .category-nav a:hover {
            color: var(--webshop-accent);
        }
        .btn-primary { 
            background: var(--webshop-primary); 
            border-color: var(--webshop-primary);
            color: white;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--webshop-secondary);
            border-color: var(--webshop-secondary);
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }
        .footer { 
            background: var(--webshop-dark); 
            color: white;
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
            <h1>Vault64 WebShop</h1>
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
            &copy; {{ date('Y') }} Vault64 &mdash; WebShop Theme
        </div>
    </div>
</body>
</html>
