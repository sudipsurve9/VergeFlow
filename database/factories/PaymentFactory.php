<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'payment_method' => 'cod',
            'transaction_id' => $this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'currency' => 'INR',
            'status' => 'completed',
            'payment_data' => null,
            'failure_reason' => null,
            'paid_at' => now(),
        ];
    }
} 