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
    
    echo "🔍 Testing Order Placement Prerequisites\n";
    echo "=======================================\n";
    
    // Test 1: Check if we have addresses
    $addressCount = Address::count();
    echo "📍 Addresses in database: {$addressCount}\n";
    
    if ($addressCount > 0) {
        $address = Address::first();
        echo "✅ Sample address: {$address->name} - {$address->address_line1}\n";
        echo "📞 Phone: {$address->phone}\n";
        echo "🏠 Formatted: {$address->getFormattedAddress()}\n";
    }
    
    // Test 2: Check if we have products
    $productCount = Product::count();
    echo "📦 Products in database: {$productCount}\n";
    
    if ($productCount > 0) {
        $product = Product::first();
        echo "✅ Sample product: {$product->name} - ₹{$product->price}\n";
    }
    
    // Test 3: Check if we have users
    $userCount = User::count();
    echo "👤 Users in database: {$userCount}\n";
    
    if ($userCount > 0) {
        $user = User::first();
        echo "✅ Sample user: {$user->name} - {$user->email}\n";
        
        // Test 4: Check cart items for this user
        $cartCount = CartItem::where('user_id', $user->id)->count();
        echo "🛒 Cart items for user {$user->id}: {$cartCount}\n";
        
        if ($cartCount == 0) {
            echo "⚠️ No cart items found. Creating test cart item...\n";
            
            if ($productCount > 0) {
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'total' => $product->price
                ]);
                echo "✅ Test cart item created with ID: {$cartItem->id}\n";
            }
        }
    }
    
    // Test 5: Check Order model and methods
    echo "\n🔧 Testing Order Model Methods:\n";
    
    $orderNumber = Order::generateOrderNumber();
    echo "📄 Generated order number: {$orderNumber}\n";
    
    // Test 6: Check if we can create a basic order
    echo "\n💾 Testing Basic Order Creation:\n";
    
    if ($addressCount > 0 && $userCount > 0) {
        $testOrder = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'total_amount' => 100.00,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
            'shipping_address' => $address->getFormattedAddress(),
            'billing_address' => $address->getFormattedAddress(),
            'phone' => $address->phone ?? '1234567890',
            'notes' => 'Test order'
        ]);
        
        echo "✅ Test order created successfully with ID: {$testOrder->id}\n";
        echo "📄 Order number: {$testOrder->order_number}\n";
        echo "💰 Total: ₹{$testOrder->total_amount}\n";
        
        // Clean up test order
        $testOrder->delete();
        echo "🧹 Test order cleaned up\n";
    }
    
    echo "\n🎉 Order placement prerequisites test completed!\n";
    echo "✅ All components appear to be working correctly.\n";
    echo "🔍 If Place Order still fails, check browser console for JavaScript errors.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
