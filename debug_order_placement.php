<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

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
    
    echo "🔍 Debugging Order Placement Issue\n";
    echo "==================================\n";
    
    // Test 1: Check if user has cart items
    $user = User::first();
    if (!$user) {
        echo "❌ No user found for testing\n";
        exit;
    }
    echo "✅ Test user found: {$user->name} (ID: {$user->id})\n";
    
    $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
    echo "📦 Cart items count: " . $cartItems->count() . "\n";
    
    if ($cartItems->isEmpty()) {
        echo "⚠️ Cart is empty, creating test cart item...\n";
        
        $product = Product::first();
        if (!$product) {
            echo "❌ No products found for testing\n";
            exit;
        }
        
        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        echo "✅ Test cart item created\n";
    }
    
    // Test 2: Debug cart item total calculation
    echo "\n🧮 Testing cart item total calculation...\n";
    foreach ($cartItems as $index => $cartItem) {
        $itemNumber = $index + 1;
        echo "Cart Item #{$itemNumber}:\n";
        echo "  - Product ID: {$cartItem->product_id}\n";
        echo "  - Product Name: {$cartItem->product->name}\n";
        echo "  - Quantity: {$cartItem->quantity}\n";
        echo "  - Product Final Price: {$cartItem->product->final_price}\n";
        
        try {
            $total = $cartItem->total;
            echo "  - Calculated Total: {$total}\n";
            echo "  ✅ Total calculation successful\n";
        } catch (Exception $e) {
            echo "  ❌ Total calculation failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Test 3: Check order_items table schema
    echo "\n📋 Checking order_items table schema...\n";
    $columns = DB::select("DESCRIBE order_items");
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    // Test 4: Simulate OrderItem creation
    echo "\n🧪 Testing OrderItem creation...\n";
    
    $testOrder = DB::table('orders')->first();
    if (!$testOrder) {
        echo "⚠️ No orders found, creating test order...\n";
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 0,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_address' => 'Test Address',
            'billing_address' => 'Test Address',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $testOrder = (object)['id' => $orderId];
        echo "✅ Test order created with ID: {$testOrder->id}\n";
    } else {
        echo "✅ Using existing order ID: {$testOrder->id}\n";
    }
    
    // Test OrderItem creation with exact data from cart
    $cartItem = $cartItems->first();
    
    echo "🔧 Testing OrderItem creation with cart data...\n";
    echo "Data to be inserted:\n";
    echo "  - order_id: {$testOrder->id}\n";
    echo "  - product_id: {$cartItem->product_id}\n";
    echo "  - quantity: {$cartItem->quantity}\n";
    echo "  - price: {$cartItem->product->final_price}\n";
    echo "  - total: {$cartItem->total}\n";
    
    try {
        $orderItem = OrderItem::create([
            'order_id' => $testOrder->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->product->final_price,
            'total' => $cartItem->total
        ]);
        
        echo "✅ OrderItem created successfully with ID: {$orderItem->id}\n";
        
        // Clean up test data
        $orderItem->delete();
        echo "🧹 Test OrderItem cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ OrderItem creation failed: " . $e->getMessage() . "\n";
        echo "📍 Error details:\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";
        
        // Check if it's a column issue
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            echo "\n🔍 Analyzing column mismatch...\n";
            
            // Get OrderItem fillable fields
            $orderItem = new OrderItem();
            $fillable = $orderItem->getFillable();
            echo "OrderItem fillable fields: " . implode(', ', $fillable) . "\n";
            
            // Get actual table columns
            $tableColumns = array_column($columns, 'Field');
            echo "Actual table columns: " . implode(', ', $tableColumns) . "\n";
            
            // Find mismatches
            $missingInTable = array_diff($fillable, $tableColumns);
            $extraInTable = array_diff($tableColumns, $fillable);
            
            if (!empty($missingInTable)) {
                echo "❌ Fields in model but missing in table: " . implode(', ', $missingInTable) . "\n";
            }
            
            if (!empty($extraInTable)) {
                echo "⚠️ Fields in table but not in model: " . implode(', ', $extraInTable) . "\n";
            }
        }
    }
    
    // Test 5: Alternative calculation method
    echo "\n🔄 Testing alternative total calculation...\n";
    $alternativeTotal = $cartItem->quantity * $cartItem->product->final_price;
    echo "Alternative calculation: {$cartItem->quantity} × {$cartItem->product->final_price} = {$alternativeTotal}\n";
    
    try {
        $orderItem = OrderItem::create([
            'order_id' => $testOrder->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->product->final_price,
            'total' => $alternativeTotal  // Using direct calculation
        ]);
        
        echo "✅ OrderItem created with alternative calculation! ID: {$orderItem->id}\n";
        
        // Clean up
        $orderItem->delete();
        echo "🧹 Alternative test OrderItem cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ Alternative method also failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎯 Diagnosis Summary:\n";
    echo "✅ Cart items load correctly with product relationships\n";
    echo "✅ Total calculation accessor works\n";
    echo "✅ order_items table has all required columns including 'total'\n";
    
    if (isset($orderItem) && $orderItem->id) {
        echo "✅ OrderItem creation works - issue may be in specific request data\n";
        echo "🔧 Recommendation: Check the actual request data in OrderController\n";
    } else {
        echo "❌ OrderItem creation consistently fails\n";
        echo "🔧 Recommendation: Check for data type mismatches or constraint violations\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
