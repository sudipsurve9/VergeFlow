<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\Client;

class MultiTenantService
{
    /**
     * Create a new client database and run migrations
     */
    public function createClientDatabase(Client $client): bool
    {
        try {
            // Prefer existing database_name on client; otherwise generate with optional prefix
            if (!empty($client->database_name)) {
                $databaseName = $client->database_name;
            } else {
                $prefix = env('DB_NAME_PREFIX', '');
                $prefix = $prefix ? rtrim($prefix, '_') . '_' : '';
                $databaseName = $prefix . "vergeflow_client_{$client->id}";
            }
            
            // Create the database
            DB::connection('main')->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Update client record with database name (store what we actually created/used)
            if ($client->database_name !== $databaseName) {
                $client->update(['database_name' => $databaseName]);
            }
            
            // Set up dynamic connection for this client
            $this->setClientDatabaseConnection($client->id, $databaseName);
            
            // Run client-specific migrations
            $this->runClientMigrations($client->id);
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to create client database for client {$client->id}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set up dynamic database connection for a client
     */
    public function setClientDatabaseConnection(int $clientId, string $databaseName = null): void
    {
        // Prefer the stored database_name from main DB if available
        if (!$databaseName) {
            $client = Client::on('main')->find($clientId);
            if ($client && !empty($client->database_name)) {
                $databaseName = $client->database_name;
            } else {
                // Fallback to legacy naming if not set (backward compatibility)
                $databaseName = "vergeflow_client_{$clientId}";
            }
        }
        
        $connectionName = "client_{$clientId}";
        
        // Prefer MAIN_DB_* credentials for tenant databases (most robust in production),
        // fallback to CLIENT_DB_* if provided, else to generic DB_* as last resort.
        Config::set("database.connections.{$connectionName}", [
            'driver' => 'mysql',
            'host' => env('CLIENT_DB_HOST', env('MAIN_DB_HOST', env('DB_HOST', '127.0.0.1'))),
            'port' => env('CLIENT_DB_PORT', env('MAIN_DB_PORT', env('DB_PORT', '3306'))),
            'database' => $databaseName,
            'username' => env('CLIENT_DB_USERNAME', env('MAIN_DB_USERNAME', env('DB_USERNAME', 'root'))),
            'password' => env('CLIENT_DB_PASSWORD', env('MAIN_DB_PASSWORD', env('DB_PASSWORD', ''))),
            'unix_socket' => env('CLIENT_DB_SOCKET', env('MAIN_DB_SOCKET', env('DB_SOCKET', ''))),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ]);
        
        // Purge existing connection if it exists
        DB::purge($connectionName);
    }
    
    /**
     * Get the database connection name for a client
     */
    public function getClientConnection(int $clientId): string
    {
        return "client_{$clientId}";
    }
    
    /**
     * Switch to client database context
     */
    public function switchToClientDatabase(int $clientId): void
    {
        // Ensure connection uses the stored database_name if present
        $client = Client::on('main')->find($clientId);
        $dbName = $client && $client->database_name ? $client->database_name : null;
        $connectionName = $this->getClientConnection($clientId);
        $this->setClientDatabaseConnection($clientId, $dbName);
        Config::set('database.default', $connectionName);
    }
    
    /**
     * Switch to main database context
     */
    public function switchToMainDatabase(): void
    {
        Config::set('database.default', 'main');
    }
    
    /**
     * Run client-specific migrations
     */
    private function runClientMigrations(int $clientId): void
    {
        $connectionName = $this->getClientConnection($clientId);
        
        // List of client-specific migration files
        $clientMigrations = [
            '2025_06_28_143525_create_products_table.php',
            '2025_06_29_061431_add_featured_to_products_table.php',
            '2025_07_05_074952_add_client_id_to_products_table.php',
            '2025_07_07_000002_rename_featured_to_is_featured_in_products_table.php',
            '2025_06_28_143520_create_categories_table.php',
            '2025_07_05_075702_add_client_id_to_categories_table.php',
            '2025_06_28_143530_create_orders_table.php',
            '2025_06_28_143535_create_order_items_table.php',
            '2025_07_05_075737_add_client_id_to_orders_table.php',
            '2025_06_30_064823_create_payments_table.php',
            '2025_06_28_143540_create_cart_items_table.php',
            '2025_06_30_065240_create_customers_table.php',
            '2025_07_05_075814_add_client_id_to_customers_table.php',
            '2025_07_07_000001_add_client_id_to_coupons_table.php',
            '2025_07_27_131500_add_amazon_style_fields_to_addresses_table.php',
            '2025_07_05_074637_add_client_id_to_users_table.php',
            '2024_01_27_000001_create_product_reviews_table.php',
            '2024_01_27_000002_create_recently_viewed_table.php',
            '2024_01_27_000003_update_product_reviews_table.php',
        ];
        
        // Run each migration on the client database
        foreach ($clientMigrations as $migration) {
            try {
                Artisan::call('migrate', [
                    '--database' => $connectionName,
                    '--path' => 'database/migrations/' . $migration,
                    '--force' => true
                ]);
            } catch (\Exception $e) {
                \Log::warning("Migration {$migration} failed for client {$clientId}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Delete client database
     */
    public function deleteClientDatabase(int $clientId): bool
    {
        try {
            $databaseName = "vergeflow_client_{$clientId}";
            
            // Drop the database
            DB::connection('main')->statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            
            // Purge the connection
            $connectionName = $this->getClientConnection($clientId);
            DB::purge($connectionName);
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to delete client database for client {$clientId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get list of tables that should be in main database only
     */
    public function getMainDatabaseTables(): array
    {
        return [
            'users', // Only super admin and admin users
            'clients',
            'settings',
            'banners',
            'pages',
            'migrations',
            'password_reset_tokens',
            'personal_access_tokens',
            'failed_jobs',
            'jobs',
            'job_batches',
        ];
    }
    
    /**
     * Get list of tables that should be in client databases only
     */
    public function getClientDatabaseTables(): array
    {
        return [
            'users', // Site users only
            'products',
            'categories',
            'orders',
            'order_items',
            'cart_items',
            'customers',
            'coupons',
            'addresses',
            'product_reviews',
            'recently_viewed',
        ];
    }
    
    /**
     * Migrate existing data to new multi-tenant structure
     */
    public function migrateToMultiTenant(): void
    {
        // This method would handle migrating existing single-database data
        // to the new multi-tenant structure
        // Implementation would depend on current data state
    }
}
