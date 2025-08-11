<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Address;
use App\Models\User;
use App\Models\Client;
use App\Services\MultiTenantService;

try {
    // Set up multi-tenant context
    $client = Client::find(1); // Vault64 client
    if ($client) {
        $multiTenantService = new MultiTenantService();
        $multiTenantService->setClientDatabaseConnection($client->id, $client->database_name);
        
        echo "✅ Multi-tenant context set for client: " . $client->name . "\n";
        echo "📊 Database: " . $client->database_name . "\n";
        
        // Create address with proper client context
        $address = new Address([
            'user_id' => 40,
            'type' => 'home',
            'name' => 'Test User',
            'phone' => '1234567890',
            'address_line1' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '123456',
            'country' => 'India',
            'address_type' => 'both'
        ]);
        
        // Set the client database connection
        $address->setConnection('client_1');
        $address->save();
        
        echo "✅ Address created successfully with ID: " . $address->id . "\n";
        echo "📋 Address details: " . json_encode($address->toArray()) . "\n";
        
        // Verify address was saved
        $savedAddress = Address::on('client_1')->find($address->id);
        if ($savedAddress) {
            echo "✅ Address verification successful!\n";
            echo "🏠 Saved address: " . $savedAddress->name . " - " . $savedAddress->address_line1 . "\n";
        }
        
    } else {
        echo "❌ Client not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
