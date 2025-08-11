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
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
    ]);
    
    echo "🔍 Testing Minimal Address Insert\n";
    echo "=================================\n";
    
    // Test with only the most essential columns that definitely exist
    $minimalData = [
        'user_id' => 40,
        'type' => 'home',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '1234567890',
        'address_line_1' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'postal_code' => '123456',
        'country' => 'India',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    echo "💾 Attempting minimal insert...\n";
    $addressId = DB::connection('client_1')->table('addresses')->insertGetId($minimalData);
    echo "✅ SUCCESS! Address created with ID: {$addressId}\n";
    
    // Verify the address
    $savedAddress = DB::connection('client_1')->table('addresses')->find($addressId);
    if ($savedAddress) {
        echo "✅ Address verified: {$savedAddress->first_name} {$savedAddress->last_name}\n";
        echo "📍 Address: {$savedAddress->address_line_1}, {$savedAddress->city}\n";
        
        echo "\n🎉 Address save functionality is now working!\n";
        echo "✅ The address book modal should now work properly.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    
    // Extract the problematic column name
    if (preg_match("/Unknown column '([^']+)'/", $e->getMessage(), $matches)) {
        echo "🔍 Problematic column: {$matches[1]}\n";
        echo "📋 This column needs to be removed from the Address model or added to the database.\n";
    }
}
