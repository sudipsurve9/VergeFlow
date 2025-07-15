<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseService;
use App\Models\Client;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class MigrateToMultiDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:migrate-multi-db {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing data to multi-database architecture and create Vault64 as first client';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will migrate all existing data to separate databases. Continue?')) {
            $this->info('Migration cancelled.');
            return;
        }

        $this->info('Starting migration to multi-database architecture...');

        try {
            // Step 1: Check if Vault64 client exists, create if not
            $this->info('Checking Vault64 client...');
            $vault64Client = Client::where('name', 'Vault64')->first();
            
            if (!$vault64Client) {
                $this->info('Creating Vault64 client...');
                $databaseService = new DatabaseService();
                $vault64Client = $databaseService->createVault64Client();
                $this->info("Vault64 client created with ID: {$vault64Client->id}");
            } else {
                $this->info("Vault64 client already exists with ID: {$vault64Client->id}");
            }

            // Step 2: Migrate existing data to Vault64 database
            $this->info('Migrating existing data to Vault64 database...');
            $this->migrateExistingDataToVault64($vault64Client);

            // Step 3: Create databases for existing clients
            $this->info('Creating databases for existing clients...');
            $databaseService->migrateExistingData();

            $this->info('Migration completed successfully!');
            $this->info('Vault64 is now the first client with all existing data.');
            $this->info('Each client now has their own separate database.');

        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Migrate existing data to Vault64 database
     */
    private function migrateExistingDataToVault64(Client $vault64Client)
    {
        // Get connection to Vault64 database
        $databaseService = new DatabaseService();
        $vault64Connection = $databaseService->getClientConnection($vault64Client);

        // Migrate users (excluding super admins)
        $users = User::where('role', '!=', 'super_admin')->get();
        foreach ($users as $user) {
            $userData = $user->toArray();
            unset($userData['id']);
            $userData['client_id'] = $vault64Client->id;
            
            // Ensure password field is included if it exists
            if (!isset($userData['password']) || empty($userData['password'])) {
                $userData['password'] = bcrypt('password'); // Default password
            }
            
            // Fix datetime format for MySQL
            if (isset($userData['created_at'])) {
                $userData['created_at'] = date('Y-m-d H:i:s', strtotime($userData['created_at']));
            }
            if (isset($userData['updated_at'])) {
                $userData['updated_at'] = date('Y-m-d H:i:s', strtotime($userData['updated_at']));
            }
            if (isset($userData['email_verified_at']) && $userData['email_verified_at']) {
                $userData['email_verified_at'] = date('Y-m-d H:i:s', strtotime($userData['email_verified_at']));
            }
            
            DB::connection($vault64Connection)->table('users')->insert($userData);
        }
        $this->info("Migrated {$users->count()} users to Vault64 database");

        // Migrate products
        $products = Product::all();
        foreach ($products as $product) {
            $productData = $product->toArray();
            unset($productData['id']);
            $productData['client_id'] = $vault64Client->id;
            
            // Fix datetime format for MySQL
            if (isset($productData['created_at'])) {
                $productData['created_at'] = date('Y-m-d H:i:s', strtotime($productData['created_at']));
            }
            if (isset($productData['updated_at'])) {
                $productData['updated_at'] = date('Y-m-d H:i:s', strtotime($productData['updated_at']));
            }
            
            DB::connection($vault64Connection)->table('products')->insert($productData);
        }
        $this->info("Migrated {$products->count()} products to Vault64 database");

        // Migrate categories
        $categories = Category::all();
        foreach ($categories as $category) {
            $categoryData = $category->toArray();
            unset($categoryData['id']);
            $categoryData['client_id'] = $vault64Client->id;
            
            // Fix datetime format for MySQL
            if (isset($categoryData['created_at'])) {
                $categoryData['created_at'] = date('Y-m-d H:i:s', strtotime($categoryData['created_at']));
            }
            if (isset($categoryData['updated_at'])) {
                $categoryData['updated_at'] = date('Y-m-d H:i:s', strtotime($categoryData['updated_at']));
            }
            
            DB::connection($vault64Connection)->table('categories')->insert($categoryData);
        }
        $this->info("Migrated {$categories->count()} categories to Vault64 database");

        // Migrate orders
        $orders = Order::all();
        foreach ($orders as $order) {
            $orderData = $order->toArray();
            unset($orderData['id']);
            $orderData['client_id'] = $vault64Client->id;
            
            // Fix datetime format for MySQL
            if (isset($orderData['created_at'])) {
                $orderData['created_at'] = date('Y-m-d H:i:s', strtotime($orderData['created_at']));
            }
            if (isset($orderData['updated_at'])) {
                $orderData['updated_at'] = date('Y-m-d H:i:s', strtotime($orderData['updated_at']));
            }
            
            DB::connection($vault64Connection)->table('orders')->insert($orderData);
        }
        $this->info("Migrated {$orders->count()} orders to Vault64 database");

        // Migrate customers
        $customers = Customer::all();
        foreach ($customers as $customer) {
            $customerData = $customer->toArray();
            unset($customerData['id']);
            $customerData['client_id'] = $vault64Client->id;
            
            // Fix datetime format for MySQL
            if (isset($customerData['created_at'])) {
                $customerData['created_at'] = date('Y-m-d H:i:s', strtotime($customerData['created_at']));
            }
            if (isset($customerData['updated_at'])) {
                $customerData['updated_at'] = date('Y-m-d H:i:s', strtotime($customerData['updated_at']));
            }
            
            DB::connection($vault64Connection)->table('customers')->insert($customerData);
        }
        $this->info("Migrated {$customers->count()} customers to Vault64 database");
    }
} 