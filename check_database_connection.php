<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\OrderItem;

try {
    echo "🔍 Checking Database Connection for OrderController\n";
    echo "==================================================\n";
    
    // Check current default database connection
    $currentConnection = Config::get('database.default');
    echo "Current default connection: $currentConnection\n";
    
    // Check all configured connections
    $connections = Config::get('database.connections');
    echo "\nConfigured database connections:\n";
    foreach ($connections as $name => $config) {
        if (isset($config['database'])) {
            echo "  - $name: {$config['database']}\n";
        }
    }
    
    // Set up client database connection (same as OrderController would use)
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
    
    echo "\n🔄 Switched to client_1 database connection\n";
    echo "Database: " . Config::get('database.connections.client_1.database') . "\n";
    
    // Test the connection
    try {
        $result = DB::select('SELECT DATABASE() as current_db');
        echo "✅ Connected to database: " . $result[0]->current_db . "\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        exit;
    }
    
    // Check order_items table structure via Laravel
    echo "\n📋 Checking order_items table via Laravel Schema...\n";
    try {
        $columns = DB::select("DESCRIBE order_items");
        echo "Columns found:\n";
        foreach ($columns as $column) {
            echo "  - {$column->Field}: {$column->Type}\n";
        }
        
        $columnNames = array_column($columns, 'Field');
        if (in_array('total', $columnNames)) {
            echo "✅ 'total' column is present via Laravel connection\n";
        } else {
            echo "❌ 'total' column is missing via Laravel connection\n";
        }
    } catch (Exception $e) {
        echo "❌ Failed to check table structure: " . $e->getMessage() . "\n";
    }
    
    // Test OrderItem model directly
    echo "\n🧪 Testing OrderItem model...\n";
    try {
        $orderItem = new OrderItem();
        $fillable = $orderItem->getFillable();
        echo "OrderItem fillable fields: " . implode(', ', $fillable) . "\n";
        
        $connection = $orderItem->getConnectionName();
        echo "OrderItem connection: " . ($connection ?: 'default') . "\n";
        
        $table = $orderItem->getTable();
        echo "OrderItem table: $table\n";
    } catch (Exception $e) {
        echo "❌ OrderItem model error: " . $e->getMessage() . "\n";
    }
    
    // Test actual insertion using OrderItem model
    echo "\n🔧 Testing OrderItem::create() method...\n";
    try {
        // Get test data
        $orders = DB::table('orders')->count();
        $products = DB::table('products')->count();
        
        echo "Available test data: $orders orders, $products products\n";
        
        if ($orders > 0 && $products > 0) {
            $testOrder = DB::table('orders')->first();
            $testProduct = DB::table('products')->first();
            
            echo "Using order ID: {$testOrder->id}, product ID: {$testProduct->id}\n";
            
            $testData = [
                'order_id' => $testOrder->id,
                'product_id' => $testProduct->id,
                'quantity' => 1,
                'price' => 349.99,
                'total' => 349.99
            ];
            
            echo "Test data: " . json_encode($testData) . "\n";
            
            $orderItem = OrderItem::create($testData);
            echo "✅ OrderItem created successfully with ID: {$orderItem->id}\n";
            
            // Verify the data
            $saved = OrderItem::find($orderItem->id);
            echo "✅ OrderItem retrieved: ID {$saved->id}, total: {$saved->total}\n";
            
            // Clean up
            $saved->delete();
            echo "🧹 Test data cleaned up\n";
            
        } else {
            echo "⚠️ Insufficient test data for OrderItem creation\n";
        }
    } catch (Exception $e) {
        echo "❌ OrderItem::create() failed: " . $e->getMessage() . "\n";
        echo "📍 Error in: " . $e->getFile() . " at line " . $e->getLine() . "\n";
        
        // Check if it's the same error as in the log
        if (strpos($e->getMessage(), "Unknown column 'total'") !== false) {
            echo "\n🚨 CRITICAL: This is the same error from the OrderController!\n";
            echo "The OrderItem model is trying to use a 'total' column that doesn't exist in the connected database.\n";
            
            // Double-check the actual connection being used
            $actualConnection = DB::getDefaultConnection();
            echo "Actual connection being used: $actualConnection\n";
            
            $connectionConfig = Config::get("database.connections.$actualConnection");
            if (isset($connectionConfig['database'])) {
                echo "Actual database: {$connectionConfig['database']}\n";
            }
        }
    }
    
    // Check if there are multiple client databases
    echo "\n🔍 Checking for multiple client databases...\n";
    try {
        $databases = DB::select("SHOW DATABASES LIKE 'vergeflow_client_%'");
        echo "Found client databases:\n";
        foreach ($databases as $db) {
            $dbName = $db->{'Database (vergeflow_client_%)'};
            echo "  - $dbName\n";
            
            // Check if this database has order_items table with total column
            try {
                DB::statement("USE $dbName");
                $columns = DB::select("DESCRIBE order_items");
                $columnNames = array_column($columns, 'Field');
                $hasTotal = in_array('total', $columnNames);
                echo "    order_items table: " . ($hasTotal ? "✅ has 'total'" : "❌ missing 'total'") . "\n";
            } catch (Exception $e) {
                echo "    order_items table: ❌ error checking - " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Failed to check multiple databases: " . $e->getMessage() . "\n";
    }
    
    echo "\n📝 Summary:\n";
    echo "If the OrderItem::create() test passed, the issue might be:\n";
    echo "1. The error in the log is old/cached\n";
    echo "2. The OrderController is using a different database connection\n";
    echo "3. There's a race condition or connection pooling issue\n";
    echo "4. The user needs to try placing an order again\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
