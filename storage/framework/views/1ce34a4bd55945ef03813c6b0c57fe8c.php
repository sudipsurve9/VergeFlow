<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\GuestLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <!-- Session Status -->
    <?php if (isset($component)) { $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-session-status','data' => ['class' => 'mb-4','status' => session('status')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('auth-session-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-4','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('status'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $attributes = $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $component = $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>

    <div class="login-box">
        <div class="login-title">Sign in to<br>VergeFlow</div>
    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label for="email" style="display:block; font-weight:600; margin-bottom:0.3rem;">Email</label>
                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus style="width:100%; padding:0.7rem; border-radius:6px; border:none; font-size:1.1rem;">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="color:#ffb300; margin-top:0.2rem; font-size:0.95rem;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label for="password" style="display:block; font-weight:600; margin-bottom:0.3rem;">Password</label>
                <input id="password" type="password" name="password" required style="width:100%; padding:0.7rem; border-radius:6px; border:none; font-size:1.1rem;">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="color:#ffb300; margin-top:0.2rem; font-size:0.95rem;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
            <div class="form-group remember-me-section" style="margin-bottom:1.2rem;">
                <div class="remember-me-container">
                    <input type="checkbox" name="remember" id="remember" class="remember-checkbox">
                    <label for="remember" class="remember-label">
                        <span class="checkmark"></span>
                        <span class="remember-text">Keep me signed in</span>
                        <small class="remember-subtitle">Stay logged in for 30 days (like Amazon)</small>
                    </label>
                </div>
        </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <a href="<?php echo e(route('password.request')); ?>" style="color:#4fc3f7; text-decoration:underline; font-size:0.98rem;">Forgot your password?</a>
                <button type="submit" class="login-btn">Log in</button>
        </div>
    </form>
    </div>
    <style>
        .login-box {
            background: #222;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.45);
            padding: 2.5rem 2rem;
            max-width: 400px;
            margin: 2.5rem auto;
        }
        .login-title {
            font-family: 'Orbitron', sans-serif;
            color: #ffb300;
            text-shadow: 0 0 8px #ffb300, 0 0 16px #fff200;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2.1rem;
            font-weight: 700;
        }
        .login-box input[type="email"],
        .login-box input[type="password"] {
            background: #333;
            color: #fff;
            border: 1px solid #444;
        }
        .login-box input[type="email"]:focus,
        .login-box input[type="password"]:focus {
            outline: 2px solid #ffb300;
            background: #222;
        }
        .login-box button[type="submit"]:hover {
            background: linear-gradient(90deg, #ff6f00, #ffb300);
        }
        .login-btn {
            background: linear-gradient(90deg, #ffb300, #ff6f00);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.45rem 1.7rem;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(255, 111, 0, 0.15);
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            min-height: 38px;
            margin-left: 1rem;
        }
        .login-btn:hover, .login-btn:focus {
            background: linear-gradient(90deg, #ff6f00, #ffb300);
            box-shadow: 0 4px 16px rgba(255, 111, 0, 0.25);
            transform: translateY(-2px) scale(1.03);
            outline: none;
        }
        
        /* Enhanced Remember Me Styling */
        .remember-me-container {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .remember-checkbox {
            display: none;
        }
        
        .remember-label {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            margin-bottom: 0;
            color: #fff;
            font-size: 0.95rem;
        }
        
        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #ffb300;
            border-radius: 4px;
            background: transparent;
            position: relative;
            flex-shrink: 0;
            margin-top: 2px;
            transition: all 0.2s ease;
        }
        
        .remember-checkbox:checked + .remember-label .checkmark {
            background: linear-gradient(135deg, #ffb300, #ff6f00);
            border-color: #ffb300;
        }
        
        .remember-checkbox:checked + .remember-label .checkmark::after {
            content: '✓';
            position: absolute;
            top: -2px;
            left: 2px;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }
        
        .remember-text {
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
        }
        
        .remember-subtitle {
            display: block;
            color: #ccc;
            font-size: 0.8rem;
            margin-top: 2px;
            line-height: 1.2;
        }
        
        .remember-label:hover .checkmark {
            border-color: #fff;
            box-shadow: 0 0 8px rgba(255, 179, 0, 0.3);
        }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/auth/login.blade.php ENDPATH**/ ?>