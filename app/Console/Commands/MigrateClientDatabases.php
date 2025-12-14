<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateClientDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:migrate 
                            {--client= : Specific client ID to migrate}
                            {--fresh : Run fresh migrations (drops all tables)}
                            {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations on all client databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Client Database Migration Tool ===');

        $clientId = $this->option('client');
        $fresh = $this->option('fresh');
        $force = $this->option('force');

        if (!$force && $this->laravel->environment('production')) {
            if (!$this->confirm('⚠️  Are you sure you want to run migrations in PRODUCTION?', false)) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        $databaseService = new DatabaseService();

        // Get clients to migrate
        if ($clientId) {
            $clients = Client::on('main')->where('id', $clientId)->get();
            if ($clients->isEmpty()) {
                $this->error("Client with ID {$clientId} not found.");
                return 1;
            }
        } else {
            $clients = Client::on('main')
                ->whereNotNull('database_name')
                ->get();
        }

        if ($clients->isEmpty()) {
            $this->error('No clients found to migrate.');
            return 1;
        }

        $this->info("Found {$clients->count()} client(s) to migrate.\n");

        $successCount = 0;
        $failCount = 0;

        foreach ($clients as $client) {
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Migrating: {$client->name} (ID: {$client->id})");
            $this->info("Database: {$client->database_name}");

            try {
                // Get or create connection
                $connectionName = $databaseService->getClientConnection($client);

                // Verify connection
                DB::connection($connectionName)->getPdo();
                $this->info("✓ Connection: {$connectionName}");

                // Check migration status first
                $this->info("Checking migration status...");
                try {
                    $migrationsTableExists = DB::connection($connectionName)
                        ->getSchemaBuilder()
                        ->hasTable('migrations');
                    
                    if ($migrationsTableExists) {
                        $runMigrations = DB::connection($connectionName)
                            ->table('migrations')
                            ->pluck('migration')
                            ->toArray();
                        $this->info("Found " . count($runMigrations) . " previously run migrations");
                    } else {
                        $this->warn("Migrations table doesn't exist - will be created");
                    }
                } catch (\Exception $e) {
                    $this->warn("Could not check migration status: " . $e->getMessage());
                }
                
                // Set the default connection temporarily to avoid main-db migrations
                $originalDefault = config('database.default');
                config(['database.default' => $connectionName]);
                
                try {
                    // Run migrations
                    if ($fresh) {
                        $this->warn("⚠️  Running FRESH migrations (will DROP all tables!)");
                        if (!$this->confirm('Continue?', false)) {
                            $this->warn("Skipped {$client->name}");
                            continue;
                        }
                        
                        Artisan::call('migrate:fresh', [
                            '--database' => $connectionName,
                            '--force' => true,
                            '--path' => 'database/migrations',
                        ]);
                    } else {
                        // Use migrate:status to see what needs to run
                        Artisan::call('migrate:status', [
                            '--database' => $connectionName,
                        ]);
                        $statusOutput = Artisan::output();
                        if (!empty(trim($statusOutput))) {
                            $this->line($statusOutput);
                        }
                        
                        // Run migrations with --pretend first to see what would happen
                        Artisan::call('migrate', [
                            '--database' => $connectionName,
                            '--force' => true,
                        ]);
                    }
                } catch (\Exception $e) {
                    // If migration fails due to existing tables/columns/enum issues, try to sync migration status
                    $errorMessage = $e->getMessage();
                    $shouldSync = strpos($errorMessage, 'already exists') !== false || 
                                  strpos($errorMessage, 'Duplicate column') !== false ||
                                  strpos($errorMessage, 'Duplicate key') !== false ||
                                  strpos($errorMessage, 'Data truncated') !== false ||
                                  strpos($errorMessage, 'truncated for column') !== false;
                    
                    if ($shouldSync) {
                        $this->warn("⚠️  Migration conflict detected. Attempting to sync migration status...");
                        $this->syncMigrationStatus($connectionName, $client);
                        
                        // Try again
                        try {
                            Artisan::call('migrate', [
                                '--database' => $connectionName,
                                '--force' => true,
                            ]);
                            $this->info("✓ Migrations completed after sync");
                        } catch (\Exception $e2) {
                            // If it still fails, sync again and try one more time
                            $errorMessage2 = $e2->getMessage();
                            $shouldSync2 = strpos($errorMessage2, 'already exists') !== false || 
                                          strpos($errorMessage2, 'Duplicate column') !== false ||
                                          strpos($errorMessage2, 'Data truncated') !== false;
                            
                            if ($shouldSync2) {
                                $this->warn("⚠️  Additional sync needed...");
                                $this->syncMigrationStatus($connectionName, $client);
                                
                                // Final attempt
                                Artisan::call('migrate', [
                                    '--database' => $connectionName,
                                    '--force' => true,
                                ]);
                                $this->info("✓ Migrations completed after additional sync");
                            } else {
                                throw $e2; // Re-throw if still fails with different error
                            }
                        }
                    } else {
                        throw $e; // Re-throw other errors
                    }
                } finally {
                    // Restore original default connection
                    config(['database.default' => $originalDefault]);
                }

                $output = trim(Artisan::output());
                if (!empty($output)) {
                    $this->line($output);
                }

                $this->info("✓ Successfully migrated {$client->name}\n");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed: " . $e->getMessage());
                $this->error("Error type: " . get_class($e));
                
                // Always show trace in production for debugging
                if ($this->laravel->environment('production') || $this->option('verbose')) {
                    $this->error("File: " . $e->getFile() . ":" . $e->getLine());
                    $this->error("Trace: " . $e->getTraceAsString());
                }
                
                $failCount++;
                $this->newLine();
            }
        }

        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("=== Migration Summary ===");
        $this->info("✓ Successful: {$successCount}");
        if ($failCount > 0) {
            $this->error("✗ Failed: {$failCount}");
        }
        $this->info("Total: " . ($successCount + $failCount));

        return $failCount > 0 ? 1 : 0;
    }
    
    /**
     * Sync migration status for existing tables
     */
    private function syncMigrationStatus(string $connectionName, Client $client): void
    {
        try {
            $this->info("  Syncing migration status for existing tables...");
            
            // Get all migration files and extract table names
            $migrationPath = database_path('migrations');
            $migrationFiles = glob($migrationPath . '/*.php');
            
            // Get existing tables
            $existingTables = array_map('strtolower', DB::connection($connectionName)
                ->getSchemaBuilder()
                ->getTableListing());
            
            // Map common tables to their migrations (extracted from filenames)
            $tableMigrationMap = [
                'users' => '2014_10_12_000000_create_users_table',
                'password_resets' => '2014_10_12_100000_create_password_resets_table',
                'failed_jobs' => '2019_08_19_000000_create_failed_jobs_table',
                'personal_access_tokens' => '2019_12_14_000001_create_personal_access_tokens_table',
                'categories' => '2025_06_28_143447_create_categories_table',
                'products' => '2025_06_28_143525_create_products_table',
                'orders' => '2025_06_28_143643_create_orders_table',
                'order_items' => '2025_06_28_143658_create_order_items_table',
                'cart_items' => '2025_06_28_143719_create_cart_items_table',
                'api_integrations' => '2025_06_29_000000_create_api_integrations_table',
                'addresses' => '2025_06_30_063019_create_addresses_table',
                'customers' => '2025_06_30_062114_create_customers_table',
                'api_logs' => '2025_06_30_060652_create_api_logs_table',
                'api_types' => '2025_07_01_000002_create_api_types_table',
                'order_status_histories' => '2025_07_01_000003_create_order_status_histories_table',
                'wishlists' => '2025_06_30_064722_create_wishlists_table',
                'payments' => '2025_06_30_064823_create_payments_table',
                'product_reviews' => '2025_06_30_064509_create_product_reviews_table',
                'pages' => '2025_06_30_064953_create_pages_table',
                'banners' => '2025_06_30_065104_create_banners_table',
                'settings' => '2025_06_30_065236_create_settings_table',
                'notifications' => '2025_06_30_065338_create_notifications_table',
                'coupons' => '2025_06_30_064325_create_coupons_table',
                'coupon_usages' => '2025_06_30_064408_create_coupon_usages_table',
                'recently_viewed' => '2025_07_27_164000_create_recently_viewed_table',
            ];
            
            // Map column-level migrations (table => column => migration)
            $columnMigrationMap = [
                'products' => [
                    'featured' => '2025_06_29_061431_add_featured_to_products_table',
                    'is_featured' => '2025_06_29_061431_add_featured_to_products_table', // Also check is_featured
                    'client_id' => '2025_07_05_074952_add_client_id_to_products_table',
                ],
                'users' => [
                    'client_id' => '2025_07_05_074637_add_client_id_to_users_table',
                ],
                'categories' => [
                    'client_id' => '2025_07_05_075702_add_client_id_to_categories_table',
                    'is_active' => '2025_08_17_145526_add_is_active_to_categories_table',
                ],
                'orders' => [
                    'client_id' => '2025_07_05_075737_add_client_id_to_orders_table',
                    'delivery_status' => '2025_07_02_104207_add_delivery_status_to_orders_table',
                    'tracking_number' => '2025_07_02_104658_add_tracking_and_courier_fields_to_orders_table',
                ],
                'customers' => [
                    'client_id' => '2025_07_05_075814_add_client_id_to_customers_table',
                ],
                'coupons' => [
                    'client_id' => '2025_07_07_000001_add_client_id_to_coupons_table',
                ],
                'api_integrations' => [
                    'updated_by' => '2025_06_30_053453_add_updated_by_to_api_integrations_table',
                ],
            ];
            
            // Map enum/type modification migrations (table => check_function => migration)
            $enumMigrationMap = [
                'users' => [
                    'migration' => '2025_07_05_080000_update_user_role_enum',
                    'check' => function($schema, $table) use ($connectionName) {
                        // Check if role enum includes 'super_admin'
                        try {
                            $columnInfo = DB::connection($connectionName)
                                ->select("SHOW COLUMNS FROM `{$table}` WHERE Field = 'role'");
                            if (!empty($columnInfo)) {
                                $type = $columnInfo[0]->Type ?? '';
                                return strpos($type, 'super_admin') !== false;
                            }
                        } catch (\Exception $e) {
                            // If check fails, assume migration is needed
                        }
                        return false;
                    }
                ],
            ];
            
            // Ensure migrations table exists
            if (!DB::connection($connectionName)->getSchemaBuilder()->hasTable('migrations')) {
                DB::connection($connectionName)->statement("
                    CREATE TABLE IF NOT EXISTS `migrations` (
                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `migration` varchar(255) NOT NULL,
                        `batch` int(11) NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
                $this->info("  ✓ Created migrations table");
            }
            
            // Get current batch number
            $maxBatch = DB::connection($connectionName)
                ->table('migrations')
                ->max('batch') ?? 0;
            $nextBatch = $maxBatch + 1;
            
            $syncedCount = 0;
            
            // Mark migrations as run based on existing tables
            foreach ($tableMigrationMap as $table => $migration) {
                $tableLower = strtolower($table);
                if (in_array($tableLower, $existingTables)) {
                    $exists = DB::connection($connectionName)
                        ->table('migrations')
                        ->where('migration', $migration)
                        ->exists();
                    
                    if (!$exists) {
                        DB::connection($connectionName)
                            ->table('migrations')
                            ->insert([
                                'migration' => $migration,
                                'batch' => $nextBatch
                            ]);
                        $syncedCount++;
                    }
                }
            }
            
            // Mark column-level migrations as run based on existing columns
            foreach ($columnMigrationMap as $table => $columns) {
                $tableLower = strtolower($table);
                if (in_array($tableLower, $existingTables)) {
                    $schema = DB::connection($connectionName)->getSchemaBuilder();
                    
                    foreach ($columns as $column => $migration) {
                        // Check if column exists (either the specified column or its renamed version)
                        $columnExists = false;
                        if ($column === 'featured' || $column === 'is_featured') {
                            // For featured column, check both 'featured' and 'is_featured'
                            $columnExists = $schema->hasColumn($table, 'featured') || 
                                          $schema->hasColumn($table, 'is_featured');
                        } else {
                            $columnExists = $schema->hasColumn($table, $column);
                        }
                        
                        if ($columnExists) {
                            $exists = DB::connection($connectionName)
                                ->table('migrations')
                                ->where('migration', $migration)
                                ->exists();
                            
                            if (!$exists) {
                                DB::connection($connectionName)
                                    ->table('migrations')
                                    ->insert([
                                        'migration' => $migration,
                                        'batch' => $nextBatch
                                    ]);
                                $syncedCount++;
                            }
                        }
                    }
                }
            }
            
            // Mark enum/type modification migrations as run if enum already has correct values
            foreach ($enumMigrationMap as $table => $enumInfo) {
                $tableLower = strtolower($table);
                if (in_array($tableLower, $existingTables)) {
                    $migration = $enumInfo['migration'];
                    $check = $enumInfo['check'];
                    
                    try {
                        if ($check(DB::connection($connectionName)->getSchemaBuilder(), $table)) {
                            $exists = DB::connection($connectionName)
                                ->table('migrations')
                                ->where('migration', $migration)
                                ->exists();
                            
                            if (!$exists) {
                                DB::connection($connectionName)
                                    ->table('migrations')
                                    ->insert([
                                        'migration' => $migration,
                                        'batch' => $nextBatch
                                    ]);
                                $syncedCount++;
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip if check fails
                    }
                }
            }
            
            if ($syncedCount > 0) {
                $this->info("  ✓ Synced {$syncedCount} migration(s) based on existing tables");
            } else {
                $this->info("  ✓ No migrations needed syncing");
            }
            
        } catch (\Exception $e) {
            $this->warn("Could not sync migration status: " . $e->getMessage());
        }
    }
}

