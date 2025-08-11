<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    echo "Checking for super admin user...\n";
    
    $user = User::where('email', 'superadmin@vergeflow.com')->first();
    
    if ($user) {
        echo "✅ Super admin user found:\n";
        echo "   Name: " . $user->name . "\n";
        echo "   Email: " . $user->email . "\n";
        echo "   Role: " . $user->role . "\n";
        echo "   Created: " . $user->created_at . "\n";
    } else {
        echo "❌ Super admin user not found. Creating new super admin...\n";
        
        $newUser = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@vergeflow.com',
            'password' => Hash::make('Stark@0910'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);
        
        echo "✅ Super admin user created successfully!\n";
        echo "   Email: superadmin@vergeflow.com\n";
        echo "   Password: Stark@0910\n";
        echo "   Role: super_admin\n";
    }
    
    // Also check total users count
    $totalUsers = User::count();
    echo "\nTotal users in database: " . $totalUsers . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
