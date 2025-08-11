@extends($layout)

@section('content')
<!-- Hero Section -->
<section class="hero-section banner-theme" role="region" aria-label="Hero section">
<div class="container">
    <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="hero-title neon-glow">Welcome to {{ config('app.name', 'Valult64') }}</h1>
                <p class="hero-subtitle subtitle-glow">Discover premium quality products and authentic collections for true enthusiasts</p>
                <div class="mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-accent btn-lg me-3 banner-btn" aria-label="Shop now - go to products">Shop Now</a>
                    <a href="#featured" class="btn btn-outline-accent btn-lg banner-btn" aria-label="Jump to featured products">Featured Products</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5" role="region" aria-label="Shop by category">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold neon-glow">Shop by Category</h2>
                <p class="text-muted">Explore our curated collections</p>
            </div>
        </div>
        <div class="row">
            @foreach($categories ?? [] as $category)
                <div class="col-md-4 mb-4">
                    <div class="product-card text-center category-card">
                        <div class="product-info">
                            <i class="fas fa-tags fa-3x text-accent mb-3"></i>
                            <h5 class="product-title">{{ $category->name }}</h5>
                            <p class="text-muted">{{ $category->products_count ?? 0 }} products</p>
                            <a href="{{ route('products.index', ['category' => $category->id]) }}" class="btn btn-accent category-btn" aria-label="View products in {{ $category->name }} category">View Products</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
