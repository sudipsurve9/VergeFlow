<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupMainDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:main-database {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unnecessary client-specific tables from main database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Cleaning up main database - removing client-specific tables...');

        // Switch to main database connection
        DB::purge('main');
        config(['database.default' => 'main']);
        
        // Verify we're connected to vergeflow_main database
        $currentDb = DB::connection('main')->getDatabaseName();
        $this->info("📍 Connected to database: {$currentDb}");

        // Tables that should STAY in main database (global data)
        $keepTables = [
            'migrations',
            'users',                    // Super admin and admin users
            'clients',                  // Client records
            'settings',                 // Global settings
            'banners',                  // Global banners
            'pages',                    // Global pages
            'password_reset_tokens',    // Global auth tokens
            'failed_jobs',              // System jobs
            'personal_access_tokens',   // API tokens
        ];

        // Tables that should be REMOVED from main database (client-specific data)
        $removeTables = [
            'products',
            'categories', 
            'orders',
            'order_items',
            'cart_items',
            'addresses',
            'product_reviews',
            'recently_viewed',
            'coupons',
            'customers',
            'wishlists',
            'notifications',
            'payments',
            'shipping_methods',
            'tax_rates',
            'inventory',
            'product_images',
            'product_attributes',
            'customer_addresses',
            'order_status_history',
        ];

        // Get all tables in main database (vergeflow_main)
        $allTables = collect(DB::connection('main')->select('SHOW TABLES'))->map(function ($table) {
            return array_values((array) $table)[0];
        })->toArray();

        $this->info("📊 Found " . count($allTables) . " tables in main database");
        
        // Show current tables
        $this->info("\n📋 Current tables in main database:");
        foreach ($allTables as $table) {
            $status = in_array($table, $keepTables) ? '✅ KEEP' : (in_array($table, $removeTables) ? '🗑️ REMOVE' : '❓ UNKNOWN');
            $this->line("  - {$table} ({$status})");
        }

        // Find tables to remove that actually exist
        $tablesToRemove = array_intersect($removeTables, $allTables);
        
        if (empty($tablesToRemove)) {
            $this->info("\n✅ No client-specific tables found in main database. Already clean!");
            return;
        }

        $this->info("\n🗑️ Tables to be removed from main database:");
        foreach ($tablesToRemove as $table) {
            $this->line("  - {$table}");
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm("\n⚠️ Are you sure you want to remove these " . count($tablesToRemove) . " tables from the main database?")) {
                $this->info("❌ Operation cancelled.");
                return;
            }
        }

        // Remove tables
        $this->info("\n🗑️ Removing client-specific tables from main database...");
        
        foreach ($tablesToRemove as $table) {
            try {
                Schema::connection('main')->dropIfExists($table);
                $this->info("  ✅ Removed table: {$table}");
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to remove {$table}: " . $e->getMessage());
            }
        }

        // Show final status
        $finalTables = collect(DB::connection('main')->select('SHOW TABLES'))->map(function ($table) {
            return array_values((array) $table)[0];
        })->toArray();

        $this->info("\n📊 Final main database tables (" . count($finalTables) . " remaining):");
        foreach ($finalTables as $table) {
            $this->line("  ✅ {$table}");
        }

        $this->info("\n🎉 Main database cleanup completed!");
        $this->info("✅ Removed " . count($tablesToRemove) . " client-specific tables");
        $this->info("✅ Kept " . count($finalTables) . " global tables");
        $this->info("🏗️ Multi-tenant architecture optimized!");
    }
}
