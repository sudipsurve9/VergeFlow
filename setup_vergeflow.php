<?php

/**
 * VergeFlow Setup Script
 * This script helps you set up the multi-database architecture
 */

echo "=== VergeFlow Setup Script ===\n\n";

// Step 1: Check if .env file exists
echo "1. Checking .env file...\n";
if (!file_exists('.env')) {
    echo "   ✗ .env file not found\n";
    echo "   Please copy env_template.txt to .env and update the database settings\n";
    echo "   cp env_template.txt .env\n\n";
    
    echo "   Required database settings:\n";
    echo "   - DB_HOST=127.0.0.1\n";
    echo "   - DB_PORT=3306\n";
    echo "   - DB_DATABASE=vergeflow_main\n";
    echo "   - DB_USERNAME=root\n";
    echo "   - DB_PASSWORD=(your password)\n\n";
    
    echo "   Make sure MySQL is running and you have permissions to create databases.\n";
    exit(1);
} else {
    echo "   ✓ .env file found\n";
}

// Step 2: Check if MySQL is accessible
echo "\n2. Testing database connection...\n";
try {
    $host = '127.0.0.1';
    $port = '3306';
    $username = 'root';
    $password = ''; // You may need to set this
    
    $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
    echo "   ✓ MySQL connection successful\n";
} catch (PDOException $e) {
    echo "   ✗ MySQL connection failed: " . $e->getMessage() . "\n";
    echo "   Please check your MySQL settings and make sure the service is running.\n";
    exit(1);
}

// Step 3: Check if main database exists
echo "\n3. Checking main database...\n";
try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname=vergeflow_main", $username, $password);
    echo "   ✓ Main database 'vergeflow_main' exists\n";
} catch (PDOException $e) {
    echo "   ✗ Main database 'vergeflow_main' not found\n";
    echo "   Creating main database...\n";
    
    try {
        $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS vergeflow_main");
        echo "   ✓ Main database created successfully\n";
    } catch (PDOException $e2) {
        echo "   ✗ Failed to create main database: " . $e2->getMessage() . "\n";
        exit(1);
    }
}

// Step 4: Provide next steps
echo "\n4. Setup instructions:\n";
echo "   ✓ Environment is ready\n";
echo "   ✓ Database connection is working\n";
echo "   ✓ Main database exists\n\n";

echo "   Next steps:\n";
echo "   1. Run migrations: php artisan migrate\n";
echo "   2. Run seeders: php artisan db:seed\n";
echo "   3. Run multi-database migration: php artisan vergeflow:migrate-multi-db --force\n";
echo "   4. Test the setup: php test_multi_db.php\n\n";

echo "   If you encounter any issues:\n";
echo "   - Check the Laravel logs: tail -f storage/logs/laravel.log\n";
echo "   - Verify MySQL permissions for database creation\n";
echo "   - Make sure all required PHP extensions are installed\n\n";

echo "=== Setup Complete ===\n"; 