<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseService;
use App\Models\Client;

class FixClientDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:fix-client-databases {--client-id= : Fix specific client by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix clients that don\'t have databases created';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databaseService = new DatabaseService();
        
        if ($clientId = $this->option('client-id')) {
            $client = Client::find($clientId);
            if (!$client) {
                $this->error("Client with ID {$clientId} not found.");
                return 1;
            }
            $this->fixClient($client, $databaseService);
        } else {
            $clients = Client::all();
            $this->info("Checking {$clients->count()} clients...");
            
            foreach ($clients as $client) {
                $this->fixClient($client, $databaseService);
            }
        }
        
        $this->info('Client database check completed!');
    }
    
    private function fixClient(Client $client, DatabaseService $databaseService)
    {
        $this->info("Checking client: {$client->name} (ID: {$client->id})");
        
        if (!$client->database_name) {
            $this->warn("  - No database name found, creating database...");
            $success = $databaseService->createClientDatabase($client);
            
            if ($success) {
                $this->info("  ✓ Database created: {$client->database_name}");
            } else {
                $this->error("  ✗ Failed to create database for client {$client->name}");
            }
        } else {
            $this->info("  ✓ Database exists: {$client->database_name}");
            
            // Test if database is accessible
            try {
                $connection = $databaseService->getClientConnection($client);
                $this->info("  ✓ Database connection working: {$connection}");
            } catch (\Exception $e) {
                $this->warn("  ⚠ Database connection failed: " . $e->getMessage());
                $this->warn("  Attempting to recreate database...");
                
                $success = $databaseService->createClientDatabase($client);
                if ($success) {
                    $this->info("  ✓ Database recreated successfully");
                } else {
                    $this->error("  ✗ Failed to recreate database");
                }
            }
        }
    }
} 