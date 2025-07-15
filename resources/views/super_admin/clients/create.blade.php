@extends('layouts.super_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Create New Client</h1>
                <a href="{{ route('super_admin.clients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Clients
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('super_admin.clients.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Basic Information</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Client Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name *</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Contact Email *</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                   id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                   id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Domain & Branding</h5>
                        
                        <div class="mb-3">
                            <label for="domain" class="form-label">Custom Domain</label>
                            <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                   id="domain" name="domain" value="{{ old('domain') }}" 
                                   placeholder="example.com">
                            <small class="form-text text-muted">Leave empty to use subdomain</small>
                            @error('domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subdomain" class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" 
                                       id="subdomain" name="subdomain" value="{{ old('subdomain') }}" 
                                       placeholder="clientname">
                                <span class="input-group-text">.vergeflow.com</span>
                            </div>
                            <small class="form-text text-muted">Will be used if no custom domain is set</small>
                            @error('subdomain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="primary_color" class="form-label">Primary Color</label>
                            <input type="color" class="form-control @error('primary_color') is-invalid @enderror" 
                                   id="primary_color" name="primary_color" value="{{ old('primary_color', '#007bff') }}">
                            @error('primary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="secondary_color" class="form-label">Secondary Color</label>
                            <input type="color" class="form-control @error('secondary_color') is-invalid @enderror" 
                                   id="secondary_color" name="secondary_color" value="{{ old('secondary_color', '#6c757d') }}">
                            @error('secondary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="theme" class="form-label">Default Theme</label>
                            <select class="form-select @error('theme') is-invalid @enderror" id="theme" name="theme">
                                <option value="default" {{ old('theme') == 'default' ? 'selected' : '' }}>Default</option>
                                <option value="modern" {{ old('theme') == 'modern' ? 'selected' : '' }}>Modern</option>
                                <option value="apparel" {{ old('theme') == 'apparel' ? 'selected' : '' }}>Apparel</option>
                                <option value="webshop" {{ old('theme') == 'webshop' ? 'selected' : '' }}>WebShop</option>
                                <option value="neon" {{ old('theme') == 'neon' ? 'selected' : '' }}>Neon Night</option>
                                <option value="luxury" {{ old('theme') == 'luxury' ? 'selected' : '' }}>Luxury Gold</option>
                                <option value="furniture" {{ old('theme') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                <option value="gadget" {{ old('theme') == 'gadget' ? 'selected' : '' }}>GadgetPro</option>
                                <option value="eco" {{ old('theme') == 'eco' ? 'selected' : '' }}>EcoMarket</option>
                                <option value="beauty" {{ old('theme') == 'beauty' ? 'selected' : '' }}>Beauty Bliss</option>
                                <option value="urban" {{ old('theme') == 'urban' ? 'selected' : '' }}>Urban Street</option>
                                <option value="kids" {{ old('theme') == 'kids' ? 'selected' : '' }}>Kids World</option>
                            </select>
                            @error('theme')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3">Additional Information</h5>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> An admin user will be automatically created for this client with the email: 
                            <code>admin@[subdomain].vault64.com</code> and password: <code>password123</code>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Client
                        </button>
                        <a href="{{ route('super_admin.clients.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 