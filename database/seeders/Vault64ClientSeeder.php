<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Hash;

class Vault64ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $databaseService = new DatabaseService();
        
        // Create Vault64 client
        $vault64Client = Client::firstOrCreate(
            [
                'subdomain' => 'vault64',
            ],
            [
                'name' => 'Vault64',
                'company_name' => 'Vault64 Original Store',
                'contact_email' => 'admin@vault64.vergeflow.com',
                'contact_phone' => '+1-555-0000',
                'theme' => 'modern',
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
                'is_active' => true,
                'database_name' => 'vergeflow_vault64_1'
            ]
        );
        
        // Create database for Vault64
        $databaseService->createClientDatabase($vault64Client);
        
        // Create admin user for Vault64
        User::firstOrCreate(
            [
                'email' => 'admin@vault64.vergeflow.com',
                'client_id' => $vault64Client->id,
            ],
            [
                'name' => 'Vault64 Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
        
        $this->command->info('Vault64 client created successfully with database: ' . $vault64Client->database_name);
    }
} 