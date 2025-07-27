<div class="profile-form-section">
    <p class="text-muted mb-4">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">
                <i class="fas fa-lock me-2"></i>{{ __('Current Password') }}
            </label>
            <div class="input-group">
                <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_current_password" name="current_password" 
                       autocomplete="current-password" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_current_password')">
                    <i class="fas fa-eye" id="update_password_current_password-icon"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">
                <i class="fas fa-key me-2"></i>{{ __('New Password') }}
            </label>
            <div class="input-group">
                <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password" name="password" 
                       autocomplete="new-password" required minlength="8">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password')">
                    <i class="fas fa-eye" id="update_password_password-icon"></i>
                </button>
            </div>
            @error('password', 'updatePassword')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
            <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>Password must be at least 8 characters long.
            </div>
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label">
                <i class="fas fa-check-double me-2"></i>{{ __('Confirm New Password') }}
            </label>
            <div class="input-group">
                <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password_confirmation" name="password_confirmation" 
                       autocomplete="new-password" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password_confirmation')">
                    <i class="fas fa-eye" id="update_password_password_confirmation-icon"></i>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-warning btn-lg">
                <i class="fas fa-shield-alt me-2"></i>{{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success mb-0 py-2" x-data="{ show: true }" x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)" x-transition>
                    <i class="fas fa-check-circle me-2"></i>{{ __('Password updated successfully!') }}
                </div>
            @endif
        </div>
    </form>

    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</div>
