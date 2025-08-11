<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveClientTablesFromMain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:client-tables-from-main {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove specific client tables that are still in main database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️ Removing remaining client-specific tables from vergeflow_main database...');

        // Specific tables to remove that I can see in the screenshot
        $tablesToRemove = [
            'coupons',
            'coupon_usages', 
            'orders',
            'order_status_histories',
            'password_resets',  // This should be password_reset_tokens in client DBs
        ];

        // Verify connection to main database
        $currentDb = DB::connection('main')->getDatabaseName();
        $this->info("📍 Connected to database: {$currentDb}");

        // Check which tables actually exist
        $existingTables = collect(DB::connection('main')->select('SHOW TABLES'))->map(function ($table) {
            return array_values((array) $table)[0];
        })->toArray();

        $tablesToActuallyRemove = array_intersect($tablesToRemove, $existingTables);

        if (empty($tablesToActuallyRemove)) {
            $this->info("✅ No client-specific tables found to remove.");
            return;
        }

        $this->info("🗑️ Found these client-specific tables to remove:");
        foreach ($tablesToActuallyRemove as $table) {
            $this->line("  - {$table}");
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm("⚠️ Remove these " . count($tablesToActuallyRemove) . " tables from vergeflow_main?")) {
                $this->info("❌ Operation cancelled.");
                return;
            }
        }

        // Disable foreign key checks
        DB::connection('main')->statement('SET FOREIGN_KEY_CHECKS = 0');

        // Remove tables
        $removed = 0;
        foreach ($tablesToActuallyRemove as $table) {
            try {
                DB::connection('main')->statement("DROP TABLE IF EXISTS `{$table}`");
                $this->info("  ✅ Removed: {$table}");
                $removed++;
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to remove {$table}: " . $e->getMessage());
            }
        }

        // Re-enable foreign key checks
        DB::connection('main')->statement('SET FOREIGN_KEY_CHECKS = 1');

        // Show final tables
        $finalTables = collect(DB::connection('main')->select('SHOW TABLES'))->map(function ($table) {
            return array_values((array) $table)[0];
        })->toArray();

        $this->info("\n📊 Remaining tables in vergeflow_main (" . count($finalTables) . " tables):");
        foreach ($finalTables as $table) {
            $this->line("  ✅ {$table}");
        }

        $this->info("\n🎉 Cleanup completed!");
        $this->info("✅ Removed {$removed} client-specific tables from vergeflow_main");
        $this->info("🏗️ Multi-tenant architecture further optimized!");
    }
}
