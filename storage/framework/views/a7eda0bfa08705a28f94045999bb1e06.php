<?php $__env->startSection('content'); ?>
<div class="container" role="main" aria-label="Checkout main content">
    <div class="row">
        <div class="col-12">
            <h1 class="fw-bold neon-glow">Checkout</h1>
        </div>
    </div>

    <!-- Error Display Section -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-times-circle"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('orders.store')); ?>" method="POST" aria-label="Checkout form">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Address Selection -->
                <div class="card mb-4" role="region" aria-label="Shipping address selection">
                    <div class="card-header neon-glow d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Shipping Address</h5>
                        <a href="<?php echo e(route('addresses.index')); ?>" class="btn btn-outline-accent btn-sm" target="_blank">
                            <i class="fas fa-plus"></i> Manage Addresses
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if($addresses->isEmpty()): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                You don't have any saved addresses. Please <a href="<?php echo e(route('addresses.index')); ?>" target="_blank">add an address</a> first.
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for="shipping_address_id" class="form-label">Select Shipping Address *</label>
                                <select class="form-select <?php $__errorArgs = ['shipping_address_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="shipping_address_id" name="shipping_address_id" required>
                                    <option value="">Choose shipping address...</option>
                                    <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($address->id); ?>" 
                                                <?php echo e((old('shipping_address_id') == $address->id || ($defaultShippingAddress && $defaultShippingAddress->id == $address->id)) ? 'selected' : ''); ?>

                                                data-address="<?php echo e($address->getFormattedAddress()); ?>"
                                                 data-phone="<?php echo e($address->phone ?? ''); ?>"
                                                 data-debug-phone="<?php echo e($address->phone ? 'HAS_PHONE' : 'NO_PHONE'); ?>">
                                            <?php echo e($address->label); ?> - <?php echo e($address->address_line1); ?>, <?php echo e($address->city); ?> - <?php echo e($address->postal_code); ?>

                                            <?php if($address->phone): ?> [Phone: <?php echo e($address->phone); ?>] <?php endif; ?>
                                            <?php if($address->is_default_shipping): ?> (Default) <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['shipping_address_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <!-- Selected Address Preview -->
                            <div id="shipping-address-preview" class="alert alert-info" style="display: none;">
                                <h6><i class="fas fa-map-marker-alt"></i> Selected Shipping Address:</h6>
                                <div id="shipping-address-details"></div>
                                <div id="shipping-phone-details" class="mt-2" style="display: none;">
                                    <small class="text-muted"><i class="fas fa-phone"></i> Phone: <span id="shipping-phone-number"></span></small>
                                </div>
                            </div>
                            
                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="phone" name="phone" value="<?php echo e(old('phone')); ?>" required>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4" role="region" aria-label="Payment method">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" <?php echo e(old('payment_method') == 'cod' ? 'checked' : ''); ?> required>
                                <label class="form-check-label" for="cod">
                                    Cash on Delivery (COD)
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" <?php echo e(old('payment_method') == 'bank_transfer' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="bank_transfer">
                                    Bank Transfer
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" <?php echo e(old('payment_method') == 'credit_card' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="credit_card">
                                    Credit Card
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" <?php echo e(old('payment_method') == 'stripe' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="stripe">
                                    Pay with Card (Stripe)
                                </label>
                            </div>
                        </div>
                        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4" role="region" aria-label="Order notes">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Order Notes (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Any special instructions or notes for your order..."><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Billing Address Selection -->
                <div class="card mb-4" role="region" aria-label="Billing address selection">
                    <div class="card-header neon-glow d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Billing Address</h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="same_as_shipping" name="same_as_shipping" <?php echo e(old('same_as_shipping') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="same_as_shipping">
                                Same as shipping
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="billing-address-section">
                        <?php if($addresses->isEmpty()): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                You don't have any saved addresses. Please <a href="<?php echo e(route('addresses.index')); ?>" target="_blank">add an address</a> first.
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for="billing_address_id" class="form-label">Select Billing Address *</label>
                                <select class="form-select <?php $__errorArgs = ['billing_address_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="billing_address_id" name="billing_address_id" required>
                                    <option value="">Choose billing address...</option>
                                    <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($address->id); ?>" 
                                                <?php echo e((old('billing_address_id') == $address->id || ($defaultBillingAddress && $defaultBillingAddress->id == $address->id)) ? 'selected' : ''); ?>

                                                data-address="<?php echo e($address->getFormattedAddress()); ?>">
                                             <?php echo e($address->label); ?> - <?php echo e($address->address_line1); ?>, <?php echo e($address->city); ?> - <?php echo e($address->postal_code); ?>

                                             <?php if($address->is_default_billing): ?> (Default) <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['billing_address_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <!-- Selected Billing Address Preview -->
                            <div id="billing-address-preview" class="alert alert-info" style="display: none;">
                                <h6><i class="fas fa-map-marker-alt"></i> Selected Billing Address:</h6>
                                <div id="billing-address-details"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card" role="region" aria-label="Order summary">
                    <div class="card-header neon-glow">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0 product-title-glow"><?php echo e($item->product->name); ?></h6>
                                    <small class="text-muted">Qty: <?php echo e($item->quantity); ?></small>
                                </div>
                                <span>₹<?php echo e(number_format($item->total, 2)); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?php echo e(number_format($total, 2)); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span>₹0.00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-accent price-glow">₹<?php echo e(number_format($total, 2)); ?></strong>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 btn-lg checkout-glow" aria-label="Place order and complete checkout" onclick="return validateAndSubmitOrder()">
                            <i class="fas fa-lock"></i> Place Order
                        </button>
                        
                        <div class="text-center mt-3">
                            <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-outline-accent btn-sm icon-btn-glow">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="stripe-card-section" style="display: none;">
    <div class="mb-3">
        <label for="card-element" class="form-label">Card Details</label>
        <div id="card-element" class="form-control"></div>
        <div id="card-errors" class="text-danger mt-2" role="alert"></div>
    </div>
</div>

<style>
.neon-glow {
    text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00;
    animation: neonPulse 3s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 4px #FFB300, 0 0 8px #FF6A00; }
    to { text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00; }
}
.checkout-glow {
    box-shadow: 0 0 16px #FFB30099;
    transition: box-shadow 0.2s;
}
.checkout-glow:hover {
    box-shadow: 0 0 32px #FFB300, 0 0 64px #FF6A00;
}
.product-title-glow {
    color: #FFB300;
    text-shadow: 0 0 8px #FF6A00, 0 0 16px #FFB300;
    font-size: 1.1rem;
    font-weight: 900;
}
.price-glow {
    color: #fff;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
    font-size: 1.2rem;
    font-weight: 900;
}
.icon-btn-glow i {
    transition: text-shadow 0.2s, color 0.2s;
}
.icon-btn-glow:hover i {
    color: #FFB300 !important;
    text-shadow: 0 0 8px #FFB300, 0 0 16px #FF6A00;
}
input, textarea, select {
    color: #fff !important;
}
input::placeholder, textarea::placeholder {
    color: #FFB300 !important;
    opacity: 1;
}
.form-label, label, .form-check-label {
    color: #FFB300 !important;
    font-weight: 600;
}
.card-body, .order-summary, .cart-summary, .card {
    color: #fff !important;
}
.order-summary span, .order-summary strong, .card-body span, .card-body strong {
    color: #fff !important;
}
.text-muted {
    color: #b3b3b3 !important;
}
@media (max-width: 768px) {
    .neon-glow { font-size: 1.2rem; }
    .checkout-glow { font-size: 1rem; padding: 12px 0; }
    .product-title-glow, .price-glow { font-size: 1rem; }
}
[data-theme="light"] input,
[data-theme="light"] textarea,
[data-theme="light"] select {
    color: #181818 !important;
    background: #fff !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style>

<?php $__env->startPush('scripts'); ?>
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('<?php echo e(config('services.stripe.key')); ?>');
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');

const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
function toggleStripeSection() {
    document.getElementById('stripe-card-section').style.display = document.getElementById('stripe').checked ? 'block' : 'none';
}
paymentRadios.forEach(radio => radio.addEventListener('change', toggleStripeSection));
toggleStripeSection();

// AGGRESSIVE PHONE AUTO-FILL - MULTIPLE APPROACHES
function fillPhoneFromAddress() {
    console.log('🔥 AGGRESSIVE PHONE AUTO-FILL STARTING');
    
    // Try multiple selectors to find elements
    const shippingSelect = document.getElementById('shipping_address_id') || document.querySelector('select[name="shipping_address_id"]');
    const phoneInput = document.getElementById('phone') || document.querySelector('input[name="phone"]');
    
    console.log('📍 Elements found:', {
        shippingSelect: !!shippingSelect,
        phoneInput: !!phoneInput,
        shippingSelectValue: shippingSelect?.value
    });
    
    if (!shippingSelect || !phoneInput) {
        console.log('❌ Elements not found - trying alternative approach');
        
        // Try alternative approach with timeout
        setTimeout(() => {
            const altShipping = document.querySelector('#shipping_address_id');
            const altPhone = document.querySelector('#phone');
            if (altShipping && altPhone) {
                console.log('🔄 Retry successful - elements found');
                fillPhoneFromAddressCore(altShipping, altPhone);
            }
        }, 1000);
        return;
    }
    
    fillPhoneFromAddressCore(shippingSelect, phoneInput);
}

function fillPhoneFromAddressCore(shippingSelect, phoneInput) {
    const selectedIndex = shippingSelect.selectedIndex;
    console.log('📊 Selected index:', selectedIndex);
    
    if (selectedIndex > 0) {
        const selectedOption = shippingSelect.options[selectedIndex];
        console.log('📋 Selected option:', selectedOption);
        
        const phone = selectedOption.getAttribute('data-phone');
        console.log('📞 Phone from data attribute:', phone);
        
        if (phone && phone.trim() !== '' && phone !== 'null') {
            phoneInput.value = phone;
            phoneInput.style.backgroundColor = '#28a745';
            phoneInput.style.color = 'white';
            phoneInput.style.fontWeight = 'bold';
            
            console.log('✅ PHONE SUCCESSFULLY FILLED:', phone);
            
            // Show success message
            const successMsg = document.createElement('div');
            successMsg.innerHTML = '✅ Phone auto-filled: ' + phone;
            successMsg.style.cssText = 'position:fixed;top:10px;right:10px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
            document.body.appendChild(successMsg);
            
            setTimeout(() => {
                phoneInput.style.backgroundColor = '';
                phoneInput.style.color = '';
                phoneInput.style.fontWeight = '';
                document.body.removeChild(successMsg);
            }, 3000);
        } else {
            console.log('❌ No valid phone data found:', phone);
        }
    } else {
        console.log('⚠️ No address selected (index 0)');
    }
}

// COMPREHENSIVE ORDER VALIDATION AND SUBMISSION
function validateAndSubmitOrder() {
    console.log('🚀 VALIDATING AND SUBMITTING ORDER');
    
    // Get form elements
    const form = document.querySelector('form[action*="orders.store"]');
    const shippingSelect = document.getElementById('shipping_address_id');
    const billingSelect = document.getElementById('billing_address_id');
    const phoneInput = document.getElementById('phone');
    
    // 1. ENSURE PHONE NUMBER IS FILLED
    if (!phoneInput.value || phoneInput.value.trim() === '') {
        console.log('📞 Attempting to auto-fill phone number');
        fillPhoneFromAddress();
        
        // If still empty after auto-fill, try to get from selected address
        if (!phoneInput.value || phoneInput.value.trim() === '') {
            const selectedShippingOption = shippingSelect.options[shippingSelect.selectedIndex];
            const phoneFromAddress = selectedShippingOption?.getAttribute('data-phone');
            
            if (phoneFromAddress && phoneFromAddress !== 'null' && phoneFromAddress.trim() !== '') {
                phoneInput.value = phoneFromAddress;
                console.log('✅ Phone auto-filled from address:', phoneFromAddress);
            } else {
                // Prompt user for phone number
                const userPhone = prompt('Please enter your phone number to complete the order:');
                if (userPhone && userPhone.trim() !== '') {
                    phoneInput.value = userPhone.trim();
                } else {
                    alert('❌ Phone number is required to place the order.');
                    return false;
                }
            }
        }
    }
    
    // 2. ENSURE PAYMENT METHOD IS SELECTED
    let selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedPayment) {
        // Auto-select COD as default
        const codOption = document.getElementById('cod');
        if (codOption) {
            codOption.checked = true;
            selectedPayment = codOption;
            console.log('✅ Auto-selected COD payment method');
        }
    }
    
    // 3. ENSURE ADDRESSES ARE SELECTED
    if (!shippingSelect.value) {
        alert('❌ Please select a shipping address.');
        shippingSelect.focus();
        return false;
    }
    
    if (!billingSelect.value) {
        // Auto-select same as shipping if available
        if (shippingSelect.value) {
            billingSelect.value = shippingSelect.value;
            console.log('✅ Auto-selected billing address same as shipping');
        } else {
            alert('❌ Please select a billing address.');
            billingSelect.focus();
            return false;
        }
    }
    
    // 4. FINAL VALIDATION CHECK
    const finalValidation = {
        shipping_address_id: shippingSelect.value,
        billing_address_id: billingSelect.value,
        phone: phoneInput.value,
        payment_method: selectedPayment?.value
    };
    
    console.log('📊 Final validation values:', finalValidation);
    
    const missingFields = [];
    if (!finalValidation.shipping_address_id) missingFields.push('Shipping Address');
    if (!finalValidation.billing_address_id) missingFields.push('Billing Address');
    if (!finalValidation.phone || finalValidation.phone.trim() === '') missingFields.push('Phone Number');
    if (!finalValidation.payment_method) missingFields.push('Payment Method');
    
    if (missingFields.length > 0) {
        alert('❌ Missing required fields:\n' + missingFields.join('\n'));
        return false;
    }
    
    // 5. SUBMIT THE FORM
    console.log('✅ ALL VALIDATION PASSED - SUBMITTING ORDER');
    
    // Show loading state
    const submitButton = document.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Order...';
    submitButton.disabled = true;
    
    // Allow form submission
    setTimeout(() => {
        form.submit();
    }, 100);
    
    return true;
}

// MULTIPLE EXECUTION ATTEMPTS FOR PHONE AUTO-FILL
function executePhoneAutoFill() {
    console.log('🚀 EXECUTING PHONE AUTO-FILL ATTEMPTS');
    
    // Attempt 1: Immediate
    fillPhoneFromAddress();
    
    // Attempt 2: After 500ms
    setTimeout(() => {
        console.log('🔄 Attempt 2: 500ms delay');
        fillPhoneFromAddress();
    }, 500);
    
    // Attempt 3: After 1000ms
    setTimeout(() => {
        console.log('🔄 Attempt 3: 1000ms delay');
        fillPhoneFromAddress();
    }, 1000);
    
    // Attempt 4: After 2000ms
    setTimeout(() => {
        console.log('🔄 Attempt 4: 2000ms delay');
        fillPhoneFromAddress();
    }, 2000);
}

// Address selection functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM LOADED - Starting phone auto-fill setup');
    const shippingSelect = document.getElementById('shipping_address_id');
    const billingSelect = document.getElementById('billing_address_id');
    const sameAsShippingCheckbox = document.getElementById('same_as_shipping');
    const billingSection = document.getElementById('billing-address-section');
    const phoneInput = document.getElementById('phone');
    
    // Execute multiple phone auto-fill attempts
    executePhoneAutoFill();
    
    // Handle shipping address selection
    if (shippingSelect) {
        shippingSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const preview = document.getElementById('shipping-address-preview');
            
            // Auto-fill phone immediately when address changes
            fillPhoneFromAddress();
            
            if (selectedOption && selectedOption.value) {
                const address = selectedOption.getAttribute('data-address');
                
                if (address) {
                    preview.innerHTML = `
                        <div class="alert alert-info">
                            <strong>Selected Address:</strong><br>
                            ${address}
                        </div>
                    `;
                    preview.style.display = 'block';
                }
                
                // If "same as shipping" is checked, update billing
                if (sameAsShippingCheckbox && sameAsShippingCheckbox.checked) {
                    updateBillingFromShipping();
                }
            } else {
                preview.style.display = 'none';
            }
        });
        
        // Trigger change event if there's a pre-selected value
        if (shippingSelect.value) {
            console.log('Triggering change event for pre-selected address:', shippingSelect.value);
            shippingSelect.dispatchEvent(new Event('change'));
        }
        
        // Simple direct approach - force phone auto-fill
        function forcePhoneAutoFill() {
            console.log('=== FORCE PHONE AUTO-FILL DEBUG ===');
            console.log('Shipping select element:', shippingSelect);
            console.log('Phone input element:', phoneInput);
            
            if (!shippingSelect || !phoneInput) {
                console.log('ERROR: Missing elements!');
                return;
            }
            
            const selectedIndex = shippingSelect.selectedIndex;
            console.log('Selected index:', selectedIndex);
            
            if (selectedIndex > 0) {
                const selectedOption = shippingSelect.options[selectedIndex];
                console.log('Selected option:', selectedOption);
                console.log('Option attributes:', {
                    value: selectedOption.value,
                    'data-phone': selectedOption.getAttribute('data-phone'),
                    'data-debug-phone': selectedOption.getAttribute('data-debug-phone'),
                    innerHTML: selectedOption.innerHTML
                });
                
                const phone = selectedOption.getAttribute('data-phone');
                console.log('Phone value retrieved:', phone, typeof phone);
                
                if (phone) {
                    phoneInput.value = phone;
                    phoneInput.style.backgroundColor = '#d4edda'; // Green background
                    console.log('✅ PHONE FILLED:', phone);
                    
                    setTimeout(() => {
                        phoneInput.style.backgroundColor = '';
                    }, 3000);
                } else {
                    console.log('❌ NO PHONE DATA FOUND');
                }
            } else {
                console.log('No address selected');
            }
        }
        
        // Try multiple times to ensure it works
        setTimeout(forcePhoneAutoFill, 100);
        setTimeout(forcePhoneAutoFill, 500);
        setTimeout(forcePhoneAutoFill, 1000);
    }
    
    // Handle billing address selection
    if (billingSelect) {
        billingSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const preview = document.getElementById('billing-address-preview');
            const details = document.getElementById('billing-address-details');
            
            if (selectedOption.value) {
                const address = selectedOption.getAttribute('data-address');
                details.innerHTML = address;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
        
        // Trigger change event if there's a pre-selected value
        if (billingSelect.value) {
            billingSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // Handle "same as shipping" functionality
    if (sameAsShippingCheckbox) {
        sameAsShippingCheckbox.addEventListener('change', function() {
            if (this.checked) {
                billingSection.style.display = 'none';
                updateBillingFromShipping();
            } else {
                billingSection.style.display = 'block';
                // Clear billing selection
                if (billingSelect) {
                    billingSelect.value = '';
                    billingSelect.dispatchEvent(new Event('change'));
                }
            }
        });
        
        // Initial state
        if (sameAsShippingCheckbox.checked) {
            billingSection.style.display = 'none';
            updateBillingFromShipping();
        }
    }
    
    function updateBillingFromShipping() {
        if (shippingSelect && billingSelect && shippingSelect.value) {
            billingSelect.value = shippingSelect.value;
            billingSelect.dispatchEvent(new Event('change'));
        }
    }
});

const form = document.querySelector('form[action="<?php echo e(route('orders.store')); ?>"]');
form.addEventListener('submit', function(e) {
    // Handle "same as shipping" before form submission
    const sameAsShippingCheckbox = document.getElementById('same_as_shipping');
    const shippingSelect = document.getElementById('shipping_address_id');
    const billingSelect = document.getElementById('billing_address_id');
    
    if (sameAsShippingCheckbox && sameAsShippingCheckbox.checked && shippingSelect && billingSelect) {
        billingSelect.value = shippingSelect.value;
    }
    
    if (document.getElementById('stripe').checked) {
        e.preventDefault();
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'stripeToken';
                input.value = result.token.id;
                form.appendChild(input);
                form.submit();
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/orders/checkout.blade.php ENDPATH**/ ?>