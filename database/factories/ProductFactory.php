<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence() ?: 'Test product description',
            'price' => $this->faker->randomFloat(2, 10, 200),
            'sale_price' => null,
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'image' => null,
            'images' => [],
            'is_featured' => false,
            'is_active' => true,
            'category_id' => Category::factory(),
        ];
    }
} 