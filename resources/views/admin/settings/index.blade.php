@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Settings Dashboard</h2>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title"><i class="fas fa-cogs me-2"></i>General Settings</h5>
                        <p class="card-text">Manage site name, logo, contact info, and more. (Coming soon)</p>
                    </div>
                    <a href="#" class="btn btn-secondary disabled mt-3">Coming Soon</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title"><i class="fas fa-envelope me-2"></i>Email Settings</h5>
                        <p class="card-text">Configure email sender, SMTP, and notifications. (Coming soon)</p>
                    </div>
                    <a href="#" class="btn btn-secondary disabled mt-3">Coming Soon</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> Template management has been moved to the Super Admin panel for centralized control.
    </div>
</div>
@endsection 