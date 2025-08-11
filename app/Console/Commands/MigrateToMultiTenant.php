<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MultiTenantService;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateToMultiTenant extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:multi-tenant {--force : Force the migration without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate existing single database to multi-tenant architecture';

    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        parent::__construct();
        $this->multiTenantService = $multiTenantService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Multi-Tenant Database Migration...');
        
        if (!$this->option('force')) {
            if (!$this->confirm('This will restructure your database. Do you want to continue?')) {
                $this->info('Migration cancelled.');
                return;
            }
        }

        try {
            // Step 1: Create main database if it doesn't exist
            $this->createMainDatabase();
            
            // Step 2: Migrate global data to main database
            $this->migrateGlobalData();
            
            // Step 3: Create client databases and migrate client-specific data
            $this->migrateClientData();
            
            // Step 4: Clean up old tables from main database
            $this->cleanupMainDatabase();
            
            $this->info('✅ Multi-tenant migration completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }

    /**
     * Create main database
     */
    private function createMainDatabase()
    {
        $this->info('📊 Creating main database...');
        
        $mainDbName = 'vergeflow_main';
        
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$mainDbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->info("✅ Main database '{$mainDbName}' created/verified");
        } catch (\Exception $e) {
            $this->error("❌ Failed to create main database: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Migrate global data to main database
     */
    private function migrateGlobalData()
    {
        $this->info('🌐 Migrating global data to main database...');
        
        // Tables that should remain in main database
        $globalTables = $this->multiTenantService->getMainDatabaseTables();
        
        foreach ($globalTables as $table) {
            if (Schema::hasTable($table)) {
                $this->migrateTableToMain($table);
            }
        }
    }

    /**
     * Migrate a table to main database
     */
    private function migrateTableToMain(string $table)
    {
        $this->info("  📋 Migrating {$table} to main database...");
        
        try {
            // Get table structure
            $createStatement = DB::select("SHOW CREATE TABLE `{$table}`")[0]->{'Create Table'};
            
            // Create table in main database
            DB::connection('main')->statement($createStatement);
            
            // Copy data (filter out client-specific users if it's users table)
            if ($table === 'users') {
                // Only migrate super admin and admin users to main database
                $users = DB::table($table)
                    ->whereIn('role', ['super_admin', 'admin'])
                    ->get();
                
                foreach ($users as $user) {
                    DB::connection('main')->table($table)->insert((array) $user);
                }
            } else {
                // Copy all data for other global tables
                $data = DB::table($table)->get();
                
                foreach ($data->chunk(100) as $chunk) {
                    $insertData = $chunk->map(function ($item) {
                        return (array) $item;
                    })->toArray();
                    
                    DB::connection('main')->table($table)->insert($insertData);
                }
            }
            
            $this->info("  ✅ {$table} migrated successfully");
            
        } catch (\Exception $e) {
            $this->warn("  ⚠️ Failed to migrate {$table}: " . $e->getMessage());
        }
    }

    /**
     * Migrate client-specific data to client databases
     */
    private function migrateClientData()
    {
        $this->info('🏢 Migrating client-specific data...');
        
        $clients = Client::all();
        
        foreach ($clients as $client) {
            $this->info("  🔄 Processing client: {$client->name} (ID: {$client->id})");
            
            // Create client database
            $databaseCreated = $this->multiTenantService->createClientDatabase($client);
            
            if (!$databaseCreated) {
                $this->error("  ❌ Failed to create database for client {$client->id}");
                continue;
            }
            
            // Migrate client-specific data
            $this->migrateClientSpecificData($client);
        }
    }

    /**
     * Migrate data for a specific client
     */
    private function migrateClientSpecificData(Client $client)
    {
        $clientTables = $this->multiTenantService->getClientDatabaseTables();
        $connectionName = $this->multiTenantService->getClientConnection($client->id);
        
        foreach ($clientTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            
            $this->info("    📋 Migrating {$table} for client {$client->id}...");
            
            try {
                if ($table === 'users') {
                    // Migrate site users for this client
                    $users = DB::table($table)
                        ->where('client_id', $client->id)
                        ->where('role', 'user')
                        ->get();
                } else {
                    // Migrate all data for this client
                    $query = DB::table($table);
                    
                    // Add client_id filter if the table has this column
                    if (Schema::hasColumn($table, 'client_id')) {
                        $query->where('client_id', $client->id);
                    }
                    
                    $users = $query->get();
                }
                
                // Insert data into client database
                foreach ($users->chunk(100) as $chunk) {
                    $insertData = $chunk->map(function ($item) {
                        return (array) $item;
                    })->toArray();
                    
                    if (!empty($insertData)) {
                        DB::connection($connectionName)->table($table)->insert($insertData);
                    }
                }
                
                $this->info("    ✅ {$table} migrated for client {$client->id}");
                
            } catch (\Exception $e) {
                $this->warn("    ⚠️ Failed to migrate {$table} for client {$client->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Clean up main database by removing client-specific tables
     */
    private function cleanupMainDatabase()
    {
        $this->info('🧹 Cleaning up main database...');
        
        if (!$this->confirm('Remove client-specific tables from main database?')) {
            $this->info('Cleanup skipped.');
            return;
        }
        
        $clientTables = $this->multiTenantService->getClientDatabaseTables();
        
        foreach ($clientTables as $table) {
            if (Schema::connection('main')->hasTable($table)) {
                try {
                    Schema::connection('main')->dropIfExists($table);
                    $this->info("  🗑️ Removed {$table} from main database");
                } catch (\Exception $e) {
                    $this->warn("  ⚠️ Failed to remove {$table}: " . $e->getMessage());
                }
            }
        }
    }
}
