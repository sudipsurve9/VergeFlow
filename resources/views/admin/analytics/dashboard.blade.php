@extends('layouts.app_modern')

@section('title', 'Analytics Dashboard - VergeFlow')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">📊 Analytics Dashboard</h1>
                    <p class="text-muted">Comprehensive multi-tenant platform analytics</p>
                </div>
                <div>
                    <a href="{{ route('analytics.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['overview']['total_clients']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($analytics['overview']['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['overview']['total_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['overview']['total_products']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Monthly Revenue Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Revenue Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">🥧 Revenue by Client</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="clientRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Performance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🏪 Client Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="clientsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Domain</th>
                                    <th>Theme</th>
                                    <th>Products</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                    <th>Users</th>
                                    <th>Conversion</th>
                                    <th>Avg Order</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['clients'] as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title rounded-circle" style="background-color: {{ $client['theme'] === 'modern' ? '#2563eb' : ($client['theme'] === 'luxury' ? '#ec4899' : '#16a34a') }};">
                                                    {{ substr($client['name'], 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $client['name'] }}</h6>
                                                <small class="text-muted">{{ $client['company_name'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($client['domain'])
                                            <span class="badge bg-primary">{{ $client['domain'] }}</span>
                                        @endif
                                        @if($client['subdomain'])
                                            <span class="badge bg-secondary">{{ $client['subdomain'] }}.vergeflow.com</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($client['theme']) }}</span>
                                    </td>
                                    <td>{{ number_format($client['products_count']) }}</td>
                                    <td>{{ number_format($client['orders_count']) }}</td>
                                    <td>₹{{ number_format($client['revenue'], 2) }}</td>
                                    <td>{{ number_format($client['users_count']) }}</td>
                                    <td>{{ $client['conversion_rate'] }}%</td>
                                    <td>₹{{ number_format($client['avg_order_value'], 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $client['status'] === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($client['status']) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Analytics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Growth Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($analytics['growth'] as $growth)
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $growth['client_name'] }}</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Revenue Growth</small>
                                            <div class="h6 mb-0 {{ $growth['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $growth['revenue_growth'] >= 0 ? '+' : '' }}{{ $growth['revenue_growth'] }}%
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">User Growth</small>
                                            <div class="h6 mb-0 {{ $growth['user_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $growth['user_growth'] >= 0 ? '+' : '' }}{{ $growth['user_growth'] }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($analytics['revenue']['monthly']);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: Object.keys(revenueData),
            datasets: [{
                label: 'Monthly Revenue (₹)',
                data: Object.values(revenueData),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Client Revenue Pie Chart
    const clientRevenueCtx = document.getElementById('clientRevenueChart').getContext('2d');
    const clientRevenueData = @json($analytics['revenue']['by_client']);
    
    new Chart(clientRevenueCtx, {
        type: 'doughnut',
        data: {
            labels: clientRevenueData.map(client => client.client_name),
            datasets: [{
                data: clientRevenueData.map(client => client.revenue),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ₹' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Initialize DataTable
    $('#clientsTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[5, 'desc']], // Sort by revenue column
        columnDefs: [
            { targets: [3, 4, 5, 6, 8], className: 'text-end' }
        ]
    });
});
</script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

@endsection
