<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Update user ID 40 to super_admin
$user = User::find(40);
if ($user) {
    $user->role = 'super_admin';
    $user->save();
    echo "✅ Success! User '{$user->name}' ({$user->email}) is now a super_admin.\n";
    echo "\nYou can now access:\n";
    echo "- Super Admin Panel: http://127.0.0.1:8000/super-admin\n";
    echo "- Admin Panel: http://127.0.0.1:8000/admin\n";
    echo "- Site: http://127.0.0.1:8000/\n";
    echo "\n⚠️  Please logout and login again to refresh your session.\n";
} else {
    echo "❌ Error: User with ID 40 not found.\n";
}
