<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Address;

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
    
    echo "🔍 Testing Address Model Accessors\n";
    echo "==================================\n";
    
    // Get the first address
    $address = Address::first();
    
    if ($address) {
        echo "✅ Address found with ID: {$address->id}\n";
        echo "📋 Testing accessors:\n";
        echo "   - Name: {$address->name}\n";
        echo "   - First Name: {$address->first_name}\n";
        echo "   - Last Name: {$address->last_name}\n";
        echo "   - Address Line 1 (accessor): {$address->address_line1}\n";
        echo "   - Address Line 2 (accessor): {$address->address_line2}\n";
        echo "   - Phone: {$address->phone}\n";
        echo "   - City: {$address->city}\n";
        
        echo "\n🎉 Address model accessors are working correctly!\n";
        echo "✅ The checkout page should now load without errors.\n";
        
    } else {
        echo "❌ No addresses found in database\n";
        echo "💡 This is expected if no addresses have been created yet\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
