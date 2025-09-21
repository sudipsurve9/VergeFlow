@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid" role="main" aria-label="Admin dashboard main content">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" role="region" aria-label="Statistics overview">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" role="region" aria-label="Total orders statistics">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" role="region" aria-label="Total revenue statistics">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2" role="region" aria-label="Total products statistics">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_products'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" role="region" aria-label="Total customers statistics">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4" role="region" aria-label="Quick actions">
        <div class="col-12">
            <div class="card shadow" role="region" aria-label="Quick actions panel">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-block" aria-label="Add new product">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-block" aria-label="Add new category">
                                <i class="fas fa-tags"></i> Add Category
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-warning btn-block" aria-label="Add new coupon">
                                <i class="fas fa-ticket-alt"></i> Add Coupon
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.banners.create') }}" class="btn btn-info btn-block" aria-label="Add new banner">
                                <i class="fas fa-images"></i> Add Banner
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mb-4" role="region" aria-label="Recent orders and system status">
        <div class="col-lg-8">
            <div class="card shadow" role="region" aria-label="Recent orders">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary" aria-label="View all orders">View All</a>
                </div>
                <div class="card-body">
                    @if(isset($recent_orders) && $recent_orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" role="table" aria-label="Recent orders table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user_name ?? 'Guest' }}</td>
                                            <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'processing' ? 'info' : ($order->status == 'delivered' ? 'success' : 'danger')) }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No recent orders found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="col-lg-4">
            <div class="card shadow" role="region" aria-label="System status">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Database</span>
                            <span class="badge bg-success text-white px-2 py-1">Online</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Storage</span>
                            <span class="badge bg-success text-white px-2 py-1">Available</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>API Integrations</span>
                            <span class="badge {{ isset($stats['active_api_integrations']) && $stats['active_api_integrations'] > 0 ? 'bg-success' : 'bg-warning' }} text-white px-2 py-1">
                                {{ $stats['active_api_integrations'] ?? 0 }} Active
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Last Backup</span>
                            <span class="text-muted">{{ now()->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Module Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <i class="fas fa-box fa-3x text-primary mb-2"></i>
                                <h5>{{ $stats['total_products'] ?? 0 }}</h5>
                                <small class="text-muted">Products</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <i class="fas fa-tags fa-3x text-success mb-2"></i>
                                <h5>{{ $stats['total_categories'] ?? 0 }}</h5>
                                <small class="text-muted">Categories</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <i class="fas fa-users fa-3x text-info mb-2"></i>
                                <h5>{{ $stats['total_users'] ?? 0 }}</h5>
                                <small class="text-muted">Customers</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <i class="fas fa-shopping-cart fa-3x text-warning mb-2"></i>
                                <h5>{{ $stats['total_orders'] ?? 0 }}</h5>
                                <small class="text-muted">Orders</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <i class="fas fa-star fa-3x text-warning mb-2"></i>
                                <h5>{{ $stats['total_reviews'] ?? 0 }}</h5>
                                <small class="text-muted">Reviews</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <i class="fas fa-ticket-alt fa-3x text-danger mb-2"></i>
                                <h5>{{ $stats['total_coupons'] ?? 0 }}</h5>
                                <small class="text-muted">Coupons</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush

<style>
.text-muted {
    color: #b3b3b3 !important;
}
[data-theme="light"] .text-muted {
    color: #555 !important;
}
</style> 