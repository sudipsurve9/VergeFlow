@extends($layout)

@section('content')
<div class="container py-5" role="main" aria-label="Edit address form">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card premium-card neon-glow" role="region" aria-label="Edit address form">
                <div class="card-header premium-header text-center">
                    <h2 class="mb-0 neon-glow"><i class="fas fa-edit me-2"></i>Edit Address</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('address.update', $address->id) }}" aria-label="Edit address form">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="shipping" {{ $address->type == 'shipping' ? 'selected' : '' }}>Shipping</option>
                                <option value="billing" {{ $address->type == 'billing' ? 'selected' : '' }}>Billing</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $address->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $address->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $address->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="address_line1" class="form-label">Address Line 1</label>
                            <input id="address_line1" type="text" class="form-control @error('address_line1') is-invalid @enderror" name="address_line1" value="{{ old('address_line1', $address->address_line1) }}" required>
                            @error('address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="address_line2" class="form-label">Address Line 2</label>
                            <input id="address_line2" type="text" class="form-control @error('address_line2') is-invalid @enderror" name="address_line2" value="{{ old('address_line2', $address->address_line2) }}">
                            @error('address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="city" class="form-label">City</label>
                            <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city', $address->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="state" class="form-label">State</label>
                            <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state', $address->state) }}" required>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ old('country', $address->country) }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input id="postal_code" type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" required>
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check mb-3">
                            <input id="is_default" type="checkbox" class="form-check-input" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                            <label for="is_default" class="form-check-label">Set as default address</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-accent btn-lg" aria-label="Update address">
                                <i class="fas fa-save me-2"></i>Update Address
                            </button>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary" aria-label="Back to profile edit">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile Edit
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 