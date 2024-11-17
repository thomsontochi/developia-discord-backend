<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{

    protected $model = Category::class;

    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Electronics',
            'Clothing',
            'Books',
            'Home & Kitchen',
            'Beauty',
            'Sports',
            'Toys & Games',
            'Automotive',
            'Health',
            'Groceries',
            'Beauty & Personal Care',
            'Pets & Animals',
        ];

        $name = $this->faker->unique()->randomElement($categories);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
        ];
    }
}
