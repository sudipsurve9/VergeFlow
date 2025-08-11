<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

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
    
    echo "🔧 Fixing Order Items Table Schema\n";
    echo "==================================\n";
    
    // Check if order_items table exists
    if (!Schema::hasTable('order_items')) {
        echo "❌ order_items table does not exist!\n";
        echo "🔄 Creating order_items table...\n";
        
        Schema::create('order_items', function ($table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
        
        echo "✅ order_items table created successfully!\n";
    } else {
        echo "✅ order_items table exists\n";
        
        // Check current columns
        $columns = Schema::getColumnListing('order_items');
        echo "📋 Current columns: " . implode(', ', $columns) . "\n";
        
        // Check if total column exists
        if (!Schema::hasColumn('order_items', 'total')) {
            echo "❌ Missing 'total' column in order_items table\n";
            echo "🔄 Adding 'total' column...\n";
            
            Schema::table('order_items', function ($table) {
                $table->decimal('total', 10, 2)->after('price');
            });
            
            echo "✅ 'total' column added successfully!\n";
        } else {
            echo "✅ 'total' column exists\n";
        }
        
        // Check other required columns
        $requiredColumns = ['id', 'order_id', 'product_id', 'quantity', 'price', 'total', 'created_at', 'updated_at'];
        $missingColumns = [];
        
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $columns)) {
                $missingColumns[] = $column;
            }
        }
        
        if (!empty($missingColumns)) {
            echo "❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
            echo "🔄 Adding missing columns...\n";
            
            Schema::table('order_items', function ($table) use ($missingColumns) {
                foreach ($missingColumns as $column) {
                    switch ($column) {
                        case 'order_id':
                            $table->foreignId('order_id')->constrained()->onDelete('cascade');
                            break;
                        case 'product_id':
                            $table->foreignId('product_id')->constrained()->onDelete('cascade');
                            break;
                        case 'quantity':
                            $table->integer('quantity');
                            break;
                        case 'price':
                            $table->decimal('price', 10, 2);
                            break;
                        case 'total':
                            $table->decimal('total', 10, 2);
                            break;
                    }
                }
            });
            
            echo "✅ Missing columns added successfully!\n";
        } else {
            echo "✅ All required columns are present\n";
        }
    }
    
    // Verify final schema
    echo "\n🔍 Final schema verification:\n";
    $finalColumns = Schema::getColumnListing('order_items');
    echo "📋 Final columns: " . implode(', ', $finalColumns) . "\n";
    
    // Test if we can now create an order item
    echo "\n🧪 Testing OrderItem creation...\n";
    
    // Check if we have test data
    $orders = DB::table('orders')->count();
    $products = DB::table('products')->count();
    
    echo "📊 Available test data:\n";
    echo "   - Orders: {$orders}\n";
    echo "   - Products: {$products}\n";
    
    if ($orders > 0 && $products > 0) {
        $testOrder = DB::table('orders')->first();
        $testProduct = DB::table('products')->first();
        
        echo "🧪 Creating test order item...\n";
        
        $testOrderItemData = [
            'order_id' => $testOrder->id,
            'product_id' => $testProduct->id,
            'quantity' => 1,
            'price' => 99.99,
            'total' => 99.99,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        $orderItemId = DB::table('order_items')->insertGetId($testOrderItemData);
        
        if ($orderItemId) {
            echo "✅ Test order item created successfully with ID: {$orderItemId}\n";
            
            // Clean up test data
            DB::table('order_items')->where('id', $orderItemId)->delete();
            echo "🧹 Test order item cleaned up\n";
        } else {
            echo "❌ Failed to create test order item\n";
        }
    } else {
        echo "⚠️ Insufficient test data for order item creation test\n";
    }
    
    echo "\n🎉 Order Items table schema fix completed!\n";
    echo "✅ The 'total' column is now present in the order_items table\n";
    echo "✅ OrderController should now be able to create order items without SQL errors\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
