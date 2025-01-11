<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dispute;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\VendorSeeder;
use Database\Seeders\DisputeSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CategorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'vendlyAdmin',
            'email' => 'vendlyAdmin@example.com',
            //  password = password
        ]);

        $this->call([
            ProductSeeder::class,
            OrderSeeder::class,
            DisputeSeeder::class,
            VendorSeeder::class,
        ]);
    }
}
