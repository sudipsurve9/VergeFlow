<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4" role="main" aria-label="Order details main content">
    <!-- Enhanced Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="order-header-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="header-info">
                        <div class="d-flex align-items-center mb-2">
                            <div class="header-icon-wrapper me-3">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h1 class="order-title mb-1">Order Details</h1>
                                <div class="order-number-badge">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Order #<?php echo e($order->order_number ?? 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?>

                                </div>
                            </div>
                        </div>
                        <div class="order-meta d-flex align-items-center gap-3">
                            <span class="meta-item">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo e($order->created_at->format('M d, Y')); ?>

                            </span>
                            <span class="meta-item">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo e($order->created_at->format('g:i A')); ?>

                            </span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('orders.index')); ?>" class="btn-back-modern">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Enhanced Order Information -->
            <div class="modern-card order-info-card mb-4" role="region" aria-label="Order information">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-title-modern">Order Information</h3>
                </div>
                <div class="card-body-modern">
                    <div class="info-grid-modern">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <div class="info-content">
                                <label class="info-label">Order Number</label>
                                <div class="info-value"><?php echo e($order->order_number ?? 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon payment-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="info-content">
                                <label class="info-label">Payment Method</label>
                                <div class="info-value"><?php echo e($order->payment_method ? ucfirst(str_replace(['_', 'cod'], [' ', 'Cash on Delivery'], strtolower($order->payment_method))) : 'Not specified'); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon date-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <label class="info-label">Order Date</label>
                                <div class="info-value"><?php echo e($order->created_at->format('F d, Y \a\t g:i A')); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon status-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="info-content">
                                <label class="info-label">Payment Status</label>
                                <div class="info-value">
                                    <span class="status-badge payment-<?php echo e($order->payment_status ?? 'pending'); ?>"><?php echo e($order->payment_status ? ucfirst($order->payment_status) : 'Pending'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon order-status-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="info-content">
                                <label class="info-label">Order Status</label>
                                <div class="info-value">
                                    <span class="status-badge order-<?php echo e($order->status ?? 'pending'); ?>"><?php echo e($order->status ? ucfirst($order->status) : 'Pending'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Order Items -->
            <div class="modern-card order-items-card mb-4" role="region" aria-label="Order items">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="card-title-modern">Order Items</h3>
                    <div class="items-count-badge"><?php echo e($order->items->count()); ?> <?php echo e($order->items->count() == 1 ? 'Item' : 'Items'); ?></div>
                </div>
                <div class="card-body-modern">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="order-item-modern">
                            <div class="item-image-wrapper">
                                <?php if($item->product && $item->product->image): ?>
                                    <?php
                                        $imagePath = $item->product->image;
                                        // Try different image path formats
                                        $possiblePaths = [
                                            'storage/' . $imagePath,
                                            'storage/products/' . basename($imagePath),
                                            'images/' . basename($imagePath)
                                        ];
                                        $imageUrl = asset('storage/' . $imagePath);
                                    ?>
                                    <img src="<?php echo e($imageUrl); ?>" 
                                         alt="<?php echo e($item->product->name); ?>" 
                                         class="item-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="item-placeholder" style="display: none;">
                                        <i class="fas fa-cube"></i>
                                        <small class="mt-1"><?php echo e($item->product->name); ?></small>
                                    </div>
                                <?php else: ?>
                                    <div class="item-placeholder">
                                        <i class="fas fa-cube"></i>
                                        <small class="mt-1"><?php echo e($item->product ? $item->product->name : 'Product'); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-details-modern">
                                <h4 class="item-name-modern"><?php echo e($item->product ? $item->product->name : 'Product not found'); ?></h4>
                                <div class="item-meta">
                                    <span class="meta-badge">
                                        <i class="fas fa-barcode me-1"></i>
                                        <?php echo e($item->product ? $item->product->sku : 'N/A'); ?>

                                    </span>
                                    <span class="meta-badge quantity-badge">
                                        <i class="fas fa-times me-1"></i>
                                        Qty: <?php echo e($item->quantity); ?>

                                    </span>
                                </div>
                            </div>
                            
                            <div class="item-pricing-modern">
                                <div class="unit-price">
                                    <span class="price-label">Unit Price</span>
                                    <span class="price-value">
                                        <i class="fas fa-rupee-sign"></i><?php echo e(number_format($item->price, 2)); ?>

                                    </span>
                                </div>
                                <div class="total-price">
                                    <span class="price-label">Total</span>
                                    <span class="price-value-total">
                                        <i class="fas fa-rupee-sign"></i><?php echo e(number_format($item->total ?? ($item->price * $item->quantity), 2)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Enhanced Shipping Information -->
            <div class="modern-card shipping-card mb-4" role="region" aria-label="Shipping information">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="card-title-modern">Shipping Information</h3>
                </div>
                <div class="card-body-modern">
                    <div class="shipping-address-wrapper">
                        <div class="address-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="address-content">
                            <label class="address-label">Delivery Address</label>
                            <div class="address-details">
                                <?php if($order->shippingAddress): ?>
                                    <div class="address-line"><?php echo e($order->shippingAddress->address_line_1); ?></div>
                                    <?php if($order->shippingAddress->address_line_2): ?>
                                        <div class="address-line"><?php echo e($order->shippingAddress->address_line_2); ?></div>
                                    <?php endif; ?>
                                    <div class="address-line"><?php echo e($order->shippingAddress->city); ?>, <?php echo e($order->shippingAddress->state); ?> <?php echo e($order->shippingAddress->postal_code); ?></div>
                                    <div class="address-line country"><?php echo e($order->shippingAddress->country); ?></div>
                                <?php else: ?>
                                    <div class="address-line no-address"><?php echo e($order->shipping_address ?? 'No shipping address provided'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($order->notes): ?>
                    <div class="order-notes-wrapper mt-3">
                        <div class="notes-icon-wrapper">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <div class="notes-content">
                            <label class="notes-label">Order Notes</label>
                            <div class="notes-text"><?php echo e($order->notes); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Status Timeline -->
            <?php if($order->statusHistories && $order->statusHistories->count() > 0): ?>
            <div class="premium-card mb-4" role="region" aria-label="Order status timeline">
                <div class="card-header premium-header bg-accent text-white d-flex align-items-center">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-clock me-2"></i>Order Status Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php $__currentLoopData = $order->statusHistories->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-status"><?php echo e(ucfirst($history->status)); ?></div>
                                <?php if($history->comment): ?>
                                <div class="timeline-comment"><?php echo e($history->comment); ?></div>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="fa-solid fa-calendar me-1"></i><?php echo e($history->created_at->format('M d, Y \a\t g:i A')); ?>

                                </small>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Enhanced Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="modern-card summary-card mb-4" role="region" aria-label="Order summary">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="card-title-modern">Order Summary</h3>
                </div>
                <div class="card-body-modern">
                    <div class="summary-breakdown">
                        <div class="summary-line">
                            <div class="summary-label">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Subtotal
                            </div>
                            <div class="summary-value">
                                <i class="fas fa-rupee-sign me-1"></i><?php echo e(number_format($order->subtotal ?? $order->items->sum(function($item) { return $item->total ?? ($item->price * $item->quantity); }), 2)); ?>

                            </div>
                        </div>
                        
                        <div class="summary-line">
                            <div class="summary-label">
                                <i class="fas fa-truck me-2"></i>
                                Shipping
                            </div>
                            <div class="summary-value">
                                <i class="fas fa-rupee-sign me-1"></i><?php echo e(number_format($order->shipping_amount ?? 0, 2)); ?>

                            </div>
                        </div>
                        
                        <div class="summary-line">
                            <div class="summary-label">
                                <i class="fas fa-percentage me-2"></i>
                                Tax
                            </div>
                            <div class="summary-value">
                                <i class="fas fa-rupee-sign me-1"></i><?php echo e(number_format($order->tax_amount ?? 0, 2)); ?>

                            </div>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-total-line">
                            <div class="total-label">
                                <i class="fas fa-receipt me-2"></i>
                                Total Amount
                            </div>
                            <div class="total-value">
                                <i class="fas fa-rupee-sign me-1"></i><?php echo e(number_format($order->total_amount ?? ($order->subtotal ?? $order->items->sum(function($item) { return $item->total ?? ($item->price * $item->quantity); })) + ($order->shipping_amount ?? 0) + ($order->tax_amount ?? 0), 2)); ?>

                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <?php if($order->status === 'pending' || $order->status === 'processing'): ?>
                        <div class="action-buttons mt-4">
                            <a href="<?php echo e(route('user.orders.invoice.tcpdf', $order->id)); ?>" class="btn-invoice-modern mb-3" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>
                                Download Invoice
                            </a>
                            <button type="button" class="btn-cancel-modern" onclick="if(confirm('Are you sure you want to cancel this order?')) { alert('Order cancellation functionality will be implemented.'); }">
                                <i class="fas fa-times-circle me-2"></i>
                                Cancel Order
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Order Status Indicator -->
                    <div class="status-indicator-wrapper mt-4">
                        <div class="status-indicator status-<?php echo e($order->status ?? 'pending'); ?>">
                            <div class="status-icon">
                                <?php if($order->status === 'delivered'): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php elseif($order->status === 'shipped'): ?>
                                    <i class="fas fa-shipping-fast"></i>
                                <?php elseif($order->status === 'processing'): ?>
                                    <i class="fas fa-cog fa-spin"></i>
                                <?php else: ?>
                                    <i class="fas fa-clock"></i>
                                <?php endif; ?>
                            </div>
                            <div class="status-text">
                                <div class="status-title"><?php echo e(ucfirst($order->status ?? 'pending')); ?></div>
                                <div class="status-subtitle">Order Status</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Order Details Styling */
:root {
    --primary-bg: #181818;
    --secondary-bg: #232323;
    --card-bg: #232323;
    --text-primary: #fff;
    --text-secondary: #bbb;
    --accent-color: #ffb300;
    --accent-glow: #ff6a00;
    --border-color: #333;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --border-radius: 0.5rem;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
    --glow-effect: 0 0 15px rgba(255, 179, 0, 0.3);
}

body {
    font-family: 'Montserrat', Arial, sans-serif;
    background: radial-gradient(ellipse at 50% 20%, #ff9900 0%, #2d1a06 80%, #18120a 100%);
    min-height: 100vh;
    color: var(--text-primary);
}

* {
    font-family: 'Montserrat', Arial, sans-serif;
}

/* Enhanced Header Styling */
.order-header-card {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-glow));
    border-radius: 20px;
    padding: 2rem;
    color: #000;
    box-shadow: var(--shadow-xl), var(--glow-effect);
    margin-bottom: 2rem;
    font-family: 'Orbitron', sans-serif;
}

.header-icon-wrapper {
    width: 60px;
    height: 60px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.header-icon-wrapper i {
    font-size: 24px;
    color: white;
}

.order-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.order-number-badge {
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    display: inline-flex;
    align-items: center;
}

.order-meta {
    margin-top: 1rem;
}

.meta-item {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.85rem;
    backdrop-filter: blur(10px);
    display: inline-flex;
    align-items: center;
}

.btn-back-modern {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 15px;
    text-decoration: none;
    font-weight: 600;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-back-modern:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Modern Card Styling */
.modern-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md), var(--glow-effect);
    overflow: hidden;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.modern-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-header-modern {
    background: linear-gradient(135deg, var(--primary-bg), var(--secondary-bg));
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-glow));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.card-title-modern {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    flex-grow: 1;
}

.items-count-badge {
    background: var(--accent-color);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.card-body-modern {
    padding: 1.5rem;
}

/* Order Information Grid */
.info-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: var(--card-bg);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: var(--secondary-bg);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md), var(--glow-effect);
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.info-icon {
    background: linear-gradient(135deg, var(--info-color), #0e7490);
}

.payment-icon {
    background: linear-gradient(135deg, var(--success-color), #047857) !important;
}

.date-icon {
    background: linear-gradient(135deg, var(--warning-color), #b45309) !important;
}

.status-icon {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-glow)) !important;
    color: #000 !important;
}

.order-status-icon {
    background: linear-gradient(135deg, var(--info-color), #0e7490) !important;
}

.info-content {
    flex-grow: 1;
}

.info-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.payment-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.payment-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.order-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.order-processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.order-shipped {
    background: #e0e7ff;
    color: #3730a3;
}

.status-badge.order-delivered {
    background: #d1fae5;
    color: #065f46;
}

/* Order Items Styling */
.order-item-modern {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: var(--card-bg);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.order-item-modern:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.order-item-modern:last-child {
    margin-bottom: 0;
}

.item-image-wrapper {
    flex-shrink: 0;
}

.item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
}

.item-placeholder {
    width: 80px;
    height: 80px;
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--accent-color);
    font-size: 1.5rem;
    text-align: center;
    padding: 0.5rem;
}

.item-placeholder small {
    color: var(--text-secondary);
    font-size: 0.7rem;
    margin-top: 0.25rem;
    line-height: 1;
}

.item-details-modern {
    flex-grow: 1;
}

.item-name-modern {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.item-meta {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.meta-badge {
    background: var(--accent-color);
    color: white;
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.quantity-badge {
    background: var(--success-color) !important;
}

.item-pricing-modern {
    text-align: right;
    flex-shrink: 0;
}

.unit-price, .total-price {
    margin-bottom: 0.5rem;
}

.price-label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.price-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
}

.price-value-total {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--success-color);
}

/* Shipping Information */
.shipping-address-wrapper {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.address-icon-wrapper {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--info-color), #0e7490);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.address-ico.info-icon {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-glow));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-size: 0.9rem;
    flex-shrink: 0;
    box-shadow: var(--glow-effect);
}

.address-content {
    flex-grow: 1;
}

.address-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: block;
}

.address-details {
    background: var(--card-bg);
    padding: 1rem;
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.address-line {
    color: var(--text-primary);
    font-weight: 500;
    line-height: 1.5;
    margin-bottom: 0.25rem;
}

.address-line:last-child {
    margin-bottom: 0;
}

.address-line.country {
    font-weight: 600;
    color: var(--accent-color);
}

.address-line.no-address {
    color: var(--text-muted);
    font-style: italic;
}

.order-notes-wrapper {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.notes-icon-wrapper {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--warning-color), #b45309);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.notes-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: block;
}

.notes-text {
    background: var(--card-bg);
    padding: 1rem;
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    font-weight: 500;
    line-height: 1.5;
}

/* Order Summary Styling */
.summary-breakdown {
    margin-bottom: 1.5rem;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.summary-line:last-of-type {
    border-bottom: none;
}

.summary-label {
    font-weight: 600;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
}

.summary-value {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 1rem;
}

.summary-divider {
    height: 2px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-glow));
    border-radius: 1px;
    margin: 1rem 0;
}

.summary-total-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, var(--card-bg), var(--secondary-bg));
    border-radius: var(--border-radius);
    border: 2px solid var(--accent-color);
}

.total-label {
    font-weight: 700;
    color: var(--accent-color);
    font-size: 1.1rem;
    display: flex;
    align-items: center;
}

.total-value {
    font-weight: 800;
    color: var(--success-color);
    font-size: 1.4rem;
}

/* Action Buttons */
.btn-invoice-modern {
    width: 100%;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-glow));
    border: none;
    color: #000;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
}

.btn-invoice-modern:hover {
    background: linear-gradient(135deg, var(--accent-glow), var(--accent-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
    color: #000;
    text-decoration: none;
}

.btn-cancel-modern {
    width: 100%;
    background: linear-gradient(135deg, var(--danger-color), #b91c1c);
    border: none;
    color: #000;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
}

.btn-cancel-modern:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
}

/* Status Indicator */
.status-indicator-wrapper {
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 15px;
    border: 2px solid;
}

.status-indicator.status-pending {
    background: #fef3c7;
    border-color: var(--warning-color);
}

.status-indicator.status-processing {
    background: #dbeafe;
    border-color: var(--primary-color);
}

.status-indicator.status-shipped {
    background: #e0e7ff;
    border-color: #6366f1;
}

.status-indicator.status-delivered {
    background: #d1fae5;
    border-color: var(--success-color);
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.status-pending .status-icon {
    background: var(--warning-color);
}

.status-processing .status-icon {
    background: var(--primary-color);
}

.status-shipped .status-icon {
    background: #6366f1;
}

.status-delivered .status-icon {
    background: var(--success-color);
}

.status-text {
    flex-grow: 1;
}

.status-title {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.status-subtitle {
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 600;
}

/* Timeline Styling */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    background: var(--primary-color);
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px var(--primary-color);
    z-index: 2;
}

.timeline-content {
    margin-left: 10px;
    background: var(--light-bg);
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.timeline-status {
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.timeline-comment {
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

/* Layout Improvements */
.row {
    margin-left: 0;
    margin-right: 0;
}

.col-lg-8, .col-lg-4 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

/* Summary Card Positioning */
.summary-card {
    position: relative;
    z-index: 1;
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .col-lg-4 {
        margin-top: 2rem;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .modern-card {
        margin-bottom: 1rem;
    }
    
    .card-header-modern {
        padding: 1rem;
    }
    
    .card-body-modern {
        padding: 1rem;
    }
    
    .info-grid-modern {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .item-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .summary-breakdown {
        font-size: 0.9rem;
    }
    
    .col-lg-8, .col-lg-4 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .order-header-card {
        padding: 1.5rem;
    }
    
    .header-icon-wrapper {
        width: 50px;
        height: 50px;
    }
    
    .order-title {
        font-size: 1.5rem;
    }
    
    .order-item-modern {
        flex-direction: column;
        text-align: center;
    }
    
    .item-pricing-modern {
        text-align: center;
        width: 100%;
    }
    
    .shipping-address-wrapper {
        flex-direction: column;
    }
    
    .order-notes-wrapper {
        flex-direction: column;
    }
}
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
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/orders/show_clean.blade.php ENDPATH**/ ?>