@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="container-fluid" role="main" aria-label="Admin orders listing main content">
    <div class="row">
        <div class="col-12">
            <div class="card" role="region" aria-label="Orders management">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Orders</h3>
                        <div>
                            <a href="{{ route('admin.orders.export') }}" class="btn btn-success" aria-label="Export orders">
                                <i class="fas fa-download"></i> Export
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert" aria-label="Success message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search" placeholder="Search orders..." aria-label="Search orders">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="status-filter" aria-label="Filter by order status">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-from" placeholder="From Date" aria-label="Filter from date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-to" placeholder="To Date" aria-label="Filter to date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clear-filters" aria-label="Clear all filters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="orders-table" role="table" aria-label="Orders table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>#{{ $order->id }}</strong>
                                            @if($order->tracking_number)
                                                <br><small class="text-muted">Track: {{ $order->tracking_number }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->user->name ?? 'Guest' }}</strong>
                                                @if($order->user)
                                                    <br><small class="text-muted">{{ $order->user->email }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $order->items->count() }} items</span>
                                            @foreach($order->items->take(2) as $item)
                                                <br><small>{{ $item->product->name ?? 'Product' }} ({{ $item->quantity }})</small>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <br><small class="text-muted">+{{ $order->items->count() - 2 }} more</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>₹{{ number_format($order->total_amount, 2) }}</strong>
                                                @if($order->shipping_cost > 0)
                                                    <br><small class="text-muted">+₹{{ number_format($order->shipping_cost, 2) }} shipping</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                    'refunded' => 'secondary'
                                                ];
                                                $color = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td>
                                            @if($order->payment)
                                                @php
                                                    $paymentColors = [
                                                        'pending' => 'warning',
                                                        'processing' => 'info',
                                                        'completed' => 'success',
                                                        'failed' => 'danger',
                                                        'refunded' => 'secondary',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $paymentColor = $paymentColors[$order->payment->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $paymentColor }}">{{ ucfirst($order->payment->status) }}</span>
                                                <br><small class="text-muted">{{ ucfirst($order->payment->payment_method) }}</small>
                                            @else
                                                <span class="badge badge-secondary">No Payment</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->created_at->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.orders.show', $order) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details" aria-label="View order #{{ $order->id }} details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.orders.edit', $order) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Edit Order" aria-label="Edit order #{{ $order->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.orders.invoice', $order) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Print Invoice" aria-label="Print invoice for order #{{ $order->id }}" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if(in_array($order->status, ['pending', 'processing']))
                                                    <form action="{{ route('admin.orders.cancel', $order) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to cancel this order?')" aria-label="Cancel order #{{ $order->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Cancel Order" aria-label="Cancel order #{{ $order->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
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

        // Search functionality
        $('#search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#orders-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Status filter
        $('#status-filter').change(function() {
            const status = $(this).val().toLowerCase();
            if (status) {
                $('#orders-table tbody tr').hide();
                $('#orders-table tbody tr').each(function() {
                    const rowStatus = $(this).find('td:nth-child(5) .badge').text().toLowerCase();
                    if (rowStatus.includes(status)) {
                        $(this).show();
                    }
                });
            } else {
                $('#orders-table tbody tr').show();
            }
        });

        // Date filter
        function filterByDate() {
            const fromDate = $('#date-from').val();
            const toDate = $('#date-to').val();
            
            if (fromDate || toDate) {
                $('#orders-table tbody tr').hide();
                $('#orders-table tbody tr').each(function() {
                    const orderDate = $(this).find('td:nth-child(7) strong').text();
                    const date = new Date(orderDate);
                    
                    let show = true;
                    if (fromDate) {
                        const from = new Date(fromDate);
                        if (date < from) show = false;
                    }
                    if (toDate) {
                        const to = new Date(toDate);
                        if (date > to) show = false;
                    }
                    
                    if (show) {
                        $(this).show();
                    }
                });
            } else {
                $('#orders-table tbody tr').show();
            }
        }

        $('#date-from, #date-to').change(filterByDate);

        // Clear filters
        $('#clear-filters').click(function() {
            $('#search').val('');
            $('#status-filter').val('');
            $('#date-from').val('');
            $('#date-to').val('');
            $('#orders-table tbody tr').show();
        });
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