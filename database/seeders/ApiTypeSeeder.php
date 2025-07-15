<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiType;

class ApiTypeSeeder extends Seeder
{
    public function run()
    {
        $apiTypes = [
            [
                'name' => 'shiprocket',
                'icon' => 'fas fa-rocket',
                'description' => 'Shiprocket API integration',
            ],
            [
                'name' => 'delhivery',
                'icon' => 'fas fa-truck',
                'description' => 'Delhivery API integration',
            ],
            [
                'name' => 'other',
                'icon' => 'fas fa-plug',
                'description' => 'Other/Custom API integration',
            ],
        ];
        foreach ($apiTypes as $type) {
            ApiType::firstOrCreate(
                ['name' => $type['name']],
                [
                    'icon' => $type['icon'],
                    'description' => $type['description'],
                ]
            );
        }
    }
} 