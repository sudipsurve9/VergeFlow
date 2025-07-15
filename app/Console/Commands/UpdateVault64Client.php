<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\DB;

class UpdateVault64Client extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:update-vault64 
                            {--email=admin@vault64.in : Email for Vault64 admin}
                            {--name=Vault64 : Client name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Vault64 client with correct email and admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $name = $this->option('name');
        
        $this->info("Updating Vault64 client with email: {$email}");
        
        // Step 1: Find or create Vault64 client
        $client = Client::where('name', $name)->first();
        
        if (!$client) {
            $this->error("Vault64 client not found. Please run vergeflow:migrate-multi-db first.");
            return 1;
        }
        
        $this->info("Found Vault64 client with ID: {$client->id}");
        
        // Step 2: Update client information
        $client->update([
            'name' => $name,
            'email' => $email,
            'domain' => 'vault64.in',
            'status' => 'active'
        ]);
        
        $this->info("✓ Client information updated");
        
        // Step 3: Update or create admin user in main database
        $adminUser = User::where('email', $email)->first();
        
        if (!$adminUser) {
            // Create new admin user
            $adminUser = User::create([
                'name' => 'Vault64 Admin',
                'email' => $email,
                'password' => bcrypt('password'), // Default password
                'role' => 'admin',
                'client_id' => $client->id,
                'is_active' => true
            ]);
            $this->info("✓ Created new admin user: {$email}");
        } else {
            // Update existing user
            $adminUser->update([
                'name' => 'Vault64 Admin',
                'client_id' => $client->id,
                'role' => 'admin',
                'is_active' => true
            ]);
            $this->info("✓ Updated existing admin user: {$email}");
        }
        
        // Step 4: Update admin user in client database
        $databaseService = new DatabaseService();
        $clientConnection = $databaseService->getClientConnection($client);
        
        try {
            // Check if user exists in client database
            $clientUser = DB::connection($clientConnection)
                ->table('users')
                ->where('email', $email)
                ->first();
            
            if ($clientUser) {
                // Update existing user
                DB::connection($clientConnection)
                    ->table('users')
                    ->where('email', $email)
                    ->update([
                        'name' => 'Vault64 Admin',
                        'role' => 'admin',
                        'client_id' => null, // Set to null in client database
                        'is_active' => true,
                        'updated_at' => now()
                    ]);
                $this->info("✓ Updated admin user in client database");
            } else {
                // Create new user in client database
                DB::connection($clientConnection)
                    ->table('users')
                    ->insert([
                        'name' => 'Vault64 Admin',
                        'email' => $email,
                        'password' => bcrypt('password'),
                        'role' => 'admin',
                        'client_id' => null, // Set to null in client database
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                $this->info("✓ Created admin user in client database");
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to update client database: " . $e->getMessage());
            return 1;
        }
        
        // Step 5: Verify the setup
        $this->info("\n=== Verification ===");
        $this->info("Client: {$client->name} (ID: {$client->id})");
        $this->info("Email: {$email}");
        $this->info("Database: {$client->database_name}");
        $this->info("Connection: {$clientConnection}");
        
        // Check user count in client database
        $userCount = DB::connection($clientConnection)->table('users')->count();
        $this->info("Users in client database: {$userCount}");
        
        $this->info("\n=== Vault64 Client Update Complete ===");
        $this->info("Admin login: {$email}");
        $this->info("Default password: password");
        $this->warn("Please change the default password after first login!");
        
        return 0;
    }
} 