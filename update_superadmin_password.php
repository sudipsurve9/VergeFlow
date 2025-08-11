<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::where('email', 'superadmin@vergeflow.com')->first();
    
    if ($user) {
        $user->password = Hash::make('Stark@0910');
        $user->save();
        echo "✅ Password updated successfully for superadmin@vergeflow.com\n";
        echo "New password: Stark@0910\n";
    } else {
        echo "❌ Super admin user not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
