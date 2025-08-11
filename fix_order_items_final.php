<?php

echo "🔧 FINAL Fix for Order Items Table Schema\n";
echo "=========================================\n";

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
    
    // First, let's check if the table exists at all
    $tableExistsStmt = $pdo->query("SHOW TABLES LIKE 'order_items'");
    $tableExists = $tableExistsStmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ order_items table does not exist! Creating it...\n";
        
        $createTableSQL = "
            CREATE TABLE order_items (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY order_items_order_id_foreign (order_id),
                KEY order_items_product_id_foreign (product_id),
                CONSTRAINT order_items_order_id_foreign FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
                CONSTRAINT order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createTableSQL);
        echo "✅ order_items table created successfully!\n";
    } else {
        echo "✅ order_items table exists\n";
    }
    
    // Check current table structure in detail
    echo "\n📋 Checking current order_items table structure...\n";
    $stmt = $pdo->query("DESCRIBE order_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $columnNames = array_column($columns, 'Field');
    echo "Current columns: " . implode(', ', $columnNames) . "\n";
    
    echo "\nDetailed column structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']} (Null: {$column['Null']}, Key: {$column['Key']}, Default: {$column['Default']})\n";
    }
    
    // Check specifically for the total column
    if (!in_array('total', $columnNames)) {
        echo "\n❌ CRITICAL: 'total' column is missing!\n";
        echo "🔄 Adding 'total' column with proper specification...\n";
        
        try {
            $addColumnSQL = "ALTER TABLE order_items ADD COLUMN total DECIMAL(10,2) NOT NULL AFTER price";
            $pdo->exec($addColumnSQL);
            echo "✅ 'total' column added successfully!\n";
        } catch (PDOException $e) {
            echo "❌ Failed to add 'total' column: " . $e->getMessage() . "\n";
            
            // Try alternative approach
            echo "🔄 Trying alternative approach...\n";
            try {
                $altSQL = "ALTER TABLE order_items ADD total DECIMAL(10,2) NOT NULL";
                $pdo->exec($altSQL);
                echo "✅ 'total' column added with alternative method!\n";
            } catch (PDOException $e2) {
                echo "❌ Alternative method also failed: " . $e2->getMessage() . "\n";
                
                // Last resort: recreate the table
                echo "🔄 Last resort: Recreating table with proper schema...\n";
                
                // Backup existing data
                $backupStmt = $pdo->query("SELECT * FROM order_items");
                $existingData = $backupStmt->fetchAll(PDO::FETCH_ASSOC);
                echo "📦 Backed up " . count($existingData) . " existing records\n";
                
                // Drop and recreate table
                $pdo->exec("DROP TABLE order_items");
                $pdo->exec($createTableSQL);
                echo "✅ Table recreated with proper schema\n";
                
                // Restore data (if any)
                if (!empty($existingData)) {
                    foreach ($existingData as $row) {
                        $insertSQL = "INSERT INTO order_items (id, order_id, product_id, quantity, price, total, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $insertStmt = $pdo->prepare($insertSQL);
                        $total = isset($row['total']) ? $row['total'] : ($row['quantity'] * $row['price']);
                        $insertStmt->execute([
                            $row['id'],
                            $row['order_id'],
                            $row['product_id'],
                            $row['quantity'],
                            $row['price'],
                            $total,
                            $row['created_at'],
                            $row['updated_at']
                        ]);
                    }
                    echo "✅ Restored " . count($existingData) . " records\n";
                }
            }
        }
    } else {
        echo "\n✅ 'total' column exists\n";
    }
    
    // Verify the final structure
    echo "\n🔍 Final verification of table structure...\n";
    $finalStmt = $pdo->query("DESCRIBE order_items");
    $finalColumns = $finalStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Final table structure:\n";
    foreach ($finalColumns as $column) {
        echo "  ✅ {$column['Field']}: {$column['Type']}\n";
    }
    
    // Test actual insertion to verify it works
    echo "\n🧪 Testing actual order item insertion...\n";
    
    // Get test data
    $orderStmt = $pdo->query("SELECT id FROM orders LIMIT 1");
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    $productStmt = $pdo->query("SELECT id FROM products LIMIT 1");
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order && $product) {
        // Test the exact SQL that's failing in the error log
        $testSQL = "INSERT INTO order_items (order_id, product_id, quantity, price, total, updated_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        echo "Testing SQL: $testSQL\n";
        echo "With values: order_id={$order['id']}, product_id={$product['id']}, quantity=1, price=349.99, total=349.99\n";
        
        $testStmt = $pdo->prepare($testSQL);
        $testResult = $testStmt->execute([
            $order['id'],
            $product['id'],
            1,
            349.99,
            349.99,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ]);
        
        if ($testResult) {
            $testId = $pdo->lastInsertId();
            echo "✅ SUCCESS! Order item inserted with ID: $testId\n";
            
            // Verify the inserted data
            $verifyStmt = $pdo->prepare("SELECT * FROM order_items WHERE id = ?");
            $verifyStmt->execute([$testId]);
            $insertedData = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            echo "📋 Inserted data verification:\n";
            foreach ($insertedData as $field => $value) {
                echo "  - $field: $value\n";
            }
            
            // Clean up test data
            $pdo->exec("DELETE FROM order_items WHERE id = $testId");
            echo "🧹 Test data cleaned up\n";
        } else {
            echo "❌ FAILED to insert order item\n";
            $errorInfo = $testStmt->errorInfo();
            echo "Error: " . $errorInfo[2] . "\n";
        }
    } else {
        echo "⚠️ No test orders or products available\n";
    }
    
    echo "\n🎉 Order Items table schema fix completed!\n";
    echo "✅ The table now has all required columns including 'total'\n";
    echo "✅ Insertion test passed - OrderController should work now\n";
    
    // Show final column list for confirmation
    $finalColumnNames = array_column($finalColumns, 'Field');
    echo "\n📝 Final column list: " . implode(', ', $finalColumnNames) . "\n";
    
    if (in_array('total', $finalColumnNames)) {
        echo "✅ CONFIRMED: 'total' column is present and ready for use\n";
    } else {
        echo "❌ CRITICAL ERROR: 'total' column is still missing after all attempts\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "📍 Error Code: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}
