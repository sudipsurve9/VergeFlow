@php
$orderUser = $order->user;
$shipping = $order->shippingAddress;
$billing = $order->billingAddress;
$payment = $order->payment;
@endphp
<html>
<head>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .section { margin-bottom: 18px; }
        .section-title { font-weight: bold; margin-bottom: 6px; }
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 4px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th, .items-table td { border: 1px solid #333; padding: 6px; }
        .items-table th { background: #f2f2f2; }
        .totals-table { width: 100%; margin-top: 10px; }
        .totals-table td { padding: 4px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2><i class="fa-solid fa-file-invoice-dollar text-accent"></i> INVOICE</h2>
        <div><i class="fa-solid fa-hashtag text-muted"></i> Order #: <strong>{{ $order->id }}</strong></div>
        <div><i class="fa-solid fa-calendar-alt text-muted"></i> Date: {{ $order->created_at->format('d M Y, H:i') }}</div>
    </div>
    <div class="section">
        <div class="section-title"><i class="fa-solid fa-building text-accent"></i> From:</div>
        <div>Your Company Name<br>Address Line 1<br>Address Line 2<br>City, State, ZIP<br><i class="fa-solid fa-phone me-1 text-muted"></i>Phone: 123-456-7890</div>
    </div>
    <div class="section">
        <div class="section-title"><i class="fa-solid fa-user text-accent"></i> Bill To:</div>
        <div>
            {!! nl2br(e($order->billing_address)) !!}
        </div>
    </div>
    <div class="section">
        <div class="section-title"><i class="fa-solid fa-truck text-accent"></i> Ship To:</div>
        <div>
            {!! nl2br(e($order->shipping_address)) !!}
        </div>
    </div>
    <div class="section">
        <div class="section-title"><i class="fa-solid fa-credit-card text-accent"></i> Payment Method:</div>
        <div>{{ $payment->payment_method ?? 'N/A' }}</div>
    </div>
    <div class="section">
        <table class="items-table">
            <thead style="background:#FFB300;color:#fff;">
                <tr>
                    <th><i class="fa-solid fa-cube"></i> Product</th>
                    <th><i class="fa-solid fa-rupee-sign"></i> Price</th>
                    <th><i class="fa-solid fa-sort-numeric-up"></i> Quantity</th>
                    <th><i class="fa-solid fa-calculator"></i> Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Product' }}</td>
                        <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="totals-table" style="background:#f8f9fa;">
            <tr>
                <td class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">₹{{ number_format($order->subtotal_amount ?? $order->total_amount, 2) }}</td>
            </tr>
            @if($order->shipping_amount > 0)
            <tr>
                <td class="text-right"><strong>Shipping:</strong></td>
                <td class="text-right">₹{{ number_format($order->shipping_amount, 2) }}</td>
            </tr>
            @endif
            @if($order->tax_amount > 0)
            <tr>
                <td class="text-right"><strong>Tax:</strong></td>
                <td class="text-right">₹{{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @endif
            @if($order->discount_amount > 0)
            <tr>
                <td class="text-right"><strong>Discount:</strong></td>
                <td class="text-right">-₹{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr style="background:#FFB300;color:#fff;font-size:1.1em;">
                <td class="text-right"><strong><i class="fa-solid fa-money-bill-wave"></i> Total:</strong></td>
                <td class="text-right"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html> 