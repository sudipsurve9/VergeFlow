<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;

echo "=== ORDER DATA TEST ===\n";

// Check if Order 5 exists
$order = Order::with(['items.product', 'shippingAddress', 'billingAddress'])->find(5);

if ($order) {
    echo "Order ID: " . $order->id . "\n";
    echo "Order Number: " . ($order->order_number ?? 'NULL') . "\n";
    echo "Total Amount: " . ($order->total_amount ?? 'NULL') . "\n";
    echo "Subtotal: " . ($order->subtotal ?? 'NULL') . "\n";
    echo "Shipping Amount: " . ($order->shipping_amount ?? 'NULL') . "\n";
    echo "Tax Amount: " . ($order->tax_amount ?? 'NULL') . "\n";
    echo "Status: " . $order->status . "\n";
    echo "Payment Method: " . ($order->payment_method ?? 'NULL') . "\n";
    echo "Payment Status: " . ($order->payment_status ?? 'NULL') . "\n";
    echo "User ID: " . $order->user_id . "\n";
    echo "Created At: " . $order->created_at . "\n";
    
    echo "\n=== ORDER ITEMS ===\n";
    echo "Items Count: " . $order->items->count() . "\n";
    
    foreach ($order->items as $item) {
        echo "- Product: " . ($item->product ? $item->product->name : 'No Product') . "\n";
        echo "  Quantity: " . $item->quantity . "\n";
        echo "  Price: " . $item->price . "\n";
        echo "  Total: " . $item->total . "\n";
        if ($item->product) {
            echo "  SKU: " . ($item->product->sku ?? 'No SKU') . "\n";
            echo "  Image: " . ($item->product->image ?? 'No Image') . "\n";
        }
        echo "\n";
    }
    
    echo "=== SHIPPING ADDRESS ===\n";
    if ($order->shippingAddress) {
        echo "Address Line 1: " . ($order->shippingAddress->address_line_1 ?? 'NULL') . "\n";
        echo "Address Line 2: " . ($order->shippingAddress->address_line_2 ?? 'NULL') . "\n";
        echo "City: " . ($order->shippingAddress->city ?? 'NULL') . "\n";
        echo "State: " . ($order->shippingAddress->state ?? 'NULL') . "\n";
        echo "Postal Code: " . ($order->shippingAddress->postal_code ?? 'NULL') . "\n";
        echo "Country: " . ($order->shippingAddress->country ?? 'NULL') . "\n";
    } else {
        echo "No shipping address relationship\n";
        echo "Raw shipping_address field: " . ($order->shipping_address ?? 'NULL') . "\n";
    }
    
} else {
    echo "Order 5 not found. Checking available orders:\n";
    
    $orders = Order::take(5)->get(['id', 'user_id', 'status', 'total_amount']);
    foreach ($orders as $o) {
        echo "Order ID: " . $o->id . " - User: " . $o->user_id . " - Status: " . $o->status . " - Total: " . ($o->total_amount ?? 'NULL') . "\n";
    }
}

echo "\n=== DONE ===\n";
