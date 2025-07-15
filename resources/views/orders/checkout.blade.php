@extends($layout)

@section('content')
<div class="container" role="main" aria-label="Checkout main content">
    <div class="row">
        <div class="col-12">
            <h1 class="fw-bold neon-glow">Checkout</h1>
        </div>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" aria-label="Checkout form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Information -->
                <div class="card mb-4" role="region" aria-label="Shipping information">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="address_line1" class="form-label">Address Line 1 *</label>
                            <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" required>
                            @error('address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="address_line2" class="form-label">Address Line 2 *</label>
                            <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" name="address_line2" value="{{ old('address_line2') }}" required>
                            @error('address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="landmark" class="form-label">Landmark (Optional)</label>
                            <input type="text" class="form-control @error('landmark') is-invalid @enderror" id="landmark" name="landmark" value="{{ old('landmark') }}">
                            @error('landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="locality" class="form-label">Locality/Area *</label>
                            <input type="text" class="form-control @error('locality') is-invalid @enderror" id="locality" name="locality" value="{{ old('locality') }}" required>
                            @error('locality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">City *</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="state" class="form-label">State *</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state') }}" required>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="pincode" class="form-label">Pincode *</label>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror" id="pincode" name="pincode" value="{{ old('pincode') }}" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4" role="region" aria-label="Payment method">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method') == 'cod' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="cod">
                                    Cash on Delivery (COD)
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="bank_transfer">
                                    Bank Transfer
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                <label class="form-check-label" for="credit_card">
                                    Credit Card
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : '' }}>
                                <label class="form-check-label" for="stripe">
                                    Pay with Card (Stripe)
                                </label>
                            </div>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4" role="region" aria-label="Order notes">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Order Notes (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Any special instructions or notes for your order...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="card mb-4" role="region" aria-label="Billing information">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Billing Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="billing_address_line1" class="form-label">Billing Address Line 1 *</label>
                            <input type="text" class="form-control @error('billing_address_line1') is-invalid @enderror" id="billing_address_line1" name="billing_address_line1" value="{{ old('billing_address_line1') }}" required>
                            @error('billing_address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_address_line2" class="form-label">Billing Address Line 2 *</label>
                            <input type="text" class="form-control @error('billing_address_line2') is-invalid @enderror" id="billing_address_line2" name="billing_address_line2" value="{{ old('billing_address_line2') }}" required>
                            @error('billing_address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_landmark" class="form-label">Billing Landmark (Optional)</label>
                            <input type="text" class="form-control @error('billing_landmark') is-invalid @enderror" id="billing_landmark" name="billing_landmark" value="{{ old('billing_landmark') }}">
                            @error('billing_landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_locality" class="form-label">Billing Locality/Area *</label>
                            <input type="text" class="form-control @error('billing_locality') is-invalid @enderror" id="billing_locality" name="billing_locality" value="{{ old('billing_locality') }}" required>
                            @error('billing_locality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_city" class="form-label">Billing City *</label>
                            <input type="text" class="form-control @error('billing_city') is-invalid @enderror" id="billing_city" name="billing_city" value="{{ old('billing_city') }}" required>
                            @error('billing_city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_state" class="form-label">Billing State *</label>
                            <input type="text" class="form-control @error('billing_state') is-invalid @enderror" id="billing_state" name="billing_state" value="{{ old('billing_state') }}" required>
                            @error('billing_state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_pincode" class="form-label">Billing Pincode *</label>
                            <input type="text" class="form-control @error('billing_pincode') is-invalid @enderror" id="billing_pincode" name="billing_pincode" value="{{ old('billing_pincode') }}" required>
                            @error('billing_pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card" role="region" aria-label="Order summary">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0 product-title-glow">{{ $item->product->name }}</h6>
                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                </div>
                                <span>₹{{ number_format($item->total, 2) }}</span>
                            </div>
                        @endforeach
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span>₹0.00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-accent price-glow">₹{{ number_format($total, 2) }}</strong>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 btn-lg checkout-glow" aria-label="Place order and complete checkout">
                            <i class="fas fa-lock"></i> Place Order
                        </button>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-accent btn-sm icon-btn-glow">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="stripe-card-section" style="display: none;">
    <div class="mb-3">
        <label for="card-element" class="form-label">Card Details</label>
        <div id="card-element" class="form-control"></div>
        <div id="card-errors" class="text-danger mt-2" role="alert"></div>
    </div>
</div>

<style>
.neon-glow {
    text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00;
    animation: neonPulse 3s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00; }
    to { text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00; }
}
.checkout-glow {
    box-shadow: 0 0 16px #FFB30099;
    transition: box-shadow 0.2s;
}
.checkout-glow:hover {
    box-shadow: 0 0 32px #FFB300, 0 0 64px #FF6A00;
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
.icon-btn-glow i {
    transition: text-shadow 0.2s, color 0.2s;
}
.icon-btn-glow:hover i {
    color: #FFB300 !important;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
}
input, textarea, select {
    color: #fff !important;
}
input::placeholder, textarea::placeholder {
    color: #FFB300 !important;
    opacity: 1;
}
.form-label, label, .form-check-label {
    color: #FFB300 !important;
    font-weight: 600;
}
.card-body, .order-summary, .cart-summary, .card {
    color: #fff !important;
}
.order-summary span, .order-summary strong, .card-body span, .card-body strong {
    color: #fff !important;
}
.text-muted {
    color: #b3b3b3 !important;
}
@media (max-width: 768px) {
    .neon-glow { font-size: 1.2rem; }
    .checkout-glow { font-size: 1rem; padding: 12px 0; }
    .product-title-glow, .price-glow { font-size: 1rem; }
}
[data-theme="light"] input,
[data-theme="light"] textarea,
[data-theme="light"] select {
    color: #181818 !important;
    background: #fff !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config('services.stripe.key') }}');
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');

const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
function toggleStripeSection() {
    document.getElementById('stripe-card-section').style.display = document.getElementById('stripe').checked ? 'block' : 'none';
}
paymentRadios.forEach(radio => radio.addEventListener('change', toggleStripeSection));
toggleStripeSection();

const form = document.querySelector('form[action="{{ route('orders.store') }}"]');
form.addEventListener('submit', function(e) {
    if (document.getElementById('stripe').checked) {
        e.preventDefault();
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'stripeToken';
                input.value = result.token.id;
                form.appendChild(input);
                form.submit();
            }
        });
    }
});
</script>
@endpush
@endsection 