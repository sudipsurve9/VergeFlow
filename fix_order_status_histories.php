<?php

echo "🔧 Fixing Missing Order Status Histories Table\n";
echo "==============================================\n";

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
    
    // Check if order_status_histories table exists
    $tableExistsStmt = $pdo->query("SHOW TABLES LIKE 'order_status_histories'");
    $tableExists = $tableExistsStmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ order_status_histories table does not exist! Creating it...\n";
        
        $createTableSQL = "
            CREATE TABLE order_status_histories (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_id BIGINT UNSIGNED NOT NULL,
                status VARCHAR(255) NOT NULL,
                comment VARCHAR(255) NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY order_status_histories_order_id_foreign (order_id),
                CONSTRAINT order_status_histories_order_id_foreign FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createTableSQL);
        echo "✅ order_status_histories table created successfully!\n";
    } else {
        echo "✅ order_status_histories table already exists\n";
    }
    
    // Verify table structure
    echo "\n📋 Checking order_status_histories table structure...\n";
    $stmt = $pdo->query("DESCRIBE order_status_histories");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']} (Null: {$column['Null']}, Key: {$column['Key']})\n";
    }
    
    // Test insertion to verify the table works
    echo "\n🧪 Testing order status history insertion...\n";
    
    // Get a test order
    $orderStmt = $pdo->query("SELECT id FROM orders LIMIT 1");
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        $testSQL = "INSERT INTO order_status_histories (order_id, status, comment, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
        
        echo "Testing with order ID: {$order['id']}\n";
        
        $testStmt = $pdo->prepare($testSQL);
        $testResult = $testStmt->execute([
            $order['id'],
            'pending',
            'Order created',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ]);
        
        if ($testResult) {
            $testId = $pdo->lastInsertId();
            echo "✅ Test order status history created with ID: $testId\n";
            
            // Verify the inserted data
            $verifyStmt = $pdo->prepare("SELECT * FROM order_status_histories WHERE id = ?");
            $verifyStmt->execute([$testId]);
            $insertedData = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            echo "📋 Inserted data verification:\n";
            foreach ($insertedData as $field => $value) {
                echo "  - $field: $value\n";
            }
            
            // Clean up test data
            $pdo->exec("DELETE FROM order_status_histories WHERE id = $testId");
            echo "🧹 Test data cleaned up\n";
        } else {
            echo "❌ FAILED to insert order status history\n";
            $errorInfo = $testStmt->errorInfo();
            echo "Error: " . $errorInfo[2] . "\n";
        }
    } else {
        echo "⚠️ No test orders available for testing\n";
    }
    
    // Check if Order model has statusHistories relationship
    echo "\n🔍 Checking for other potentially missing tables...\n";
    
    $requiredTables = [
        'orders',
        'order_items', 
        'order_status_histories',
        'products',
        'categories',
        'users',
        'addresses',
        'cart_items'
    ];
    
    foreach ($requiredTables as $tableName) {
        $checkStmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        $exists = $checkStmt->rowCount() > 0;
        
        if ($exists) {
            echo "  ✅ $tableName: exists\n";
        } else {
            echo "  ❌ $tableName: missing\n";
        }
    }
    
    echo "\n🎉 Order Status Histories table fix completed!\n";
    echo "✅ The table now exists and is ready for use\n";
    echo "✅ OrderController should now be able to load order details without errors\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "📍 Error Code: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}
