<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecentlyViewed;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AmazonStyleFeaturesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create demo categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Latest electronic gadgets and devices'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel for all'],
            ['name' => 'Books', 'description' => 'Books and literature'],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies'],
            ['name' => 'Sports', 'description' => 'Sports equipment and accessories'],
            ['name' => 'Beauty', 'description' => 'Beauty and personal care products'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(['name' => $categoryData['name']], $categoryData);
        }

        // Create demo users
        $demoUsers = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@vergeflow.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        ];

        foreach ($demoUsers as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Create demo products
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and 30-hour battery life.',
                'short_description' => 'Premium wireless headphones with excellent sound quality.',
                'price' => 199.99,
                'category_id' => Category::where('name', 'Electronics')->first()->id,
                'stock_quantity' => 50,
                'status' => 'active',
                'brand' => 'TechSound'
            ],
            [
                'name' => 'Organic Cotton T-Shirt',
                'description' => 'Comfortable 100% organic cotton t-shirt available in multiple colors and sizes.',
                'short_description' => 'Eco-friendly organic cotton t-shirt.',
                'price' => 29.99,
                'category_id' => Category::where('name', 'Clothing')->first()->id,
                'stock_quantity' => 100,
                'status' => 'active',
                'brand' => 'EcoWear'
            ],
            [
                'name' => 'The Art of Programming',
                'description' => 'Comprehensive guide to modern programming techniques and best practices.',
                'short_description' => 'Essential programming book for developers.',
                'price' => 49.99,
                'category_id' => Category::where('name', 'Books')->first()->id,
                'stock_quantity' => 25,
                'status' => 'active',
                'brand' => 'TechBooks'
            ],
            [
                'name' => 'Smart LED Light Bulbs (4-Pack)',
                'description' => 'WiFi-enabled smart LED bulbs with color changing and dimming capabilities.',
                'short_description' => 'Smart home LED lighting solution.',
                'price' => 79.99,
                'category_id' => Category::where('name', 'Home & Garden')->first()->id,
                'stock_quantity' => 30,
                'status' => 'active',
                'brand' => 'SmartHome'
            ],
            [
                'name' => 'Professional Yoga Mat',
                'description' => 'Non-slip yoga mat with excellent grip and cushioning for all yoga practices.',
                'short_description' => 'High-quality non-slip yoga mat.',
                'price' => 39.99,
                'category_id' => Category::where('name', 'Sports')->first()->id,
                'stock_quantity' => 75,
                'status' => 'active',
                'brand' => 'YogaPro'
            ],
            [
                'name' => 'Vitamin C Serum',
                'description' => 'Anti-aging vitamin C serum with hyaluronic acid for radiant skin.',
                'short_description' => 'Brightening vitamin C facial serum.',
                'price' => 24.99,
                'category_id' => Category::where('name', 'Beauty')->first()->id,
                'stock_quantity' => 60,
                'status' => 'active',
                'brand' => 'GlowSkin'
            ]
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(['name' => $productData['name']], $productData);
        }

        // Create demo orders
        $users = User::where('role', 'user')->get();
        $products = Product::all();

        foreach ($users->take(3) as $user) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => 'delivered',
                    'payment_status' => 'paid',
                    'payment_method' => 'stripe',
                    'created_at' => $faker->dateTimeBetween('-3 months', '-1 week'),
                    'updated_at' => $faker->dateTimeBetween('-2 weeks', 'now')
                ]);

                $orderTotal = 0;
                $orderProducts = $products->random(rand(1, 3));

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 2);
                    $price = $product->price;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $price * $quantity
                    ]);

                    $orderTotal += $price * $quantity;
                }

                $order->update(['total_amount' => $orderTotal]);
            }
        }

        // Create demo reviews
        $reviewTitles = [
            'Excellent product!',
            'Great value for money',
            'Highly recommended',
            'Good quality',
            'Amazing features',
            'Perfect for my needs',
            'Outstanding performance',
            'Love this product',
            'Exceeded expectations',
            'Fantastic purchase'
        ];

        $reviewTexts = [
            'This product has exceeded my expectations in every way. The quality is outstanding and it works perfectly.',
            'I\'ve been using this for a few weeks now and I\'m very impressed with the build quality and performance.',
            'Great product at a reasonable price. Would definitely recommend to others.',
            'The features are exactly what I was looking for. Very satisfied with this purchase.',
            'Excellent customer service and fast shipping. The product itself is top-notch.',
            'This has made my life so much easier. The design is sleek and it works flawlessly.',
            'I did a lot of research before buying this and I\'m glad I chose this one. No regrets!',
            'The quality is impressive for the price point. Very happy with this purchase.',
            'Works exactly as described. The packaging was also very professional.',
            'I would buy this again and recommend it to friends and family.'
        ];

        $orders = Order::with('orderItems.product')->get();
        
        foreach ($orders as $order) {
            foreach ($order->orderItems as $orderItem) {
                // 70% chance of having a review
                if (rand(1, 100) <= 70) {
                    ProductReview::create([
                        'user_id' => $order->user_id,
                        'product_id' => $orderItem->product_id,
                        'order_id' => $order->id,
                        'rating' => rand(3, 5), // Mostly positive reviews
                        'title' => $faker->randomElement($reviewTitles),
                        'review' => $faker->randomElement($reviewTexts),
                        'is_verified_purchase' => true,
                        'is_approved' => rand(1, 100) <= 85, // 85% approved
                        'helpful_count' => rand(0, 15),
                        'created_at' => $faker->dateTimeBetween($order->created_at, 'now')
                    ]);
                }
            }
        }

        // Create recently viewed records
        foreach ($users as $user) {
            $viewedProducts = $products->random(rand(3, 8));
            foreach ($viewedProducts as $product) {
                RecentlyViewed::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'viewed_at' => $faker->dateTimeBetween('-1 week', 'now')
                ]);
            }
        }

        // Create wishlist items
        foreach ($users as $user) {
            $wishlistProducts = $products->random(rand(2, 5));
            foreach ($wishlistProducts as $product) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ]);
            }
        }

        $this->command->info('Amazon-style features demo data seeded successfully!');
        $this->command->info('Demo users created:');
        $this->command->info('- john@example.com (password: password)');
        $this->command->info('- sarah@example.com (password: password)');
        $this->command->info('- mike@example.com (password: password)');
        $this->command->info('- emily@example.com (password: password)');
        $this->command->info('- admin@vergeflow.com (password: admin123) - Admin User');
    }
}
