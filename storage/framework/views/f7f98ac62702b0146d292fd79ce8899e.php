<?php $__env->startSection('content'); ?>
<div class="container-fluid py-5" style="background: var(--primary-bg); border-radius: 24px;" role="main" aria-label="Products listing main content">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold mb-3 neon-glow">
                <?php if(request('category')): ?>
                    <?php echo e($categories->where('slug', request('category'))->first()->name ?? 'Products'); ?>

                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h1>
            <p class="subtitle-glow">Discover our premium collection</p>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar with categories -->
        <div class="col-lg-3 mb-4">
            <div class="card sidebar-card theme-card" role="region" aria-label="Product categories">
                <div class="card-header sidebar-header theme-card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo e(route('products.index')); ?>" 
                           class="list-group-item list-group-item-action <?php echo e(!request('category') ? 'active' : ''); ?>" aria-label="View all categories">
                            <i class="fas fa-th-large me-2"></i>All Categories
                        </a>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('products.index', ['category' => $category->slug])); ?>" 
                               class="list-group-item list-group-item-action <?php echo e(request('category') == $category->slug ? 'active' : ''); ?>" aria-label="View products in <?php echo e($category->name); ?> category">
                                <i class="fas fa-tag me-2"></i><?php echo e($category->name); ?>

                                <span class="badge bg-secondary float-end"><?php echo e($category->products_count); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Price Filter -->
            <div class="card mt-4 sidebar-card theme-card" role="region" aria-label="Price filter">
                <div class="card-header sidebar-header theme-card-header">
                    <h5 class="mb-0"><i class="fas fa-indian-rupee-sign me-2"></i>Price Range</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('products.index')); ?>" aria-label="Price filter form">
                        <?php if(request('category')): ?>
                            <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Min Price</label>
                            <input type="number" class="form-control" name="min_price" value="<?php echo e(request('min_price')); ?>" placeholder="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Price</label>
                            <input type="number" class="form-control" name="max_price" value="<?php echo e(request('max_price')); ?>" placeholder="1000">
                        </div>
                        <button type="submit" class="btn btn-accent w-100 filter-btn" aria-label="Apply price filter">Apply Filter</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-lg-9">
            <!-- Featured Products Heading -->
            <div class="mb-5 text-center">
                <h2 class="fw-bold neon-glow section-title">Featured Products</h2>
                <p class="subtitle-glow">Handpicked premium items for you</p>
            </div>
            <!-- Search and filter bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('products.index')); ?>" class="row g-3" aria-label="Search and sort form">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search products..." 
                                       value="<?php echo e(request('search')); ?>" aria-label="Search products">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="sort" class="form-select" aria-label="Sort products by">
                                <option value="">Sort by</option>
                                <option value="price_asc" <?php echo e(request('sort') == 'price_asc' ? 'selected' : ''); ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo e(request('sort') == 'price_desc' ? 'selected' : ''); ?>>Price: High to Low</option>
                                <option value="name_asc" <?php echo e(request('sort') == 'name_asc' ? 'selected' : ''); ?>>Name: A to Z</option>
                                <option value="name_desc" <?php echo e(request('sort') == 'name_desc' ? 'selected' : ''); ?>>Name: Z to A</option>
                            </select>
                        </div>
                        <?php if(request('category')): ?>
                            <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
                        <?php endif; ?>
                        <?php if(request('min_price')): ?>
                            <input type="hidden" name="min_price" value="<?php echo e(request('min_price')); ?>">
                        <?php endif; ?>
                        <?php if(request('max_price')): ?>
                            <input type="hidden" name="max_price" value="<?php echo e(request('max_price')); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Results count -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted mb-0">
                    Showing <?php echo e($products->firstItem() ?? 0); ?> - <?php echo e($products->lastItem() ?? 0); ?> of <?php echo e($products->total()); ?> products
                </p>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-accent active" onclick="setViewMode('grid')" aria-label="Grid view">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-accent" onclick="setViewMode('list')" aria-label="List view">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Products grid -->
            <div class="products-flex-container d-flex flex-wrap justify-content-start align-items-stretch" style="gap: 24px;">
                <?php
                    $userWishlists = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray() : [];
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo e(asset('storage/products/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>">
                        </div>
                        <div class="product-info">
                            <div class="product-category category-glow"><?php echo e($product->category->name); ?></div>
                            <h5 class="product-title product-title-glow"><?php echo e($product->name); ?></h5>
                            <p class="product-desc"><?php echo e(Str::limit($product->description, 80)); ?></p>
                            <div class="product-price price-glow">
                                <?php if($product->sale_price): ?>
                                    <span class="original">₹<?php echo e(number_format($product->price, 2)); ?></span>
                                    ₹<?php echo e(number_format($product->sale_price, 2)); ?>

                                <?php else: ?>
                                    ₹<?php echo e(number_format($product->price, 2)); ?>

                                <?php endif; ?>
                            </div>
                            <div class="d-grid gap-2">
                                <?php if(!empty($product->slug)): ?>
                                    <a href="<?php echo e(route('products.show', ['product' => $product->slug])); ?>" class="btn btn-accent view-btn checkout-glow" aria-label="View details for <?php echo e($product->name); ?>">View Details</a>
                                <?php else: ?>
                                    <button class="btn btn-accent view-btn checkout-glow" disabled title="Product link unavailable">View Details</button>
                                <?php endif; ?>
                                <button onclick="addToCart(<?php echo e($product->id); ?>)" class="btn btn-outline-accent add-btn icon-btn-glow" aria-label="Add <?php echo e($product->name); ?> to cart" title="Add this product to your cart">
                                    <i class="fa-solid fa-cart-plus me-2"></i>Add to Cart
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 wishlist-btn" data-product-id="<?php echo e($product->id); ?>" aria-label="<?php echo e(in_array($product->id, $userWishlists) ? 'Remove' : 'Add'); ?> <?php echo e($product->name); ?> to wishlist">
                                    <i class="fa<?php echo e(in_array($product->id, $userWishlists) ? 's' : 'r'); ?> fa-heart me-1"></i>
                                    <span class="wishlist-btn-text"><?php echo e(in_array($product->id, $userWishlists) ? 'Remove from Wishlist' : 'Add to Wishlist'); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fa-solid fa-search fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No products found</h4>
                            <p class="text-muted">Try adjusting your search or filter criteria.</p>
                            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-accent" aria-label="Clear all filters">Clear Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($products->hasPages()): ?>
                <div class="d-flex justify-content-center mt-5">
                    <?php echo e($products->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("cart.add")); ?>';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '<?php echo e(csrf_token()); ?>';
    
    const productInput = document.createElement('input');
    productInput.type = 'hidden';
    productInput.name = 'product_id';
    productInput.value = productId;
    
    const quantityInput = document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.name = 'quantity';
    quantityInput.value = 1;
    
    form.appendChild(csrfToken);
    form.appendChild(productInput);
    form.appendChild(quantityInput);
    
    document.body.appendChild(form);
    form.submit();
}

function setViewMode(mode) {
    const container = document.getElementById('products-container');
    if (mode === 'list') {
        container.classList.add('list-view');
    } else {
        container.classList.remove('list-view');
    }
}

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

<style>
.theme-card {
    background: var(--card-bg, #232323);
    border: 2px solid var(--accent-color, #FF6A00);
    border-radius: 16px;
    box-shadow: 0 0 16px var(--accent-glow, #FF6A0033);
}
.theme-card-header {
    background: linear-gradient(90deg, var(--primary-bg, #181818) 0%, var(--accent-glow, #FF6A00) 100%);
    color: var(--accent-color, #FFB300);
    font-family: 'Orbitron', 'Montserrat', Arial, sans-serif;
    font-size: 1.1rem;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    border-bottom: 2px solid var(--accent-color, #FFB300);
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
    font-size: 2.5rem;
    letter-spacing: 2px;
    margin-bottom: 0.5rem;
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
.products-flex-container {
    gap: 24px;
    margin-bottom: 2rem;
}
.product-card {
    width: 300px;
    min-height: 420px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
    background: #232323;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
}
.product-image {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 16px 16px 0 0;
}
.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 16px 16px 0 0;
}
.product-title-glow {
    color: var(--accent-color, #FFB300);
    text-shadow: 0 0 8px var(--accent-glow, #FF6A00), 0 0 16px var(--accent-color, #FFB300);
    font-size: 1.1rem;
    font-weight: 900;
}
.price-glow {
    color: var(--text-primary, #fff);
    text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00);
    font-size: 1.2rem;
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
.view-btn, .add-btn {
    font-weight: 700;
    border-radius: 30px;
    padding: 10px 24px;
    font-size: 1rem;
    margin-bottom: 6px;
}
.view-btn.btn-accent {
    background: linear-gradient(45deg, var(--accent-color, #FFB300), var(--accent-glow, #FF6A00));
    color: #fff !important;
    border: none;
    box-shadow: 0 0 8px var(--accent-color, #FFB30099);
}
.view-btn.btn-accent:hover {
    background: linear-gradient(45deg, var(--accent-glow, #FF6A00), var(--accent-color, #FFB300));
    color: #181818 !important;
    box-shadow: 0 0 24px var(--accent-color, #FFB300);
}
.add-btn.btn-outline-accent {
    border: 2px solid var(--accent-color, #FFB300);
    color: var(--accent-color, #FFB300) !important;
    background: transparent;
}
.add-btn.btn-outline-accent:hover {
    background: var(--accent-color, #FFB300);
    color: #181818 !important;
    box-shadow: 0 0 24px var(--accent-color, #FFB300);
}
.filter-btn {
    background: linear-gradient(90deg, var(--accent-glow, #FF6A00) 0%, var(--accent-color, #FFB300) 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    padding: 10px 20px;
}
.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
.list-group-item.active, .list-group-item-action.active {
    background: linear-gradient(90deg, var(--accent-color, #FFB300) 0%, var(--accent-glow, #FF6A00) 100%) !important;
    color: #181818 !important;
    border: none;
    font-weight: 700;
}
.list-group-item {
    background: var(--card-bg, #232323);
    color: var(--text-primary, #fff);
    border: none;
    transition: background 0.2s, color 0.2s;
}
.list-group-item:hover {
    background: var(--accent-color, #FFB300);
    color: #181818;
}
.badge.bg-secondary {
    background: var(--accent-glow, #FF6A00) !important;
    color: #fff !important;
}
.card, .card-body {
    background: var(--card-bg, #232323) !important;
    color: var(--text-primary, #fff) !important;
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
.pagination .page-link {
    background: var(--card-bg, #232323);
    border-color: var(--accent-color, #FFB300);
    color: var(--accent-color, #FFB300);
}
.pagination .page-link:hover {
    background: var(--accent-color, #FFB300);
    color: #181818;
}
.pagination .page-item.active .page-link {
    background: linear-gradient(90deg, var(--accent-glow, #FF6A00) 0%, var(--accent-color, #FFB300) 100%);
    border-color: var(--accent-color, #FFB300);
    color: #181818;
}
[data-theme="light"] .theme-card,
[data-theme="light"] .product-card.theme-card,
[data-theme="light"] .card,
[data-theme="light"] .card-body {
    background: #fff !important;
    color: #181818 !important;
    border: 1.5px solid #eee !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
}
[data-theme="light"] .product-card.theme-card,
[data-theme="light"] .theme-card {
    box-shadow: none !important;
    border-radius: 16px !important;
    border: 1.5px solid #FFB30022 !important;
}
[data-theme="light"] .product-title-glow,
[data-theme="light"] .price-glow,
[data-theme="light"] .category-glow,
[data-theme="light"] .neon-glow,
[data-theme="light"] .subtitle-glow {
    color: #FF6A00 !important;
    text-shadow: none !important;
}
[data-theme="light"] .view-btn.btn-accent,
[data-theme="light"] .add-btn.btn-outline-accent {
    box-shadow: none !important;
    border: 1.5px solid #FFB30055 !important;
    background: linear-gradient(45deg, #FFB300, #FF6A00);
    color: #fff !important;
}
[data-theme="light"] .view-btn.btn-accent:hover,
[data-theme="light"] .add-btn.btn-outline-accent:hover {
    background: linear-gradient(45deg, #FF6A00, #FFB300);
    color: #fff !important;
}
[data-theme="light"] .theme-card-header, [data-theme="light"] .sidebar-header {
    background: linear-gradient(90deg, #fffbe6 0%, #FFB300 100%) !important;
    color: #FF6A00 !important;
    border-bottom: 2px solid #FF6A00 !important;
}
@media (max-width: 768px) {
    .section-title {
        font-size: 1.5rem;
    }
    .product-title-glow, .price-glow {
        font-size: 1rem;
    }
    .view-btn, .add-btn {
        font-size: 0.95rem;
        padding: 8px 12px;
    }
    .sidebar-card, .theme-card {
        margin-bottom: 24px;
    }
}
[data-theme="light"] .container.py-5 {
    background: #fff !important;
}
[data-theme="light"] #products-container {
    background: #f8f9fa !important;
    box-shadow: 0 4px 32px rgba(0,0,0,0.04);
}
html[data-theme="light"] body .container.py-5,
html[data-theme="light"] body #products-container {
    background: #fff !important;
    box-shadow: none !important;
}
/* Fix for product grid alignment in light mode */
[data-theme="light"] .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}
[data-theme="light"] .col-lg-9 {
    flex: 0 0 75%;
    max-width: 75%;
    margin-left: 0 !important;
    margin-right: 0 !important;
}
[data-theme="light"] #products-container {
    margin-left: 0 !important;
    margin-right: 0 !important;
    width: 100% !important;
    justify-content: flex-start !important;
}
</style>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/products/index.blade.php ENDPATH**/ ?>