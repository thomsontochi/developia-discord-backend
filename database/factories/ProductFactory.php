<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // public function definition(): array
    // {
    //     $products = [
    //         // Electronics
    //         'iPhone 14 Pro Max' => ['price' => 1099, 'category' => 'Electronics'],
    //         'Samsung Galaxy S23 Ultra' => ['price' => 999, 'category' => 'Electronics'],
    //         'MacBook Pro 16-inch' => ['price' => 1999, 'category' => 'Electronics'],
    //         'Sony WH-1000XM4 Headphones' => ['price' => 299, 'category' => 'Electronics'],
    //         'iPad Air 5th Generation' => ['price' => 599, 'category' => 'Electronics'],
    //         'Nintendo Switch OLED' => ['price' => 349, 'category' => 'Electronics'],
            
    //         // Clothing
    //         'Nike Air Max 270' => ['price' => 150, 'category' => 'Clothing'],
    //         'Adidas Ultraboost 22' => ['price' => 180, 'category' => 'Clothing'],
    //         'Levi\'s 501 Original Jeans' => ['price' => 98, 'category' => 'Clothing'],
    //         'North Face Thermoball Jacket' => ['price' => 199, 'category' => 'Clothing'],
    //         'Ray-Ban Aviator Sunglasses' => ['price' => 154, 'category' => 'Clothing'],
            
    //         // Home & Kitchen
    //         'Ninja Foodi 9-in-1 Deluxe XL' => ['price' => 249, 'category' => 'Home & Kitchen'],
    //         'Dyson V15 Detect Vacuum' => ['price' => 699, 'category' => 'Home & Kitchen'],
    //         'KitchenAid Stand Mixer' => ['price' => 379, 'category' => 'Home & Kitchen'],
    //         'Nespresso Vertuo Coffee Maker' => ['price' => 199, 'category' => 'Home & Kitchen'],
            
    //         // Beauty
    //         'Olaplex Hair Perfector No 3' => ['price' => 28, 'category' => 'Beauty'],
    //         'Dyson Airwrap Styler' => ['price' => 599, 'category' => 'Beauty'],
    //         'La Mer Moisturizing Cream' => ['price' => 190, 'category' => 'Beauty'],
    //         'MAC Ruby Woo Lipstick' => ['price' => 19, 'category' => 'Beauty'],
            
    //         // Sports
    //         'Peloton Bike+' => ['price' => 2495, 'category' => 'Sports'],
    //         'Yeti Tundra 45 Cooler' => ['price' => 325, 'category' => 'Sports'],
    //         'Wilson NFL Official Football' => ['price' => 45, 'category' => 'Sports'],
    //         'Nike Yoga Mat' => ['price' => 60, 'category' => 'Sports']
    //     ];

    //     $name = array_rand($products);
    //     $productInfo = $products[$name];

    //     // Add a unique identifier to the slug
    //     $uniqueId = fake()->unique()->numberBetween(1000, 9999);
    //     $slug = Str::slug($name) . '-' . $uniqueId;
        
    //     return [
    //         'name' => $name,
    //         'description' => fake()->paragraphs(2, true),
    //         'price' => $productInfo['price'],
    //         'stock' => fake()->numberBetween(0, 100),
    //         'category_id' => Category::factory(), // This will create a category if none exists
    //         'status' => fake()->randomElement(['active', 'inactive']),
    //         'image' => 'https://
    //         /640/480?random=' . fake()->numberBetween(1, 1000),
    //          'slug' => $slug,
    //         'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
    //         'updated_at' => now(),
    //     ];
    // }

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 1000);
        $hasDiscount = $this->faker->boolean(20);

        return [
            'name' => $this->faker->productName,
            'description' => $this->faker->paragraphs(2, true),
            'price' => $price,
            'original_price' => $hasDiscount ? $price * 1.2 : null,
            'discount_percentage' => $hasDiscount ? 20 : null,
            'discount_ends_at' => $hasDiscount ? now()->addDays(7) : null,
            'stock' => $this->faker->numberBetween(0, 100),
            'vendor_id' => Vendor::factory(),
            'category_id' => Category::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'is_visible' => true,
            'views_count' => $this->faker->numberBetween(0, 1000),
            'sales_count' => $this->faker->numberBetween(0, 100),
            'average_rating' => $this->faker->randomFloat(1, 3.5, 5),
            'review_count' => $this->faker->numberBetween(0, 50),
            'specifications' => [
                'brand' => $this->faker->company,
                'model' => $this->faker->bothify('##??###'),
                'warranty' => $this->faker->randomElement(['1 year', '2 years', 'No warranty'])
            ],
            'shipping_info' => [
                'weight' => $this->faker->randomFloat(2, 0.1, 10),
                'dimensions' => [
                    'length' => $this->faker->numberBetween(1, 100),
                    'width' => $this->faker->numberBetween(1, 100),
                    'height' => $this->faker->numberBetween(1, 100)
                ]
            ],
            'is_featured' => $this->faker->boolean(10)
        ];
    }

    // Use existing category
    public function existingCategory()
    {
        return $this->state(function (array $attributes) {
            return [
                'category_id' => Category::inRandomOrder()->first()?->id
            ];
        });
    }

    public function outOfStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => 0,
                'status' => 'inactive',
            ];
        });
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'stock' => fake()->numberBetween(10, 100),
            ];
        });
    }
}
