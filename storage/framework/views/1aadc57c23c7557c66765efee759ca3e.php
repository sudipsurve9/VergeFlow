<div class="profile-form-section">
    <p class="text-muted mb-4">
        <?php echo e(__("Update your account's profile information and email address.")); ?>

    </p>

    <form id="send-verification" method="post" action="<?php echo e(route('verification.send')); ?>">
        <?php echo csrf_field(); ?>
    </form>

    <form method="post" action="<?php echo e(route('profile.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('patch'); ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-user me-2"></i><?php echo e(__('Full Name')); ?>

            </label>
            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" 
                   required autofocus autocomplete="name">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i><?php echo e(__('Email Address')); ?>

            </label>
            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" 
                   required autocomplete="username">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()): ?>
                <div class="alert alert-warning mt-2" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo e(__('Your email address is unverified.')); ?>

                    <button form="send-verification" class="btn btn-link p-0 ms-2 text-decoration-underline">
                        <?php echo e(__('Click here to re-send the verification email.')); ?>

                    </button>
                </div>

                <?php if(session('status') === 'verification-link-sent'): ?>
                    <div class="alert alert-success mt-2" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo e(__('A new verification link has been sent to your email address.')); ?>

                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">
                <i class="fas fa-phone me-2"></i><?php echo e(__('Phone Number')); ?>

            </label>
            <input type="tel" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="phone" name="phone" value="<?php echo e(old('phone', $user->phone ?? '')); ?>" 
                   autocomplete="tel">
            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-4">
            <label for="address" class="form-label">
                <i class="fas fa-map-marker-alt me-2"></i><?php echo e(__('Address')); ?>

            </label>
            <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                      id="address" name="address" rows="3" 
                      autocomplete="street-address"><?php echo e(old('address', $user->address ?? '')); ?></textarea>
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-accent btn-lg">
                <i class="fas fa-save me-2"></i><?php echo e(__('Save Changes')); ?>

            </button>

            <?php if(session('status') === 'profile-updated'): ?>
                <div class="alert alert-success mb-0 py-2" x-data="{ show: true }" x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)" x-transition>
                    <i class="fas fa-check-circle me-2"></i><?php echo e(__('Profile updated successfully!')); ?>

                </div>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/profile/partials/update-profile-information-form.blade.php ENDPATH**/ ?>