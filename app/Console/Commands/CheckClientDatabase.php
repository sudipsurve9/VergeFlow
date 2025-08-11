<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckClientDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:client-database {client_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if client database exists and show its structure and data';

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
        $clientId = $this->argument('client_id');
        
        $this->info("🔍 Checking Database for Client ID: {$clientId}");
        $this->newLine();

        // Get client info from main database
        $this->multiTenantService->switchToMainDatabase();
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error("❌ Client with ID {$clientId} not found!");
            return 1;
        }

        $this->info("🏢 Client: {$client->name} ({$client->company_name})");
        $this->newLine();

        try {
            // Switch to client database
            $this->multiTenantService->switchToClientDatabase($clientId);
            $dbName = DB::getDatabaseName();
            
            $this->info("✅ Database Connected: {$dbName}");
            $this->newLine();

            // Check tables
            $tables = DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);

            $this->info("📊 Database Tables (" . count($tableNames) . " tables):");
            $this->line('----------------------------------------');

            foreach ($tableNames as $tableName) {
                try {
                    $count = DB::table($tableName)->count();
                    $this->line("📋 {$tableName}: {$count} records");
                } catch (\Exception $e) {
                    $this->line("📋 {$tableName}: Error counting - " . $e->getMessage());
                }
            }

            $this->line('----------------------------------------');
            $this->newLine();

            // Show detailed data for key tables
            $keyTables = ['products', 'categories', 'orders', 'customers', 'users'];
            
            foreach ($keyTables as $table) {
                if (in_array($table, $tableNames)) {
                    $this->showTableDetails($table);
                }
            }

            $this->info("✅ Client database is fully operational!");

        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: " . $e->getMessage());
            $this->warn("💡 You may need to run: php artisan setup:client-databases");
            return 1;
        }

        // Switch back to main database
        $this->multiTenantService->switchToMainDatabase();
        
        return 0;
    }

    private function showTableDetails($tableName)
    {
        try {
            $count = DB::table($tableName)->count();
            $this->info("📋 {$tableName} Table Details:");
            $this->line("   Total Records: {$count}");
            
            if ($count > 0) {
                // Show sample data
                $sample = DB::table($tableName)->limit(3)->get();
                
                if ($tableName === 'products') {
                    foreach ($sample as $item) {
                        $this->line("   - {$item->name} (₹{$item->price}) - Stock: {$item->stock_quantity}");
                    }
                } elseif ($tableName === 'categories') {
                    foreach ($sample as $item) {
                        $this->line("   - {$item->name}");
                    }
                } elseif ($tableName === 'orders') {
                    foreach ($sample as $item) {
                        $this->line("   - Order #{$item->id} - ₹{$item->total_amount} ({$item->status})");
                    }
                } elseif ($tableName === 'customers') {
                    foreach ($sample as $item) {
                        $this->line("   - {$item->name} ({$item->email})");
                    }
                } elseif ($tableName === 'users') {
                    foreach ($sample as $item) {
                        $this->line("   - {$item->name} ({$item->email}) - Role: {$item->role}");
                    }
                }
            } else {
                $this->warn("   No data found in {$tableName}");
            }
            
            $this->newLine();
            
        } catch (\Exception $e) {
            $this->error("   Error reading {$tableName}: " . $e->getMessage());
        }
    }
}
