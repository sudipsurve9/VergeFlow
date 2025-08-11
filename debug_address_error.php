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
    
    echo "🔍 Debugging Address Column Error\n";
    echo "=================================\n";
    
    // Get all column names from addresses table
    $columns = DB::connection('client_1')->select('DESCRIBE addresses');
    $columnNames = array_map(function($col) { return $col->Field; }, $columns);
    
    echo "📋 Available columns in addresses table:\n";
    foreach ($columnNames as $column) {
        echo "   - {$column}\n";
    }
    
    // Test minimal insert with only essential columns
    echo "\n💾 Testing minimal insert with only essential columns...\n";
    
    $minimalData = [
        'user_id' => 40,
        'type' => 'home',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '1234567890',
        'address_line1' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'postal_code' => '123456',
        'country' => 'India',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    echo "🔧 Attempting insert with columns:\n";
    foreach ($minimalData as $key => $value) {
        echo "   - {$key}: {$value}\n";
    }
    
    $addressId = DB::connection('client_1')->table('addresses')->insertGetId($minimalData);
    echo "✅ Minimal address created successfully with ID: {$addressId}\n";
    
    // Verify the minimal address
    $savedAddress = DB::connection('client_1')->table('addresses')->find($addressId);
    if ($savedAddress) {
        echo "✅ Minimal address verification successful!\n";
        echo "🏠 Saved: {$savedAddress->first_name} {$savedAddress->last_name} - {$savedAddress->address_line1}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    
    // Try to extract the specific column name from the error
    if (strpos($e->getMessage(), "Unknown column") !== false) {
        preg_match("/Unknown column '([^']+)'/", $e->getMessage(), $matches);
        if (isset($matches[1])) {
            echo "🔍 Problematic column: {$matches[1]}\n";
        }
    }
}
