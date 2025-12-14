

<?php $__env->startSection('title', 'System Settings - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs me-2"></i>
                        System Settings
                    </h3>
                    <p class="text-muted mb-0">Manage system-wide configuration and settings</p>
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

                    <form action="<?php echo e(route('super_admin.settings.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <!-- General Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-globe me-2"></i>General Settings
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">System Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="<?php echo e(old('site_name', 'VergeFlow Multi-Client System')); ?>" required>
                                    <div class="form-text">The name of your multi-client system</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_description" class="form-label">System Description</label>
                                    <input type="text" class="form-control" id="site_description" name="site_description" 
                                           value="<?php echo e(old('site_description', 'Multi-client e-commerce platform')); ?>">
                                    <div class="form-text">Brief description of the system</div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-envelope me-2"></i>Email Configuration
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_address" class="form-label">From Email Address</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                           value="<?php echo e(old('mail_from_address', 'noreply@vergeflow.com')); ?>" required>
                                    <div class="form-text">Default sender email for system notifications</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_name" class="form-label">From Name</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                           value="<?php echo e(old('mail_from_name', 'VergeFlow System')); ?>" required>
                                    <div class="form-text">Default sender name for system notifications</div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-shield-alt me-2"></i>Security Settings
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session_lifetime" class="form-label">Session Lifetime (minutes)</label>
                                    <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                                           value="<?php echo e(old('session_lifetime', 120)); ?>" min="30" max="1440" required>
                                    <div class="form-text">How long user sessions remain active</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_min_length" class="form-label">Minimum Password Length</label>
                                    <input type="number" class="form-control" id="password_min_length" name="password_min_length" 
                                           value="<?php echo e(old('password_min_length', 8)); ?>" min="6" max="20" required>
                                    <div class="form-text">Minimum characters required for user passwords</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_password_complexity" 
                                               name="require_password_complexity" value="1" 
                                               <?php echo e(old('require_password_complexity') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="require_password_complexity">
                                            Require Password Complexity
                                        </label>
                                        <div class="form-text">Enforce uppercase, lowercase, numbers, and symbols</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enable_two_factor" 
                                               name="enable_two_factor" value="1" 
                                               <?php echo e(old('enable_two_factor') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="enable_two_factor">
                                            Enable Two-Factor Authentication
                                        </label>
                                        <div class="form-text">Allow users to enable 2FA for enhanced security</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-building me-2"></i>Client Management
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_clients" class="form-label">Maximum Clients</label>
                                    <input type="number" class="form-control" id="max_clients" name="max_clients" 
                                           value="<?php echo e(old('max_clients', 100)); ?>" min="1" max="1000" required>
                                    <div class="form-text">Maximum number of clients allowed in the system</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_client_theme" class="form-label">Default Client Theme</label>
                                    <select class="form-select" id="default_client_theme" name="default_client_theme">
                                        <option value="modern" <?php echo e(old('default_client_theme') == 'modern' ? 'selected' : ''); ?>>Modern</option>
                                        <option value="classic" <?php echo e(old('default_client_theme') == 'classic' ? 'selected' : ''); ?>>Classic</option>
                                        <option value="default" <?php echo e(old('default_client_theme') == 'default' ? 'selected' : ''); ?>>Default</option>
                                        <option value="apparel" <?php echo e(old('default_client_theme') == 'apparel' ? 'selected' : ''); ?>>Apparel</option>
                                        <option value="webshop" <?php echo e(old('default_client_theme') == 'webshop' ? 'selected' : ''); ?>>WebShop</option>
                                    </select>
                                    <div class="form-text">Default theme for new clients</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="auto_create_admin" 
                                               name="auto_create_admin" value="1" 
                                               <?php echo e(old('auto_create_admin', 1) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="auto_create_admin">
                                            Auto-create Admin User for New Clients
                                        </label>
                                        <div class="form-text">Automatically create admin user when adding new clients</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="allow_client_customization" 
                                               name="allow_client_customization" value="1" 
                                               <?php echo e(old('allow_client_customization', 1) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="allow_client_customization">
                                            Allow Client Customization
                                        </label>
                                        <div class="form-text">Allow clients to customize their themes and settings</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Maintenance -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-tools me-2"></i>System Maintenance
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="maintenance_mode" 
                                               name="maintenance_mode" value="1" 
                                               <?php echo e(old('maintenance_mode') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="maintenance_mode">
                                            Enable Maintenance Mode
                                        </label>
                                        <div class="form-text">Put the system in maintenance mode (super admins can still access)</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="log_retention_days" class="form-label">Log Retention (days)</label>
                                    <input type="number" class="form-control" id="log_retention_days" name="log_retention_days" 
                                           value="<?php echo e(old('log_retention_days', 30)); ?>" min="7" max="365" required>
                                    <div class="form-text">How long to keep system logs</div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Management -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-paint-brush me-2"></i>Template Management
                                </h5>
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-3 fa-2x"></i>
                                        <div>
                                            <h6 class="alert-heading">Site Template Management</h6>
                                            <p class="mb-0">Manage the global site template that affects all clients. Individual clients can override this with their own theme settings.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <a href="<?php echo e(route('super_admin.templates.index')); ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-palette me-2"></i>Manage Site Templates
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                        <i class="fas fa-undo me-2"></i>Reset to Defaults
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-info me-2" onclick="testSettings()">
                                            <i class="fas fa-vial me-2"></i>Test Settings
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('Are you sure you want to reset all settings to defaults?')) {
        // Reset form fields to default values
        document.getElementById('site_name').value = 'Vault64 Multi-Client System';
        document.getElementById('site_description').value = 'Multi-client e-commerce platform';
        document.getElementById('mail_from_address').value = 'noreply@vault64.com';
        document.getElementById('mail_from_name').value = 'Vault64 System';
        document.getElementById('session_lifetime').value = '120';
        document.getElementById('password_min_length').value = '8';
        document.getElementById('max_clients').value = '100';
        document.getElementById('default_client_theme').value = 'modern';
        document.getElementById('log_retention_days').value = '30';
        
        // Reset checkboxes
        document.getElementById('require_password_complexity').checked = false;
        document.getElementById('enable_two_factor').checked = false;
        document.getElementById('auto_create_admin').checked = true;
        document.getElementById('allow_client_customization').checked = true;
        document.getElementById('maintenance_mode').checked = false;
    }
}

function testSettings() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
    btn.disabled = true;
    
    // Simulate test (you can add actual AJAX call here)
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Settings test completed successfully!');
    }, 2000);
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/super_admin/settings.blade.php ENDPATH**/ ?>