@extends($layout)

@section('content')
<div class="container py-5" role="main" aria-label="Shopping cart main content">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold mb-3 neon-glow">Shopping Cart</h1>
            <p class="subtitle-glow">Review your items and proceed to checkout</p>
        </div>
    </div>

    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card theme-card" style="box-shadow: 0 0 32px var(--accent-color, #FFB30055), 0 0 64px var(--accent-glow, #FF6A0033);">
                    <div class="card-header theme-card-header neon-glow">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Cart Items ({{ $cartItems->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Product</th>
                                        <th class="border-0">Price</th>
                                        <th class="border-0">Quantity</th>
                                        <th class="border-0">Total</th>
                                        <th class="border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                        <tr class="cart-item-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->image)
                                                        <img src="{{ asset('storage/products/' . $item->product->image) }}" 
                                                             alt="{{ $item->product->name }}" 
                                                             style="width: 80px; height: 80px; object-fit: cover;"
                                                             class="rounded me-3">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 80px; height: 80px;">
                                                            <i class="fas fa-image fa-2x text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1 fw-bold product-title-glow">{{ $item->product->name }}</h6>
                                                        <small class="text-muted">{{ $item->product->category->name ?? 'No Category' }}</small>
                                                        <br>
                                                        <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->product->sale_price)
                                                    <div class="text-decoration-line-through text-muted">₹{{ number_format($item->product->price, 2) }}</div>
                                                    <div class="text-danger fw-bold">₹{{ number_format($item->product->sale_price, 2) }}</div>
                                                @else
                                                    <div class="fw-bold price-glow">₹{{ number_format($item->product->price, 2) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center" aria-label="Update quantity for {{ $item->product->name }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="input-group" style="width: 120px;">
                                                        <input type="number" 
                                                               name="quantity" 
                                                               value="{{ $item->quantity }}" 
                                                               min="1" 
                                                               max="{{ $item->product->stock_quantity }}"
                                                               class="form-control form-control-sm">
                                                        <button type="submit" class="btn btn-sm btn-outline-accent icon-btn-glow" aria-label="Update quantity for {{ $item->product->name }}">
                                                            <i class="fas fa-sync-alt fa-lg"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <span class="fw-bold fs-5 text-accent">₹{{ number_format($item->total, 2) }}</span>
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline" aria-label="Remove {{ $item->product->name }} from cart">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-accent icon-btn-glow" aria-label="Remove {{ $item->product->name }} from cart" onclick="return confirm('Remove this item from cart?')">
                                                        <i class="fas fa-trash fa-lg"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-accent banner-btn" aria-label="Continue shopping - back to products">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" aria-label="Clear all items from cart">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-accent banner-btn" aria-label="Clear all items from cart" 
                                onclick="return confirm('Clear all items from cart?')">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top theme-card" style="top: 100px; box-shadow: 0 0 32px var(--accent-color, #FFB30055), 0 0 64px var(--accent-glow, #FF6A0033);">
                    <div class="card-header theme-card-header neon-glow">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal ({{ $cartItems->count() }} items):</span>
                            <span class="fw-bold">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax:</span>
                            <span>₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong class="fs-5">Total:</strong>
                            <strong class="fs-5 text-accent">₹{{ number_format($total, 2) }}</strong>
                        </div>
                        
                        @auth
                            <a href="{{ route('orders.checkout') }}" class="btn btn-accent w-100 btn-lg checkout-glow banner-btn" aria-label="Proceed to checkout">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                        @else
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="btn btn-accent w-100 btn-lg mb-2 banner-btn" aria-label="Login to checkout">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                </a>
                                <small class="text-muted">Don't have an account? <a href="{{ route('register') }}">Register here</a></small>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-5x text-muted"></i>
            </div>
            <h3 class="text-muted mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to your cart to get started.</p>
            <a href="{{ route('products.index') }}" class="btn btn-accent btn-lg banner-btn" aria-label="Start shopping - go to products">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    @endif
</div>

<style>
.theme-card {
    background: var(--card-bg, #232323);
    border: 2px solid var(--accent-color, #FFB300);
    border-radius: 16px;
    box-shadow: 0 0 16px var(--accent-glow, #FF6A0033);
}
.theme-card-header {
    background: linear-gradient(90deg, var(--primary-bg, #181818) 0%, var(--accent-glow, #FF6A00) 100%) !important;
    color: var(--accent-color, #FFB300) !important;
    font-family: 'Orbitron', 'Montserrat', Arial, sans-serif;
    font-size: 1.1rem;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    border-bottom: 2px solid var(--accent-color, #FFB300);
}
.subtitle-glow {
    color: var(--text-primary, #fff);
    font-size: 1.2rem;
    font-weight: 600;
    text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00), 0 0 32px #000;
    letter-spacing: 0.5px;
    margin-bottom: 1.5rem;
}
[data-theme="light"] .subtitle-glow {
    color: var(--text-primary, #181818);
    text-shadow: 0 0 8px #FFB30033, 0 0 16px #FF6A0033, 0 0 32px #fff;
}
.banner-btn {
    font-weight: 700;
    border-radius: 30px;
    padding: 10px 24px;
    font-size: 1rem;
    margin-bottom: 6px;
    box-shadow: 0 0 8px var(--accent-color, #FFB30099);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.banner-btn.btn-accent {
    background: linear-gradient(45deg, var(--accent-color, #FFB300), var(--accent-glow, #FF6A00));
    color: #fff !important;
    border: none;
}
.banner-btn.btn-accent:hover {
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
.card, .card-body {
    background: var(--card-bg, #232323) !important;
    color: var(--text-primary, #fff) !important;
}
.table th {
    font-weight: 700;
    color: var(--accent-color, #FFB300);
    text-shadow: 0 0 8px var(--accent-glow, #FF6A00), 0 0 16px var(--accent-color, #FFB300);
    background: transparent !important;
}
.table td {
    vertical-align: middle;
    color: var(--text-primary, #fff);
}
input, select, textarea {
    background: var(--card-bg, #232323) !important;
    color: var(--text-primary, #fff) !important;
    border: 1px solid var(--accent-color, #FFB300) !important;
}
input:focus, select:focus, textarea:focus {
    border-color: var(--accent-glow, #FF6A00) !important;
    box-shadow: 0 0 8px var(--accent-glow, #FF6A00);
}
[data-theme="light"] .theme-card, [data-theme="light"] .card, [data-theme="light"] .card-body {
    background: #fff !important;
    color: #181818 !important;
    border-color: #FFB300 !important;
}
[data-theme="light"] .theme-card-header {
    background: linear-gradient(90deg, #fffbe6 0%, #FFB300 100%) !important;
    color: #FF6A00 !important;
    border-bottom: 2px solid #FF6A00 !important;
}
[data-theme="light"] .table th {
    color: #FF6A00;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
}
[data-theme="light"] .table td {
    color: #181818;
}
[data-theme="light"] .banner-btn.btn-accent {
    background: linear-gradient(45deg, #FF6A00, #FFB300);
    color: #fff !important;
}
[data-theme="light"] .banner-btn.btn-accent:hover {
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
    .table-responsive {
        font-size: 14px;
    }
    .table img {
        width: 60px !important;
        height: 60px !important;
    }
    .banner-btn {
        font-size: 0.95rem;
        padding: 8px 12px;
    }
}
.neon-glow {
    text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00);
    animation: neonPulse 3s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00); }
    to { text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00); }
}
.cart-item-row:hover {
    background: rgba(255, 179, 0, 0.08) !important;
    box-shadow: 0 0 16px var(--accent-color, #FFB30055);
    transition: background 0.2s, box-shadow 0.2s;
}
/* Product Name & Price Glow */
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
/* Icon Button Glow */
.icon-btn-glow i {
    transition: text-shadow 0.2s, color 0.2s;
}
.icon-btn-glow:hover i {
    color: #FFB300 !important;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
}
/* Quantity Input Focus Glow */
input[type="number"]:focus {
    border-color: #FFB300 !important;
    box-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
    background: #181818 !important;
    color: #fff !important;
}
/* Checkout Button Glow */
.checkout-glow {
    box-shadow: 0 0 16px #FFB30099;
    transition: box-shadow 0.2s;
}
.checkout-glow:hover {
    box-shadow: 0 0 32px #FFB300, 0 0 64px #FF6A00;
}
/* Cart Item Card Gradient/Shadow */
.table tbody tr {
    background: linear-gradient(90deg, #232323 0%, #181818 100%) !important;
    box-shadow: 0 0 8px #FFB30022;
    border-radius: 12px;
}
.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style>
@endsection 