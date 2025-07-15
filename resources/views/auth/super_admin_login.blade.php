<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login - Vault64</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #f39c12 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="crown" patternUnits="userSpaceOnUse" width="20" height="20"><path d="M10 2l2 4h6l-2 4 2 4H8l2-4-2-4h6l-2-4z" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23crown)"/></svg>');
            opacity: 0.3;
        }
        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        .login-header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 50%, #d35400 100%);
            color: white;
            padding: 2.5rem;
            text-align: center;
            position: relative;
        }
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="crown-pattern" patternUnits="userSpaceOnUse" width="30" height="30"><path d="M15 5l3 6h9l-3 6 3 6H12l3-6-3-6h9l-3-6z" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23crown-pattern)"/></svg>');
        }
        .login-header h3 {
            margin: 0;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .crown-icon {
            position: relative;
            z-index: 1;
            animation: crown-glow 2s ease-in-out infinite alternate;
        }
        @keyframes crown-glow {
            from { filter: drop-shadow(0 0 5px rgba(255,255,255,0.5)); }
            to { filter: drop-shadow(0 0 15px rgba(255,255,255,0.8)); }
        }
        .login-body {
            padding: 2.5rem;
        }
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.875rem 1.25rem;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
        }
        .form-control:focus {
            border-color: #f39c12;
            box-shadow: 0 0 0 0.25rem rgba(243, 156, 18, 0.25);
            background: white;
        }
        .btn-login {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover::before {
            left: 100%;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(243, 156, 18, 0.4);
        }
        .input-group-text {
            background: rgba(243, 156, 18, 0.1);
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #f39c12;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        .back-link {
            color: white;
            text-decoration: none;
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            z-index: 10;
        }
        .back-link:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateY(-2px);
        }
        .security-badge {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="back-link">
        <i class="fas fa-arrow-left me-2"></i>Back to Site
    </a>

    <div class="login-card">
        <div class="login-header">
            <div class="crown-icon mb-3">
                <i class="fas fa-crown fa-4x"></i>
            </div>
            <h3>Super Admin Portal</h3>
            <p>System-wide administration & client management</p>
            <div class="security-badge">
                <i class="fas fa-shield-alt me-1"></i>RESTRICTED ACCESS
            </div>
        </div>
        
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('super_admin.login') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="form-label fw-bold">Super Admin Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="superadmin@vault64.com">
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Security Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required placeholder="Enter your password">
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        <i class="fas fa-user-shield me-1"></i>Remember this session
                    </label>
                </div>

                <button type="submit" class="btn btn-warning btn-login w-100">
                    <i class="fas fa-crown me-2"></i>Access Super Admin Portal
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-key me-1"></i>Forgot your password?
                </a>
            </div>

            <hr class="my-4">

            <div class="text-center">
                <p class="text-muted mb-2">Need regular admin access?</p>
                <a href="{{ route('admin.login') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-user-shield me-1"></i>Admin Login
                </a>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    This portal is restricted to super administrators only
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 