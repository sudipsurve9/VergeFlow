<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\Schema;

class CheckTableStructure extends Command
{
    protected $signature = 'check:table-structure {table} {client_id=1}';
    protected $description = 'Check table structure for a client database';

    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        parent::__construct();
        $this->multiTenantService = $multiTenantService;
    }

    public function handle()
    {
        $table = $this->argument('table');
        $clientId = $this->argument('client_id');
        
        $this->info("🔍 Checking {$table} table structure for client {$clientId}");
        
        try {
            $this->multiTenantService->switchToClientDatabase($clientId);
            
            if (!Schema::hasTable($table)) {
                $this->error("❌ Table {$table} does not exist!");
                return 1;
            }
            
            $columns = Schema::getColumnListing($table);
            
            $this->info("✅ Table {$table} columns:");
            foreach ($columns as $column) {
                $this->line("   - {$column}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        $this->multiTenantService->switchToMainDatabase();
        return 0;
    }
}
