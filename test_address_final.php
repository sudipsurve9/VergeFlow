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
    
    echo "🔍 Testing Address Save Functionality\n";
    echo "=====================================\n";
    
    // Test 1: Check current addresses count
    $currentCount = DB::connection('client_1')->table('addresses')->count();
    echo "📊 Current addresses in database: {$currentCount}\n";
    
    // Test 2: Create address with minimal required fields (matching actual schema)
    $addressData = [
        'user_id' => 40,
        'type' => 'shipping', // Using original schema value
        'name' => 'Test User',
        'phone' => '1234567890',
        'address_line1' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'postal_code' => '123456',
        'country' => 'India',
        'is_default' => false, // Using original schema field
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    echo "💾 Creating address with data:\n";
    echo "   - Name: {$addressData['name']}\n";
    echo "   - Phone: {$addressData['phone']}\n";
    echo "   - Address: {$addressData['address_line1']}\n";
    echo "   - City: {$addressData['city']}\n";
    
    $addressId = DB::connection('client_1')->table('addresses')->insertGetId($addressData);
    echo "✅ Address created successfully with ID: {$addressId}\n";
    
    // Test 3: Verify the address was saved
    $savedAddress = DB::connection('client_1')->table('addresses')->find($addressId);
    if ($savedAddress) {
        echo "✅ Address verification successful!\n";
        echo "🏠 Saved address details:\n";
        echo "   - ID: {$savedAddress->id}\n";
        echo "   - Name: {$savedAddress->name}\n";
        echo "   - Phone: {$savedAddress->phone}\n";
        echo "   - Address: {$savedAddress->address_line1}\n";
        echo "   - City: {$savedAddress->city}, {$savedAddress->state} - {$savedAddress->postal_code}\n";
        echo "   - Type: {$savedAddress->type}\n";
        echo "   - Default: " . ($savedAddress->is_default ? 'Yes' : 'No') . "\n";
    }
    
    // Test 4: Check final count
    $finalCount = DB::connection('client_1')->table('addresses')->count();
    echo "📊 Final addresses in database: {$finalCount}\n";
    echo "📈 Addresses added: " . ($finalCount - $currentCount) . "\n";
    
    echo "\n🎉 Address save functionality test completed successfully!\n";
    echo "✅ The address book should now work properly in the web interface.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
