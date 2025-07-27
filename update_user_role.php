<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== VergeFlow User Role Manager ===\n\n";

// Show current users
echo "Current users in the system:\n";
echo "ID | Name | Email | Role\n";
echo "---|------|-------|-----\n";

$users = User::select('id', 'name', 'email', 'role')->get();
foreach ($users as $user) {
    $role = $user->role ?? 'site_user';
    echo "{$user->id} | {$user->name} | {$user->email} | {$role}\n";
}

echo "\n";

// Get user input for which user to update
echo "Enter the ID of the user you want to make super_admin: ";
$userId = trim(fgets(STDIN));

if (!is_numeric($userId)) {
    echo "Error: Please enter a valid user ID number.\n";
    exit(1);
}

$user = User::find($userId);
if (!$user) {
    echo "Error: User with ID {$userId} not found.\n";
    exit(1);
}

echo "\nSelected user: {$user->name} ({$user->email})\n";
echo "Current role: " . ($user->role ?? 'site_user') . "\n";
echo "\nConfirm: Make this user a super_admin? (y/N): ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) !== 'y') {
    echo "Operation cancelled.\n";
    exit(0);
}

// Update the user role
$user->role = 'super_admin';
$user->save();

echo "\n✅ Success! User '{$user->name}' is now a super_admin.\n";
echo "You can now access:\n";
echo "- Super Admin Panel: http://127.0.0.1:8000/super-admin\n";
echo "- Admin Panel: http://127.0.0.1:8000/admin\n";
echo "- Site: http://127.0.0.1:8000/\n\n";

echo "Please logout and login again to refresh your session.\n";
