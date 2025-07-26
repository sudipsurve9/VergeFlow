<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="login-box">
        <div class="login-title">Sign in to<br>VergeFlow</div>
    <form method="POST" action="{{ route('login') }}">
        @csrf
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label for="email" style="display:block; font-weight:600; margin-bottom:0.3rem;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus style="width:100%; padding:0.7rem; border-radius:6px; border:none; font-size:1.1rem;">
                @error('email')
                    <div style="color:#ffb300; margin-top:0.2rem; font-size:0.95rem;">{{ $message }}</div>
                @enderror
        </div>
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label for="password" style="display:block; font-weight:600; margin-bottom:0.3rem;">Password</label>
                <input id="password" type="password" name="password" required style="width:100%; padding:0.7rem; border-radius:6px; border:none; font-size:1.1rem;">
                @error('password')
                    <div style="color:#ffb300; margin-top:0.2rem; font-size:0.95rem;">{{ $message }}</div>
                @enderror
        </div>
            <div class="form-group" style="display:flex; align-items:center; margin-bottom:1.2rem;">
                <input type="checkbox" name="remember" id="remember" style="margin-right:0.5rem;">
                <label for="remember" style="margin-bottom:0;">Remember me</label>
        </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <a href="{{ route('password.request') }}" style="color:#4fc3f7; text-decoration:underline; font-size:0.98rem;">Forgot your password?</a>
                <button type="submit" class="login-btn">Log in</button>
        </div>
    </form>
    </div>
    <style>
        .login-box {
            background: #222;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.45);
            padding: 2.5rem 2rem;
            max-width: 400px;
            margin: 2.5rem auto;
        }
        .login-title {
            font-family: 'Orbitron', sans-serif;
            color: #ffb300;
            text-shadow: 0 0 8px #ffb300, 0 0 16px #fff200;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2.1rem;
            font-weight: 700;
        }
        .login-box input[type="email"],
        .login-box input[type="password"] {
            background: #333;
            color: #fff;
            border: 1px solid #444;
        }
        .login-box input[type="email"]:focus,
        .login-box input[type="password"]:focus {
            outline: 2px solid #ffb300;
            background: #222;
        }
        .login-box button[type="submit"]:hover {
            background: linear-gradient(90deg, #ff6f00, #ffb300);
        }
        .login-btn {
            background: linear-gradient(90deg, #ffb300, #ff6f00);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.45rem 1.7rem;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(255, 111, 0, 0.15);
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            min-height: 38px;
            margin-left: 1rem;
        }
        .login-btn:hover, .login-btn:focus {
            background: linear-gradient(90deg, #ff6f00, #ffb300);
            box-shadow: 0 4px 16px rgba(255, 111, 0, 0.25);
            transform: translateY(-2px) scale(1.03);
            outline: none;
        }
    </style>
</x-guest-layout>
