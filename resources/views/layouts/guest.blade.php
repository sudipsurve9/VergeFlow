<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VergeFlow') }} - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: radial-gradient(ellipse at 50% 20%, #ff9900 0%, #2d1a06 80%, #18120a 100%);
            min-height: 100vh;
            color: #fff;
            transition: all 0.3s ease;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .login-logo {
            margin-bottom: 2rem;
        }
        .login-card {
            background: #232323;
            border: 1px solid #333;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 6px 32px rgba(0,0,0,0.13);
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            font-family: 'Orbitron', sans-serif;
            color: #ffb300;
            text-align: center;
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px #ff6a00;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="/" class="login-logo">
            <img src="{{ asset('logo.png') }}" alt="Vault64 Logo" style="width:180px;height:auto;max-width:100%;object-fit:contain;">
        </a>
        <div class="login-card">
            <div class="login-title">Sign in to VergeFlow</div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
