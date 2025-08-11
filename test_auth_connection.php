<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== DATABASE CONNECTION TEST ===\n\n";
    
    // Check default connection
    echo "1. Default database connection: " . config('database.default') . "\n";
    
    // Check main database connection
    echo "2. Testing main database connection...\n";
    $mainUsers = DB::connection('main')->table('users')->where('email', 'superadmin@vergeflow.com')->first();
    if ($mainUsers) {
        echo "   ✅ Super admin found in MAIN database\n";
        echo "   Name: " . $mainUsers->name . "\n";
        echo "   Role: " . $mainUsers->role . "\n";
    } else {
        echo "   ❌ Super admin NOT found in main database\n";
    }
    
    // Check if there are client databases
    echo "\n3. Testing client database connections...\n";
    $clientConnections = ['client_1', 'client_2', 'client_3'];
    
    foreach ($clientConnections as $connection) {
        try {
            $clientUsers = DB::connection($connection)->table('users')->where('email', 'superadmin@vergeflow.com')->first();
            if ($clientUsers) {
                echo "   ✅ Super admin found in $connection database\n";
            } else {
                echo "   ❌ Super admin NOT found in $connection database\n";
            }
        } catch (Exception $e) {
            echo "   ⚠️  $connection database not accessible: " . $e->getMessage() . "\n";
        }
    }
    
    // Test User model connection
    echo "\n4. Testing User model connection...\n";
    $userModel = new User();
    echo "   User model connection: " . $userModel->getConnectionName() . "\n";
    
    // Test authentication manually
    echo "\n5. Testing manual authentication...\n";
    
    // Force User model to use main connection
    $user = User::on('main')->where('email', 'superadmin@vergeflow.com')->first();
    if ($user) {
        echo "   ✅ User found via main connection\n";
        $passwordCheck = Hash::check('Stark@0910', $user->password);
        echo "   Password verification: " . ($passwordCheck ? "✅ CORRECT" : "❌ INCORRECT") . "\n";
    } else {
        echo "   ❌ User NOT found via main connection\n";
    }
    
    // Test default User model query
    echo "\n6. Testing default User model query...\n";
    $defaultUser = User::where('email', 'superadmin@vergeflow.com')->first();
    if ($defaultUser) {
        echo "   ✅ User found via default query\n";
        echo "   Connection used: " . $defaultUser->getConnectionName() . "\n";
    } else {
        echo "   ❌ User NOT found via default query\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
