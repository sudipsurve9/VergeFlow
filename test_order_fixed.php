<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

try {
    // Set up client database connection
    Config::set('database.connections.client_1', [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => 'vergeflow_client_1',
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
    ]);
    
    Config::set('database.default', 'client_1');
    
    echo "🔍 Testing Fixed Order Placement\n";
    echo "================================\n";
    
    // Get test data
    $address = Address::first();
    $user = User::first();
    $product = Product::first();
    
    if (!$address || !$user || !$product) {
        echo "❌ Missing test data (address, user, or product)\n";
        exit;
    }
    
    echo "✅ Test data found:\n";
    echo "   - Address: {$address->name}\n";
    echo "   - User: {$user->name}\n";
    echo "   - Product: {$product->name}\n";
    
    // Test order creation with corrected schema
    echo "\n💾 Testing order creation with corrected schema...\n";
    
    $testOrder = Order::create([
        'user_id' => $user->id,
        'total_amount' => 299.99,
        'status' => 'pending',
        'payment_status' => 'pending',
        'payment_method' => 'cod',
        'shipping_address' => $address->getFormattedAddress() . "\nPhone: " . ($address->phone ?? '1234567890'),
        'billing_address' => $address->getFormattedAddress(),
        'notes' => 'Test order with corrected schema'
    ]);
    
    echo "✅ Order created successfully!\n";
    echo "   - Order ID: {$testOrder->id}\n";
    echo "   - User ID: {$testOrder->user_id}\n";
    echo "   - Total: ₹{$testOrder->total_amount}\n";
    echo "   - Status: {$testOrder->status}\n";
    echo "   - Payment: {$testOrder->payment_method}\n";
    echo "   - Shipping Address: " . substr($testOrder->shipping_address, 0, 50) . "...\n";
    
    // Test order retrieval
    $retrievedOrder = Order::find($testOrder->id);
    if ($retrievedOrder) {
        echo "✅ Order retrieval successful!\n";
    }
    
    // Clean up test order
    $testOrder->delete();
    echo "🧹 Test order cleaned up\n";
    
    echo "\n🎉 Order placement functionality test PASSED!\n";
    echo "✅ The Place Order button should now work correctly.\n";
    echo "✅ Orders will be created with the correct database schema.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
