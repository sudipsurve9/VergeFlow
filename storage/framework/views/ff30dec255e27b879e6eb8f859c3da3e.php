

<?php $__env->startSection('title', 'Site Templates - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themes = [
        [
            'slug' => 'layouts.app',
            'name' => 'Default',
            'description' => 'The default Vault64 theme with a modern touch.',
            'preview' => asset('images/themes/default.png')
        ],
        [
            'slug' => 'layouts.app_modern',
            'name' => 'Modern',
            'description' => 'A sleek, dark, neon-inspired modern theme.',
            'preview' => asset('images/themes/modern.png')
        ],
        [
            'slug' => 'layouts.app_classic',
            'name' => 'Classic',
            'description' => 'A classic, light theme for traditional look.',
            'preview' => asset('images/themes/classic.png')
        ],
        [
            'slug' => 'layouts.app_apparel',
            'name' => 'Apparel',
            'description' => 'Chic, minimalist fashion and apparel theme with dark mode.',
            'preview' => asset('images/themes/apparel.png')
        ],
        [
            'slug' => 'layouts.app_webshop',
            'name' => 'WebShop',
            'description' => 'Clean, modern, Gutenberg-powered theme for any store.',
            'preview' => asset('images/themes/webshop.png')
        ],
        [
            'slug' => 'layouts.app_neon',
            'name' => 'Neon Night',
            'description' => 'Vibrant, neon-lit style for bold, modern brands.',
            'preview' => asset('images/themes/neon.png')
        ],
        [
            'slug' => 'layouts.app_furniture',
            'name' => 'FurnitureStore',
            'description' => 'Elegant, spacious layout for furniture and home decor.',
            'preview' => asset('images/themes/furniture.png')
        ],
        [
            'slug' => 'layouts.app_gadgetpro',
            'name' => 'GadgetPro',
            'description' => 'Sleek, tech-focused theme for electronics and gadgets.',
            'preview' => asset('images/themes/gadgetpro.png')
        ],
        [
            'slug' => 'layouts.app_ecomarket',
            'name' => 'EcoMarket',
            'description' => 'Fresh, green design for eco-friendly and organic shops.',
            'preview' => asset('images/themes/ecomarket.png')
        ],
        [
            'slug' => 'layouts.app_beauty',
            'name' => 'Beauty Bliss',
            'description' => 'Soft, elegant theme for beauty and wellness brands.',
            'preview' => asset('images/themes/beauty.png')
        ],
        [
            'slug' => 'layouts.app_urban',
            'name' => 'Urban Street',
            'description' => 'Trendy, urban-inspired look for streetwear and youth brands.',
            'preview' => asset('images/themes/urban.png')
        ],
        [
            'slug' => 'layouts.app_boutique',
            'name' => 'Classic Boutique',
            'description' => 'Timeless, boutique style for luxury and classic stores.',
            'preview' => asset('images/themes/boutique.png')
        ],
        [
            'slug' => 'layouts.app_kids',
            'name' => 'Kids World',
            'description' => 'Colorful, playful theme for kids toys and clothing.',
            'preview' => asset('images/themes/kids.png')
        ],
        [
            'slug' => 'layouts.app_supermart',
            'name' => 'SuperMart',
            'description' => 'Versatile, supermarket-style theme for large catalogs.',
            'preview' => asset('images/themes/supermart.png')
        ],
        [
            'slug' => 'layouts.app_crafted',
            'name' => 'Crafted Goods',
            'description' => 'Handmade, artisan vibe for crafts and small businesses.',
            'preview' => asset('images/themes/crafted.png')
        ],
        [
            'slug' => 'layouts.app_luxury',
            'name' => 'Luxury Gold',
            'description' => 'Premium, gold-accented theme for high-end products.',
            'preview' => asset('images/themes/luxury.png')
        ],
        [
            'slug' => 'layouts.app_techminimal',
            'name' => 'Tech Minimal',
            'description' => 'Minimalist, high-contrast theme for tech and gadgets.',
            'preview' => asset('images/themes/techminimal.png')
        ],
        [
            'slug' => 'layouts.app_sushi',
            'name' => 'Sushi Bar',
            'description' => 'Fresh, modern theme for food, restaurants, and takeout.',
            'preview' => asset('images/themes/sushi.png')
        ]
    ];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">
                                <i class="fas fa-paint-brush me-2"></i>
                                Site Template Management
                            </h3>
                            <p class="text-muted mb-0">Manage the global site template for all clients</p>
                        </div>
                        <div>
                            <span class="badge bg-info me-2">
                                <i class="fas fa-info-circle me-1"></i>System-wide Setting
                            </span>
                            <a href="<?php echo e(route('super_admin.settings')); ?>" class="btn btn-secondary">
                                <i class="fas fa-cogs me-2"></i>System Settings
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This template setting affects the global site appearance. 
                        Individual clients can override this with their own theme settings.
                    </div>

                    <!-- Current Template Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-star me-2"></i>Current Active Template
                                    </h5>
                                    <?php
                                        $currentTheme = collect($themes)->firstWhere('slug', $current);
                                    ?>
                                    <?php if($currentTheme): ?>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3" style="width: 60px; height: 60px; background: linear-gradient(45deg, #<?php echo e(substr(md5($currentTheme['name']), 0, 6)); ?>, #<?php echo e(substr(md5($currentTheme['name']), 6, 6)); ?>); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                <?php echo e(substr($currentTheme['name'], 0, 2)); ?>

                                            </div>
                                            <div>
                                                <h6 class="mb-1"><?php echo e($currentTheme['name']); ?></h6>
                                                <p class="text-muted mb-0"><?php echo e($currentTheme['description']); ?></p>
                                                <small class="text-muted">Template ID: <?php echo e($current); ?></small>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No template currently selected</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Templates -->
                    <div class="row">
                        <?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 <?php echo e($current === $theme['slug'] ? 'border-success shadow' : 'border-light'); ?>">
                                    <div class="card-img-top" style="height:180px;background:linear-gradient(45deg, #<?php echo e(substr(md5($theme['name']), 0, 6)); ?>, #<?php echo e(substr(md5($theme['name']), 6, 6)); ?>);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:1.2rem;cursor:pointer;" 
                                         data-bs-toggle="modal" data-bs-target="#previewModal" 
                                         data-theme-name="<?php echo e($theme['name']); ?>" data-theme-img="<?php echo e($theme['preview']); ?>">
                                        <?php echo e($theme['name']); ?> Theme
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo e($theme['name']); ?></h5>
                                        <p class="card-text flex-grow-1"><?php echo e($theme['description']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <button type="button" class="btn btn-outline-secondary btn-sm preview-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#previewModal" 
                                                    data-theme-name="<?php echo e($theme['name']); ?>" data-theme-img="<?php echo e($theme['preview']); ?>">
                                                <i class="fas fa-eye me-1"></i>Preview
                                            </button>
                                            <?php if($current === $theme['slug']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Active
                                                </span>
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo e(route('super_admin.templates.update')); ?>" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="layout" value="<?php echo e($theme['slug']); ?>">
                                                    <button class="btn btn-primary btn-sm" type="submit">
                                                        <i class="fas fa-check me-1"></i>Activate
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for theme preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Theme Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="themePreviewContent" style="min-height:400px;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:bold;color:white;border-radius:8px;">
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var previewModal = document.getElementById('previewModal');
        previewModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var themeName = button.getAttribute('data-theme-name');
            var themeImg = button.getAttribute('data-theme-img');
            document.getElementById('previewModalLabel').textContent = themeName + ' Preview';
            
            var previewContent = document.getElementById('themePreviewContent');
            var color1 = '#' + themeName.split('').reduce((a, b) => {a = ((a << 5) - a) + b.charCodeAt(0); return a & a}, 0).toString(16).substr(0, 6);
            var color2 = '#' + themeName.split('').reverse().reduce((a, b) => {a = ((a << 5) - a) + b.charCodeAt(0); return a & a}, 0).toString(16).substr(0, 6);
            
            previewContent.style.background = 'linear-gradient(45deg, ' + color1 + ', ' + color2 + ')';
            previewContent.innerHTML = '<div><h3>' + themeName + '</h3><p>Theme Preview</p></div>';
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/super_admin/templates/index.blade.php ENDPATH**/ ?>