<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['fixed', 'percentage', 'free_shipping']),
            'value' => $this->faker->numberBetween(10, 100),
            'minimum_amount' => $this->faker->numberBetween(100, 500),
            'maximum_discount' => $this->faker->numberBetween(50, 200),
            'usage_limit' => $this->faker->numberBetween(1, 10),
            'usage_limit_per_user' => $this->faker->numberBetween(1, 5),
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(30),
            'is_active' => true,
            'first_time_only' => false,
        ];
    }
} 