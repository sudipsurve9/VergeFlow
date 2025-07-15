@extends('layouts.super_admin')

@section('title', 'Edit Client - Super Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">
                                <i class="fas fa-edit me-2"></i>
                                Edit Client: {{ $client->name }}
                            </h3>
                            <p class="text-muted mb-0">Update client information and settings</p>
                        </div>
                        <a href="{{ route('super_admin.clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Clients
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('super_admin.clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-building me-2"></i>Basic Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $client->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name', $client->company_name) }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" name="contact_email" value="{{ old('contact_email', $client->contact_email) }}" required>
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control @error('contact_phone') is-invalid @enderror" 
                                           id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $client->contact_phone) }}">
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Domain & Branding -->
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-globe me-2"></i>Domain & Branding
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="domain" class="form-label">Custom Domain</label>
                                    <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                           id="domain" name="domain" value="{{ old('domain', $client->domain) }}" 
                                           placeholder="example.com">
                                    @error('domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty to use subdomain</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subdomain" class="form-label">Subdomain</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('subdomain') is-invalid @enderror" 
                                               id="subdomain" name="subdomain" value="{{ old('subdomain', $client->subdomain) }}" 
                                               placeholder="client">
                                        <span class="input-group-text">.vault64.com</span>
                                    </div>
                                    @error('subdomain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Default Theme</label>
                                    <select class="form-select @error('theme') is-invalid @enderror" id="theme" name="theme">
                                        <option value="">Select Theme</option>
                                        <option value="modern" {{ old('theme', $client->theme) == 'modern' ? 'selected' : '' }}>Modern</option>
                                        <option value="classic" {{ old('theme', $client->theme) == 'classic' ? 'selected' : '' }}>Classic</option>
                                        <option value="default" {{ old('theme', $client->theme) == 'default' ? 'selected' : '' }}>Default</option>
                                        <option value="apparel" {{ old('theme', $client->theme) == 'apparel' ? 'selected' : '' }}>Apparel</option>
                                        <option value="webshop" {{ old('theme', $client->theme) == 'webshop' ? 'selected' : '' }}>WebShop</option>
                                        <option value="neon-night" {{ old('theme', $client->theme) == 'neon-night' ? 'selected' : '' }}>Neon Night</option>
                                        <option value="luxury-gold" {{ old('theme', $client->theme) == 'luxury-gold' ? 'selected' : '' }}>Luxury Gold</option>
                                        <option value="furniture" {{ old('theme', $client->theme) == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                        <option value="gadgetpro" {{ old('theme', $client->theme) == 'gadgetpro' ? 'selected' : '' }}>GadgetPro</option>
                                        <option value="ecomarket" {{ old('theme', $client->theme) == 'ecomarket' ? 'selected' : '' }}>EcoMarket</option>
                                        <option value="beauty-bliss" {{ old('theme', $client->theme) == 'beauty-bliss' ? 'selected' : '' }}>Beauty Bliss</option>
                                        <option value="urban-street" {{ old('theme', $client->theme) == 'urban-street' ? 'selected' : '' }}>Urban Street</option>
                                        <option value="kids-world" {{ old('theme', $client->theme) == 'kids-world' ? 'selected' : '' }}>Kids World</option>
                                        <option value="sports-zone" {{ old('theme', $client->theme) == 'sports-zone' ? 'selected' : '' }}>Sports Zone</option>
                                        <option value="tech-hub" {{ old('theme', $client->theme) == 'tech-hub' ? 'selected' : '' }}>Tech Hub</option>
                                        <option value="vintage" {{ old('theme', $client->theme) == 'vintage' ? 'selected' : '' }}>Vintage</option>
                                        <option value="minimalist" {{ old('theme', $client->theme) == 'minimalist' ? 'selected' : '' }}>Minimalist</option>
                                        <option value="corporate" {{ old('theme', $client->theme) == 'corporate' ? 'selected' : '' }}>Corporate</option>
                                    </select>
                                    @error('theme')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Branding Colors -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-palette me-2"></i>Branding Colors
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Primary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color @error('primary_color') is-invalid @enderror" 
                                               id="primary_color" name="primary_color" value="{{ old('primary_color', $client->primary_color ?? '#007bff') }}">
                                        <input type="text" class="form-control" id="primary_color_text" 
                                               value="{{ old('primary_color', $client->primary_color ?? '#007bff') }}" 
                                               placeholder="#007bff">
                                    </div>
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">Secondary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                               id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $client->secondary_color ?? '#6c757d') }}">
                                        <input type="text" class="form-control" id="secondary_color_text" 
                                               value="{{ old('secondary_color', $client->secondary_color ?? '#6c757d') }}" 
                                               placeholder="#6c757d">
                                    </div>
                                    @error('secondary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                </h5>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $client->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Client Statistics -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-chart-bar me-2"></i>Client Statistics
                                </h5>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $client->users_count ?? 0 }}</h4>
                                        <p class="mb-0">Users</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $client->products_count ?? 0 }}</h4>
                                        <p class="mb-0">Products</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $client->orders_count ?? 0 }}</h4>
                                        <p class="mb-0">Orders</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $client->created_at->diffForHumans() }}</h4>
                                        <p class="mb-0">Created</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger" onclick="deleteClient()">
                                        <i class="fas fa-trash me-2"></i>Delete Client
                                    </button>
                                    <div>
                                        <a href="{{ route('super_admin.clients.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Client
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $client->name }}</strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action will permanently delete:</p>
                <ul class="text-danger">
                    <li>All client users</li>
                    <li>All client products</li>
                    <li>All client orders</li>
                    <li>All client data</li>
                </ul>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('super_admin.clients.delete', $client) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Sync color picker with text input
document.getElementById('primary_color').addEventListener('input', function() {
    document.getElementById('primary_color_text').value = this.value;
});

document.getElementById('primary_color_text').addEventListener('input', function() {
    document.getElementById('primary_color').value = this.value;
});

document.getElementById('secondary_color').addEventListener('input', function() {
    document.getElementById('secondary_color_text').value = this.value;
});

document.getElementById('secondary_color_text').addEventListener('input', function() {
    document.getElementById('secondary_color').value = this.value;
});

// Delete client confirmation
function deleteClient() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const companyName = document.getElementById('company_name').value.trim();
    const email = document.getElementById('contact_email').value.trim();
    
    if (!name || !companyName || !email) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address.');
        return false;
    }
});
</script>
@endsection 