@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Payment Details</h3>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Order</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a>
                        </dd>
                        <dt class="col-sm-4">Transaction ID</dt>
                        <dd class="col-sm-8">{{ $payment->transaction_id ?? '-' }}</dd>
                        <dt class="col-sm-4">Payment Method</dt>
                        <dd class="col-sm-8">{{ ucfirst($payment->payment_method) }}</dd>
                        <dt class="col-sm-4">Amount</dt>
                        <dd class="col-sm-8">₹{{ number_format($payment->amount, 2) }}</dd>
                        <dt class="col-sm-4">Currency</dt>
                        <dd class="col-sm-8">{{ $payment->currency }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
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
                        </dd>
                        <dt class="col-sm-4">Paid At</dt>
                        <dd class="col-sm-8">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : '-' }}</dd>
                        <dt class="col-sm-4">Created At</dt>
                        <dd class="col-sm-8">{{ $payment->created_at->format('M d, Y H:i') }}</dd>
                        <dt class="col-sm-4">Failure Reason</dt>
                        <dd class="col-sm-8">{{ $payment->failure_reason ?? '-' }}</dd>
                        <dt class="col-sm-4">Payment Data</dt>
                        <dd class="col-sm-8">
                            <pre class="bg-light p-2 rounded small">{{ json_encode($payment->payment_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 