<section id="featured" class="py-5" role="region" aria-label="Featured products">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold neon-glow">Featured Products</h2>
                <p class="text-muted">Handpicked premium items for you</p>
            </div>
        </div>
        <div class="row">
            @foreach($featuredProducts ?? [] as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card">
                        @if($product->image)
                            <img src="{{ asset('storage/products/' . $product->image) }}" 
                                 class="product-image" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="product-image bg-dark d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="product-info">
                            <div class="product-category">{{ $product->category->name }}</div>
                            <h5 class="product-title product-title-glow">{{ $product->name }}</h5>
                            <div class="product-price price-glow">
                                @if($product->sale_price)
                                    <span class="original">₹{{ number_format($product->price, 2) }}</span>
                                    ₹{{ number_format($product->sale_price, 2) }}
                                @else
                                    ₹{{ number_format($product->price, 2) }}
                    @endif
                            </div>
                            <div class="d-grid">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-accent checkout-glow" aria-label="View details for {{ $product->name }}">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 text-center mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline-accent btn-lg" aria-label="View all products">View All Products</a>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Why Choose {{ config('app.name', 'Valult64') }}</h2>
                <p class="text-muted">We're committed to providing the best shopping experience</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-shipping-fast fa-3x text-accent"></i>
                </div>
                <h5>Fast Shipping</h5>
                <p class="text-muted">Quick and reliable delivery to your doorstep</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-shield-alt fa-3x text-accent"></i>
                </div>
                <h5>Secure Payment</h5>
                <p class="text-muted">Safe and encrypted payment processing</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-medal fa-3x text-accent"></i>
                </div>
                <h5>Quality Guarantee</h5>
                <p class="text-muted">Premium products with quality assurance</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-headset fa-3x text-accent"></i>
                </div>
                <h5>24/7 Support</h5>
                <p class="text-muted">Round-the-clock customer service</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 newsletter-section" role="region" aria-label="Newsletter signup">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h3 class="fw-bold mb-3" style="font-family: 'Orbitron', 'Montserrat', Arial, sans-serif; color: #FFB300; text-shadow: 0 0 16px #FF6A00;">Stay Updated</h3>
                <p class="mb-4" style="color: #FFB347;">Subscribe to our newsletter for exclusive offers and updates</p>
                <form class="input-group newsletter-form" action="#" method="POST" style="box-shadow: 0 0 16px #FF6A00;" aria-label="Newsletter signup form">
                    <input type="email" class="form-control newsletter-input" placeholder="Enter your email" aria-label="Email address for newsletter signup" style="background: #232323; color: #fff; border: 2px solid #FF6A00; border-right: none;">
                    <button class="btn btn-accent newsletter-btn" type="submit" aria-label="Subscribe to newsletter">Subscribe</button>
                </form>
        </div>
    </div>
</div>
</section>
<style>
.newsletter-section {
    border-top: 2px solid #FF6A00;
    border-bottom: 2px solid #FFB300;
    margin-top: 48px;
    margin-bottom: 0;
}
@media (max-width: 768px) {
    section {
        margin-bottom: 32px;
        margin-top: 32px;
    }
}
.newsletter-form .newsletter-input:focus {
    border-color: #FFB300;
    box-shadow: 0 0 8px #FFB300;
    color: #fff;
    background: #181818;
}
.newsletter-btn:hover {
    background: #FFB300 !important;
    color: #181818 !important;
    box-shadow: 0 0 16px #FFB300;
}
.neon-glow {
    text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00;
    animation: neonPulse 3s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00; }
    to { text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00; }
}
.product-title-glow {
    color: #FFB300;
    text-shadow: 0 0 8px #FF6A00, 0 0 16px #FFB300;
    font-size: 1.1rem;
    font-weight: 900;
}
.price-glow {
    color: #fff;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
    font-size: 1.2rem;
    font-weight: 900;
}
.product-card {
    transition: box-shadow 0.3s, transform 0.3s;
}
.product-card:hover {
    box-shadow: 0 0 32px #FFB30099, 0 0 64px #FF6A00;
    transform: translateY(-4px) scale(1.02);
}
.checkout-glow {
    box-shadow: 0 0 16px #FFB30099;
    transition: box-shadow 0.2s;
}
.checkout-glow:hover {
    box-shadow: 0 0 32px #FFB300, 0 0 64px #FF6A00;
}
@media (max-width: 768px) {
    .product-title-glow, .price-glow {
        font-size: 1rem;
    }
    .checkout-glow {
        font-size: 1rem;
        padding: 12px 0;
    }
}
.category-card {
    border: 2px solid var(--accent-color);
    background: var(--card-bg, #232323);
    box-shadow: 0 2px 12px rgba(255, 179, 0, 0.08);
    border-radius: 18px;
    transition: box-shadow 0.3s, transform 0.3s, border-color 0.3s;
}
.category-card:hover {
    box-shadow: 0 0 32px #FFB30099, 0 0 64px #FF6A00;
    border-color: #FFB300;
    transform: translateY(-6px) scale(1.03);
}
.category-btn {
    margin-top: 10px;
    font-weight: 600;
    border-radius: 30px;
    padding: 8px 28px;
    font-size: 1rem;
    background: linear-gradient(45deg, var(--accent-color), var(--accent-glow));
    color: #fff !important;
    border: none;
    box-shadow: 0 0 8px #FFB30099;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.category-btn:hover {
    background: linear-gradient(45deg, #FFB300, #FF6A00);
    color: #181818 !important;
    box-shadow: 0 0 24px #FFB300;
}
.subtitle-glow {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 600;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00, 0 0 32px #000;
    letter-spacing: 0.5px;
    margin-bottom: 1.5rem;
}
[data-theme="light"] .subtitle-glow {
    color: #181818;
    text-shadow: 0 0 8px #FFB30033, 0 0 16px #FF6A0033, 0 0 32px #fff;
}
@media (max-width: 768px) {
    .subtitle-glow {
        font-size: 1.1rem;
    }
}
.hero-section.banner-theme {
    background: radial-gradient(circle at 50% 0, #181818 0%, #0a0a0a 100%);
    background-size: cover;
    background-repeat: no-repeat;
    border-bottom: 2px solid #FFB300;
    padding: 80px 0 60px 0;
    position: relative;
    z-index: 1;
}
[data-theme="light"] .hero-section.banner-theme {
    background: radial-gradient(circle at 50% 0, #fffbe6 0%, #fff 100%);
    border-bottom: 2px solid #FFB300;
}
.hero-title.neon-glow {
    color: #FFB300;
    font-family: 'Orbitron', 'Montserrat', Arial, sans-serif;
    font-size: 3rem;
    font-weight: 900;
    text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00;
    letter-spacing: 2px;
    margin-bottom: 1.2rem;
    animation: neonPulse 3s infinite alternate;
}
[data-theme="light"] .hero-title.neon-glow {
    color: #FF6A00;
    text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00;
}
.banner-btn {
    font-size: 1.15rem;
    font-weight: 700;
    border-radius: 30px;
    padding: 12px 36px;
    box-shadow: 0 0 16px #FFB30099;
    margin-bottom: 10px;
}
.banner-btn.btn-accent {
    background: linear-gradient(45deg, #FFB300, #FF6A00);
    color: #fff !important;
    border: none;
}
.banner-btn.btn-accent:hover {
    background: linear-gradient(45deg, #FF6A00, #FFB300);
    color: #181818 !important;
    box-shadow: 0 0 32px #FFB300, 0 0 64px #FF6A00;
}
.banner-btn.btn-outline-accent {
    border: 2px solid #FFB300;
    color: #FFB300 !important;
    background: transparent;
}
.banner-btn.btn-outline-accent:hover {
    background: #FFB300;
    color: #181818 !important;
    box-shadow: 0 0 24px #FFB300;
}
@media (max-width: 768px) {
    .hero-title.neon-glow {
        font-size: 2rem;
    }
    .subtitle-glow {
        font-size: 1.1rem;
    }
    .banner-btn {
        font-size: 1rem;
        padding: 10px 18px;
    }
    .hero-section.banner-theme {
        padding: 40px 0 30px 0;
    }
}
.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style>
@endsection
