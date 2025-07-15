<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'VergeFlow') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: 'Georgia', serif; background: #f5f5f5; color: #333; }
        .classic-header { background: #eaeaea; border-bottom: 2px solid #bfa76a; padding: 1.5rem 0; text-align: center; }
        .classic-header h1 { font-family: 'Georgia', serif; color: #bfa76a; font-size: 2.5rem; margin: 0; }
        .classic-footer { background: #eaeaea; border-top: 2px solid #bfa76a; text-align: center; padding: 1rem 0; color: #888; margin-top: 2rem; }
        .classic-nav { background: #bfa76a; padding: 0.5rem 0; text-align: center; }
        .classic-nav a { color: #fff; margin: 0 1rem; text-decoration: none; font-weight: bold; }
        .classic-nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="classic-header">
        <h1>VergeFlow Classic</h1>
    </div>
    <div class="classic-nav">
        <a href="/">Home</a>
        <a href="/products">All Products</a>
        <a href="/profile">Profile</a>
    </div>
    <main class="container py-4">
        @yield('content')
    </main>
    <div class="classic-footer">
        &copy; {{ date('Y') }} VergeFlow &mdash; Classic Theme
    </div>
</body>
</html> 