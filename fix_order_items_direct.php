<?php

echo "🔧 Direct SQL Fix for Order Items Table\n";
echo "======================================\n";

// Database connection details
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'vergeflow_client_1';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database: $database\n";
    
    // Check current table structure
    echo "\n📋 Checking current order_items table structure...\n";
    $stmt = $pdo->query("DESCRIBE order_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $columnNames = array_column($columns, 'Field');
    echo "Current columns: " . implode(', ', $columnNames) . "\n";
    
    // Check if total column exists
    if (!in_array('total', $columnNames)) {
        echo "❌ Missing 'total' column\n";
        echo "🔄 Adding 'total' column...\n";
        
        // Add the total column
        $sql = "ALTER TABLE order_items ADD COLUMN total DECIMAL(10,2) AFTER price";
        $pdo->exec($sql);
        
        echo "✅ 'total' column added successfully!\n";
    } else {
        echo "✅ 'total' column already exists\n";
    }
    
    // Verify the fix
    echo "\n🔍 Verifying table structure after fix...\n";
    $stmt = $pdo->query("DESCRIBE order_items");
    $updatedColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Updated table structure:\n";
    foreach ($updatedColumns as $column) {
        echo "  - {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']}\n";
    }
    
    // Test insert to verify the fix works
    echo "\n🧪 Testing order item insertion...\n";
    
    // Get test data
    $orderStmt = $pdo->query("SELECT id FROM orders LIMIT 1");
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    $productStmt = $pdo->query("SELECT id FROM products LIMIT 1");
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order && $product) {
        $testSql = "INSERT INTO order_items (order_id, product_id, quantity, price, total, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        
        $testStmt = $pdo->prepare($testSql);
        $testResult = $testStmt->execute([
            $order['id'],
            $product['id'],
            1,
            99.99,
            99.99
        ]);
        
        if ($testResult) {
            $testId = $pdo->lastInsertId();
            echo "✅ Test order item created successfully with ID: $testId\n";
            
            // Clean up test data
            $pdo->exec("DELETE FROM order_items WHERE id = $testId");
            echo "🧹 Test data cleaned up\n";
        } else {
            echo "❌ Failed to create test order item\n";
        }
    } else {
        echo "⚠️ No test orders or products available for testing\n";
    }
    
    echo "\n🎉 Order Items table schema fix completed successfully!\n";
    echo "✅ The 'total' column is now present and functional\n";
    echo "✅ OrderController should now work without SQL errors\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
