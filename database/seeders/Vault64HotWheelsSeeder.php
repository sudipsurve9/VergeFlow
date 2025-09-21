<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Str;

class Vault64HotWheelsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Vault64 client if not exists (main DB)
        $client = Client::firstOrCreate([
            'name' => 'Vault64',
        ], [
            'company_name' => 'Vault64 Original Store',
            'contact_email' => 'admin@vault64.com',
            'contact_phone' => '+1-555-0000',
            'subdomain' => 'vault64',
            'theme' => 'modern',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'is_active' => true,
            'database_name' => 'vergeflow_vault64_1',
        ]);

        // 2. Link admin user if present (main DB)
        $user = User::where('email', 'admin@vault64.com')
            ->orWhere('email', 'admin@vault64.vergeflow.com')->first();
        if ($user) {
            $user->client_id = $client->id;
            $user->save();
        }

        // Helper to get or create category in client DB
        $getOrCreateCategory = function($attributes, $client) {
            $existing = \App\Models\Category::on((new \App\Services\DatabaseService)->getClientConnection($client))
                ->where('slug', $attributes['slug'])
                ->where('client_id', $client->id)
                ->first();
            if ($existing) return $existing;
            return \App\Models\Category::createForClient($attributes, $client);
        };

        // 3. Create categories in client DB (with slug)
        $cat1 = $getOrCreateCategory([
            'name' => 'Hot Wheels Cars',
            'slug' => Str::slug('Hot Wheels Cars'),
            'description' => 'Collectible die-cast toy cars by Hot Wheels.',
            'is_active' => true,
            'client_id' => $client->id,
        ], $client);
        $cat2 = $getOrCreateCategory([
            'name' => 'Hot Wheels Track Sets',
            'slug' => Str::slug('Hot Wheels Track Sets'),
            'description' => 'Exciting track sets for racing Hot Wheels cars.',
            'is_active' => true,
            'client_id' => $client->id,
        ], $client);
        $cat3 = $getOrCreateCategory([
            'name' => 'Hot Wheels Premium',
            'slug' => Str::slug('Hot Wheels Premium'),
            'description' => 'Premium Hot Wheels cars with special features.',
            'is_active' => true,
            'client_id' => $client->id,
        ], $client);

        // Log category IDs
        echo "Category IDs: Cars={$cat1->id}, TrackSets={$cat2->id}, Premium={$cat3->id}\n";

        // Ensure categories exist before creating products
        if (!$cat1->id || !$cat2->id || !$cat3->id) {
            throw new \Exception('One or more categories were not created. Cannot create products.');
        }

        // 4. Create products in client DB (use actual category IDs)
        $products = [
            [
                'name' => 'Hot Wheels 1969 Dodge Charger',
                'description' => 'Classic muscle car from the Hot Wheels collection.',
                'price' => 4.99,
                'stock_quantity' => 50,
                'category_id' => $cat1->id,
                'is_featured' => true,
                'image' => 'hotwheels-1.jpg',
            ],
            [
                'name' => 'Hot Wheels Twin Mill',
                'description' => 'Iconic Hot Wheels original design with dual engines.',
                'price' => 5.99,
                'stock_quantity' => 40,
                'category_id' => $cat1->id,
                'is_featured' => true,
                'image' => 'hotwheels-2.jpg',
            ],
            [
                'name' => 'Hot Wheels Bone Shaker',
                'description' => 'Hot rod with a skull grille, a fan favorite.',
                'price' => 3.99,
                'stock_quantity' => 60,
                'category_id' => $cat1->id,
                'is_featured' => true,
                'image' => 'hotwheels-3.jpg',
            ],
            [
                'name' => 'Hot Wheels Custom Otto',
                'description' => 'A rare and collectible Hot Wheels car.',
                'price' => 7.99,
                'stock_quantity' => 25,
                'category_id' => $cat1->id,
                'is_featured' => false,
                'image' => 'hotwheels-4.jpg',
            ],
            [
                'name' => 'Hot Wheels Track Builder Unlimited',
                'description' => 'Create your own Hot Wheels tracks with this set.',
                'price' => 19.99,
                'stock_quantity' => 15,
                'category_id' => $cat2->id,
                'is_featured' => false,
                'image' => 'hotwheels-5.jpg',
            ],
            [
                'name' => 'Hot Wheels Lamborghini Sesto Elemento',
                'description' => 'Premium edition of the Lamborghini Sesto Elemento.',
                'price' => 12.99,
                'stock_quantity' => 10,
                'category_id' => $cat3->id,
                'is_featured' => false,
                'image' => 'hotwheels-6.jpg',
            ],
            [
                'name' => 'Hot Wheels Monster Trucks',
                'description' => 'Oversized Hot Wheels trucks for extreme fun.',
                'price' => 8.99,
                'stock_quantity' => 30,
                'category_id' => $cat1->id,
                'is_featured' => false,
                'image' => 'hotwheels-7.jpg',
            ],
            [
                'name' => 'Hot Wheels City Mega Garage',
                'description' => 'A multi-level garage for storing and racing your cars.',
                'price' => 29.99,
                'stock_quantity' => 8,
                'category_id' => $cat2->id,
                'is_featured' => false,
                'image' => 'hotwheels-8.jpg',
            ],
        ];
        foreach ($products as $prod) {
            Product::on((new \App\Services\DatabaseService)->getClientConnection($client))
                ->updateOrCreate(
                    [
                        'slug' => Str::slug($prod['name']),
                        'client_id' => $client->id,
                    ],
                    array_merge($prod, [
                        'is_active' => true,
                        'slug' => Str::slug($prod['name']),
                        'client_id' => $client->id,
                        'sku' => 'HW-' . Str::slug($prod['name']) . '-' . uniqid(),
                    ])
                );
        }

        // 5. Create a coupon in CLIENT DB (tenant-specific)
        Coupon::on((new \App\Services\DatabaseService)->getClientConnection($client))
            ->firstOrCreate([
                'code' => 'HOTWHEELS10',
                'client_id' => $client->id,
            ], [
                'name' => 'Hot Wheels 10% Off',
                'description' => '10% off on all Hot Wheels products',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 0,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'is_active' => true,
                'applicable_categories' => null,
                'applicable_products' => null,
                'excluded_products' => null,
                'first_time_only' => false,
                'status' => 'active',
            ]);
    }
}