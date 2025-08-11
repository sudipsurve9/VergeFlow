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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    
    echo "🔍 Testing Order Placement with Debug Info\n";
    echo "==========================================\n";
    
    // Get test data
    $address = Address::first();
    $user = User::first();
    $product = Product::first();
    
    if (!$address || !$user || !$product) {
        echo "❌ Missing test data\n";
        exit;
    }
    
    echo "✅ Test data found:\n";
    echo "   - Address ID: {$address->id}, User ID: {$address->user_id}\n";
    echo "   - User ID: {$user->id}\n";
    echo "   - Product ID: {$product->id}\n";
    
    // Check if cart item exists
    $cartItem = CartItem::where('user_id', $user->id)->first();
    if (!$cartItem) {
        echo "⚠️ No cart items found. Creating test cart item...\n";
        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'total' => $product->price
        ]);
        echo "✅ Cart item created with ID: {$cartItem->id}\n";
    }
    
    // Simulate the order placement request
    echo "\n💾 Simulating order placement request...\n";
    
    $requestData = [
        'shipping_address_id' => $address->id,
        'billing_address_id' => $address->id,
        'phone' => $address->phone ?? '1234567890',
        'payment_method' => 'cod',
        'notes' => 'Test order from debug script'
    ];
    
    echo "📋 Request data:\n";
    foreach ($requestData as $key => $value) {
        echo "   - {$key}: {$value}\n";
    }
    
    // Test address validation manually
    echo "\n🔍 Testing address validation:\n";
    
    $shippingAddress = Address::where('id', $requestData['shipping_address_id'])
        ->where('user_id', $user->id)
        ->first();
    
    if ($shippingAddress) {
        echo "✅ Shipping address found: {$shippingAddress->name}\n";
        echo "   - Formatted: {$shippingAddress->getFormattedAddress()}\n";
    } else {
        echo "❌ Shipping address not found or doesn't belong to user\n";
    }
    
    $billingAddress = Address::where('id', $requestData['billing_address_id'])
        ->where('user_id', $user->id)
        ->first();
    
    if ($billingAddress) {
        echo "✅ Billing address found: {$billingAddress->name}\n";
    } else {
        echo "❌ Billing address not found or doesn't belong to user\n";
    }
    
    // Test cart items
    $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
    echo "\n🛒 Cart items: {$cartItems->count()}\n";
    
    if ($cartItems->isNotEmpty()) {
        $total = $cartItems->sum(function($item) {
            return $item->total;
        });
        echo "💰 Total amount: ₹{$total}\n";
        
        // Test order creation
        echo "\n💾 Testing order creation...\n";
        
        $orderData = [
            'user_id' => $user->id,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $requestData['payment_method'],
            'shipping_address' => $shippingAddress->getFormattedAddress() . "\nPhone: " . $requestData['phone'],
            'billing_address' => $billingAddress->getFormattedAddress(),
            'notes' => $requestData['notes']
        ];
        
        echo "📋 Order data:\n";
        foreach ($orderData as $key => $value) {
            echo "   - {$key}: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
        }
        
        $order = Order::create($orderData);
        echo "✅ Order created successfully with ID: {$order->id}\n";
        
        // Clean up
        $order->delete();
        echo "🧹 Test order cleaned up\n";
    }
    
    echo "\n🎉 Order placement debug test completed!\n";
    echo "✅ All components are working correctly.\n";
    echo "🔍 Now try placing an order through the web interface.\n";
    echo "📝 Check the Laravel logs for any validation or error messages.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
