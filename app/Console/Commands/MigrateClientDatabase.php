<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Client;

class MigrateClientDatabase extends Command
{
    protected $signature = 'migrate:client {clientId} {--fresh : Drop all tables and re-run all migrations}';
    protected $description = 'Run migrations for a specific client database';

    public function handle()
    {
        $clientId = $this->argument('clientId');
        
        // Get the client
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error("Client with ID {$clientId} not found");
            return 1;
        }
        
        $this->info("Running migrations for client: {$client->name} (Database: {$client->database_name})");
        
        // Set up the database connection
        Config::set('database.connections.client', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $client->database_name,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);
        
        // Run migrations
        $command = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        
        $this->call($command, [
            '--database' => 'client',
            '--force' => true,
        ]);
        
        $this->info("Migrations completed successfully for client: {$client->name}");
        
        return 0;
    }
}
