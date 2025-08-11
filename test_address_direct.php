<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

try {
    // Set up client database connection manually
    Config::set('database.connections.client_1', [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => 'vergeflow_client_1',
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
    ]);
    
    // Test database connection
    $tables = DB::connection('client_1')->select('SHOW TABLES');
    echo "✅ Connected to client database successfully!\n";
    echo "📊 Found " . count($tables) . " tables in vergeflow_client_1\n";
    
    // Check addresses table structure
    $columns = DB::connection('client_1')->select('DESCRIBE addresses');
    echo "📋 Addresses table columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    // Test direct insert
    $addressData = [
        'user_id' => 40,
        'type' => 'home',
        'name' => 'Test User',
        'phone' => '1234567890',
        'address_line1' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'postal_code' => '123456',
        'country' => 'India',
        'address_type' => 'both',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    $addressId = DB::connection('client_1')->table('addresses')->insertGetId($addressData);
    echo "✅ Address created successfully with ID: {$addressId}\n";
    
    // Verify the address was saved
    $savedAddress = DB::connection('client_1')->table('addresses')->find($addressId);
    if ($savedAddress) {
        echo "✅ Address verification successful!\n";
        echo "🏠 Saved address: {$savedAddress->name} - {$savedAddress->address_line1}\n";
        echo "📍 Full address: {$savedAddress->address_line1}, {$savedAddress->city}, {$savedAddress->state} - {$savedAddress->postal_code}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
