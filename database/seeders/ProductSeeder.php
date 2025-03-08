<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //    // First create categories
        //     Category::factory(12)->create();

        //     // Then create products with existing categories
        //     Product::factory(50)->existingCategory()->create();

        // Create products for each vendor
        Vendor::all()->each(function ($vendor) {
            // Create 5-15 products for each vendor
            $numProducts = fake()->numberBetween(5, 15);

            Product::factory()
                ->count($numProducts)
                ->state([
                    'vendor_id' => $vendor->id,
                    'is_visible' => $vendor->status === 'approved'
                ])
                ->create()
                ->each(function ($product) {
                    // Could add product images, variations, etc. here
                });
        });
    }
}
