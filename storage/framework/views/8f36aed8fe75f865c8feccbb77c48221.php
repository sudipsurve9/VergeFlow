<?php $__env->startSection('content'); ?>
<div class="container py-5" role="main" aria-label="Edit profile main content">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="neon-glow mb-3">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </h1>
                <p class="text-muted">Manage your account settings and preferences</p>
            </div>

            <!-- Profile Information Form -->
            <div class="card premium-card neon-glow mb-4" role="region" aria-label="Profile information">
                <div class="card-header premium-header">
                    <h3 class="mb-0">
                        <i class="fas fa-user me-2"></i>Profile Information
                    </h3>
                </div>
                <div class="card-body">
                    <?php echo $__env->make('profile.partials.update-profile-information-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <!-- Password Update Form -->
            <div class="card premium-card neon-glow mb-4" role="region" aria-label="Password settings">
                <div class="card-header premium-header">
                    <h3 class="mb-0">
                        <i class="fas fa-lock me-2"></i>Update Password
                    </h3>
                </div>
                <div class="card-body">
                    <?php echo $__env->make('profile.partials.update-password-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <!-- Delete Account Form -->
            <div class="card premium-card neon-glow border-danger" role="region" aria-label="Danger zone">
                <div class="card-header premium-header bg-danger">
                    <h3 class="mb-0 text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                    </h3>
                </div>
                <div class="card-body">
                    <?php echo $__env->make('profile.partials.delete-user-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <!-- Back to Profile Button -->
            <div class="text-center mt-4">
                <a href="<?php echo e(route('profile')); ?>" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/profile/edit.blade.php ENDPATH**/ ?>