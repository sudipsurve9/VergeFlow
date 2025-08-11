<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupClientDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:client-databases {--force : Force setup even if databases exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up client databases with all necessary tables';

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
        $this->info('Setting up client databases...');

        // Ensure we're using main database
        $this->multiTenantService->switchToMainDatabase();

        $clients = Client::all();
        
        if ($clients->isEmpty()) {
            $this->warn('No clients found. Please create clients first.');
            return;
        }

        $this->info("Found {$clients->count()} clients");

        foreach ($clients as $client) {
            $this->info("\nProcessing client: {$client->name} (ID: {$client->id})");
            
            $databaseName = "vergeflow_client_{$client->id}";
            
            try {
                // Create database if it doesn't exist
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
                $this->info("✓ Database {$databaseName} created/verified");
                
                // Set up connection for this client
                $this->multiTenantService->setClientDatabaseConnection($client->id, $databaseName);
                
                // Switch to client database
                $this->multiTenantService->switchToClientDatabase($client->id);
                
                // Create all necessary tables
                $this->createClientTables();
                
                // Update client record with database name
                $this->multiTenantService->switchToMainDatabase();
                $client->update(['database_name' => $databaseName]);
                
                $this->info("✓ Client {$client->name} database setup completed");
                
            } catch (\Exception $e) {
                $this->error("❌ Error setting up database for {$client->name}: " . $e->getMessage());
            }
        }

        $this->info("\n✅ Client database setup completed!");
    }

    private function createClientTables()
    {
        $tables = [
            'migrations' => "CREATE TABLE IF NOT EXISTS `migrations` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `migration` varchar(255) NOT NULL,
                `batch` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'users' => "CREATE TABLE IF NOT EXISTS `users` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `role` varchar(50) DEFAULT 'user',
                `phone` varchar(20) DEFAULT NULL,
                `address` text DEFAULT NULL,
                `is_active` tinyint(1) DEFAULT 1,
                `profile_image` varchar(255) DEFAULT NULL,
                `client_id` bigint(20) unsigned DEFAULT NULL,
                `remember_token` varchar(100) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'password_reset_tokens' => "CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
                `email` varchar(255) NOT NULL,
                `token` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'categories' => "CREATE TABLE IF NOT EXISTS `categories` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `image` varchar(255) DEFAULT NULL,
                `client_id` bigint(20) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'products' => "CREATE TABLE IF NOT EXISTS `products` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `price` decimal(10,2) NOT NULL,
                `sale_price` decimal(10,2) DEFAULT NULL,
                `sku` varchar(100) DEFAULT NULL,
                `stock_quantity` int(11) DEFAULT 0,
                `category_id` bigint(20) unsigned DEFAULT NULL,
                `client_id` bigint(20) unsigned DEFAULT NULL,
                `featured` tinyint(1) DEFAULT 0,
                `is_featured` tinyint(1) DEFAULT 0,
                `status` varchar(50) DEFAULT 'active',
                `is_active` tinyint(1) DEFAULT 1,
                `image` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `products_category_id_foreign` (`category_id`),
                UNIQUE KEY `products_sku_unique` (`sku`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'orders' => "CREATE TABLE IF NOT EXISTS `orders` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL,
                `total_amount` decimal(10,2) NOT NULL,
                `status` varchar(50) DEFAULT 'pending',
                `shipping_address` text DEFAULT NULL,
                `billing_address` text DEFAULT NULL,
                `payment_method` varchar(100) DEFAULT NULL,
                `payment_status` varchar(50) DEFAULT 'pending',
                `notes` text DEFAULT NULL,
                `client_id` bigint(20) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `orders_user_id_foreign` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'order_items' => "CREATE TABLE IF NOT EXISTS `order_items` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `order_id` bigint(20) unsigned NOT NULL,
                `product_id` bigint(20) unsigned NOT NULL,
                `quantity` int(11) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `order_items_order_id_foreign` (`order_id`),
                KEY `order_items_product_id_foreign` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'cart_items' => "CREATE TABLE IF NOT EXISTS `cart_items` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `session_id` varchar(255) DEFAULT NULL,
                `product_id` bigint(20) unsigned NOT NULL,
                `quantity` int(11) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `cart_items_user_id_foreign` (`user_id`),
                KEY `cart_items_product_id_foreign` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'addresses' => "CREATE TABLE IF NOT EXISTS `addresses` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL,
                `type` varchar(50) DEFAULT 'home',
                `usage_type` varchar(50) DEFAULT 'both',
                `label` varchar(100) DEFAULT NULL,
                `first_name` varchar(100) NOT NULL,
                `last_name` varchar(100) NOT NULL,
                `company` varchar(100) DEFAULT NULL,
                `address_line_1` varchar(255) NOT NULL,
                `address_line_2` varchar(255) DEFAULT NULL,
                `city` varchar(100) NOT NULL,
                `state` varchar(100) NOT NULL,
                `postal_code` varchar(20) NOT NULL,
                `country` varchar(100) NOT NULL DEFAULT 'India',
                `phone` varchar(20) DEFAULT NULL,
                `landmark` varchar(255) DEFAULT NULL,
                `delivery_instructions` text DEFAULT NULL,
                `is_default_shipping` tinyint(1) DEFAULT 0,
                `is_default_billing` tinyint(1) DEFAULT 0,
                `is_verified` tinyint(1) DEFAULT 0,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `addresses_user_id_foreign` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'product_reviews' => "CREATE TABLE IF NOT EXISTS `product_reviews` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `product_id` bigint(20) unsigned NOT NULL,
                `user_id` bigint(20) unsigned NOT NULL,
                `rating` int(11) NOT NULL,
                `title` varchar(255) DEFAULT NULL,
                `review` text DEFAULT NULL,
                `images` json DEFAULT NULL,
                `is_verified_purchase` tinyint(1) DEFAULT 0,
                `helpful_votes` int(11) DEFAULT 0,
                `status` varchar(50) DEFAULT 'pending',
                `admin_notes` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `product_reviews_product_id_foreign` (`product_id`),
                KEY `product_reviews_user_id_foreign` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'recently_viewed' => "CREATE TABLE IF NOT EXISTS `recently_viewed` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `session_id` varchar(255) DEFAULT NULL,
                `product_id` bigint(20) unsigned NOT NULL,
                `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `recently_viewed_user_id_foreign` (`user_id`),
                KEY `recently_viewed_product_id_foreign` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'coupons' => "CREATE TABLE IF NOT EXISTS `coupons` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `code` varchar(50) NOT NULL,
                `type` varchar(50) NOT NULL,
                `value` decimal(10,2) NOT NULL,
                `minimum_amount` decimal(10,2) DEFAULT NULL,
                `usage_limit` int(11) DEFAULT NULL,
                `used_count` int(11) DEFAULT 0,
                `starts_at` timestamp NULL DEFAULT NULL,
                `expires_at` timestamp NULL DEFAULT NULL,
                `is_active` tinyint(1) DEFAULT 1,
                `client_id` bigint(20) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `coupons_code_unique` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'customers' => "CREATE TABLE IF NOT EXISTS `customers` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `address` text DEFAULT NULL,
                `client_id` bigint(20) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `customers_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'wishlists' => "CREATE TABLE IF NOT EXISTS `wishlists` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL,
                `product_id` bigint(20) unsigned NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `wishlists_user_id_foreign` (`user_id`),
                KEY `wishlists_product_id_foreign` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'notifications' => "CREATE TABLE IF NOT EXISTS `notifications` (
                `id` char(36) NOT NULL,
                `type` varchar(255) NOT NULL,
                `notifiable_type` varchar(255) NOT NULL,
                `notifiable_id` bigint(20) unsigned NOT NULL,
                `data` text NOT NULL,
                `read_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        
        foreach ($tables as $tableName => $createSql) {
            try {
                DB::statement($createSql);
                $this->info("  ✓ Table {$tableName} created/verified");
            } catch (\Exception $e) {
                $this->error("  ❌ Error creating table {$tableName}: " . $e->getMessage());
            }
        }
    }
}
