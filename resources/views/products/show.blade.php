@extends($layout)

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-image-container theme-card">
                @if($product->image)
                    <img src="{{ asset('storage/products/' . $product->image) }}" 
                         class="img-fluid rounded" 
                         alt="{{ $product->name }}"
                         style="width: 100%; max-height: 500px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                         style="height: 500px;">
                        <i class="fas fa-image fa-5x text-muted"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details theme-card">
                <div class="product-category mb-2">
                    <span class="badge badge-accent category-glow">{{ $product->category->name }}</span>
                </div>
                
                <h1 class="product-title neon-glow mb-3">{{ $product->name }}</h1>
                
                <div class="product-price price-glow mb-4">
                    @if($product->sale_price)
                        <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                        <span class="current-price">₹{{ number_format($product->sale_price, 2) }}</span>
                        <span class="discount-badge">Save {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                    @else
                        <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <div class="product-description mb-4">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>

                <div class="product-info mb-4">
                    <div class="row">
                        <div class="col-6">
                            <strong>SKU:</strong> {{ $product->sku }}
                        </div>
                        <div class="col-6">
                            <strong>Stock:</strong> 
                            @if($product->stock_quantity > 0)
                                <span class="text-success">{{ $product->stock_quantity }} available</span>
                            @else
                                <span class="text-danger">Out of stock</span>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $inWishlist = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists() : false;
                @endphp

                @if($product->stock_quantity > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-4" aria-label="Add product to cart form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $product->stock_quantity }}"
                                       class="form-control">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-accent w-100 btn-lg checkout-glow" aria-label="Add {{ $product->name }} to cart" title="Add this product to your cart">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                    <button type="button" class="btn btn-outline-danger w-100 wishlist-btn mb-4" data-product-id="{{ $product->id }}" aria-label="{{ $inWishlist ? 'Remove' : 'Add' }} {{ $product->name }} to wishlist">
                        <i class="fa{{ $inWishlist ? 's' : 'r' }} fa-heart me-1"></i>
                        <span class="wishlist-btn-text">{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}</span>
                    </button>
                @else
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This product is currently out of stock.
                    </div>
                @endif

                <div class="product-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-accent me-2 banner-btn" aria-label="Back to all products">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="btn btn-outline-secondary banner-btn" aria-label="More products in {{ $product->category->name }} category">
                        <i class="fas fa-tags me-2"></i>More {{ $product->category->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h2 class="fw-bold neon-glow section-title">Related Products</h2>
                    <p class="subtitle-glow">You might also like these products</p>
                </div>
                
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="product-card theme-card">
                                @if($relatedProduct->image)
                                    <img src="{{ asset('storage/products/' . $relatedProduct->image) }}" 
                                         class="product-image" 
                                         alt="{{ $relatedProduct->name }}">
                                @else
                                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="product-info">
                                    <div class="product-category category-glow">{{ $relatedProduct->category->name }}</div>
                                    <h5 class="product-title product-title-glow">{{ $relatedProduct->name }}</h5>
                                    <p class="product-desc">{{ Str::limit($relatedProduct->description, 60) }}</p>
                                    <div class="product-price price-glow">
                                        @if($relatedProduct->sale_price)
                                            <span class="original">₹{{ number_format($relatedProduct->price, 2) }}</span>
                                            ₹{{ number_format($relatedProduct->sale_price, 2) }}
                                        @else
                                            ₹{{ number_format($relatedProduct->price, 2) }}
                                        @endif
                                    </div>
                                    <div class="d-grid">
                                        <a href="{{ route('products.show', $relatedProduct->slug) }}" 
                                           class="btn btn-accent view-btn checkout-glow" aria-label="View details for {{ $relatedProduct->name }}">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.theme-card {
    background: var(--card-bg, #232323);
    border: 2px solid var(--accent-color, #FF6A00);
    border-radius: 16px;
    box-shadow: 0 0 16px var(--accent-glow, #FF6A0033);
}
.neon-glow {
    text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00);
    animation: neonPulse 3s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00); }
    to { text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00); }
}
.section-title {
    color: var(--accent-color, #FFB300);
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    letter-spacing: 2px;
    margin-bottom: 0.5rem;
}
.subtitle-glow {
    color: var(--text-primary, #fff);
    font-size: 1.1rem;
    font-weight: 600;
    text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00), 0 0 32px #000;
    letter-spacing: 0.5px;
    margin-bottom: 1.5rem;
}
[data-theme="light"] .subtitle-glow {
    color: var(--text-primary, #181818);
    text-shadow: 0 0 8px #FFB30033, 0 0 16px #FF6A0033, 0 0 32px #fff;
}
.product-title.neon-glow {
    color: var(--accent-color, #FFB300);
    font-size: 2rem;
    font-weight: 900;
    font-family: 'Orbitron', 'Montserrat', Arial, sans-serif;
    text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00);
    letter-spacing: 1px;
}
.price-glow {
    color: var(--text-primary, #fff);
    text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00);
    font-size: 1.5rem;
    font-weight: 900;
}
.category-glow {
    color: var(--accent-color, #FFB300);
    font-weight: 600;
    text-shadow: 0 0 8px var(--accent-glow, #FF6A00);
    font-size: 0.95rem;
    margin-bottom: 4px;
}
.product-desc {
    color: var(--text-secondary, #ccc);
    font-size: 0.95rem;
    margin-bottom: 8px;
}
.view-btn, .banner-btn {
    font-weight: 700;
    border-radius: 30px;
    padding: 10px 24px;
    font-size: 1rem;
    margin-bottom: 6px;
}
.view-btn.btn-accent, .banner-btn.btn-accent {
    background: linear-gradient(45deg, var(--accent-color, #FFB300), var(--accent-glow, #FF6A00));
    color: #fff !important;
    border: none;
    box-shadow: 0 0 8px var(--accent-color, #FFB30099);
}
.view-btn.btn-accent:hover, .banner-btn.btn-accent:hover {
    background: linear-gradient(45deg, var(--accent-glow, #FF6A00), var(--accent-color, #FFB300));
    color: #181818 !important;
    box-shadow: 0 0 24px var(--accent-color, #FFB300);
}
.banner-btn.btn-outline-accent {
    border: 2px solid var(--accent-color, #FFB300);
    color: var(--accent-color, #FFB300) !important;
    background: transparent;
}
.banner-btn.btn-outline-accent:hover {
    background: var(--accent-color, #FFB300);
    color: #181818 !important;
    box-shadow: 0 0 24px var(--accent-color, #FFB300);
}
.product-image-container.theme-card {
    background: var(--card-bg, #232323);
    border: 2px solid var(--accent-color, #FF6A00);
    border-radius: 16px;
    box-shadow: 0 0 16px var(--accent-glow, #FF6A0033);
}
[data-theme="light"] .theme-card, [data-theme="light"] .product-card.theme-card, [data-theme="light"] .card, [data-theme="light"] .card-body {
    background: #fff !important;
    color: #181818 !important;
    border-color: #FFB300 !important;
}
[data-theme="light"] .product-title.neon-glow {
    color: #FF6A00;
    text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00);
}
[data-theme="light"] .price-glow {
    color: #181818;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
}
[data-theme="light"] .category-glow {
    color: #FF6A00;
    text-shadow: 0 0 8px #FFB300;
}
[data-theme="light"] .product-desc {
    color: #666;
}
[data-theme="light"] .view-btn.btn-accent, [data-theme="light"] .banner-btn.btn-accent {
    background: linear-gradient(45deg, #FF6A00, #FFB300);
    color: #fff !important;
}
[data-theme="light"] .view-btn.btn-accent:hover, [data-theme="light"] .banner-btn.btn-accent:hover {
    background: linear-gradient(45deg, #FFB300, #FF6A00);
    color: #fff !important;
}
[data-theme="light"] .banner-btn.btn-outline-accent {
    border: 2px solid #FF6A00;
    color: #FF6A00 !important;
}
[data-theme="light"] .banner-btn.btn-outline-accent:hover {
    background: #FF6A00;
    color: #fff !important;
}
@media (max-width: 768px) {
    .product-title.neon-glow {
        font-size: 1.2rem;
    }
    .section-title {
        font-size: 1.2rem;
    }
    .price-glow {
        font-size: 1.1rem;
    }
    .view-btn, .banner-btn {
        font-size: 0.95rem;
        padding: 8px 12px;
    }
}
.related-products .text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .related-products .text-muted {
    color: #555 !important;
}
</style>

<script>
document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const isInWishlist = this.querySelector('i').classList.contains('fas');
        const url = isInWishlist ? `/wishlist/${productId}` : `/wishlist`;
        const method = isInWishlist ? 'DELETE' : 'POST';
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: method === 'POST' ? JSON.stringify({ product_id: productId }) : null
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'added') {
                this.querySelector('i').classList.remove('far');
                this.querySelector('i').classList.add('fas');
                this.querySelector('.wishlist-btn-text').textContent = 'Remove from Wishlist';
                this.setAttribute('aria-label', `Remove product from wishlist`);
            } else if (data.status === 'removed') {
                this.querySelector('i').classList.remove('fas');
                this.querySelector('i').classList.add('far');
                this.querySelector('.wishlist-btn-text').textContent = 'Add to Wishlist';
                this.setAttribute('aria-label', `Add product to wishlist`);
            }
        });
    });
});
</script>
@endsection 