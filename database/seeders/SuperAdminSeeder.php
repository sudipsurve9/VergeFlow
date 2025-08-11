<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@vergeflow.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@vergeflow.com',
                'password' => Hash::make('Stark@0910'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@vergeflow.com');
        $this->command->info('Password: Stark@0910');
        $this->command->info('Role: super_admin');
    }
}
