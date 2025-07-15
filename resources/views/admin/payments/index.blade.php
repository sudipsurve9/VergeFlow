@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payments</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search" placeholder="Search payments...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="status-filter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="method-filter">
                                <option value="">All Methods</option>
                                <option value="razorpay">Razorpay</option>
                                <option value="paypal">PayPal</option>
                                <option value="cod">COD</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-from" placeholder="From Date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clear-filters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="payments-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Transaction ID</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a>
                                        </td>
                                        <td>{{ $payment->transaction_id ?? '-' }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'completed' => 'success',
                                                    'failed' => 'danger',
                                                    'refunded' => 'secondary',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$payment->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">{{ ucfirst($payment->status) }}</span>
                                        </td>
                                        <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No payments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $payments->links() }}
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
        setTimeout(function() { $('.alert').fadeOut('slow'); }, 5000);
        $('#search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#payments-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        $('#status-filter, #method-filter').change(function() {
            const status = $('#status-filter').val();
            const method = $('#method-filter').val();
            $('#payments-table tbody tr').show().filter(function() {
                let show = true;
                if (status && !$(this).find('td:nth-child(5) .badge').text().toLowerCase().includes(status)) show = false;
                if (method && !$(this).find('td:nth-child(3)').text().toLowerCase().includes(method)) show = false;
                return !show;
            }).hide();
        });
        $('#clear-filters').click(function() {
            $('#search').val('');
            $('#status-filter').val('');
            $('#method-filter').val('');
            $('#date-from').val('');
            $('#payments-table tbody tr').show();
        });
    });
</script>
@endpush 