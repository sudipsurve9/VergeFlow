@extends($layout)

@section('content')
<div class="welcome-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title neon-glow">
                            <i class="fas fa-warehouse me-3"></i>Welcome to Vault64
                        </h1>
                        <p class="hero-subtitle">Your premier destination for authentic racing collectibles and premium motorsport merchandise</p>
                        <div class="hero-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Authentic Products</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Premium Quality</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Fast Shipping</span>
                            </div>
                        </div>
                        <div class="hero-buttons">
                            <a href="{{ route('products.index') }}" class="btn btn-accent btn-lg">
                                <i class="fas fa-shopping-bag me-2"></i>Shop Now
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-accent btn-lg">
                                <i class="fas fa-info-circle me-2"></i>Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="hero-image">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <div class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title neon-glow">
                    <i class="fas fa-tags me-2"></i>Shop by Category
                </h2>
                <p class="section-subtitle">Explore our curated collections</p>
            </div>

            <div class="row">
                @php
                $categories = \App\Models\Category::active()->withCount('products')->take(4)->get();
                @endphp

                @foreach($categories as $category)
                <div class="col-md-3 mb-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h5 class="category-name">{{ $category->name }}</h5>
                        <p class="category-count">{{ $category->products_count }} products</p>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-link">
                            <i class="fas fa-arrow-right me-1"></i>View Products
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title neon-glow">
                    <i class="fas fa-star me-2"></i>Featured Products
                </h2>
                <p class="section-subtitle">Handpicked racing collectibles</p>
            </div>

            <div class="row">
                @php
                $featuredProducts = \App\Models\Product::featured()->active()->with('category')->take(8)->get();
                @endphp

                @foreach($featuredProducts as $product)
                <div class="col-md-3 mb-4">
                    <div class="product-card">
                        <div class="product-image">
                            @if($product->image)
                            <img src="{{ asset('storage/products/' . $product->image) }}"
                                alt="{{ $product->name }}">
                            @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                            <div class="product-overlay">
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-accent btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>

                        <div class="product-content">
                            <h5 class="product-name">{{ $product->name }}</h5>
                            <p class="product-category">{{ $product->category->name ?? 'No Category' }}</p>
                            <p class="product-description">{{ Str::limit($product->description, 80) }}</p>

                            <div class="product-pricing">
                                @if($product->sale_price)
                                <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                                <span class="sale-price">₹{{ number_format($product->sale_price, 2) }}</span>
                                @else
                                <span class="price">₹{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>

                            <div class="product-actions">
                                @if($product->stock_quantity > 0)
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-accent btn-sm" title="Add this product to your cart" aria-label="Add {{ $product->name ?? 'this product' }} to cart">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-times me-1"></i>Out of Stock
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="cta-section">
        <div class="container">
            <div class="cta-card">
                <div class="cta-content">
                    <h3 class="cta-title">Ready to Start Your Collection?</h3>
                    <p class="cta-subtitle">Join thousands of racing enthusiasts who trust Vault64 for their premium collectibles</p>
                    <div class="cta-buttons">
                        <a href="{{ route('products.index') }}" class="btn btn-accent btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Browse All Products
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-accent btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .welcome-container {
        min-height: 100vh;
    }

    .hero-section {
        background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
        color: var(--text-primary);
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 1rem;
        letter-spacing: 2px;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }

    .hero-features {
        margin-bottom: 2rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        font-weight: 500;
    }

    .feature-item i {
        color: var(--accent-glow);
        font-size: 1.1rem;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .hero-visual {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .hero-image {
        font-size: 8rem;
        color: var(--accent-glow);
        text-shadow: 0 0 30px var(--accent-color);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .categories-section,
    .products-section {
        padding: 4rem 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        letter-spacing: 2px;
    }

    .section-subtitle {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin: 0;
    }

    .category-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    .category-icon {
        font-size: 3rem;
        color: var(--accent-color);
        margin-bottom: 1rem;
        text-shadow: 0 0 15px var(--accent-glow);
    }

    .category-name {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
    }

    .category-count {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .category-link {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .category-link:hover {
        color: var(--accent-secondary);
        text-shadow: 0 0 8px var(--accent-glow);
        text-decoration: none;
    }

    .product-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    .product-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .no-image {
        width: 100%;
        height: 100%;
        background: var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 2rem;
    }

    .product-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .product-content {
        padding: 1.5rem;
    }

    .product-name {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .product-category {
        color: var(--accent-color);
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-description {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .product-pricing {
        margin-bottom: 1rem;
    }

    .original-price {
        color: var(--text-muted);
        text-decoration: line-through;
        font-size: 0.9rem;
        margin-right: 0.5rem;
    }

    .sale-price {
        color: var(--danger-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .price {
        color: var(--accent-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .product-actions {
        display: flex;
        gap: 0.5rem;
    }

    .cta-section {
        background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
        color: var(--text-primary);
        padding: 4rem 0;
        position: relative;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .cta-card {
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .cta-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
        letter-spacing: 2px;
    }

    .cta-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }

    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-accent {
        background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
        border: none;
        color: var(--text-primary);
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-accent:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 179, 0, 0.3);
        color: var(--text-primary);
        text-decoration: none;
    }

    .btn-outline-accent {
        border: 2px solid var(--text-primary);
        color: var(--text-primary);
        background: transparent;
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-outline-accent:hover {
        background: var(--text-primary);
        color: var(--accent-color);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        text-decoration: none;
    }

    .neon-glow {
        text-shadow: 0 0 8px var(--accent-glow), 0 0 16px var(--accent-color);
        animation: neonPulse 2s infinite alternate;
    }

    @keyframes neonPulse {
        from {
            text-shadow: 0 0 8px var(--accent-glow), 0 0 16px var(--accent-color);
        }

        to {
            text-shadow: 0 0 24px var(--accent-glow), 0 0 48px var(--accent-color);
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .section-title,
        .cta-title {
            font-size: 2rem;
        }

        .hero-buttons,
        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }

        .hero-image {
            font-size: 5rem;
            margin-top: 2rem;
        }
    }

    .products-section {
        background: var(--secondary-bg);
        color: var(--text-primary);
        transition: background 0.3s, color 0.3s;
    }

    [data-theme="light"] .products-section {
        background: #f8f9fa !important;
        color: #181818 !important;
    }

    [data-theme="light"] #featured {
        background: radial-gradient(circle at 50% 0, #fffbe6 0%, #fff 100%) !important;
    }

    html[data-theme="light"] #featured[style] {
        background: radial-gradient(circle at 50% 0, #fffbe6 0%, #fff 100%) !important;
    }
    [data-theme="dark"] .newsletter-section {
    background: radial-gradient(circle at 50% 0, #232323 0%, #181818 100%);
      
   }
    html[data-theme="light"] .newsletter-section,
    html[data-theme="light"] section.newsletter-section,
    html[data-theme="light"] div.newsletter-section,
    html[data-theme="light"] .newsletter-section[style] {
        border-top: 2px solid #FFB300 !important;
        border-bottom: 2px solid #FF6A00 !important;
    }

    .newsletter-section {
        border-top: 2px solid #FF6A00;
        border-bottom: 2px solid #FFB300;
        margin-top: 48px;
        margin-bottom: 0;
    }

    

    [data-theme="light"] .newsletter-section {
        background: #f8f9fa !important;
        border-top: 2px solid #FFB300 !important;
        border-bottom: 2px solid #FF6A00 !important;
    }
</style>
@endsection