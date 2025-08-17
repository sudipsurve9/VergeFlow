<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Order;
use App\Services\MultiTenantService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set up basic environment
$app->loadEnvironmentFrom('.env');
$app->make('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($app);
$app->make('Illuminate\Foundation\Bootstrap\HandleExceptions')->bootstrap($app);
$app->make('Illuminate\Foundation\Bootstrap\RegisterFacades')->bootstrap($app);
$app->make('Illuminate\Foundation\Bootstrap\RegisterProviders')->bootstrap($app);
$app->make('Illuminate\Foundation\Bootstrap\BootProviders')->bootstrap($app);

echo "Testing Order model database connection...\n";

try {
    // Simulate authenticated user context (Vault64 client)
    session(['client_id' => 1]);
    
    // Create a new Order instance to test connection
    $order = new Order();
    $order->setTenantConnection();
    
    echo "Current connection: " . $order->getConnectionName() . "\n";
    
    // Try to find an order
    $testOrder = Order::find(6);
    
    if ($testOrder) {
        echo "✅ Successfully found order ID 6\n";
        echo "Order details: " . json_encode([
            'id' => $testOrder->id,
            'status' => $testOrder->status,
            'total_amount' => $testOrder->total_amount
        ]) . "\n";
    } else {
        echo "❌ Order ID 6 not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Connection being used: " . (isset($order) ? $order->getConnectionName() : 'unknown') . "\n";
}

echo "Test completed.\n";
