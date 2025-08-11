<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;

class FixClientDatabaseColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:client-database-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing columns in client databases';

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
        $this->info('Fixing client database columns...');

        // Ensure we're using main database
        $this->multiTenantService->switchToMainDatabase();

        $clients = Client::all();
        
        if ($clients->isEmpty()) {
            $this->warn('No clients found.');
            return;
        }

        $this->info("Found {$clients->count()} clients");

        foreach ($clients as $client) {
            $this->info("\nFixing client: {$client->name} (ID: {$client->id})");
            
            try {
                // Switch to client database
                $this->multiTenantService->switchToClientDatabase($client->id);
                
                // Add missing columns to products table
                $this->info("  Adding missing columns to products table...");
                
                try {
                    DB::statement('ALTER TABLE products ADD COLUMN is_featured TINYINT(1) DEFAULT 0');
                    $this->info("  ✓ Added is_featured column");
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        $this->info("  ✓ is_featured column already exists");
                    } else {
                        $this->error("  ❌ Error adding is_featured: " . $e->getMessage());
                    }
                }
                
                try {
                    DB::statement('ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1');
                    $this->info("  ✓ Added is_active column");
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        $this->info("  ✓ is_active column already exists");
                    } else {
                        $this->error("  ❌ Error adding is_active: " . $e->getMessage());
                    }
                }
                
                // Update existing products
                $this->info("  Updating existing products...");
                DB::statement('UPDATE products SET is_active = 1 WHERE is_active IS NULL');
                DB::statement('UPDATE products SET is_featured = featured WHERE is_featured IS NULL');
                
                $this->info("✓ Client {$client->name} database columns fixed");
                
            } catch (\Exception $e) {
                $this->error("❌ Error fixing database for {$client->name}: " . $e->getMessage());
            }
        }

        // Switch back to main database
        $this->multiTenantService->switchToMainDatabase();
        
        $this->info("\n✅ Client database column fixes completed!");
    }
}
