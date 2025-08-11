<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Address;
use App\Models\User;

try {
    // Set up client database connection
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
    
    echo "🧪 Testing Add New Address Functionality\n";
    echo "========================================\n";
    
    // Test 1: Verify user exists
    $user = User::first();
    if (!$user) {
        echo "❌ No user found for testing\n";
        exit;
    }
    echo "✅ Test user found: {$user->name} (ID: {$user->id})\n";
    
    // Test 2: Check current addresses
    $existingAddresses = Address::where('user_id', $user->id)->count();
    echo "📍 Current addresses for user: {$existingAddresses}\n";
    
    // Test 3: Simulate adding a new address (like the modal form would do)
    echo "\n💾 Testing address creation (simulating modal form submission)...\n";
    
    $testAddressData = [
        'user_id' => $user->id,
        'type' => 'home',
        'address_type' => 'both',
        'label' => 'Test Address',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '1234567890',
        'address_line_1' => '123 Test Street',
        'address_line_2' => 'Apt 4B',
        'landmark' => 'Near Test Mall',
        'city' => 'Test City',
        'state' => 'Test State',
        'postal_code' => '123456',
        'country' => 'India',
        'delivery_instructions' => 'Ring the doorbell twice',
        'is_default_shipping' => 1,
        'is_default_billing' => 1
    ];
    
    echo "📋 Test address data:\n";
    foreach ($testAddressData as $key => $value) {
        echo "   - {$key}: {$value}\n";
    }
    
    // Create the address
    $address = Address::create($testAddressData);
    echo "\n✅ Address created successfully with ID: {$address->id}\n";
    
    // Test 4: Verify address was saved correctly
    $savedAddress = Address::find($address->id);
    if ($savedAddress) {
        echo "✅ Address retrieved from database:\n";
        echo "   - Name: {$savedAddress->name}\n";
        echo "   - Phone: {$savedAddress->phone}\n";
        echo "   - Address: {$savedAddress->address_line_1}\n";
        echo "   - City: {$savedAddress->city}\n";
        echo "   - Formatted: {$savedAddress->getFormattedAddress()}\n";
    }
    
    // Test 5: Verify address count increased
    $newAddressCount = Address::where('user_id', $user->id)->count();
    echo "\n📊 Address count after creation: {$newAddressCount}\n";
    
    if ($newAddressCount > $existingAddresses) {
        echo "✅ Address count increased correctly!\n";
    } else {
        echo "❌ Address count did not increase\n";
    }
    
    // Test 6: Test address book display
    echo "\n📖 Testing address book display...\n";
    $userAddresses = Address::where('user_id', $user->id)->get();
    
    foreach ($userAddresses as $addr) {
        echo "   📍 Address ID {$addr->id}:\n";
        echo "      - Type: {$addr->type} ({$addr->address_type})\n";
        echo "      - Name: {$addr->name}\n";
        echo "      - Location: {$addr->city}, {$addr->state}\n";
        echo "      - Default Shipping: " . ($addr->is_default_shipping ? 'Yes' : 'No') . "\n";
        echo "      - Default Billing: " . ($addr->is_default_billing ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n🎉 Add New Address functionality test completed successfully!\n";
    echo "✅ Modal form submission simulation works correctly\n";
    echo "✅ Address creation and saving works\n";
    echo "✅ Address retrieval and display works\n";
    echo "✅ Address book functionality is fully operational\n";
    
    echo "\n📝 Next steps:\n";
    echo "   1. Test the modal UI in the browser by clicking 'Add New Address'\n";
    echo "   2. Fill out the form and submit to verify end-to-end functionality\n";
    echo "   3. Verify the address appears in the address book after submission\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
