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
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            color: #ffb300;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #ff9900;
            box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff9900 0%, #ff6a00 100%);
            border: none;
            color: #fff;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
        }
        .form-link {
            color: #ffb300;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .form-link:hover {
            color: #ff9900;
            text-decoration: underline;
        }
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        .error-message {
            color: #ff4444;
            font-size: 0.85rem;
            margin-top: 0.25rem;
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
