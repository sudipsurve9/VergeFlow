<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Call the new seeders
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ApiTypeSeeder::class,
            Vault64ClientSeeder::class,
            Vault64HotWheelsSeeder::class,
        ]);
    }
}
