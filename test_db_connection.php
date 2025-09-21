<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing database connections...\n\n";

// Test main database
try {
    $mainCount = DB::connection('main')->table('clients')->count();
    echo "Main DB (clients): $mainCount\n";
} catch (Exception $e) {
    echo "Main DB Error: " . $e->getMessage() . "\n";
}

// Test client database
try {
    echo "Testing client database connection...\n";
    $config = config('database.connections.client');
    echo "Client DB Config: " . json_encode($config) . "\n";
    
    $categoriesCount = DB::connection('client')->table('categories')->count();
    $productsCount = DB::connection('client')->table('products')->count();
    $usersCount = DB::connection('client')->table('users')->where('role', 'user')->count();
    $ordersCount = DB::connection('client')->table('orders')->count();
    $revenue = DB::connection('client')->table('orders')->where('payment_status', 'paid')->sum('total_amount');
    
    echo "Client DB Results:\n";
    echo "Categories: $categoriesCount\n";
    echo "Products: $productsCount\n";
    echo "Users: $usersCount\n";
    echo "Orders: $ordersCount\n";
    echo "Revenue: $revenue\n";
    
} catch (Exception $e) {
    echo "Client DB Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test direct MySQL connection to vergeflow_client_1
try {
    echo "\nTesting direct connection to vergeflow_client_1...\n";
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=vergeflow_client_1', 'root', '');
    $stmt = $pdo->query('SELECT COUNT(*) FROM products');
    $productCount = $stmt->fetchColumn();
    echo "Direct MySQL - Products: $productCount\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM orders');
    $orderCount = $stmt->fetchColumn();
    echo "Direct MySQL - Orders: $orderCount\n";
    
} catch (Exception $e) {
    echo "Direct MySQL Error: " . $e->getMessage() . "\n";
}
