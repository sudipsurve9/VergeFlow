<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SetupClientDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:setup-client-db 
                            {client-id : The ID of the client to setup}
                            {--force : Force setup even if database exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a client database with all tables and migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientId = $this->argument('client-id');
        $force = $this->option('force');
        
        $client = Client::find($clientId);
        if (!$client) {
            $this->error("Client with ID {$clientId} not found.");
            return 1;
        }
        
        $this->info("Setting up database for client: {$client->name}");
        
        if (!$client->database_name) {
            $this->error("Client {$client->name} has no database name.");
            return 1;
        }
        
        $databaseService = new DatabaseService();
        
        // Step 1: Create database if it doesn't exist
        $this->info("1. Creating database: {$client->database_name}");
        $success = $databaseService->createClientDatabase($client);
        
        if (!$success) {
            $this->error("Failed to create database for client {$client->name}");
            return 1;
        }
        
        $this->info("   ✓ Database created successfully");
        
        // Step 2: Get client connection
        $this->info("2. Setting up database connection");
        $connection = $databaseService->getClientConnection($client);
        
        // Step 3: Run migrations on the client database
        $this->info("3. Running migrations on client database");
        
        try {
            // Temporarily set the database name for the client connection
            $config = config('database.connections.mysql');
            $config['database'] = $client->database_name;
            
            // Create a temporary connection configuration
            $tempConnectionName = 'temp_client_' . $client->id;
            Config::set("database.connections.{$tempConnectionName}", $config);
            
            // Run migrations using the temporary connection
            $this->call('migrate', [
                '--database' => $tempConnectionName,
                '--force' => true
            ]);
            
            $this->info("   ✓ Migrations completed successfully");
            
            // Clean up temporary connection
            Config::set("database.connections.{$tempConnectionName}", null);
            
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
            return 1;
        }
        
        // Step 4: Verify tables were created
        $this->info("4. Verifying database setup");
        
        try {
            $pdo = new \PDO(
                "mysql:host={$config['host']};port={$config['port']};dbname={$client->database_name}",
                $config['username'],
                $config['password']
            );
            
            $tables = ['users', 'products', 'categories', 'orders', 'migrations'];
            $createdTables = [];
            
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
                if ($stmt->rowCount() > 0) {
                    $createdTables[] = $table;
                }
            }
            
            $this->info("   ✓ Created tables: " . implode(', ', $createdTables));
            
        } catch (\Exception $e) {
            $this->error("Verification failed: " . $e->getMessage());
            return 1;
        }
        
        $this->info("=== Client database setup completed successfully ===");
        $this->info("Client: {$client->name}");
        $this->info("Database: {$client->database_name}");
        $this->info("Connection: {$connection}");
        
        return 0;
    }
} 