<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Vault64DemoSeeder extends Seeder
{
    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        $this->multiTenantService = $multiTenantService;
    }

    public function run(): void
    {
        echo "🌱 Seeding demo data for Vault64 client...\n";

        // Switch to client database (client ID 1 = Vault64)
        $this->multiTenantService->switchToClientDatabase(1);
        
        echo "✅ Connected to client database: " . DB::getDatabaseName() . "\n";

        // 1. Create categories
        $categories = [
            [
                'name' => 'Classic Cars',
                'description' => 'Vintage and classic car models from the golden era',
                'image' => 'classic-cars.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sports Cars',
                'description' => 'High-performance sports cars and supercars',
                'image' => 'sports-cars.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Muscle Cars',
                'description' => 'American muscle cars with powerful engines',
                'image' => 'muscle-cars.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trucks & SUVs',
                'description' => 'Heavy-duty trucks and sport utility vehicles',
                'image' => 'trucks-suvs.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fantasy Cars',
                'description' => 'Imaginative and futuristic car designs',
                'image' => 'fantasy-cars.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Racing Cars',
                'description' => 'Formula 1 and professional racing vehicles',
                'image' => 'racing-cars.jpg',
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            $existing = DB::table('categories')->where('name', $category['name'])->first();
            if ($existing) {
                DB::table('categories')->where('name', $category['name'])->update($category);
            } else {
                DB::table('categories')->insert($category);
            }
        }

        echo "✅ Created 6 categories\n";

        // Get category IDs for products
        $classicCarsId = DB::table('categories')->where('name', 'Classic Cars')->value('id');
        $sportsCarsId = DB::table('categories')->where('name', 'Sports Cars')->value('id');
        $muscleCarsId = DB::table('categories')->where('name', 'Muscle Cars')->value('id');
        $trucksId = DB::table('categories')->where('name', 'Trucks & SUVs')->value('id');
        $fantasyCarsId = DB::table('categories')->where('name', 'Fantasy Cars')->value('id');
        $racingCarsId = DB::table('categories')->where('name', 'Racing Cars')->value('id');

        // 2. Create products
        $products = [
            [
                'name' => '1967 Camaro SS',
                'description' => 'Classic American muscle car with racing stripes and powerful V8 engine. A true icon of the muscle car era.',
                'price' => 299.99,
                'sale_price' => 249.99,
                'sku' => 'HW-CAM-67-SS',
                'stock_quantity' => 25,
                'category_id' => $classicCarsId,
                'client_id' => 1,
                'featured' => 1,
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'camaro-ss-1967.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1969 Dodge Charger R/T',
                'description' => 'Legendary muscle car featured in movies and TV shows. Known for its aggressive styling and HEMI engine.',
                'price' => 349.99,
                'sale_price' => null,
                'sku' => 'HW-CHR-69-RT',
                'stock_quantity' => 18,
                'category_id' => $muscleCarsId,
                'client_id' => 1,
                'featured' => 1,
                'is_featured' => 1,
                'status' => 'active',
                'is_active' => 1,
                'image' => 'charger-rt-1969.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lamborghini Aventador',
                'slug' => 'lamborghini-aventador',
                'description' => 'Italian supercar with scissor doors and V12 engine. The pinnacle of automotive engineering.',
                'price' => 799.99,
                'sale_price' => 699.99,
                'sku' => 'HW-LAM-AVE',
                'stock_quantity' => 12,
                'category_id' => $sportsCarsId,
                'is_featured' => 1,
                'is_active' => 1,
                'image' => 'lamborghini-aventador.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ferrari 488 GTB',
                'slug' => 'ferrari-488-gtb',
                'description' => 'Italian sports car with twin-turbo V8 engine. Racing heritage meets street performance.',
                'price' => 649.99,
                'sale_price' => null,
                'sku' => 'HW-FER-488',
                'stock_quantity' => 15,
                'category_id' => $sportsCarsId,
                'is_featured' => 0,
                'is_active' => 1,
                'image' => 'ferrari-488-gtb.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ford Mustang GT',
                'slug' => 'ford-mustang-gt',
                'description' => 'American pony car with modern styling and classic appeal. Perfect blend of power and style.',
                'price' => 199.99,
                'sale_price' => 179.99,
                'sku' => 'HW-MUS-GT',
                'stock_quantity' => 30,
                'category_id' => $muscleCarsId,
                'is_featured' => 0,
                'is_active' => 1,
                'image' => 'mustang-gt.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ford F-150 Raptor',
                'slug' => 'ford-f150-raptor',
                'description' => 'High-performance off-road truck built for extreme terrain. The ultimate adventure vehicle.',
                'price' => 449.99,
                'sale_price' => null,
                'sku' => 'HW-F150-RAP',
                'stock_quantity' => 20,
                'category_id' => $trucksId,
                'is_featured' => 0,
                'is_active' => 1,
                'image' => 'f150-raptor.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Twin Mill III',
                'slug' => 'twin-mill-iii',
                'description' => 'Futuristic fantasy car with dual engines and unique design. A Hot Wheels original creation.',
                'price' => 149.99,
                'sale_price' => 129.99,
                'sku' => 'HW-TM3',
                'stock_quantity' => 35,
                'category_id' => $fantasyCarsId,
                'is_featured' => 1,
                'is_active' => 1,
                'image' => 'twin-mill-iii.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Formula 1 Red Bull Racing',
                'slug' => 'formula-1-red-bull-racing',
                'description' => 'Professional Formula 1 racing car with aerodynamic design and championship pedigree.',
                'price' => 399.99,
                'sale_price' => null,
                'sku' => 'HW-F1-RBR',
                'stock_quantity' => 22,
                'category_id' => $racingCarsId,
                'is_featured' => 0,
                'is_active' => 1,
                'image' => 'f1-red-bull.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            $existing = DB::table('products')->where('sku', $product['sku'])->first();
            if ($existing) {
                DB::table('products')->where('sku', $product['sku'])->update($product);
            } else {
                DB::table('products')->insert($product);
            }
        }

        echo "✅ Created 8 products\n";

        // 3. Create demo users
        $users = [
            [
                'name' => 'Vault64 Admin',
                'email' => 'admin@vault64.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Store Manager',
                'email' => 'manager@vault64.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            $existing = DB::table('users')->where('email', $user['email'])->first();
            if ($existing) {
                DB::table('users')->where('email', $user['email'])->update($user);
            } else {
                DB::table('users')->insert($user);
            }
        }

        echo "✅ Created 2 admin users\n";

        // 4. Create demo customers
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'phone' => '+1-555-0101',
                'date_of_birth' => '1985-06-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'phone' => '+1-555-0102',
                'date_of_birth' => '1990-03-22',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'phone' => '+1-555-0103',
                'date_of_birth' => '1988-11-08',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            $existing = DB::table('customers')->where('email', $customer['email'])->first();
            if ($existing) {
                DB::table('customers')->where('email', $customer['email'])->update($customer);
            } else {
                DB::table('customers')->insert($customer);
            }
        }

        echo "✅ Created 3 demo customers\n";

        // Switch back to main database
        $this->multiTenantService->switchToMainDatabase();
        
        echo "🎉 Demo data seeding completed for Vault64!\n";
        echo "📊 Summary:\n";
        echo "   - 6 categories created\n";
        echo "   - 8 products created\n";
        echo "   - 2 admin users created\n";
        echo "   - 3 demo customers created\n";
        echo "\n🔐 Admin Login: admin@vault64.com / password123\n";
        echo "🌐 Visit: http://127.0.0.1:8000\n";
    }
}
