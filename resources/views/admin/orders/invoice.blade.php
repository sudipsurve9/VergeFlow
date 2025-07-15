@extends('layouts.admin')

@section('title', 'Invoice for Order #' . $order->id)

@section('content')
<div class="container py-4" id="invoice-area">
    <div class="row mb-4">
        <div class="col-6">
            <h2><i class="fa-solid fa-file-invoice-dollar text-accent me-2"></i>INVOICE</h2>
            <p><strong><i class="fa-solid fa-hashtag me-1 text-muted"></i>Order #:</strong> {{ $order->id }}</p>
            <p><strong><i class="fa-solid fa-calendar-alt me-1 text-muted"></i>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="col-6 text-right">
            <h4><i class="fa-solid fa-building text-accent me-2"></i>Your Company Name</h4>
            <p><i class="fa-solid fa-location-dot me-1 text-muted"></i>Address Line 1<br>Address Line 2<br>City, State, ZIP<br><i class="fa-solid fa-phone me-1 text-muted"></i>Phone: 123-456-7890</p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <h5><i class="fa-solid fa-user text-accent me-2"></i>Bill To:</h5>
            <p>
                {{ $order->shippingAddress->name ?? $order->user->name }}<br>
                {{ $order->shippingAddress->address ?? '' }}<br>
                {{ $order->shippingAddress->city ?? '' }}, {{ $order->shippingAddress->state ?? '' }}<br>
                {{ $order->shippingAddress->country ?? '' }} - {{ $order->shippingAddress->pincode ?? '' }}<br>
                <i class="fa-solid fa-phone me-1 text-muted"></i>Phone: {{ $order->shippingAddress->phone ?? '' }}
            </p>
        </div>
        <div class="col-6 text-right">
            <h5><i class="fa-solid fa-credit-card text-accent me-2"></i>Payment Method:</h5>
            <p>{{ $order->payment->method ?? 'N/A' }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="bg-accent text-white">
                    <tr>
                        <th><i class="fa-solid fa-cube me-1"></i>Product</th>
                        <th><i class="fa-solid fa-rupee-sign me-1"></i>Price</th>
                        <th><i class="fa-solid fa-sort-numeric-up me-1"></i>Quantity</th>
                        <th><i class="fa-solid fa-calculator me-1"></i>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Product' }}</td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
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
                    <tr class="table-active bg-accent text-white fs-5">
                        <td colspan="3" class="text-right"><strong><i class="fa-solid fa-money-bill-wave me-1"></i>Total:</strong></td>
                        <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>Print Invoice</button>
            <a href="{{ route('admin.orders.invoice.tcpdf', $order) }}" class="btn btn-outline-success" target="_blank">
                <i class="fa-solid fa-file-arrow-down me-1"></i>Download PDF
            </a>
        </div>
    </div>
</div>
<style>
* {
    font-family: Arial, Helvetica, sans-serif !important;
}
@media print {
    #invoice-area button { display: none; }
    .container { max-width: 100% !important; }
}
</style>
@endsection 