<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiLog;

class ApiLogSeeder extends Seeder
{
    public function run()
    {
        ApiLog::factory()->count(10)->create();
    }
} 