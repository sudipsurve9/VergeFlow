<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - @yield('title', 'Home')</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Source+Code+Pro:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <style>
        :root {
            --gadget-primary: #00d4ff;
            --gadget-secondary: #ff6b35;
            --gadget-accent: #7c3aed;
            --gadget-dark: #0f172a;
            --gadget-darker: #020617;
            --gadget-light: #1e293b;
            --text-color: #f1f5f9;
            --bg-color: #0f172a;
        }
        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        .main-header { 
            background: var(--gadget-darker);
            color: var(--gadget-primary);
            padding: 1.5rem 0;
            text-align: center;
            border-bottom: 2px solid var(--gadget-primary);
        }
        .main-header h1 { 
            font-family: 'Source Code Pro', monospace; 
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 0 0 10px var(--gadget-primary);
        }
        .category-nav { 
            background: var(--gadget-light); 
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid var(--gadget-primary);
        }
        .category-nav a {
            color: var(--text-color);
            text-decoration: none;
            margin: 0 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .category-nav a:hover {
            color: var(--gadget-primary);
            text-shadow: 0 0 5px var(--gadget-primary);
        }
        .btn-primary { 
            background: var(--gadget-primary); 
            border-color: var(--gadget-primary);
            color: var(--gadget-darker);
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--gadget-secondary);
            border-color: var(--gadget-secondary);
            box-shadow: 0 0 15px var(--gadget-secondary);
        }
        .footer { 
            background: var(--gadget-darker); 
            color: var(--gadget-primary);
            text-align: center;
            padding: 2rem 0;
            margin-top: 2rem;
            border-top: 2px solid var(--gadget-primary);
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
            <h1>VERGEFLOW GADGETPRO</h1>
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
            &copy; {{ date('Y') }} VergeFlow &mdash; GadgetPro Theme
        </div>
    </div>
</body>
</html>
