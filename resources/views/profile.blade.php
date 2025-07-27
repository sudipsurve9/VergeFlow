@extends($layout)

@section('content')
<div class="container py-5" role="main" aria-label="Profile main content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card premium-card neon-glow" role="region" aria-label="Profile information">
                <div class="card-header premium-header text-center">
                    <h2 class="mb-0 neon-glow"><i class="fas fa-user-circle me-2"></i>My Profile</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" aria-label="Success message">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close alert"></button>
                        </div>
                    @endif
                    
                    <div class="mb-4 text-center">
                        <i class="fas fa-user-circle fa-5x neon-glow"></i>
                    </div>
                    <dl class="row mb-0">
                        <dt class="col-4">Name:</dt>
                        <dd class="col-8">{{ $user->name }}</dd>
                        <dt class="col-4">Email:</dt>
                        <dd class="col-8">{{ $user->email }}</dd>
                        <dt class="col-4">Phone:</dt>
                        <dd class="col-8">{{ $user->phone ?? '—' }}</dd>
                        <dt class="col-4">Address:</dt>
                        <dd class="col-8">{{ $user->address ?? '—' }}</dd>
                    </dl>
                    <div class="mt-4 text-center">
                        <a href="{{ route('profile.edit') }}" class="btn btn-accent btn-lg me-3 mb-2" aria-label="Edit profile information">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('addresses.index') }}" class="btn btn-outline-accent btn-lg mb-2" aria-label="Manage your addresses">
                            <i class="fas fa-map-marker-alt me-2"></i>Address Book
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

