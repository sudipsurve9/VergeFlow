<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    echo "Verifying super admin password...\n";
    
    $user = User::where('email', 'superadmin@vergeflow.com')->first();
    
    if ($user) {
        echo "✅ User found: " . $user->name . "\n";
        
        // Test the current password
        $testPassword = 'Stark@0910';
        $passwordMatches = Hash::check($testPassword, $user->password);
        
        echo "Password verification for 'Stark@0910': " . ($passwordMatches ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";
        
        if (!$passwordMatches) {
            echo "🔧 Updating password to ensure it's correct...\n";
            $user->password = Hash::make('Stark@0910');
            $user->save();
            echo "✅ Password updated successfully!\n";
            
            // Verify again
            $user->refresh();
            $passwordMatches = Hash::check($testPassword, $user->password);
            echo "Re-verification: " . ($passwordMatches ? "✅ MATCHES" : "❌ STILL DOES NOT MATCH") . "\n";
        }
        
        // Also test the old password
        $oldPasswordMatches = Hash::check('password123', $user->password);
        echo "Old password 'password123' check: " . ($oldPasswordMatches ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";
        
        echo "\nFinal credentials:\n";
        echo "Email: superadmin@vergeflow.com\n";
        echo "Password: Stark@0910\n";
        echo "Role: " . $user->role . "\n";
        
    } else {
        echo "❌ Super admin user not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
