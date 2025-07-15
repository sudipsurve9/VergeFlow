<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create sample clients
        $clients = [
            [
                'name' => 'Hot Wheels Store',
                'company_name' => 'Hot Wheels Collectibles Inc.',
                'contact_email' => 'admin@hotwheels.vergeflow.com',
                'contact_phone' => '+1-555-0123',
                'subdomain' => 'hotwheels',
                'theme' => 'modern',
                'primary_color' => '#e74c3c',
                'secondary_color' => '#2c3e50',
                'is_active' => true,
            ],
            [
                'name' => 'Luxury Cars',
                'company_name' => 'Luxury Automotive Group',
                'contact_email' => 'admin@luxury.vergeflow.com',
                'contact_phone' => '+1-555-0456',
                'subdomain' => 'luxury',
                'theme' => 'luxury',
                'primary_color' => '#f39c12',
                'secondary_color' => '#34495e',
                'is_active' => true,
            ],
            [
                'name' => 'Neon Racing',
                'company_name' => 'Neon Racing Collectibles',
                'contact_email' => 'admin@neon.vergeflow.com',
                'contact_phone' => '+1-555-0789',
                'subdomain' => 'neon',
                'theme' => 'neon',
                'primary_color' => '#ff00ff',
                'secondary_color' => '#00ffff',
                'is_active' => true,
            ],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create($clientData);

            // Create admin user for each client
            User::create([
                'name' => $clientData['company_name'] . ' Admin',
                'email' => $clientData['contact_email'],
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'client_id' => $client->id,
            ]);

            // Create some sample users for each client
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'name' => 'Customer ' . $i . ' - ' . $clientData['name'],
                    'email' => 'customer' . $i . '@' . $clientData['subdomain'] . '.vergeflow.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'client_id' => $client->id,
                ]);
            }
        }
    }
}
