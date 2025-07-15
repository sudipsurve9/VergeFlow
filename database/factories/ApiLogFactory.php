<?php

namespace Database\Factories;

use App\Models\ApiLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiLogFactory extends Factory
{
    protected $model = ApiLog::class;

    public function definition()
    {
        return [
            'api_type' => $this->faker->randomElement([
                ApiLog::TYPE_SHIPROCKET,
                ApiLog::TYPE_DELHIVERY,
                ApiLog::TYPE_OTHER
            ]),
            'endpoint' => $this->faker->url(),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'request_data' => ['foo' => 'bar'],
            'response_data' => ['result' => 'ok'],
            'status_code' => $this->faker->numberBetween(200, 500),
            'status' => $this->faker->randomElement([
                ApiLog::STATUS_PENDING,
                ApiLog::STATUS_SUCCESS,
                ApiLog::STATUS_FAILED,
                ApiLog::STATUS_ERROR
            ]),
            'error_message' => $this->faker->optional()->sentence(),
            'response_time_ms' => $this->faker->numberBetween(50, 5000),
            'user_agent' => $this->faker->userAgent(),
            'ip_address' => $this->faker->ipv4(),
            'created_by' => $this->faker->userName(),
        ];
    }
} 