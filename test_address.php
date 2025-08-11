<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Address;
use App\Models\User;

try {
    $address = Address::create([
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
    
    echo "Address created successfully with ID: " . $address->id . "\n";
    echo "Address details: " . json_encode($address->toArray()) . "\n";
    
} catch (Exception $e) {
    echo "Error creating address: " . $e->getMessage() . "\n";
}
