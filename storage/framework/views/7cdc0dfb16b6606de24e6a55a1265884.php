<?php $__env->startSection('title', 'Order #' . $order->id); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0">Order #<?php echo e($order->id); ?></h3>
                            <small class="text-muted">Placed on <?php echo e($order->created_at->format('F d, Y \a\t H:i')); ?></small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>
                            <a href="<?php echo e(route('admin.orders.edit', $order->id)); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Order
                            </a>
                            <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" class="btn btn-success" target="_blank">
                                <i class="fas fa-print"></i> Print Invoice
                            </a>
                            <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" class="btn btn-info" target="_blank">
                                <i class="fas fa-download"></i> Download Swiggy-Style Invoice
                            </a>
                            <form action="<?php echo e(route('admin.orders.shiprocket.place', $order->id)); ?>" method="POST" class="d-inline-block" onsubmit="return confirm('Place this order on Shiprocket?');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-rocket"></i> Place Order on Shiprocket
                                </button>
                            </form>
                            <button type="button" class="btn btn-info" id="check-couriers-btn">
                                <i class="fas fa-shipping-fast"></i> Check Available Couriers
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Details -->
                <div class="col-lg-8">
                    <!-- Order Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Status</h5>
                        </div>
                        <div class="card-body">
                            <?php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    'refunded' => 'secondary'
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            ?>
                            
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-<?php echo e($color); ?> badge-lg mr-3"><?php echo e(ucfirst($order->status)); ?></span>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateStatusModal">
                                    <i class="fas fa-edit"></i> Update Status
                                </button>
                            </div>

                            <?php if($order->tracking_number): ?>
                                <div class="mb-3">
                                    <strong>Tracking Number:</strong> <?php echo e($order->tracking_number); ?>

                                    <?php if($order->tracking_url): ?>
                                        <a href="<?php echo e($order->tracking_url); ?>" target="_blank" class="btn btn-sm btn-info ml-2">
                                            <i class="fas fa-external-link-alt"></i> Track Package
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if($order->notes): ?>
                                <div class="mb-3">
                                    <strong>Notes:</strong> <?php echo e($order->notes); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if($item->product && $item->product->images): ?>
                                                            <?php
                                                                $images = json_decode($item->product->images, true);
                                                                $firstImage = $images[0] ?? null;
                                                            ?>
                                                            <?php if($firstImage): ?>
                                                                <img src="<?php echo e(asset('storage/' . $firstImage)); ?>" 
                                                                     alt="<?php echo e($item->product->name); ?>" 
                                                                     class="img-thumbnail mr-3" 
                                                                     style="max-width: 50px; max-height: 50px;">
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?php echo e($item->product->name ?? 'Product'); ?></strong>
                                                            <?php if($item->product && $item->product->sku): ?>
                                                                <br><small class="text-muted">SKU: <?php echo e($item->product->sku); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>₹<?php echo e(number_format($item->price, 2)); ?></td>
                                                <td><?php echo e($item->quantity); ?></td>
                                                <td>₹<?php echo e(number_format($item->price * $item->quantity, 2)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                            <td>₹<?php echo e(number_format($order->subtotal_amount, 2)); ?></td>
                                        </tr>
                                        <?php if($order->shipping_cost > 0): ?>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Shipping:</strong></td>
                                                <td>₹<?php echo e(number_format($order->shipping_cost, 2)); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if($order->tax_amount > 0): ?>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Tax:</strong></td>
                                                <td>₹<?php echo e(number_format($order->tax_amount, 2)); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if($order->discount_amount > 0): ?>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Discount:</strong></td>
                                                <td>-₹<?php echo e(number_format($order->discount_amount, 2)); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr class="table-active">
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td><strong>₹<?php echo e(number_format($order->total_amount, 2)); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Status History</h5>
                        </div>
                        <div class="card-body">
                            <?php if($order->statusHistory->count() > 0): ?>
                                <div class="timeline">
                                    <?php $__currentLoopData = $order->statusHistory->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong><?php echo e(ucfirst($history->status)); ?></strong>
                                                        <?php if($history->notes): ?>
                                                            <br><small class="text-muted"><?php echo e($history->notes); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted"><?php echo e($history->created_at->format('M d, Y H:i')); ?></small>
                                                </div>
                                                <?php if($history->user): ?>
                                                    <small class="text-muted">Updated by: <?php echo e($history->user->name); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No status history available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if($order->user && $order->user->customer): ?>
                                <div class="mb-3">
                                    <strong>Name:</strong> <?php echo e($order->user->name); ?>

                                </div>
                                <div class="mb-3">
                                    <strong>Email:</strong> <?php echo e($order->user->email); ?>

                                </div>
                                <?php if($order->user->customer && $order->user->customer->phone): ?>
                                    <div class="mb-3">
                                        <strong>Phone:</strong> <?php echo e($order->user->customer->phone); ?>

                                    </div>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.customers.show', $order->user->customer)); ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-user"></i> View Customer
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No customer profile</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <?php if($order->shipping_address): ?>
                                <?php
                                    $shippingAddress = is_string($order->shipping_address) ? json_decode($order->shipping_address, true) : $order->shipping_address;
                                ?>
                                <?php if(is_array($shippingAddress)): ?>
                                    <div class="mb-2">
                                        <strong><?php echo e($shippingAddress['first_name'] ?? ''); ?> <?php echo e($shippingAddress['last_name'] ?? ''); ?></strong>
                                    </div>
                                    <div class="mb-2">
                                        <?php echo e($shippingAddress['address_line_1'] ?? $shippingAddress['address'] ?? ''); ?>

                                        <?php if(!empty($shippingAddress['address_line_2'])): ?>
                                            <br><?php echo e($shippingAddress['address_line_2']); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-2">
                                        <?php echo e($shippingAddress['city'] ?? ''); ?><?php if(!empty($shippingAddress['state'])): ?>, <?php echo e($shippingAddress['state']); ?><?php endif; ?> <?php if(!empty($shippingAddress['postal_code'])): ?><?php echo e($shippingAddress['postal_code']); ?><?php endif; ?>
                                    </div>
                                    <?php if(!empty($shippingAddress['country'])): ?>
                                        <div class="mb-2">
                                            <?php echo e($shippingAddress['country']); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($shippingAddress['phone'])): ?>
                                        <div class="mb-2">
                                            <strong>Phone:</strong> <?php echo e($shippingAddress['phone']); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="mb-2"><?php echo e($order->shipping_address); ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">No shipping address provided</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <?php if($order->billing_address): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Billing Address</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                    $billingAddress = is_string($order->billing_address) ? json_decode($order->billing_address, true) : $order->billing_address;
                                ?>
                                <?php if(is_array($billingAddress)): ?>
                                    <div class="mb-2">
                                        <strong><?php echo e($billingAddress['first_name'] ?? ''); ?> <?php echo e($billingAddress['last_name'] ?? ''); ?></strong>
                                    </div>
                                    <div class="mb-2">
                                        <?php echo e($billingAddress['address_line_1'] ?? $billingAddress['address'] ?? ''); ?>

                                        <?php if(!empty($billingAddress['address_line_2'])): ?>
                                            <br><?php echo e($billingAddress['address_line_2']); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-2">
                                        <?php echo e($billingAddress['city'] ?? ''); ?><?php if(!empty($billingAddress['state'])): ?>, <?php echo e($billingAddress['state']); ?><?php endif; ?> <?php if(!empty($billingAddress['postal_code'])): ?><?php echo e($billingAddress['postal_code']); ?><?php endif; ?>
                                    </div>
                                    <?php if(!empty($billingAddress['country'])): ?>
                                        <div class="mb-2">
                                            <?php echo e($billingAddress['country']); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($billingAddress['phone'])): ?>
                                        <div class="mb-2">
                                            <strong>Phone:</strong> <?php echo e($billingAddress['phone']); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="mb-2"><?php echo e($order->billing_address); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Payment Information -->
                    <?php if($order->payment): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Method:</strong> <?php echo e(ucfirst($order->payment->payment_method)); ?>

                                </div>
                                <div class="mb-2">
                                    <strong>Status:</strong> 
                                    <?php
                                        $paymentColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                            'refunded' => 'secondary',
                                            'cancelled' => 'danger'
                                        ];
                                        $paymentColor = $paymentColors[$order->payment->status] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?php echo e($paymentColor); ?>"><?php echo e(ucfirst($order->payment->status)); ?></span>
                                </div>
                                <div class="mb-2">
                                    <strong>Amount:</strong> ₹<?php echo e(number_format($order->payment->amount, 2)); ?>

                                </div>
                                <?php if($order->payment->transaction_id): ?>
                                    <div class="mb-2">
                                        <strong>Transaction ID:</strong> <?php echo e($order->payment->transaction_id); ?>

                                    </div>
                                <?php endif; ?>
                                <?php if($order->payment->paid_at): ?>
                                    <div class="mb-2">
                                        <strong>Paid At:</strong> <?php echo e($order->payment->paid_at->format('M d, Y H:i')); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <?php if(in_array($order->status, ['pending', 'processing'])): ?>
                                <form action="<?php echo e(route('admin.orders.cancel', $order->id)); ?>" 
                                      method="POST" 
                                      class="mb-2"
                                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if($order->status === 'delivered'): ?>
                                <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#refundModal">
                                    <i class="fas fa-undo"></i> Process Refund
                                </button>
                            <?php endif; ?>

                            <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" class="btn btn-success btn-block" target="_blank">
                                <i class="fas fa-print"></i> Print Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.orders.update-status', $order->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending" <?php echo e($order->status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="processing" <?php echo e($order->status === 'processing' ? 'selected' : ''); ?>>Processing</option>
                            <option value="shipped" <?php echo e($order->status === 'shipped' ? 'selected' : ''); ?>>Shipped</option>
                            <option value="delivered" <?php echo e($order->status === 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                            <option value="cancelled" <?php echo e($order->status === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            <option value="refunded" <?php echo e($order->status === 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tracking_number">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" value="<?php echo e($order->tracking_number); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tracking_url">Tracking URL</label>
                        <input type="url" class="form-control" name="tracking_url" value="<?php echo e($order->tracking_url); ?>">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<?php if($order->status === 'delivered'): ?>
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.orders.refund', $order->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refund_amount">Refund Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₹</span>
                            </div>
                            <input type="number" class="form-control" name="refund_amount" 
                                   value="<?php echo e($order->total_amount); ?>" 
                                   step="0.01" 
                                   min="0" 
                                   max="<?php echo e($order->total_amount); ?>" 
                                   required>
                        </div>
                        <small class="form-text text-muted">Maximum refund amount: ₹<?php echo e(number_format($order->total_amount, 2)); ?></small>
                    </div>
                    <div class="form-group">
                        <label for="refund_reason">Refund Reason</label>
                        <textarea class="form-control" name="refund_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Courier Modal -->
<div class="modal fade" id="courierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Available Couriers</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="courier-modal-body">
                <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 17px;
    width: 2px;
    height: calc(100% + 3px);
    background: #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    document.getElementById('check-couriers-btn').addEventListener('click', function() {
        $('#courierModal').modal('show');
        const body = document.getElementById('courier-modal-body');
        body.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        fetch("<?php echo e(route('admin.orders.shiprocket.couriers', $order->id)); ?>", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.couriers && res.couriers.length > 0) {
                let html = '<table class="table table-bordered"><thead><tr><th>Courier</th><th>Rate</th><th>Delivery Days</th><th>COD</th></tr></thead><tbody>';
                res.couriers.forEach(courier => {
                    html += `<tr><td>${courier.courier_name}</td><td>₹${courier.rate}</td><td>${courier.etd}</td><td>${courier.cod ? 'Yes' : 'No'}</td></tr>`;
                });
                html += '</tbody></table>';
                body.innerHTML = html;
            } else {
                body.innerHTML = '<div class="alert alert-warning">' + (res.message || 'No couriers available.') + '</div>';
            }
        })
        .catch(() => {
            body.innerHTML = '<div class="alert alert-danger">Failed to fetch courier companies.</div>';
        });
    });
</script>
<?php $__env->stopPush(); ?> 

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>