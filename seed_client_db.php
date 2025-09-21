<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Seeding client database with test data...\n";
    
    // Check if categories exist
    $categoryCount = DB::connection('client')->table('categories')->count();
    echo "Current categories: $categoryCount\n";
    
    // Clear existing data first
    try {
        DB::connection('client')->table('orders')->truncate();
        DB::connection('client')->table('products')->truncate();
        DB::connection('client')->table('categories')->truncate();
        DB::connection('client')->table('users')->where('role', 'user')->delete();
        echo "Cleared existing data\n";
    } catch (Exception $e) {
        echo "Error clearing data: " . $e->getMessage() . "\n";
    }
    
    // Insert categories
        $categories = [
            ['name' => 'Classic Cars', 'slug' => 'classic-cars', 'description' => 'Vintage and classic car models', 'is_active' => 1, 'client_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sports Cars', 'slug' => 'sports-cars', 'description' => 'High-performance sports cars', 'is_active' => 1, 'client_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Muscle Cars', 'slug' => 'muscle-cars', 'description' => 'American muscle cars', 'is_active' => 1, 'client_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fantasy Cars', 'slug' => 'fantasy-cars', 'description' => 'Fantasy and concept cars', 'is_active' => 1, 'client_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
        
        DB::connection('client')->table('categories')->insert($categories);
        echo "Categories inserted\n";
    
    // Get category IDs
    $categoryIds = DB::connection('client')->table('categories')->pluck('id', 'name')->toArray();
    
    // Check if products exist
    $productCount = DB::connection('client')->table('products')->count();
    echo "Current products: $productCount\n";
    
    // Insert products
        $products = [
            [
                'name' => '1967 Camaro SS',
                'slug' => '1967-camaro-ss',
                'description' => 'Classic American muscle car with racing stripes and powerful V8 engine.',
                'price' => 299.99,
                'sale_price' => 249.99,
                'sku' => 'HW-CAM-67-SS',
                'stock_quantity' => 25,
                'category_id' => $categoryIds['Classic Cars'],
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'hotwheels-1.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1969 Dodge Charger R/T',
                'slug' => '1969-dodge-charger-rt',
                'description' => 'Legendary muscle car featured in movies and TV shows.',
                'price' => 349.99,
                'sale_price' => null,
                'sku' => 'HW-CHR-69-RT',
                'stock_quantity' => 18,
                'category_id' => $categoryIds['Muscle Cars'],
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'hotwheels-2.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lamborghini Aventador',
                'slug' => 'lamborghini-aventador',
                'description' => 'Italian supercar with scissor doors and V12 engine.',
                'price' => 799.99,
                'sale_price' => 699.99,
                'sku' => 'HW-LAM-AVE',
                'stock_quantity' => 12,
                'category_id' => $categoryIds['Sports Cars'],
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'hotwheels-3.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Twin Mill III',
                'slug' => 'twin-mill-iii',
                'description' => 'Futuristic fantasy car with dual engines and unique design.',
                'price' => 149.99,
                'sale_price' => 129.99,
                'sku' => 'HW-TM3',
                'stock_quantity' => 35,
                'category_id' => $categoryIds['Fantasy Cars'],
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'hotwheels-4.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::connection('client')->table('products')->insert($products);
        echo "Products inserted\n";
    
    // Check if users exist
    $userCount = DB::connection('client')->table('users')->where('role', 'user')->count();
    echo "Current users: $userCount\n";
    
    // Insert test users
        $users = [
            [
                'name' => 'John Customer',
                'email' => 'john@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'user',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'user',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::connection('client')->table('users')->insert($users);
        echo "Users inserted\n";
    
    // Insert sample orders if none exist
    $orderCount = DB::connection('client')->table('orders')->count();
    echo "Current orders: $orderCount\n";
    
    // Insert sample orders
        $orders = [
            [
                'user_id' => 1,
                'order_number' => 'ORD-001',
                'status' => 'completed',
                'payment_status' => 'paid',
                'total_amount' => 249.99,
                'shipping_address' => '123 Main St, City, State 12345',
                'billing_address' => '123 Main St, City, State 12345',
                'payment_method' => 'stripe',
                'client_id' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 2,
                'order_number' => 'ORD-002',
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => 699.99,
                'shipping_address' => '456 Oak Ave, City, State 67890',
                'billing_address' => '456 Oak Ave, City, State 67890',
                'payment_method' => 'cod',
                'client_id' => 1,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];
        
        DB::connection('client')->table('orders')->insert($orders);
        echo "Orders inserted\n";
    
    echo "Database seeding completed successfully!\n";
    
    // Show final counts
    echo "\nFinal counts:\n";
    echo "Categories: " . DB::connection('client')->table('categories')->count() . "\n";
    echo "Products: " . DB::connection('client')->table('products')->count() . "\n";
    echo "Users: " . DB::connection('client')->table('users')->where('role', 'user')->count() . "\n";
    echo "Orders: " . DB::connection('client')->table('orders')->count() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
