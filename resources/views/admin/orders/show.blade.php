@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0">Order #{{ $order->id }}</h3>
                            <small class="text-muted">Placed on {{ $order->created_at->format('F d, Y \a\t H:i') }}</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Order
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-success" target="_blank">
                                <i class="fas fa-print"></i> Print Invoice
                            </a>
                            <!-- Shiprocket Buttons -->
                            <form action="{{ route('admin.orders.shiprocket.place', $order) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Place this order on Shiprocket?');">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-rocket"></i> Place Order on Shiprocket
                                </button>
                            </form>
                            <button type="button" class="btn btn-info" id="check-couriers-btn">
                                <i class="fas fa-shipping-fast"></i> Check Available Couriers
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Details -->
                <div class="col-lg-8">
                    <!-- Order Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Status</h5>
                        </div>
                        <div class="card-body">
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
                            
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-{{ $color }} badge-lg mr-3">{{ ucfirst($order->status) }}</span>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateStatusModal">
                                    <i class="fas fa-edit"></i> Update Status
                                </button>
                            </div>

                            @if($order->tracking_number)
                                <div class="mb-3">
                                    <strong>Tracking Number:</strong> {{ $order->tracking_number }}
                                    @if($order->tracking_url)
                                        <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-sm btn-info ml-2">
                                            <i class="fas fa-external-link-alt"></i> Track Package
                                        </a>
                                    @endif
                                </div>
                            @endif

                            @if($order->notes)
                                <div class="mb-3">
                                    <strong>Notes:</strong> {{ $order->notes }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->product && $item->product->images)
                                                            @php
                                                                $images = json_decode($item->product->images, true);
                                                                $firstImage = $images[0] ?? null;
                                                            @endphp
                                                            @if($firstImage)
                                                                <img src="{{ asset('storage/' . $firstImage) }}" 
                                                                     alt="{{ $item->product->name }}" 
                                                                     class="img-thumbnail mr-3" 
                                                                     style="max-width: 50px; max-height: 50px;">
                                                            @endif
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->product->name ?? 'Product' }}</strong>
                                                            @if($item->product && $item->product->sku)
                                                                <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                            <td>₹{{ number_format($order->subtotal_amount, 2) }}</td>
                                        </tr>
                                        @if($order->shipping_cost > 0)
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Shipping:</strong></td>
                                                <td>₹{{ number_format($order->shipping_cost, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($order->tax_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Tax:</strong></td>
                                                <td>₹{{ number_format($order->tax_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($order->discount_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Discount:</strong></td>
                                                <td>-₹{{ number_format($order->discount_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-active">
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Status History</h5>
                        </div>
                        <div class="card-body">
                            @if($order->statusHistory->count() > 0)
                                <div class="timeline">
                                    @foreach($order->statusHistory->sortBy('created_at') as $history)
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ ucfirst($history->status) }}</strong>
                                                        @if($history->notes)
                                                            <br><small class="text-muted">{{ $history->notes }}</small>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</small>
                                                </div>
                                                @if($history->user)
                                                    <small class="text-muted">Updated by: {{ $history->user->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No status history available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            @if($order->user && $order->user->customer)
                                <div class="mb-3">
                                    <strong>Name:</strong> {{ $order->user->name }}
                                </div>
                                <div class="mb-3">
                                    <strong>Email:</strong> {{ $order->user->email }}
                                </div>
                                @if($order->user->customer && $order->user->customer->phone)
                                    <div class="mb-3">
                                        <strong>Phone:</strong> {{ $order->user->customer->phone }}
                                    </div>
                                @endif
                                <a href="{{ route('admin.customers.show', $order->user->customer) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-user"></i> View Customer
                                </a>
                            @else
                                <span class="text-muted">No customer profile</span>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    @if($order->shippingAddress)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</strong>
                                </div>
                                <div class="mb-2">
                                    {{ $order->shippingAddress->address_line_1 }}
                                    @if($order->shippingAddress->address_line_2)
                                        <br>{{ $order->shippingAddress->address_line_2 }}
                                    @endif
                                </div>
                                <div class="mb-2">
                                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}
                                </div>
                                <div class="mb-2">
                                    {{ $order->shippingAddress->country }}
                                </div>
                                @if($order->shippingAddress->phone)
                                    <div class="mb-2">
                                        <strong>Phone:</strong> {{ $order->shippingAddress->phone }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Payment Information -->
                    @if($order->payment)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Method:</strong> {{ ucfirst($order->payment->payment_method) }}
                                </div>
                                <div class="mb-2">
                                    <strong>Status:</strong> 
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
                                </div>
                                <div class="mb-2">
                                    <strong>Amount:</strong> ₹{{ number_format($order->payment->amount, 2) }}
                                </div>
                                @if($order->payment->transaction_id)
                                    <div class="mb-2">
                                        <strong>Transaction ID:</strong> {{ $order->payment->transaction_id }}
                                    </div>
                                @endif
                                @if($order->payment->paid_at)
                                    <div class="mb-2">
                                        <strong>Paid At:</strong> {{ $order->payment->paid_at->format('M d, Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('admin.orders.cancel', $order) }}" 
                                      method="POST" 
                                      class="mb-2"
                                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'delivered')
                                <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#refundModal">
                                    <i class="fas fa-undo"></i> Process Refund
                                </button>
                            @endif

                            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-success btn-block" target="_blank">
                                <i class="fas fa-print"></i> Print Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tracking_number">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" value="{{ $order->tracking_number }}">
                    </div>
                    <div class="form-group">
                        <label for="tracking_url">Tracking URL</label>
                        <input type="url" class="form-control" name="tracking_url" value="{{ $order->tracking_url }}">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refund Modal -->
@if($order->status === 'delivered')
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.orders.refund', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refund_amount">Refund Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₹</span>
                            </div>
                            <input type="number" class="form-control" name="refund_amount" 
                                   value="{{ $order->total_amount }}" 
                                   step="0.01" 
                                   min="0" 
                                   max="{{ $order->total_amount }}" 
                                   required>
                        </div>
                        <small class="form-text text-muted">Maximum refund amount: ₹{{ number_format($order->total_amount, 2) }}</small>
                    </div>
                    <div class="form-group">
                        <label for="refund_reason">Refund Reason</label>
                        <textarea class="form-control" name="refund_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Courier Modal -->
<div class="modal fade" id="courierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Available Couriers</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="courier-modal-body">
                <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 17px;
    width: 2px;
    height: calc(100% + 3px);
    background: #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    document.getElementById('check-couriers-btn').addEventListener('click', function() {
        $('#courierModal').modal('show');
        const body = document.getElementById('courier-modal-body');
        body.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        fetch("{{ route('admin.orders.shiprocket.couriers', $order) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.couriers && res.couriers.length > 0) {
                let html = '<table class="table table-bordered"><thead><tr><th>Courier</th><th>Rate</th><th>Delivery Days</th><th>COD</th></tr></thead><tbody>';
                res.couriers.forEach(courier => {
                    html += `<tr><td>${courier.courier_name}</td><td>₹${courier.rate}</td><td>${courier.etd}</td><td>${courier.cod ? 'Yes' : 'No'}</td></tr>`;
                });
                html += '</tbody></table>';
                body.innerHTML = html;
            } else {
                body.innerHTML = '<div class="alert alert-warning">' + (res.message || 'No couriers available.') + '</div>';
            }
        })
        .catch(() => {
            body.innerHTML = '<div class="alert alert-danger">Failed to fetch courier companies.</div>';
        });
    });
</script>
@endpush 
