@extends($layout)

@section('content')
<div class="container" role="main" aria-label="Order details main content">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title neon-glow">
                            <i class="fa-solid fa-receipt me-2 text-accent"></i>Order Details
                        </h1>
                        <p class="page-subtitle"><i class="fa-solid fa-hashtag me-1 text-muted"></i>Order #{{ $order->order_number }}</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="btn btn-accent icon-btn-glow" aria-label="Back to orders">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="premium-card mb-4" role="region" aria-label="Order information">
                <div class="card-header premium-header bg-accent text-white d-flex align-items-center">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>Order Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="order-info-table mb-0">
                        <div class="info-grid">
                            <div class="info-label-cell"><i class="fa-solid fa-hashtag me-1 text-muted"></i>ORDER NUMBER:</div>
                            <div class="info-value-cell">{{ $order->order_number }}</div>
                            <div class="info-label-cell"><i class="fa-solid fa-credit-card me-1 text-muted"></i>PAYMENT METHOD:</div>
                            <div class="info-value-cell">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                            <div class="info-label-cell"><i class="fa-solid fa-calendar-alt me-1 text-muted"></i>ORDER DATE:</div>
                            <div class="info-value-cell">{{ $order->created_at->format('F d, Y \a\t g:i A') }}</div>
                            <div class="info-label-cell"><i class="fa-solid fa-money-check-alt me-1 text-muted"></i>PAYMENT STATUS:</div>
                            <div class="info-value-cell">
                                <span class="badge payment-badge payment-{{ $order->payment_status }} fs-6 px-3 py-2 shadow-sm">{{ ucfirst($order->payment_status) }}</span>
                            </div>
                            <div class="info-label-cell"><i class="fa-solid fa-flag me-1 text-muted"></i>STATUS:</div>
                            <div class="info-value-cell">
                                <span class="badge status-badge status-{{ $order->status }} fs-6 px-3 py-2 shadow-sm">{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="info-label-cell"></div>
                            <div class="info-value-cell"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="premium-card mb-4" role="region" aria-label="Order items">
                <div class="card-header premium-header bg-accent text-white d-flex align-items-center">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-boxes me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="order-item mb-3 p-3 rounded d-flex align-items-center shadow-sm" style="background:#fafafa; border:1px solid #eee;">
                            <div class="item-image me-3 flex-shrink-0">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/products/' . $item->product->image) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="product-img">
                                @else
                                    <div class="no-image bg-light border rounded p-2">
                                        <i class="fa-solid fa-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="item-details flex-grow-1 me-3">
                                <h6 class="item-name mb-1"><i class="fa-solid fa-cube me-1 text-accent"></i>{{ $item->product->name }}</h6>
                                <p class="item-sku mb-1 text-muted"><i class="fa-solid fa-barcode me-1"></i>SKU: {{ $item->product->sku }}</p>
                                <p class="item-quantity mb-0"><i class="fa-solid fa-sort-numeric-up me-1"></i>Quantity: {{ $item->quantity }}</p>
                            </div>
                            <div class="item-pricing text-end flex-shrink-0">
                                <p class="item-price mb-1 text-muted">
                                    <i class="fa-solid fa-rupee-sign me-1"></i>{{ number_format($item->price, 2) }} each
                                </p>
                                <strong class="item-total fs-5 text-accent">
                                    <i class="fa-solid fa-rupee-sign me-1"></i>{{ number_format($item->total, 2) }}
                                </strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="premium-card mb-4" role="region" aria-label="Shipping information">
                <div class="card-header premium-header bg-accent text-white d-flex align-items-center">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-shipping-fast me-2"></i>Shipping Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="shipping-info-table mb-0">
                        <div class="info-grid">
                            <div class="info-label-cell"><i class="fa-solid fa-location-dot me-1 text-muted"></i>SHIPPING ADDRESS:</div>
                            <div class="info-value-cell">{!! nl2br(e($order->shipping_address)) !!}</div>
                            <div class="info-label-cell"><i class="fa-solid fa-file-invoice me-1 text-muted"></i>BILLING ADDRESS:</div>
                            <div class="info-value-cell">{!! nl2br(e($order->billing_address)) !!}</div>
                            <div class="info-label-cell"><i class="fa-solid fa-phone me-1 text-muted"></i>PHONE:</div>
                            <div class="info-value-cell">{{ $order->phone }}</div>
                            <div class="info-label-cell"></div>
                            <div class="info-value-cell"></div>
                            @if($order->notes)
                            <div class="info-label-cell"><i class="fa-solid fa-sticky-note me-1 text-muted"></i>ORDER NOTES:</div>
                            <div class="info-value-cell" style="grid-column: span 3;">{{ $order->notes }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Stepper & Timeline -->
            <div class="premium-card mb-4" role="region" aria-label="Order status timeline">
                <div class="card-header premium-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-stream me-2"></i>Order Status Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Stepper -->
                    @php
                        $mainSteps = ['Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
                        $currentStep = array_search($order->delivery_status, $mainSteps);
                        if ($currentStep === false) $currentStep = 0;
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center stepper-horizontal">
                            @foreach($mainSteps as $i => $step)
                                <div class="stepper-step text-center {{ $i <= $currentStep ? 'active' : '' }}">
                                    <div class="stepper-circle">{{ $i+1 }}</div>
                                    <div class="stepper-label">{{ $step }}</div>
                                </div>
                                @if($i < count($mainSteps) - 1)
                                    <div class="stepper-line"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <!-- Timeline -->
                    @if($order->statusHistories->count() > 0)
                        <ul class="timeline list-unstyled">
                            @foreach($order->statusHistories as $history)
                                <li class="timeline-item mb-4">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <span class="timeline-status badge bg-accent mb-1">{{ $history->status }}</span>
                                        <div class="small text-muted">{{ $history->created_at->format('M d, Y g:i A') }}</div>
                                        @if($history->comment)
                                            <div class="timeline-comment mt-1">{{ $history->comment }}</div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted py-3" role="status">
                            <i class="fa-solid fa-clock fa-2x mb-2"></i>
                            <p>No status updates yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Delivery Tracking -->
            <div class="premium-card mb-4" role="region" aria-label="Delivery tracking">
                <div class="card-header premium-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-truck-moving me-2"></i>Delivery Tracking
                    </h5>
                </div>
                <div class="card-body">
                    @if(!$order->delivery_status && !$order->tracking_number)
                        <div class="alert alert-info mb-0" role="status">No delivery tracking information available yet.</div>
                    @else
                        <!-- Stepper/Progress Bar -->
                        <div class="mb-3">
                            <div class="progress" style="height: 18px;">
                                @php
                                    $steps = ['Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
                                    $currentStep = array_search($order->delivery_status, $steps);
                                    if ($currentStep === false) $currentStep = 0;
                                    $percent = ($currentStep / (count($steps) - 1)) * 100;
                                @endphp
                                <div class="progress-bar bg-accent" role="progressbar" style="width: {{ $percent }}%">
                                    {{ $order->delivery_status }}
                                </div>
                            </div>
                            @if($order->delivery_status == 'Cancelled')
                                <div class="alert alert-danger mt-2 mb-0 py-2">Order Cancelled</div>
                            @endif
                        </div>
                        @if($order->tracking_number)
                            <div class="mb-2">
                                <strong>Tracking Number:</strong> {{ $order->tracking_number }}
                            </div>
                        @endif
                        @if($order->courier_name)
                            <div class="mb-2">
                                <strong>Courier:</strong> {{ $order->courier_name }}
                                @if($order->courier_url)
                                    <a href="{{ $order->courier_url }}" target="_blank" class="ms-2 btn btn-sm btn-outline-primary">Track Package</a>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="premium-card mb-4">
                <div class="card-header premium-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-calculator me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Tax:</span>
                        <span class="summary-value">₹{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Shipping:</span>
                        <span class="summary-value">₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="summary-total">
                        <span class="total-label">TOTAL:</span>
                        <span class="summary-value total-value">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    @if($order->status == 'pending')
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 cancel-order-btn" 
                                    style="margin-top:12px; font-size:1.1rem; padding:12px 0; border-radius:8px;"
                                    onclick="return confirm('Are you sure you want to cancel this order?')">
                                <i class="fa-solid fa-times me-2"></i>Cancel Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Invoice button temporarily removed due to route configuration issues -->
        </div>
    </div>
</div>

<style>
* {
    font-family: Arial, Helvetica, sans-serif !important;
}

.page-header {
    margin-bottom: 2rem;
    padding: 2rem 0;
}

.page-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--accent-color);
    text-shadow: 0 0 20px var(--accent-glow);
    margin-bottom: 0.5rem;
    letter-spacing: 2px;
}

.page-subtitle {
    color: var(--text-muted);
    font-size: 1.1rem;
    margin: 0;
}

.premium-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2.2rem;
    padding-top: 1.2rem;
    padding-bottom: 1.2rem;
}

.premium-card .card-header.premium-header {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.premium-header {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
    display: flex;
    align-items: center;
}

.premium-header h5 {
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.7rem;
    font-size: 1.18rem;
}

.premium-header h5 i {
    font-size: 1.35em;
    vertical-align: middle;
    margin-right: 0.4em;
    color: var(--accent-color);
    flex-shrink: 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--accent-color);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.info-value {
    color: var(--text-primary);
    font-weight: 500;
}

.status-badge, .payment-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: linear-gradient(135deg, #FFB300, #FF6A00);
    color: white;
}

.status-completed {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-cancelled {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
}

.payment-paid {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.payment-pending {
    background: linear-gradient(135deg, #FFB300, #FF6A00);
    color: white;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.order-item:hover {
    background: #f8f9fa !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    margin-right: 1.5rem;
    flex-shrink: 0;
}

.product-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid var(--border-color);
    transition: transform 0.3s ease;
}

.product-img:hover {
    transform: scale(1.05);
}

.no-image {
    width: 80px;
    height: 80px;
    background: var(--border-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 1.5rem;
}

.item-details {
    flex-grow: 1;
    min-width: 0;
}

.item-name {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
    line-height: 1.3;
}

.item-sku, .item-quantity {
    color: var(--text-muted);
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    line-height: 1.4;
}

.item-pricing {
    text-align: right;
    flex-shrink: 0;
    min-width: 120px;
}

.item-price {
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.item-total {
    color: var(--accent-color);
    font-size: 1.2rem;
    font-weight: 700;
}

.address-section, .contact-section, .notes-section {
    margin-bottom: 1.5rem;
}

.section-title {
    color: var(--accent-color);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.address-text, .contact-text, .notes-text {
    color: var(--text-primary);
    line-height: 1.6;
    margin: 0;
}

.summary-item, .summary-total {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0.5rem 0;
    margin-bottom: 0;
}
.summary-label, .summary-value, .total-label, .total-value {
    font-size: 1.08rem;
    line-height: 1.3;
    font-weight: 500;
}
.summary-label, .total-label {
    margin-bottom: 0.15em;
}
.total-value {
    /* Remove text-shadow for perfect alignment */
    text-shadow: none;
}
.summary-divider {
    border-color: var(--border-color);
    margin: 1rem 0;
}
.summary-total {
    font-weight: 700;
    font-size: 1.13rem;
    color: var(--accent-color);
    letter-spacing: 0.5px;
    border-top: 1.5px solid var(--border-color);
    margin-top: 0.5rem;
    margin-bottom: 1.2rem;
    padding: 0.7rem 0 0.7rem 0;
    background: none !important;
    border-radius: 0;
}
.total-label, .total-value {
    line-height: 1.2;
}
.total-value {
    color: var(--accent-color);
    font-weight: 700;
    font-size: 1.13rem;
    text-shadow: 0 0 4px var(--accent-glow);
}
.cancel-order-btn {
    display: block;
    width: 100%;
    margin: 1.2rem auto 0 auto;
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    border: none;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    padding: 0.65rem 0;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(220, 53, 69, 0.08);
    letter-spacing: 0.5px;
}
.cancel-order-btn:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.18);
    color: white;
    background: linear-gradient(135deg, #e74c3c, #dc3545);
}
.icon-btn-glow {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
    border: none;
    color: var(--text-primary);
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.icon-btn-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 179, 0, 0.3);
    color: var(--text-primary);
    text-decoration: none;
}
.neon-glow {
    text-shadow: 0 0 8px var(--accent-glow), 0 0 16px var(--accent-color);
    animation: neonPulse 2s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 8px var(--accent-glow), 0 0 16px var(--accent-color); }
    to { text-shadow: 0 0 24px var(--accent-glow), 0 0 48px var(--accent-color); }
}
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .order-item {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    .item-image {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .item-details {
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .item-pricing {
        text-align: center;
        margin-top: 0.5rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .stepper-horizontal {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stepper-step {
        flex-direction: row;
        text-align: left;
        align-items: center;
    }
    
    .stepper-circle {
        margin: 0 1rem 0 0;
    }
    
    .stepper-line {
        display: none;
    }
    
    .timeline {
        margin-left: 15px;
        padding-left: 15px;
    }
    
    .timeline-marker {
        left: -21px;
        width: 14px;
        height: 14px;
    }
    
    .timeline-content {
        margin-left: 5px;
        padding: 0.75rem;
    }
    
    .premium-card {
        margin-bottom: 1.2rem;
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
    }
    
    .premium-card .card-header.premium-header {
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
    }
    
    .info-label-cell, .info-value-cell {
        min-height: 44px;
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
    }
    .summary-item, .summary-total {
        font-size: 0.98rem;
        padding: 0.45rem 0;
    }
    .summary-total {
        font-size: 1.03rem;
        padding: 0.6rem 0 0.6rem 0;
    }
    .total-value {
        font-size: 1.1rem;
    }
    .premium-card .card-body form {
        margin-top: 0.7rem;
    }
}

/* Stepper Styles */
.stepper-horizontal { 
    gap: 0.5rem; 
    align-items: flex-start;
}
.stepper-step { 
    flex: 1; 
    position: relative; 
    display: flex;
    flex-direction: column;
    align-items: center;
}
.stepper-circle {
    width: 40px; 
    height: 40px; 
    border-radius: 50%;
    background: var(--border-color);
    color: var(--text-muted);
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-weight: bold; 
    font-size: 1.1rem; 
    margin: 0 auto 0.75rem auto;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.stepper-step.active .stepper-circle {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
    color: #fff;
    border: 2px solid var(--accent-color);
    box-shadow: 0 0 15px var(--accent-glow);
    transform: scale(1.1);
}
.stepper-label {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 500;
    text-align: center;
    line-height: 1.3;
    max-width: 100%;
}
.stepper-step.active .stepper-label {
    color: var(--accent-color);
    font-weight: 700;
}
.stepper-line {
    flex: none;
    width: 50px; 
    height: 3px;
    background: var(--border-color);
    margin: 20px 0 0 0;
    border-radius: 2px;
    transition: background 0.3s ease;
}
.stepper-step.active ~ .stepper-line {
    background: var(--accent-color);
}

/* Timeline Styles */
.timeline {
    border-left: 3px solid var(--accent-color);
    margin-left: 20px;
    padding-left: 20px;
    position: relative;
}
.timeline::before {
    content: '';
    position: absolute;
    left: -3px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, var(--accent-color), var(--accent-secondary));
}
.timeline-item { 
    position: relative; 
    margin-bottom: 1.5rem;
}
.timeline-marker {
    position: absolute; 
    left: -26px; 
    top: 5px;
    width: 16px; 
    height: 16px; 
    border-radius: 50%;
    background: var(--accent-color);
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px var(--accent-color);
    z-index: 2;
}
.timeline-content { 
    margin-left: 10px; 
    background: rgba(255,255,255,0.05);
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid var(--accent-color);
}
.timeline-status {
    font-size: 0.85rem;
    font-weight: 600;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
    color: #fff;
    border-radius: 12px;
    padding: 0.3rem 0.8rem;
    display: inline-block;
    margin-bottom: 0.5rem;
}
.timeline-comment { 
    color: var(--text-primary); 
    font-size: 0.95rem; 
    line-height: 1.4;
    margin-top: 0.5rem;
}

/* Info Table Styles */
.info-grid {
    display: grid;
    grid-template-columns: 1.2fr 1.8fr 1.2fr 1.8fr;
    align-items: center;
    width: 100%;
}
.info-label-cell, .info-value-cell {
    min-height: 56px;
    display: flex;
    align-items: center;
    padding: 1rem 0.5rem;
    font-size: 1rem;
}
.info-label-cell {
    font-weight: 700;
    color: var(--accent-color);
    text-transform: uppercase;
    background: transparent;
}
.info-value-cell {
    font-weight: 500;
    color: var(--text-primary);
    background: transparent;
    word-break: break-word;
}
@media (max-width: 900px) {
    .info-grid {
        grid-template-columns: 1fr 1fr;
    }
    .info-label-cell, .info-value-cell {
        font-size: 0.97rem;
        padding: 0.6rem 0.5rem;
        min-height: 44px;
    }
}
@media (max-width: 600px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    .info-label-cell, .info-value-cell {
        font-size: 0.97rem;
        padding: 0.5rem 0.5rem;
        min-height: 38px;
    }
}
.premium-card.sticky-top.mb-4 {
    margin-bottom: 2.5rem !important;
    margin-top: 1.5rem !important;
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
}
.premium-card .card-body {
    padding-top: 1.2rem;
    padding-bottom: 1.2rem;
}
.summary-item, .summary-total {
    padding-top: 1.1rem;
    padding-bottom: 1.1rem;
    margin-bottom: 0.2rem;
}
.summary-total {
    margin-top: 1.2rem;
    padding-top: 1.3rem;
    padding-bottom: 1.3rem;
    border-radius: 8px;
    background: rgba(255,179,0,0.06);
}
@media (max-width: 768px) {
    .premium-card.sticky-top.mb-4 {
        margin-bottom: 1.2rem !important;
        margin-top: 0.7rem !important;
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
    }
    .premium-card .card-body {
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
    }
    .summary-item, .summary-total {
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
    }
    .summary-total {
        margin-top: 0.7rem;
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
    }
}
.premium-card .card-body form {
    margin-top: 0;
}

[data-theme='dark'] .btn-accent.icon-btn-glow {
    background: linear-gradient(135deg, #ff4e50, #f9d423) !important;
    color: #222 !important;
    border: none;
    box-shadow: 0 8px 25px rgba(255, 78, 80, 0.18);
}
[data-theme='dark'] .btn-accent.icon-btn-glow:hover {
    background: linear-gradient(135deg, #f9d423, #ff4e50) !important;
    color: #222 !important;
}

[data-theme='dark'] .order-item {
    background: #232323 !important;
    color: #fff !important;
}
[data-theme='dark'] .order-item .item-name,
[data-theme='dark'] .order-item .item-sku,
[data-theme='dark'] .order-item .item-quantity {
    color: #fff !important;
}
[data-theme='dark'] .order-item .item-price {
    color: #ffd700 !important;
}
[data-theme='dark'] .order-item .item-total {
    color: #ffb300 !important;
}

/* Fallback for missing Font Awesome icons */
.fa-solid, .fa-regular, .fa-light, .fa-thin, .fa-duotone, .fa-brands {
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome', Arial, sans-serif !important;
    font-weight: 900;
}
.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style>
@endsection