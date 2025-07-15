@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Edit API Integration</h1>
        <a href="{{ route('admin.api-integrations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.api-integrations.update', $integration->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="type" class="form-label">Type <span class="text-danger">*</span>
                        <i class="fas fa-info-circle" title="Select the API provider."></i>
                    </label>
                    <select class="form-select" id="type" name="type" required onchange="toggleShiprocketFields()">
                        @foreach($apiTypes as $type)
                            <option value="{{ $type->name }}" {{ $integration->type == $type->name ? 'selected' : '' }}>
                                @if($type->icon) <i class="{{ $type->icon }}"></i> @endif
                                {{ ucfirst($type->name) }}{{ $type->description ? ' - ' . $type->description : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Shiprocket Specific Fields -->
                <div id="shiprocket-fields" class="shiprocket-specific" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Shiprocket Configuration:</strong> This API requires authentication before tracking shipments.
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span>
                            <i class="fas fa-info-circle" title="Shiprocket account email for authentication"></i>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $integration->email }}" placeholder="your-email@example.com">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span> <small>(Leave blank to keep current)</small>
                            <i class="fas fa-info-circle" title="Shiprocket account password for authentication"></i>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Your Shiprocket password" autocomplete="new-password" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label for="login_url" class="form-label">Login URL
                            <i class="fas fa-info-circle" title="Shiprocket authentication endpoint"></i>
                        </label>
                        <input type="url" class="form-control" id="login_url" name="login_url" 
                               value="{{ $integration->meta['login_url'] ?? 'https://apiv2.shiprocket.in/v1/external/auth/login' }}" 
                               placeholder="https://apiv2.shiprocket.in/v1/external/auth/login">
                    </div>
                    
                    <div class="mb-3">
                        <label for="tracking_url" class="form-label">Tracking URL
                            <i class="fas fa-info-circle" title="Shiprocket tracking endpoint (use {token} placeholder)"></i>
                        </label>
                        <input type="url" class="form-control" id="tracking_url" name="tracking_url" 
                               value="{{ $integration->meta['tracking_url'] ?? 'https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb}' }}" 
                               placeholder="https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb}">
                        <small class="form-text text-muted">Use {awb} placeholder for AWB number and {token} for authentication token</small>
                    </div>
                </div>
                
                <!-- Generic Fields -->
                <div id="generic-fields" class="generic-specific">
                    <div class="mb-3">
                        <label for="email_generic" class="form-label">Email
                            <i class="fas fa-info-circle" title="API account email (if required)"></i>
                        </label>
                        <input type="text" class="form-control" id="email_generic" name="email_generic" value="{{ $integration->email }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_generic" class="form-label">Password <small>(Leave blank to keep current)</small>
                            <i class="fas fa-info-circle" title="API account password (if required)"></i>
                        </label>
                        <input type="password" class="form-control" id="password_generic" name="password_generic" placeholder="API Password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="curl_command" class="form-label">cURL Command
                            <i class="fas fa-info-circle" title="The cURL command for the API"></i>
                        </label>
                        <input type="text" class="form-control" id="curl_command" name="curl_command" value="{{ $integration->curl_command }}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="meta" class="form-label">Meta (JSON, optional)
                        <i class="fas fa-info-circle" title="Any extra configuration as JSON."></i>
                    </label>
                    <textarea class="form-control" id="meta" name="meta" rows="4">{{ $integration->meta ? json_encode($integration->meta, JSON_PRETTY_PRINT) : '' }}</textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Integration</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="testConnection()">Test Connection</button>
                </div>
            </form>
            <div id="test-connection-result" class="mt-2"></div>
        </div>
    </div>
</div>

<script>
function toggleShiprocketFields() {
    const type = document.getElementById('type').value;
    const shiprocketFields = document.getElementById('shiprocket-fields');
    const genericFields = document.getElementById('generic-fields');
    
    if (type === 'shiprocket') {
        shiprocketFields.style.display = 'block';
        genericFields.style.display = 'none';
    } else {
        shiprocketFields.style.display = 'none';
        genericFields.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleShiprocketFields();
    // Force password field to be empty on load
    document.getElementById('password').value = '';
});

function testConnection() {
    const form = document.querySelector('form');
    const data = new FormData();
    
    // Add the integration ID
    data.append('id', '{{ $integration->id }}');
    
    // Explicitly add the type field first
    const typeField = document.getElementById('type');
    const selectedType = typeField ? typeField.value : '';
    
    console.log('Selected type:', selectedType); // Debug log
    
    if (!selectedType) {
        document.getElementById('test-connection-result').innerHTML = "<div class='alert alert-danger'>Please select an API type first.</div>";
        return;
    }
    
    // Add the type field explicitly
    data.append('type', selectedType);
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name=csrf-token]').content;
    data.append('_token', csrfToken);
    
    // Add the correct email and password based on the selected type
    if (selectedType === 'shiprocket') {
        data.append('email', document.getElementById('email').value);
        data.append('password', document.getElementById('password').value);
        data.append('login_url', document.getElementById('login_url').value);
        data.append('tracking_url', document.getElementById('tracking_url').value);
    } else {
        // For delhivery and other types, use generic fields
        const emailField = document.getElementById('email_generic');
        const passwordField = document.getElementById('password_generic');
        const curlField = document.getElementById('curl_command');
        
        if (emailField) data.append('email_generic', emailField.value);
        if (passwordField) data.append('password_generic', passwordField.value);
        if (curlField) data.append('curl_command', curlField.value);
    }
    
    // Debug: Log all form data
    console.log('Form data being sent:');
    for (let [key, value] of data.entries()) {
        console.log(key + ': ' + value);
    }
    
    fetch("{{ route('admin.api-integrations.test', $integration->id) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: data
    })
    .then(res => res.json())
    .then(res => {
        const el = document.getElementById('test-connection-result');
        el.innerHTML = `<div class='alert alert-${res.success ? 'success' : 'danger'}'>${res.message}</div>`;
    })
    .catch((error) => {
        console.error('Test connection error:', error);
        document.getElementById('test-connection-result').innerHTML = "<div class='alert alert-danger'>Error testing connection.</div>";
    });
}
</script>
@endsection 