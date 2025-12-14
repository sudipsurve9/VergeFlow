@extends('layouts.guest')

@section('content')
<div class="error-container">
    <div class="error-content">
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <div class="error-actions">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Go to Homepage
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </a>
        </div>
    </div>
</div>

<style>
.error-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: radial-gradient(ellipse at 50% 20%, #ff9900 0%, #2d1a06 80%, #18120a 100%);
}

.error-content {
    text-align: center;
    max-width: 600px;
    background: rgba(35, 35, 35, 0.95);
    padding: 3rem;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}

.error-code {
    font-size: 8rem;
    font-weight: 900;
    color: #ffb300;
    text-shadow: 0 0 20px #ff6a00;
    font-family: 'Orbitron', sans-serif;
    line-height: 1;
    margin-bottom: 1rem;
}

.error-title {
    font-size: 2rem;
    color: #fff;
    margin-bottom: 1rem;
    font-family: 'Montserrat', sans-serif;
}

.error-message {
    font-size: 1.1rem;
    color: #ccc;
    margin-bottom: 2rem;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
}

.btn-primary {
    background: linear-gradient(90deg, #ffb300, #ff6f00);
    color: #fff;
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(90deg, #ff6f00, #ffb300);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 179, 0, 0.3);
}

.btn-outline-secondary {
    background: transparent;
    color: #fff;
    border: 2px solid #666;
}

.btn-outline-secondary:hover {
    border-color: #ffb300;
    color: #ffb300;
}

@media (max-width: 768px) {
    .error-code {
        font-size: 5rem;
    }
    
    .error-title {
        font-size: 1.5rem;
    }
    
    .error-content {
        padding: 2rem 1.5rem;
    }
}
</style>
@endsection

