<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
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
    
    echo "🔧 Fixing User/Address Context Alignment\n";
    echo "========================================\n";
    
    // Check current users and addresses
    $users = User::all();
    $addresses = Address::all();
    
    echo "👤 Users in database:\n";
    foreach ($users as $user) {
        echo "   - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    }
    
    echo "\n📍 Addresses in database:\n";
    foreach ($addresses as $address) {
        echo "   - ID: {$address->id}, User ID: {$address->user_id}, Name: {$address->name}\n";
    }
    
    // Find the mismatch
    $addressUser = $addresses->first();
    $currentUser = $users->first();
    
    if ($addressUser && $currentUser && $addressUser->user_id != $currentUser->id) {
        echo "\n⚠️ MISMATCH DETECTED:\n";
        echo "   - Address belongs to user ID: {$addressUser->user_id}\n";
        echo "   - Current user ID: {$currentUser->id}\n";
        
        echo "\n🔧 Fixing address ownership...\n";
        
        // Update address to belong to the current user
        Address::where('user_id', $addressUser->user_id)->update(['user_id' => $currentUser->id]);
        
        echo "✅ Updated address ownership to user ID: {$currentUser->id}\n";
        
        // Also fix any cart items
        $cartItems = CartItem::where('user_id', '!=', $currentUser->id)->get();
        if ($cartItems->isNotEmpty()) {
            echo "🛒 Fixing cart items ownership...\n";
            CartItem::where('user_id', '!=', $currentUser->id)->update(['user_id' => $currentUser->id]);
            echo "✅ Updated {$cartItems->count()} cart items to user ID: {$currentUser->id}\n";
        }
        
        // Verify the fix
        echo "\n✅ Verification:\n";
        $updatedAddress = Address::first();
        echo "   - Address now belongs to user ID: {$updatedAddress->user_id}\n";
        echo "   - Current user ID: {$currentUser->id}\n";
        
        if ($updatedAddress->user_id == $currentUser->id) {
            echo "🎉 User/Address context is now aligned!\n";
            echo "✅ Place Order should now work correctly.\n";
        }
        
    } else {
        echo "✅ User/Address context is already aligned.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
