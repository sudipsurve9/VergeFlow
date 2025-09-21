<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SimpleVault64Seeder extends Seeder
{
    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        $this->multiTenantService = $multiTenantService;
    }

    public function run(): void
    {
        echo "🌱 Seeding simple demo data for Vault64 client...\n";

        // Switch to client database (client ID 1 = Vault64)
        $this->multiTenantService->switchToClientDatabase(1);
        
        echo "✅ Connected to client database: " . DB::getDatabaseName() . "\n";

        // 1. Create categories
        $categoryIds = [];
        
        $categories = [
            'Classic Cars' => 'Vintage and classic car models from the golden era',
            'Sports Cars' => 'High-performance sports cars and supercars',
            'Muscle Cars' => 'American muscle cars with powerful engines',
            'Trucks & SUVs' => 'Heavy-duty trucks and sport utility vehicles',
            'Fantasy Cars' => 'Imaginative and futuristic car designs',
            'Racing Cars' => 'Formula 1 and professional racing vehicles',
        ];

        foreach ($categories as $name => $description) {
            $existing = DB::table('categories')->where('name', $name)->first();
            if (!$existing) {
                $categoryData = [
                    'name' => $name,
                    'slug' => \Illuminate\Support\Str::slug($name),
                    'description' => $description,
                    'client_id' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Check if image column exists before adding it
                if (Schema::hasColumn('categories', 'image')) {
                    $categoryData['image'] = strtolower(str_replace([' ', '&'], ['-', ''], $name)) . '.jpg';
                }
                
                $id = DB::table('categories')->insertGetId($categoryData);
                $categoryIds[$name] = $id;
                echo "✅ Created category: {$name}\n";
            } else {
                $categoryIds[$name] = $existing->id;
                echo "ℹ️  Category already exists: {$name}\n";
            }
        }

        echo "✅ Created/verified 6 categories\n";

        // 2. Create products
        $products = [
            [
                'name' => '1967 Camaro SS',
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
            ],
            [
                'name' => '1969 Dodge Charger R/T',
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
            ],
            [
                'name' => 'Lamborghini Aventador',
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
            ],
            [
                'name' => 'Ferrari 488 GTB',
                'description' => 'Italian sports car with twin-turbo V8 engine.',
                'price' => 649.99,
                'sale_price' => null,
                'sku' => 'HW-FER-488',
                'stock_quantity' => 15,
                'category_id' => $categoryIds['Sports Cars'],
                'is_featured' => 0,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'ferrari-488-gtb.jpg',
            ],
            [
                'name' => 'Ford F-150 Raptor',
                'description' => 'High-performance off-road truck built for extreme terrain.',
                'price' => 449.99,
                'sale_price' => null,
                'sku' => 'HW-F150-RAP',
                'stock_quantity' => 20,
                'category_id' => $categoryIds['Trucks & SUVs'],
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'f150-raptor.jpg',
            ],
            [
                'name' => 'Twin Mill III',
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
            ],
        ];

        foreach ($products as $product) {
            $existing = DB::table('products')->where('sku', $product['sku'])->first();
            if (!$existing) {
                DB::table('products')->insert(array_merge($product, [
                    'client_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        echo "✅ Created/verified 6 products\n";

        // 3. Create admin user
        $existing = DB::table('users')->where('email', 'admin@vault64.com')->first();
        if (!$existing) {
            DB::table('users')->insert([
                'name' => 'Vault64 Admin',
                'email' => 'admin@vault64.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ Created admin user\n";

        // 4. Create demo customers
        $customers = [
            ['name' => 'John Smith', 'email' => 'john@example.com', 'phone' => '+1-555-0101', 'address' => '123 Main St, New York, NY'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com', 'phone' => '+1-555-0102', 'address' => '456 Oak Ave, Los Angeles, CA'],
            ['name' => 'Mike Wilson', 'email' => 'mike@example.com', 'phone' => '+1-555-0103', 'address' => '789 Pine Rd, Chicago, IL'],
        ];

        foreach ($customers as $customer) {
            $existing = DB::table('customers')->where('email', $customer['email'])->first();
            if (!$existing) {
                DB::table('customers')->insert(array_merge($customer, [
                    'client_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        echo "✅ Created 3 demo customers\n";

        // Switch back to main database
        $this->multiTenantService->switchToMainDatabase();
        
        echo "🎉 Simple demo data seeding completed for Vault64!\n";
        echo "📊 Summary: 6 categories, 6 products, 1 admin user, 3 customers\n";
        echo "🔐 Admin Login: admin@vault64.com / password123\n";
        echo "🌐 Visit: http://127.0.0.1:8000\n";
    }
}
