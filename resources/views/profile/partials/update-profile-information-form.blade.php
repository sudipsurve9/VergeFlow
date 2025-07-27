<div class="profile-form-section">
    <p class="text-muted mb-4">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-user me-2"></i>{{ __('Full Name') }}
            </label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name', $user->name) }}" 
                   required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>{{ __('Email Address') }}
            </label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $user->email) }}" 
                   required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn btn-link p-0 ms-2 text-decoration-underline">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">
                <i class="fas fa-phone me-2"></i>{{ __('Phone Number') }}
            </label>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" 
                   autocomplete="tel">
            @error('phone')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="address" class="form-label">
                <i class="fas fa-map-marker-alt me-2"></i>{{ __('Address') }}
            </label>
            <textarea class="form-control @error('address') is-invalid @enderror" 
                      id="address" name="address" rows="3" 
                      autocomplete="street-address">{{ old('address', $user->address ?? '') }}</textarea>
            @error('address')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-accent btn-lg">
                <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success mb-0 py-2" x-data="{ show: true }" x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)" x-transition>
                    <i class="fas fa-check-circle me-2"></i>{{ __('Profile updated successfully!') }}
                </div>
            @endif
        </div>
    </form>
</div>
