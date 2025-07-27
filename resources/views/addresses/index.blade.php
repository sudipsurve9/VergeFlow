@extends('layouts.app_modern')

@section('title', 'Your Address Book')

@section('content')
<div class="container py-5" role="main" aria-label="Address book main content">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold mb-3 neon-glow"><i class="fas fa-map-marker-alt me-2"></i>Your Address Book</h1>
            <p class="subtitle-glow">Manage your shipping and billing addresses</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 text-end">
            <button onclick="openAddAddressModal()" class="btn btn-accent btn-lg banner-btn" aria-label="Add new address">
                <i class="fas fa-plus me-2"></i>Add New Address
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Address Cards -->
    <div class="row">
        @forelse($addresses as $address)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card theme-card h-100" style="box-shadow: 0 0 32px var(--accent-color, #FFB30055), 0 0 64px var(--accent-glow, #FF6A0033);">
                    <div class="card-header theme-card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($address->type === 'home')
                                <i class="fas fa-home text-accent me-2 fa-lg"></i>
                            @elseif($address->type === 'work')
                                <i class="fas fa-building text-accent me-2 fa-lg"></i>
                            @else
                                <i class="fas fa-map-marker-alt text-accent me-2 fa-lg"></i>
                            @endif
                            <div>
                                <h5 class="mb-0 neon-glow">{{ $address->display_name }}</h5>
                                <small class="text-muted">{{ ucfirst($address->address_type) }} Address</small>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            @if($address->is_default_shipping)
                                <span class="badge bg-primary">Default Shipping</span>
                            @endif
                            @if($address->is_default_billing)
                                <span class="badge bg-success">Default Billing</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Address Details -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-accent">{{ $address->name }}</h6>
                            <p class="text-muted mb-1"><i class="fas fa-phone me-1"></i>{{ $address->phone }}</p>
                            <div class="mt-2">
                                <p class="mb-1">{{ $address->address_line1 }}</p>
                                @if($address->address_line2)
                                    <p class="mb-1">{{ $address->address_line2 }}</p>
                                @endif
                                @if($address->landmark)
                                    <p class="mb-1 text-muted"><i class="fas fa-map-pin me-1"></i>Near {{ $address->landmark }}</p>
                                @endif
                                <p class="mb-0">{{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}</p>
                            </div>
                        </div>

                        <!-- Delivery Instructions -->
                        @if($address->delivery_instructions)
                            <div class="alert alert-warning py-2 mb-3">
                                <small class="fw-bold">Delivery Instructions:</small><br>
                                <small>{{ $address->delivery_instructions }}</small>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-footer d-flex flex-wrap gap-2 justify-content-center">
                        <button onclick="editAddress({{ $address->id }})" class="btn btn-sm btn-outline-accent icon-btn-glow">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        
                        @if(!$address->is_default_shipping && in_array($address->address_type, ['shipping', 'both']))
                            <button onclick="setDefaultShipping({{ $address->id }})" class="btn btn-sm btn-outline-success icon-btn-glow">
                                <i class="fas fa-truck me-1"></i>Default Shipping
                            </button>
                        @endif
                        
                        @if(!$address->is_default_billing && in_array($address->address_type, ['billing', 'both']))
                            <button onclick="setDefaultBilling({{ $address->id }})" class="btn btn-sm btn-outline-info icon-btn-glow">
                                <i class="fas fa-credit-card me-1"></i>Default Billing
                            </button>
                        @endif
                        
                        <button onclick="deleteAddress({{ $address->id }})" class="btn btn-sm btn-outline-danger icon-btn-glow">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-map-marker-alt fa-5x text-muted"></i>
                    </div>
                    <h3 class="text-muted mb-3 neon-glow">No addresses yet</h3>
                    <p class="text-muted mb-4">Add your first address to get started with faster checkout</p>
                    <button onclick="openAddAddressModal()" class="btn btn-accent btn-lg banner-btn" aria-label="Add your first address">
                        <i class="fas fa-plus me-2"></i>Add Your First Address
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div id="addressModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content theme-card">
            <div class="modal-header theme-card-header">
                <h5 id="modalTitle" class="modal-title neon-glow"><i class="fas fa-map-marker-alt me-2"></i>Add New Address</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeAddressModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="addressForm" method="POST">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="row g-3">
                        <!-- Address Type -->
                        <div class="col-12">
                            <label class="form-label text-accent fw-bold">Address Type</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="address-type-card d-flex align-items-center p-3 border border-accent rounded cursor-pointer">
                                        <input type="radio" name="type" value="home" class="me-3" checked>
                                        <div>
                                            <i class="fas fa-home text-accent me-2"></i>
                                            <span class="fw-medium text-light">Home</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="address-type-card d-flex align-items-center p-3 border border-accent rounded cursor-pointer">
                                        <input type="radio" name="type" value="work" class="me-3">
                                        <div>
                                            <i class="fas fa-building text-accent me-2"></i>
                                            <span class="fw-medium text-light">Work</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="address-type-card d-flex align-items-center p-3 border border-accent rounded cursor-pointer">
                                        <input type="radio" name="type" value="other" class="me-3">
                                        <div>
                                            <i class="fas fa-map-marker-alt text-accent me-2"></i>
                                            <span class="fw-medium text-light">Other</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Label -->
                        <div class="col-12">
                            <label for="label" class="form-label text-accent fw-bold">Custom Label (Optional)</label>
                            <input type="text" id="label" name="label" class="form-control theme-input" placeholder="e.g., Mom's House, Office Building">
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label text-accent fw-bold">Full Name *</label>
                            <input type="text" id="name" name="name" required class="form-control theme-input">
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label text-accent fw-bold">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required class="form-control theme-input">
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-12">
                            <label for="address_line1" class="form-label text-accent fw-bold">Address Line 1 *</label>
                            <input type="text" id="address_line1" name="address_line1" required class="form-control theme-input" placeholder="House/Flat number, Building name">
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-12">
                            <label for="address_line2" class="form-label text-accent fw-bold">Address Line 2 (Optional)</label>
                            <input type="text" id="address_line2" name="address_line2" class="form-control theme-input" placeholder="Street, Area">
                        </div>

                        <!-- Landmark -->
                        <div class="col-md-6">
                            <label for="landmark" class="form-label text-accent fw-bold">Landmark (Optional)</label>
                            <input type="text" id="landmark" name="landmark" class="form-control theme-input" placeholder="Near...">
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <label for="city" class="form-label text-accent fw-bold">City *</label>
                            <input type="text" id="city" name="city" required class="form-control theme-input">
                        </div>

                        <!-- State -->
                        <div class="col-md-4">
                            <label for="state" class="form-label text-accent fw-bold">State *</label>
                            <input type="text" id="state" name="state" required class="form-control theme-input">
                        </div>

                        <!-- Postal Code -->
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label text-accent fw-bold">Postal Code *</label>
                            <input type="text" id="postal_code" name="postal_code" required class="form-control theme-input">
                        </div>

                        <!-- Country -->
                        <div class="col-md-4">
                            <label for="country" class="form-label text-accent fw-bold">Country *</label>
                            <select id="country" name="country" required class="form-select theme-input">
                                <option value="India">India</option>
                                <option value="United States">United States</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <option value="Australia">Australia</option>
                            </select>
                        </div>

                        <!-- Address Usage -->
                        <div class="col-12">
                            <label class="form-label text-accent fw-bold">Use this address for</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="address-type-card d-flex align-items-center p-3 border border-accent rounded cursor-pointer">
                                        <input type="radio" name="address_type" value="shipping" class="me-3">
                                        <span class="fw-medium text-light">Shipping Only</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="address-type-card d-flex align-items-center p-3 border border-accent rounded cursor-pointer">
                                        <input type="radio" name="address_type" value="billing" class="me-3">
                                        <span class="fw-medium text-light">Billing Only</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="address-type-card d-flex align-items-center p-3 border border-accent rounded cursor-pointer">
                                        <input type="radio" name="address_type" value="both" class="me-3" checked>
                                        <span class="fw-medium text-light">Both</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Instructions -->
                        <div class="col-12">
                            <label for="delivery_instructions" class="form-label text-accent fw-bold">Delivery Instructions (Optional)</label>
                            <textarea id="delivery_instructions" name="delivery_instructions" rows="3" class="form-control theme-input" placeholder="Special delivery instructions..."></textarea>
                        </div>

                        <!-- Default Settings -->
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default_shipping" value="1" class="form-check-input" id="defaultShipping">
                                    <label class="form-check-label text-light" for="defaultShipping">Set as default shipping address</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="is_default_billing" value="1" class="form-check-input" id="defaultBilling">
                                    <label class="form-check-label text-light" for="defaultBilling">Set as default billing address</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAddressModal()" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" onclick="submitAddressForm()" class="btn btn-accent banner-btn" id="saveAddressBtn">
                    <i class="fas fa-save me-1"></i><span id="submitText">Save Address</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content theme-card">
            <div class="modal-header theme-card-header">
                <h5 class="modal-title neon-glow">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    <span id="confirmTitle">Confirm Action</span>
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i id="confirmIcon" class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                </div>
                <p id="confirmMessage" class="text-light fs-5 mb-0">Are you sure you want to delete this address?</p>
                <p class="text-muted small mt-2">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" onclick="closeConfirmModal()" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" id="confirmOkBtn" class="btn btn-danger banner-btn">
                    <i class="fas fa-trash me-1"></i>Delete Address
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openAddAddressModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>Add New Address';
    document.getElementById('submitText').textContent = 'Save Address';
    document.getElementById('addressForm').action = '{{ route("addresses.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('addressForm').reset();
    
    // Show Bootstrap modal
    const modal = document.getElementById('addressModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.classList.add('modal-open');
    
    // Add backdrop
    if (!document.querySelector('.modal-backdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
}

function editAddress(id) {
    // Fetch address data and populate form
    fetch(`/addresses/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            // Populate form with address data
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Address';
            document.getElementById('submitText').textContent = 'Update Address';
            document.getElementById('addressForm').action = `/addresses/${id}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            
            // Populate form fields
            if (data.address) {
                document.getElementById('label').value = data.address.label || '';
                document.getElementById('name').value = data.address.name || '';
                document.getElementById('phone').value = data.address.phone || '';
                document.getElementById('address_line1').value = data.address.address_line1 || '';
                document.getElementById('address_line2').value = data.address.address_line2 || '';
                document.getElementById('landmark').value = data.address.landmark || '';
                document.getElementById('city').value = data.address.city || '';
                document.getElementById('state').value = data.address.state || '';
                document.getElementById('postal_code').value = data.address.postal_code || '';
                document.getElementById('country').value = data.address.country || '';
                document.getElementById('delivery_instructions').value = data.address.delivery_instructions || '';
                
                // Set radio buttons
                document.querySelector(`input[name="type"][value="${data.address.type}"]`).checked = true;
                document.querySelector(`input[name="address_type"][value="${data.address.address_type}"]`).checked = true;
                
                // Set checkboxes
                document.getElementById('defaultShipping').checked = data.address.is_default_shipping || false;
                document.getElementById('defaultBilling').checked = data.address.is_default_billing || false;
            }
            
            // Show Bootstrap modal
            const modal = document.getElementById('addressModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        })
        .catch(error => {
            console.error('Error fetching address:', error);
            alert('Error loading address data. Please try again.');
        });
}

function submitAddressForm() {
    const form = document.getElementById('addressForm');
    const saveBtn = document.getElementById('saveAddressBtn');
    const submitText = document.getElementById('submitText');
    
    // Validate required fields
    const requiredFields = ['name', 'phone', 'address_line1', 'city', 'state', 'postal_code', 'country'];
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        alert('Please fill in all required fields.');
        return;
    }
    
    // Show loading state
    saveBtn.disabled = true;
    submitText.textContent = 'Saving...';
    
    // Submit form
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message || 'Address saved successfully!');
            
            // Close modal
            closeAddressModal();
            
            // Reload page to show updated addresses
            window.location.reload();
        } else {
            // Show error message
            alert(data.message || 'Error saving address. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error saving address:', error);
        alert('Error saving address. Please try again.');
    })
    .finally(() => {
        // Reset button state
        saveBtn.disabled = false;
        submitText.textContent = 'Save Address';
    });
}

function closeAddressModal() {
    const modal = document.getElementById('addressModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
    
    // Remove backdrop
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

function setDefaultShipping(id) {
    fetch(`/addresses/${id}/set-default-shipping`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function setDefaultBilling(id) {
    fetch(`/addresses/${id}/set-default-billing`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

let currentDeleteId = null;

function showConfirmModal(title, message, icon, onConfirm) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmIcon').className = `fas ${icon} fa-3x text-danger mb-3`;
    
    // Set up the confirm button click handler
    const confirmBtn = document.getElementById('confirmOkBtn');
    confirmBtn.onclick = onConfirm;
    
    // Show the modal
    const modal = document.getElementById('confirmModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.classList.add('modal-open');
    
    // Add backdrop
    if (!document.querySelector('.modal-backdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
    
    // Remove backdrop
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    
    currentDeleteId = null;
}

function deleteAddress(id) {
    currentDeleteId = id;
    
    showConfirmModal(
        'Delete Address',
        'Are you sure you want to delete this address?',
        'fa-trash-alt',
        function() {
            // Show loading state on confirm button
            const confirmBtn = document.getElementById('confirmOkBtn');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';
            confirmBtn.disabled = true;
            
            fetch(`/addresses/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Close modal
                    closeConfirmModal();
                    
                    // Show success toast (you can replace with a nicer notification)
                    showSuccessToast(data.message || 'Address deleted successfully!');
                    
                    // Refresh the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Show error message
                    alert(data.message || 'Error deleting address. Please try again.');
                    
                    // Reset button state
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error deleting address:', error);
                alert('Error deleting address. Please try again.');
                
                // Reset button state
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            });
        }
    );
}

function showSuccessToast(message) {
    // Create a simple success toast
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Success</strong>
            </div>
            <div class="toast-body bg-dark text-light">
                ${message}
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Form submission
document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>

<style>
.theme-card {
    background: var(--card-bg, #232323);
    border: 2px solid var(--accent-color, #FFB300);
    border-radius: 16px;
    box-shadow: 0 0 16px var(--accent-glow, #FF6A0033);
}
.theme-card-header {
    background: linear-gradient(90deg, var(--primary-bg, #181818) 0%, var(--accent-glow, #FF6A00) 100%) !important;
    color: var(--accent-color, #FFB300) !important;
    font-family: 'Orbitron', 'Montserrat', Arial, sans-serif;
    font-size: 1.1rem;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    border-bottom: 2px solid var(--accent-color, #FFB300);
}
.subtitle-glow {
    color: var(--text-primary, #fff);
    font-size: 1.2rem;
    font-weight: 600;
    text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00), 0 0 32px #000;
    letter-spacing: 0.5px;
    margin-bottom: 1.5rem;
}
[data-theme="light"] .subtitle-glow {
    color: var(--text-primary, #181818);
    text-shadow: 0 0 8px #FFB30033, 0 0 16px #FF6A0033, 0 0 32px #fff;
}
.banner-btn {
    font-weight: 700;
    border-radius: 30px;
    padding: 10px 24px;
    font-size: 1rem;
    margin-bottom: 6px;
    box-shadow: 0 0 8px var(--accent-color, #FFB30099);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.banner-btn.btn-accent {
    background: linear-gradient(45deg, var(--accent-color, #FFB300), var(--accent-glow, #FF6A00));
    color: #fff !important;
    border: none;
}
.banner-btn.btn-accent:hover {
    background: linear-gradient(45deg, var(--accent-glow, #FF6A00), var(--accent-color, #FFB300));
    color: #181818 !important;
    box-shadow: 0 0 24px var(--accent-color, #FFB300);
}
.banner-btn.btn-outline-accent {
    border: 2px solid var(--accent-color, #FFB300);
    color: var(--accent-color, #FFB300) !important;
    background: transparent;
}
.banner-btn.btn-outline-accent:hover {
    background: var(--accent-color, #FFB300);
    color: #181818 !important;
    box-shadow: 0 0 24px var(--accent-color, #FFB300);
}
.card, .card-body {
    background: var(--card-bg, #232323) !important;
    color: var(--text-primary, #fff) !important;
}
.card-footer {
    background: var(--card-bg, #232323) !important;
    border-top: 1px solid var(--accent-color, #FFB300);
}
.text-accent {
    color: var(--accent-color, #FFB300) !important;
}
.icon-btn-glow {
    transition: all 0.2s;
}
.icon-btn-glow:hover {
    box-shadow: 0 0 16px var(--accent-color, #FFB30099);
    transform: translateY(-2px);
}
[data-theme="light"] .theme-card, [data-theme="light"] .card, [data-theme="light"] .card-body {
    background: #fff !important;
    color: #181818 !important;
    border-color: #FFB300 !important;
}
[data-theme="light"] .theme-card-header {
    background: linear-gradient(90deg, #fffbe6 0%, #FFB300 100%) !important;
    color: #FF6A00 !important;
    border-bottom: 2px solid #FF6A00 !important;
}
[data-theme="light"] .banner-btn.btn-accent {
    background: linear-gradient(45deg, #FF6A00, #FFB300);
    color: #fff !important;
}
[data-theme="light"] .banner-btn.btn-accent:hover {
    background: linear-gradient(45deg, #FFB300, #FF6A00);
    color: #fff !important;
}
[data-theme="light"] .banner-btn.btn-outline-accent {
    border: 2px solid #FF6A00;
    color: #FF6A00 !important;
}
[data-theme="light"] .banner-btn.btn-outline-accent:hover {
    background: #FF6A00;
    color: #fff !important;
}
.neon-glow {
    text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00);
    animation: neonPulse 3s infinite alternate;
}
@keyframes neonPulse {
    from { text-shadow: 0 0 4px var(--accent-color, #FFB300), 0 0 8px var(--accent-glow, #FF6A00); }
    to { text-shadow: 0 0 8px var(--accent-color, #FFB300), 0 0 16px var(--accent-glow, #FF6A00); }
}
.modal-content {
    background: var(--card-bg, #232323) !important;
    border: 2px solid var(--accent-color, #FFB300) !important;
    border-radius: 16px !important;
}
.modal-header {
    border-bottom: 2px solid var(--accent-color, #FFB300) !important;
}
.modal-footer {
    border-top: 1px solid var(--accent-color, #FFB300) !important;
    background: var(--card-bg, #232323) !important;
}
.theme-input {
    background: var(--input-bg, #1a1a1a) !important;
    border: 1px solid var(--accent-color, #FFB300) !important;
    color: var(--text-primary, #fff) !important;
    border-radius: 8px !important;
}
.theme-input:focus {
    background: var(--input-bg, #1a1a1a) !important;
    border-color: var(--accent-glow, #FF6A00) !important;
    box-shadow: 0 0 8px var(--accent-color, #FFB30066) !important;
    color: var(--text-primary, #fff) !important;
}
.theme-input::placeholder {
    color: var(--text-secondary, #ccc) !important;
}
.address-type-card {
    background: var(--input-bg, #1a1a1a) !important;
    transition: all 0.2s;
}
.address-type-card:hover {
    background: var(--accent-color, #FFB300) !important;
    color: #181818 !important;
    box-shadow: 0 0 8px var(--accent-color, #FFB30099);
}
.address-type-card:hover span {
    color: #181818 !important;
}
.form-check-input:checked {
    background-color: var(--accent-color, #FFB300) !important;
    border-color: var(--accent-color, #FFB300) !important;
}
.form-check-input {
    border-color: var(--accent-color, #FFB300) !important;
}
[data-theme="light"] .modal-content {
    background: #fff !important;
    color: #181818 !important;
}
[data-theme="light"] .theme-input {
    background: #f8f9fa !important;
    color: #181818 !important;
    border-color: #FF6A00 !important;
}
[data-theme="light"] .theme-input:focus {
    background: #f8f9fa !important;
    color: #181818 !important;
}
[data-theme="light"] .address-type-card {
    background: #f8f9fa !important;
}
[data-theme="light"] .address-type-card:hover {
    background: #FF6A00 !important;
    color: #fff !important;
}
[data-theme="light"] .address-type-card:hover span {
    color: #fff !important;
}
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 8px rgba(220, 53, 69, 0.3) !important;
}
.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 8px rgba(220, 53, 69, 0.5) !important;
}
@media (max-width: 768px) {
    .banner-btn {
        font-size: 0.95rem;
        padding: 8px 12px;
    }
}
</style>

@endsection
