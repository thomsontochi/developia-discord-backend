<?php

namespace Database\Seeders;

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
       // First create categories
        Category::factory(12)->create();

        // Then create products with existing categories
        Product::factory(50)->existingCategory()->create();
    }
}
