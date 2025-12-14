<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" role="main" aria-label="Admin products listing main content">
    <div class="row">
        <div class="col-12">
            <div class="card" role="region" aria-label="Products management">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Products</h3>
                        <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary" aria-label="Add new product">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
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
                            <input type="text" class="form-control" id="search" placeholder="Search products..." aria-label="Search products">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="category-filter" aria-label="Filter by category">
                                <option value="">All Categories</option>
                                <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="status-filter" aria-label="Filter by status">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="featured-filter" aria-label="Filter by featured status">
                                <option value="">All Products</option>
                                <option value="1">Featured</option>
                                <option value="0">Regular</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clear-filters" aria-label="Clear all filters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form id="bulk-action-form" action="<?php echo e(route('admin.products.bulk-action')); ?>" method="POST" aria-label="Bulk actions form">
                                <?php echo csrf_field(); ?>
                                <div class="d-flex align-items-center">
                                    <select class="form-control mr-2" style="width: auto;" name="action" id="bulk-action" aria-label="Select bulk action">
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate</option>
                                        <option value="deactivate">Deactivate</option>
                                        <option value="feature">Mark as Featured</option>
                                        <option value="unfeature">Remove Featured</option>
                                        <option value="delete">Delete</option>
                                    </select>
                                    <button type="submit" class="btn btn-warning" id="apply-bulk-action" disabled aria-label="Apply bulk action">
                                        Apply
                                    </button>
                                    <span class="ml-2 text-muted" id="selected-count">0 items selected</span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="products-table" role="table" aria-label="Products table">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="select-all" aria-label="Select all products">
                                    </th>
                                    <th width="80">Image</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="products[]" value="<?php echo e($product->id); ?>" class="product-checkbox" aria-label="Select <?php echo e($product->name); ?>">
                                        </td>
                                        <td>
                                            <?php if($product->images): ?>
                                                <?php
                                                    $images = json_decode($product->images, true);
                                                    $firstImage = $images[0] ?? null;
                                                ?>
                                                <?php if($firstImage): ?>
                                                    <img src="<?php echo e(asset('storage/' . $firstImage)); ?>" 
                                                         alt="<?php echo e($product->name); ?>" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 60px; max-height: 60px;" aria-label="Product image for <?php echo e($product->name); ?>">
                                                <?php else: ?>
                                                    <div class="bg-light text-center p-2" style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="bg-light text-center p-2" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo e($product->name); ?></strong>
                                                <?php if($product->sku): ?>
                                                    <br><small class="text-muted">SKU: <?php echo e($product->sku); ?></small>
                                                <?php endif; ?>
                                                <?php if($product->description): ?>
                                                    <br><small class="text-muted"><?php echo e(Str::limit($product->description, 50)); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($product->category): ?>
                                                <span class="badge badge-info"><?php echo e($product->category->name); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">No Category</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>₹<?php echo e(number_format($product->price, 2)); ?></strong>
                                                <?php if($product->compare_price && $product->compare_price > $product->price): ?>
                                                    <br><small class="text-muted text-decoration-line-through">₹<?php echo e(number_format($product->compare_price, 2)); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($product->stock_quantity <= 0): ?>
                                                <span class="badge badge-danger">Out of Stock</span>
                                            <?php elseif($product->stock_quantity <= ($product->low_stock_threshold ?? 5)): ?>
                                                <span class="badge badge-warning"><?php echo e($product->stock_quantity); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-success"><?php echo e($product->stock_quantity); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($product->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($product->is_featured): ?>
                                                <span class="badge badge-warning">Featured</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Regular</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($product->created_at->format('M d, Y')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if($product->id): ?>
                                                    <a href="<?php echo e(route('admin.products.show', $product->id)); ?>" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View" aria-label="View <?php echo e($product->name); ?> details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-info" disabled title="Product ID missing">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if($product->id): ?>
                                                    <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Edit" aria-label="Edit <?php echo e($product->name); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-warning" disabled title="Product ID missing">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if($product->id): ?>
                                                    <form action="<?php echo e(route('admin.products.toggle-status', $product->id)); ?>" 
                                                          method="POST" 
                                                          style="display: inline;" aria-label="Toggle status for <?php echo e($product->name); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                        <button type="submit" 
                                                                class="btn btn-sm <?php echo e($product->is_active ? 'btn-secondary' : 'btn-success'); ?>" 
                                                                title="<?php echo e($product->is_active ? 'Deactivate' : 'Activate'); ?>">
                                                            <i class="fas <?php echo e($product->is_active ? 'fa-toggle-off' : 'fa-toggle-on'); ?>"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Product ID missing">
                                                        <i class="fas fa-toggle-off"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if($product->id): ?>
                                                    <form action="<?php echo e(route('admin.products.toggle-featured', $product->id)); ?>" 
                                                          method="POST" 
                                                          style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                        <button type="submit" 
                                                                class="btn btn-sm <?php echo e($product->is_featured ? 'btn-secondary' : 'btn-warning'); ?>" 
                                                                title="<?php echo e($product->is_featured ? 'Remove Featured' : 'Mark as Featured'); ?>">
                                                            <i class="fas <?php echo e($product->is_featured ? 'fa-star' : 'fa-star-o'); ?>"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Product ID missing">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if($product->id): ?>
                                                    <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-danger" disabled title="Product ID missing">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No products found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        <?php echo e($products->links()); ?>

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

        // Select all functionality
        $('#select-all').change(function() {
            $('.product-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectedCount();
        });

        // Individual checkbox change
        $('.product-checkbox').change(function() {
            updateSelectedCount();
            updateSelectAll();
        });

        function updateSelectedCount() {
            const count = $('.product-checkbox:checked').length;
            $('#selected-count').text(count + ' items selected');
            $('#apply-bulk-action').prop('disabled', count === 0);
        }

        function updateSelectAll() {
            const total = $('.product-checkbox').length;
            const checked = $('.product-checkbox:checked').length;
            $('#select-all').prop('checked', checked === total && total > 0);
        }

        // Bulk action confirmation
        $('#bulk-action-form').submit(function(e) {
            const action = $('#bulk-action').val();
            const count = $('.product-checkbox:checked').length;
            
            if (!action) {
                e.preventDefault();
                alert('Please select an action.');
                return false;
            }
            
            if (count === 0) {
                e.preventDefault();
                alert('Please select at least one product.');
                return false;
            }
            
            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete ' + count + ' product(s)? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Search functionality
        $('#search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#products-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Clear filters
        $('#clear-filters').click(function() {
            $('#search').val('');
            $('#category-filter').val('');
            $('#status-filter').val('');
            $('#featured-filter').val('');
            $('#products-table tbody tr').show();
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/admin/products/index.blade.php ENDPATH**/ ?>