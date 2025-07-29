<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Client;

class HotWheelsProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get HotWheels client (assuming it's ID 1)
        $client = Client::find(1);
        
        if (!$client) {
            echo "Client ID 1 not found. Please run ClientSeeder first.\n";
            return;
        }

        // Create HotWheels Categories
        $categories = [
            [
                'name' => 'Classic Cars',
                'slug' => 'classic-cars',
                'description' => 'Vintage and classic car models from the golden era',
                'image' => 'categories/classic-cars.jpg',
                'is_active' => true,
                'client_id' => $client->id,
            ],
            [
                'name' => 'Sports Cars',
                'slug' => 'sports-cars',
                'description' => 'High-performance sports cars and supercars',
                'image' => 'categories/sports-cars.jpg',
                'is_active' => true,
                'client_id' => $client->id,
            ],
            [
                'name' => 'Muscle Cars',
                'slug' => 'muscle-cars',
                'description' => 'American muscle cars with powerful engines',
                'image' => 'categories/muscle-cars.jpg',
                'is_active' => true,
                'client_id' => $client->id,
            ],
            [
                'name' => 'Trucks & SUVs',
                'slug' => 'trucks-suvs',
                'description' => 'Heavy-duty trucks and sport utility vehicles',
                'image' => 'categories/trucks-suvs.jpg',
                'is_active' => true,
                'client_id' => $client->id,
            ],
            [
                'name' => 'Fantasy Cars',
                'slug' => 'fantasy-cars',
                'description' => 'Imaginative and futuristic vehicle designs',
                'image' => 'categories/fantasy-cars.jpg',
                'is_active' => true,
                'client_id' => $client->id,
            ],
            [
                'name' => 'Racing Cars',
                'slug' => 'racing-cars',
                'description' => 'Formula 1, NASCAR, and other racing vehicles',
                'image' => 'categories/racing-cars.jpg',
                'is_active' => true,
                'client_id' => $client->id,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug'], 'client_id' => $categoryData['client_id']],
                $categoryData
            );
            $createdCategories[$category->slug] = $category;
        }

        // Create HotWheels Products
        $products = [
            // Classic Cars
            [
                'name' => '1967 Camaro SS',
                'slug' => '1967-camaro-ss',
                'description' => 'Classic American muscle car with iconic design and powerful V8 engine. Features detailed interior and authentic paint scheme. Perfect collectible for car enthusiasts.',
                'sku' => 'HW-CC-001',
                'price' => 299.00,
                'sale_price' => 249.00,
                'stock_quantity' => 50,
                'image' => 'products/camaro-ss-main.jpg',
                'images' => json_encode(['products/camaro-ss-1.jpg', 'products/camaro-ss-2.jpg']),
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $createdCategories['classic-cars']->id,
                'client_id' => $client->id,
            ],
            [
                'name' => '1969 Dodge Charger R/T',
                'slug' => '1969-dodge-charger-rt',
                'description' => 'Legendary muscle car made famous by movies and TV shows. Features opening hood and detailed engine bay. A must-have for any collection.',
                'sku' => 'HW-CC-002',
                'price' => 349.00,
                'sale_price' => null,
                'stock_quantity' => 35,
                'image' => 'products/charger-rt-main.jpg',
                'images' => json_encode(['products/charger-rt-1.jpg', 'products/charger-rt-2.jpg']),
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $createdCategories['classic-cars']->id,
                'client_id' => $client->id,
            ],
            
            // Sports Cars
            [
                'name' => 'Lamborghini Aventador',
                'slug' => 'lamborghini-aventador',
                'description' => 'Stunning Italian supercar with scissor doors and aggressive styling. Premium die-cast with rubber tires and exceptional detail work.',
                'sku' => 'HW-SC-001',
                'price' => 599.00,
                'sale_price' => 499.00,
                'stock_quantity' => 25,
                'image' => 'products/aventador-main.jpg',
                'images' => json_encode(['products/aventador-1.jpg', 'products/aventador-2.jpg']),
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $createdCategories['sports-cars']->id,
                'client_id' => $client->id,
            ],
            [
                'name' => 'Ferrari 488 GTB',
                'slug' => 'ferrari-488-gtb',
                'description' => 'Iconic Ferrari sports car with turbocharged V8 engine. Features detailed interior and authentic Ferrari red paint finish.',
                'sku' => 'HW-SC-002',
                'price' => 549.00,
                'sale_price' => null,
                'stock_quantity' => 30,
                'image' => 'products/ferrari-488-main.jpg',
                'images' => json_encode(['products/ferrari-488-1.jpg', 'products/ferrari-488-2.jpg']),
                'is_active' => true,
                'is_featured' => false,
                'category_id' => $createdCategories['sports-cars']->id,
                'client_id' => $client->id,
            ],
            
            // Muscle Cars
            [
                'name' => 'Ford Mustang GT',
                'slug' => 'ford-mustang-gt',
                'description' => 'American icon with powerful V8 engine and aggressive styling. Features opening hood and detailed engine compartment.',
                'sku' => 'HW-MC-001',
                'price' => 399.00,
                'sale_price' => 329.00,
                'stock_quantity' => 45,
                'image' => 'products/mustang-gt-main.jpg',
                'images' => json_encode(['products/mustang-gt-1.jpg', 'products/mustang-gt-2.jpg']),
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $createdCategories['muscle-cars']->id,
                'client_id' => $client->id,
            ],
            
            // Trucks & SUVs
            [
                'name' => 'Ford F-150 Raptor',
                'slug' => 'ford-f150-raptor',
                'description' => 'High-performance off-road pickup truck with aggressive styling and oversized tires. Built for adventure and tough terrain.',
                'sku' => 'HW-TS-001',
                'price' => 449.00,
                'sale_price' => null,
                'stock_quantity' => 40,
                'image' => 'products/f150-raptor-main.jpg',
                'images' => json_encode(['products/f150-raptor-1.jpg', 'products/f150-raptor-2.jpg']),
                'is_active' => true,
                'is_featured' => false,
                'category_id' => $createdCategories['trucks-suvs']->id,
                'client_id' => $client->id,
            ],
            
            // Fantasy Cars
            [
                'name' => 'Twin Mill III',
                'slug' => 'twin-mill-iii',
                'description' => 'Futuristic HotWheels original design with twin engines and radical styling. Limited edition collectible with unique design elements.',
                'sku' => 'HW-FC-001',
                'price' => 199.00,
                'sale_price' => 149.00,
                'stock_quantity' => 60,
                'image' => 'products/twin-mill-main.jpg',
                'images' => json_encode(['products/twin-mill-1.jpg', 'products/twin-mill-2.jpg']),
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $createdCategories['fantasy-cars']->id,
                'client_id' => $client->id,
            ],
            
            // Racing Cars
            [
                'name' => 'Formula 1 Red Bull Racing',
                'slug' => 'formula-1-red-bull-racing',
                'description' => 'Official Red Bull Racing Formula 1 car with authentic livery and sponsor decals. Precision engineering meets racing excellence.',
                'sku' => 'HW-RC-001',
                'price' => 799.00,
                'sale_price' => 699.00,
                'stock_quantity' => 20,
                'image' => 'products/f1-redbull-main.jpg',
                'images' => json_encode(['products/f1-redbull-1.jpg', 'products/f1-redbull-2.jpg']),
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $createdCategories['racing-cars']->id,
                'client_id' => $client->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku'], 'client_id' => $productData['client_id']],
                $productData
            );
        }

        echo "HotWheels categories and products seeded successfully!\n";
        echo "Created " . count($categories) . " categories and " . count($products) . " products.\n";
    }
}
