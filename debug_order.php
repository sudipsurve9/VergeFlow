<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

echo "=== ORDER DEBUG SCRIPT ===\n";

// Check current database connection
echo "Current DB Connection: " . DB::connection()->getDatabaseName() . "\n";
echo "Default Connection: " . config('database.default') . "\n\n";

// Try to find any orders in current database
echo "=== CHECKING FOR ORDERS ===\n";
try {
    $orderCount = Order::count();
    echo "Total orders in current DB: " . $orderCount . "\n";
    
    if ($orderCount > 0) {
        echo "\n=== AVAILABLE ORDERS ===\n";
        $orders = Order::take(10)->get(['id', 'user_id', 'status', 'total_amount', 'created_at']);
        foreach ($orders as $order) {
            echo "Order ID: " . $order->id . " | User: " . $order->user_id . " | Status: " . $order->status . " | Total: " . ($order->total_amount ?? 'NULL') . " | Date: " . $order->created_at . "\n";
        }
        
        // Check Order 5 specifically
        echo "\n=== ORDER 5 DETAILS ===\n";
        $order5 = Order::with(['items.product'])->find(5);
        if ($order5) {
            echo "Order 5 found!\n";
            echo "ID: " . $order5->id . "\n";
            echo "Order Number: " . ($order5->order_number ?? 'NULL') . "\n";
            echo "User ID: " . $order5->user_id . "\n";
            echo "Status: " . $order5->status . "\n";
            echo "Payment Method: " . ($order5->payment_method ?? 'NULL') . "\n";
            echo "Payment Status: " . ($order5->payment_status ?? 'NULL') . "\n";
            echo "Total Amount: " . ($order5->total_amount ?? 'NULL') . "\n";
            echo "Subtotal: " . ($order5->subtotal ?? 'NULL') . "\n";
            echo "Shipping Amount: " . ($order5->shipping_amount ?? 'NULL') . "\n";
            echo "Tax Amount: " . ($order5->tax_amount ?? 'NULL') . "\n";
            echo "Created At: " . $order5->created_at . "\n";
            
            echo "\n--- ORDER ITEMS ---\n";
            echo "Items Count: " . $order5->items->count() . "\n";
            foreach ($order5->items as $item) {
                echo "Item ID: " . $item->id . "\n";
                echo "Product ID: " . $item->product_id . "\n";
                echo "Product Name: " . ($item->product ? $item->product->name : 'NULL') . "\n";
                echo "Quantity: " . $item->quantity . "\n";
                echo "Price: " . $item->price . "\n";
                echo "Total: " . ($item->total ?? 'NULL') . "\n";
                echo "---\n";
            }
        } else {
            echo "Order 5 not found\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error accessing orders: " . $e->getMessage() . "\n";
}

// Check if we need to switch to client database
echo "\n=== CHECKING CLIENT DATABASES ===\n";
try {
    $clients = DB::connection('mysql')->table('clients')->get(['id', 'name', 'database_name']);
    foreach ($clients as $client) {
        echo "Client: " . $client->name . " | DB: " . $client->database_name . "\n";
        
        // Try to check orders in client database
        try {
            $clientOrderCount = DB::connection('mysql')->table($client->database_name . '.orders')->count();
            echo "  Orders in " . $client->database_name . ": " . $clientOrderCount . "\n";
        } catch (Exception $e) {
            echo "  Cannot access " . $client->database_name . ": " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error checking clients: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
