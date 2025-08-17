<?php $__env->startSection('title', 'Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" role="main" aria-label="Admin orders listing main content">
    <div class="row">
        <div class="col-12">
            <div class="card" role="region" aria-label="Orders management">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Orders</h3>
                        <div>
                            <a href="<?php echo e(route('admin.orders.export')); ?>" class="btn btn-success" aria-label="Export orders">
                                <i class="fas fa-download"></i> Export
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible" role="alert" aria-label="Success message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search" placeholder="Search orders..." aria-label="Search orders">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="status-filter" aria-label="Filter by order status">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-from" placeholder="From Date" aria-label="Filter from date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-to" placeholder="To Date" aria-label="Filter to date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clear-filters" aria-label="Clear all filters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="orders-table" role="table" aria-label="Orders table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo e($order->id); ?></strong>
                                            <?php if($order->tracking_number): ?>
                                                <br><small class="text-muted">Track: <?php echo e($order->tracking_number); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo e($order->user->name ?? 'Guest'); ?></strong>
                                                <?php if($order->user): ?>
                                                    <br><small class="text-muted"><?php echo e($order->user->email); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?php echo e($order->items->count()); ?> items</span>
                                            <?php $__currentLoopData = $order->items->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <br><small><?php echo e($item->product->name ?? 'Product'); ?> (<?php echo e($item->quantity); ?>)</small>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($order->items->count() > 2): ?>
                                                <br><small class="text-muted">+<?php echo e($order->items->count() - 2); ?> more</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>₹<?php echo e(number_format($order->total_amount, 2)); ?></strong>
                                                <?php if($order->shipping_cost > 0): ?>
                                                    <br><small class="text-muted">+₹<?php echo e(number_format($order->shipping_cost, 2)); ?> shipping</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
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
                                            <span class="badge badge-<?php echo e($color); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                                        </td>
                                        <td>
                                            <?php if($order->payment): ?>
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
                                                <br><small class="text-muted"><?php echo e(ucfirst($order->payment->payment_method)); ?></small>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">No Payment</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo e($order->created_at->format('M d, Y')); ?></strong>
                                                <br><small class="text-muted"><?php echo e($order->created_at->format('H:i')); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details" aria-label="View order #<?php echo e($order->id); ?> details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.orders.edit', $order->id)); ?>" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Edit Order" aria-label="Edit order #<?php echo e($order->id); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Print Invoice" aria-label="Print invoice for order #<?php echo e($order->id); ?>" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.orders.invoice.tcpdf', $order->id)); ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Download Swiggy-Style Invoice" aria-label="Download Swiggy-style invoice for order #<?php echo e($order->id); ?>" target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <?php if(in_array($order->status, ['pending', 'processing'])): ?>
                                                    <form action="<?php echo e(route('admin.orders.cancel', $order->id)); ?>" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to cancel this order?')" aria-label="Cancel order #<?php echo e($order->id); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Cancel Order" aria-label="Cancel order #<?php echo e($order->id); ?>">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        <?php echo e($orders->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Search functionality
        $('#search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#orders-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Status filter
        $('#status-filter').change(function() {
            const status = $(this).val().toLowerCase();
            if (status) {
                $('#orders-table tbody tr').hide();
                $('#orders-table tbody tr').each(function() {
                    const rowStatus = $(this).find('td:nth-child(5) .badge').text().toLowerCase();
                    if (rowStatus.includes(status)) {
                        $(this).show();
                    }
                });
            } else {
                $('#orders-table tbody tr').show();
            }
        });

        // Date filter
        function filterByDate() {
            const fromDate = $('#date-from').val();
            const toDate = $('#date-to').val();
            
            if (fromDate || toDate) {
                $('#orders-table tbody tr').hide();
                $('#orders-table tbody tr').each(function() {
                    const orderDate = $(this).find('td:nth-child(7) strong').text();
                    const date = new Date(orderDate);
                    
                    let show = true;
                    if (fromDate) {
                        const from = new Date(fromDate);
                        if (date < from) show = false;
                    }
                    if (toDate) {
                        const to = new Date(toDate);
                        if (date > to) show = false;
                    }
                    
                    if (show) {
                        $(this).show();
                    }
                });
            } else {
                $('#orders-table tbody tr').show();
            }
        }

        $('#date-from, #date-to').change(filterByDate);

        // Clear filters
        $('#clear-filters').click(function() {
            $('#search').val('');
            $('#status-filter').val('');
            $('#date-from').val('');
            $('#date-to').val('');
            $('#orders-table tbody tr').show();
        });
    });
</script>
<?php $__env->stopPush(); ?> 

<style>
.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style> 
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>