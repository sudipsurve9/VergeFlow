<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $products = [
            [
                'name' => 'Hot Wheels Redline Racer',
                'slug' => 'hot-wheels-redline-racer',
                'sku' => 'HW-RED-001',
                'description' => 'A classic Hot Wheels Redline Racer collectible.',
                'price' => 499.00,
                'stock_quantity' => 20,
                'category_slug' => 'hot-wheels',
                'is_featured' => true,
                'image' => 'hotwheels-1.jpg',
            ],
            [
                'name' => 'Limited Edition Gold Car',
                'slug' => 'limited-edition-gold-car',
                'sku' => 'HW-LTD-001',
                'description' => 'Rare limited edition gold-plated Hot Wheels car.',
                'price' => 1999.00,
                'stock_quantity' => 5,
                'category_slug' => 'limited-edition',
                'is_featured' => true,
                'image' => 'hotwheels-2.jpg',
            ],
            [
                'name' => 'Muscle Car Thunderbolt',
                'slug' => 'muscle-car-thunderbolt',
                'sku' => 'HW-MUS-001',
                'description' => 'Powerful muscle car with authentic details.',
                'price' => 799.00,
                'stock_quantity' => 10,
                'category_slug' => 'muscle-cars',
                'is_featured' => true,
                'image' => 'hotwheels-3.jpg',
            ],
            [
                'name' => 'Supercar Lightning',
                'slug' => 'supercar-lightning',
                'sku' => 'HW-SUP-001',
                'description' => 'Exotic supercar with premium finish.',
                'price' => 1499.00,
                'stock_quantity' => 8,
                'category_slug' => 'supercars',
                'is_featured' => true,
                'image' => 'hotwheels-4.jpg',
            ],
            [
                'name' => 'Classic Roadster',
                'slug' => 'classic-roadster',
                'sku' => 'HW-CLS-001',
                'description' => 'Vintage classic roadster for collectors.',
                'price' => 599.00,
                'stock_quantity' => 12,
                'category_slug' => 'classics',
                'is_featured' => true,
                'image' => 'hotwheels-5.jpg',
            ],
        ];

        foreach ($products as $prod) {
            $category = $categories->where('slug', $prod['category_slug'])->first();
            if ($category) {
                Product::updateOrCreate([
                    'slug' => $prod['slug']
                ], [
                    'name' => $prod['name'],
                    'sku' => $prod['sku'],
                    'description' => $prod['description'],
                    'price' => $prod['price'],
                    'stock_quantity' => $prod['stock_quantity'],
                    'category_id' => $category->id,
                    'is_featured' => $prod['is_featured'],
                    'image' => $prod['image'],
                ]);
            }
        }
    }
} 