

<?php $__env->startSection('content'); ?>
<div class="container" role="main" aria-label="Orders listing main content">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title neon-glow">
                    <i class="fas fa-trophy me-2"></i>My Orders
                </h1>
                <p class="page-subtitle">Track your racing collection orders</p>
            </div>
        </div>
    </div>

    <?php if($orders->count() > 0): ?>
        <div class="orders-container" role="region" aria-label="Order history">
            <div class="card premium-card" role="region" aria-label="Order history table">
                <div class="card-header premium-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>Order History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover premium-table" role="table" aria-label="Order history table">
                            <thead>
                                <tr>
                                    <th class="neon-glow">Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="order-row">
                                        <td>
                                            <strong class="order-number"><?php echo e($order->order_number); ?></strong>
                                        </td>
                                        <td><?php echo e($order->created_at->format('M d, Y')); ?></td>
                                        <td class="order-total">₹<?php echo e(number_format($order->total_amount, 2)); ?></td>
                                        <td>
                                            <span class="badge status-badge status-<?php echo e($order->status); ?>">
                                                <?php echo e(ucfirst($order->status)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge payment-badge payment-<?php echo e($order->payment_status); ?>">
                                                <?php echo e(ucfirst($order->payment_status)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-sm btn-accent icon-btn-glow" aria-label="View order <?php echo e($order->order_number); ?> details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if($order->status == 'pending'): ?>
                                                    <form action="<?php echo e(route('orders.cancel', $order->id)); ?>" method="POST" class="d-inline" aria-label="Cancel order <?php echo e($order->order_number); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger cancel-btn" aria-label="Cancel order <?php echo e($order->order_number); ?>" onclick="return confirm('Are you sure you want to cancel this order?')">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($orders->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h4 class="empty-title">No Orders Yet</h4>
            <p class="empty-subtitle">Start building your racing collection to see your orders here</p>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-accent btn-lg" aria-label="Start shopping - go to products">
                <i class="fas fa-shopping-cart me-2"></i>Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.page-header {
    text-align: center;
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

.orders-container {
    margin-bottom: 3rem;
}

.premium-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.premium-header {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
}

.premium-header h5 {
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}

.premium-table {
    margin: 0;
}

.premium-table th {
    background: var(--table-header-bg);
    color: var(--accent-color);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid var(--accent-color);
    padding: 1rem;
}

.premium-table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
}

.order-row:hover {
    background: var(--hover-bg);
    transform: translateY(-1px);
    transition: all 0.3s ease;
}

.order-number {
    color: var(--accent-color);
    font-weight: 600;
}

.order-total {
    font-weight: 600;
    color: var(--text-primary);
}

.status-badge {
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

.payment-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-paid {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.payment-pending {
    background: linear-gradient(135deg, #FFB300, #FF6A00);
    color: white;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.icon-btn-glow {
    background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
    border: none;
    color: var(--text-primary);
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.icon-btn-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 179, 0, 0.3);
    color: var(--text-primary);
    text-decoration: none;
}

.cancel-btn {
    border: 2px solid var(--danger-color);
    color: var(--danger-color);
    background: transparent;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.cancel-btn:hover {
    background: var(--danger-color);
    color: white;
    transform: translateY(-2px);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--card-bg);
    border-radius: 15px;
    border: 1px solid var(--border-color);
}

.empty-icon {
    font-size: 4rem;
    color: var(--accent-color);
    margin-bottom: 1.5rem;
    text-shadow: 0 0 20px var(--accent-glow);
}

.empty-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.empty-subtitle {
    color: var(--text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
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
    
    .premium-table {
        font-size: 0.9rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .empty-state {
        padding: 2rem 1rem;
    }
}

[data-theme="light"] .premium-card,
[data-theme="light"] .empty-state {
    background: #fff !important;
    border: 1.5px solid #eee !important;
    color: #181818 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
}
[data-theme="light"] .premium-header {
    background: linear-gradient(90deg, #fffbe6 0%, #FFB300 100%) !important;
    color: #FF6A00 !important;
    border-bottom: 2px solid #FF6A00 !important;
}
[data-theme="light"] .premium-table th {
    background: #fffbe6 !important;
    color: #FF6A00 !important;
    border-bottom: 2px solid #FFB300 !important;
}
[data-theme="light"] .premium-table td {
    background: #fff !important;
    color: #181818 !important;
    border-bottom: 1px solid #eee !important;
}
[data-theme="light"] .order-row:hover {
    background: #fffbe6 !important;
}
[data-theme="light"] .order-number {
    color: #FF6A00 !important;
}
[data-theme="light"] .order-total {
    color: #181818 !important;
}
[data-theme="light"] .status-badge,
[data-theme="light"] .payment-badge {
    color: #fff !important;
    box-shadow: none !important;
}
[data-theme="light"] .icon-btn-glow,
[data-theme="light"] .icon-btn-glow *,
[data-theme="light"] .icon-btn-glow i {
    color: #fff !important;
    text-shadow: 0 0 2px #FF6A00, 0 0 4px #FFB300;
}
[data-theme="light"] .icon-btn-glow[disabled],
[data-theme="light"] .icon-btn-glow.disabled,
[data-theme="light"] .icon-btn-glow:disabled {
    background: #eee !important;
    color: #aaa !important;
    border: 2px solid #ccc !important;
    opacity: 0.7 !important;
    pointer-events: none !important;
}
[data-theme="light"] .icon-btn-glow[disabled] i,
[data-theme="light"] .icon-btn-glow.disabled i,
[data-theme="light"] .icon-btn-glow:disabled i {
    color: #aaa !important;
    text-shadow: none !important;
}
[data-theme="light"] .cancel-btn {
    border: 2px solid #dc3545 !important;
    color: #dc3545 !important;
    background: transparent !important;
    font-weight: 700 !important;
}
[data-theme="light"] .cancel-btn i {
    color: #dc3545 !important;
    font-weight: 900 !important;
}
[data-theme="light"] .cancel-btn:hover {
    background: #dc3545 !important;
    color: #fff !important;
}
[data-theme="light"] .premium-table td,
[data-theme="light"] .premium-table th {
    border-bottom: 1.5px solid #e0e0e0 !important;
}
[data-theme="light"] .order-row:hover {
    background: #fffbe6 !important;
    filter: brightness(0.98);
}
[data-theme="light"] .order-number {
    color: #FF6A00 !important;
    font-weight: 700 !important;
}
[data-theme="light"] .neon-glow {
    color: #FF6A00 !important;
    text-shadow: none !important;
}
/* --- Light Mode Button and Table Fixes --- */
[data-theme="light"] .icon-btn-glow {
    background: #FF6A00 !important;
    background-image: none !important;
    border: 2px solid #FFB300 !important;
    color: #fff !important;
    box-shadow: 0 2px 8px #FFB30033 !important;
    opacity: 1 !important;
    filter: none !important;
    pointer-events: auto !important;
    display: inline-flex !important;
    align-items: center !important;
}
[data-theme="light"] .icon-btn-glow *,
[data-theme="light"] .icon-btn-glow i {
    color: #fff !important;
    text-shadow: 0 0 2px #FF6A00, 0 0 4px #FFB300;
}
[data-theme="light"] .icon-btn-glow[disabled],
[data-theme="light"] .icon-btn-glow.disabled,
[data-theme="light"] .icon-btn-glow:disabled {
    background: #eee !important;
    color: #aaa !important;
    border: 2px solid #ccc !important;
    opacity: 0.7 !important;
    pointer-events: none !important;
}
[data-theme="light"] .icon-btn-glow[disabled] i,
[data-theme="light"] .icon-btn-glow.disabled i,
[data-theme="light"] .icon-btn-glow:disabled i {
    color: #aaa !important;
    text-shadow: none !important;
}

[data-theme="dark"] .icon-btn-glow {
    background: transparent !important;
    background-image: none !important;
    border: 2px solid var(--accent-color, #FFB300) !important;
    color: var(--accent-color, #FFB300) !important;
    box-shadow: none !important;
}

.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/orders/index.blade.php ENDPATH**/ ?>