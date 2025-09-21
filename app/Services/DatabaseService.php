<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use Exception;

class DatabaseService
{
    /**
     * Create a new database for a client
     */
    public function createClientDatabase(Client $client): bool
    {
        try {
            // If client already has a database, skip creation
            if ($client->database_name) {
                Log::info("Client {$client->name} already has database: {$client->database_name}");
                return true;
            }
            
            $databaseName = $this->generateDatabaseName($client);
            
            // Create the database
            $this->createDatabase($databaseName);
            
            // Run migrations on the new database
            $this->runMigrationsOnDatabase($databaseName);
            
            // Update client with database name
            $client->update(['database_name' => $databaseName]);
            
            Log::info("Successfully created database {$databaseName} for client {$client->name}");
            return true;
        } catch (Exception $e) {
            Log::error('Failed to create client database: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate database name for client
     */
    private function generateDatabaseName(Client $client): string
    {
        $baseName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $client->name));
        return 'vergeflow_' . $baseName . '_' . $client->id;
    }
    
    /**
     * Create database
     */
    private function createDatabase(string $databaseName): void
    {
        // Prefer MAIN_DB_* or CLIENT_DB_* credentials, fallback to mysql connection
        $host = env('CLIENT_DB_HOST', env('MAIN_DB_HOST', config('database.connections.mysql.host')));
        $port = env('CLIENT_DB_PORT', env('MAIN_DB_PORT', config('database.connections.mysql.port')));
        $user = env('CLIENT_DB_USERNAME', env('MAIN_DB_USERNAME', config('database.connections.mysql.username')));
        $pass = env('CLIENT_DB_PASSWORD', env('MAIN_DB_PASSWORD', config('database.connections.mysql.password')));
        
        try {
            // Use direct PDO connection for database creation
            $pdo = new \PDO(
                "mysql:host={$host};port={$port}",
                $user,
                $pass
            );
            
            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            Log::info("Database created successfully: {$databaseName}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create database {$databaseName}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Run migrations on specific database
     */
    private function runMigrationsOnDatabase(string $databaseName): void
    {
        // Build a base connection preferring MAIN_DB_* / CLIENT_DB_* envs
        $base = config('database.connections.mysql');
        $base['host'] = env('CLIENT_DB_HOST', env('MAIN_DB_HOST', $base['host']));
        $base['port'] = env('CLIENT_DB_PORT', env('MAIN_DB_PORT', $base['port']));
        $base['username'] = env('CLIENT_DB_USERNAME', env('MAIN_DB_USERNAME', $base['username']));
        $base['password'] = env('CLIENT_DB_PASSWORD', env('MAIN_DB_PASSWORD', $base['password']));
        
        // Create temporary connection for the new database
        $clientConnection = array_merge($base, ['database' => $databaseName]);
        
        DB::purge('client_temp');
        config(['database.connections.client_temp' => $clientConnection]);
        
        try {
            // Run migrations
            Artisan::call('migrate', [
                '--database' => 'client_temp',
                '--force' => true,
                '--quiet' => true
            ]);
            
            Log::info("Migrations completed for database: {$databaseName}");
        } catch (Exception $e) {
            Log::error("Failed to run migrations on database {$databaseName}: " . $e->getMessage());
            // Don't throw the exception, just log it
        } finally {
            DB::purge('client_temp');
        }
    }
    
    /**
     * Get client database connection
     */
    public function getClientConnection(Client $client): string
    {
        if (!$client->database_name) {
            throw new Exception('Client database not found');
        }
        
        $connectionName = 'client_' . $client->id;
        
        if (!config("database.connections.{$connectionName}")) {
            $this->createClientConnection($client, $connectionName);
        }
        
        return $connectionName;
    }
    
    /**
     * Create client database connection
     */
    public function createClientConnection(Client $client, string $connectionName): void
    {
        // Build a base connection preferring MAIN_DB_* / CLIENT_DB_* envs
        $base = config('database.connections.mysql');
        $base['host'] = env('CLIENT_DB_HOST', env('MAIN_DB_HOST', $base['host']));
        $base['port'] = env('CLIENT_DB_PORT', env('MAIN_DB_PORT', $base['port']));
        $base['username'] = env('CLIENT_DB_USERNAME', env('MAIN_DB_USERNAME', $base['username']));
        $base['password'] = env('CLIENT_DB_PASSWORD', env('MAIN_DB_PASSWORD', $base['password']));
        
        $clientConnection = array_merge($base, [
            'database' => $client->database_name
        ]);
        
        config(["database.connections.{$connectionName}" => $clientConnection]);
    }
    
    /**
     * Migrate existing data to separate databases
     */
    public function migrateExistingData(): void
    {
        $clients = Client::all();
        
        foreach ($clients as $client) {
            if (!$client->database_name) {
                $this->createClientDatabase($client);
            }
        }
    }
    
    /**
     * Create Vault64 as first client with all existing data
     */
    public function createVault64Client(): Client
    {
        // Create Vault64 client
        $vault64Client = Client::create([
            'name' => 'Vault64',
            'company_name' => 'Vault64 Original Store',
            'contact_email' => 'admin@vault64.vergeflow.com',
            'contact_phone' => '+1-555-0000',
            'subdomain' => 'vault64',
            'theme' => 'modern',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'is_active' => true,
            'database_name' => 'vergeflow_vault64_1'
        ]);
        
        // Create database for Vault64
        $this->createClientDatabase($vault64Client);
        
        return $vault64Client;
    }
} 