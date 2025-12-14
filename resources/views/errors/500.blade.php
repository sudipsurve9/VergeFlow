@extends('layouts.guest')

@section('content')
<div class="error-container">
    <div class="error-content">
        <div class="error-code">500</div>
        <h1 class="error-title">Internal Server Error</h1>
        <p class="error-message">
            Something went wrong on our end. We've been notified and are working to fix it.
        </p>
        <div class="error-actions">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Go to Homepage
            </a>
            <a href="javascript:location.reload()" class="btn btn-outline-secondary">
                <i class="fas fa-redo me-2"></i>Try Again
            </a>
        </div>
        @if(config('app.debug'))
        <div class="error-debug mt-4">
            <details>
                <summary>Error Details (Debug Mode)</summary>
                <pre>{{ $exception->getMessage() }}</pre>
            </details>
        </div>
        @endif
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
    color: #ff4444;
    text-shadow: 0 0 20px #ff0000;
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

.error-debug {
    margin-top: 2rem;
    text-align: left;
    background: #1a1a1a;
    padding: 1rem;
    border-radius: 8px;
    color: #ff4444;
}

.error-debug pre {
    margin: 0;
    font-size: 0.9rem;
    overflow-x: auto;
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

