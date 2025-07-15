<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hot Wheels', 'slug' => 'hot-wheels', 'description' => 'Official Hot Wheels cars and collectibles.'],
            ['name' => 'Limited Edition', 'slug' => 'limited-edition', 'description' => 'Rare and exclusive limited edition cars.'],
            ['name' => 'Muscle Cars', 'slug' => 'muscle-cars', 'description' => 'Classic and modern muscle cars.'],
            ['name' => 'Supercars', 'slug' => 'supercars', 'description' => 'Exotic and high-performance supercars.'],
            ['name' => 'Classics', 'slug' => 'classics', 'description' => 'Vintage and classic car models.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
} 