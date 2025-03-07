<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        $total_amount = $this->faker->numberBetween(2, 10, 1000);

        return [
            'customer_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'status' => $this->faker->randomElement($statuses),
            'total_amount' => $total_amount,
            'dispute_flag' => $this->faker->boolean(20), // 20% chance of having a dispute
            // 'shipping_address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'stripe']),
            'notes' => $this->faker->text(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];

    }

    // Different order states
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    public function disputed()
    {
        return $this->state(function (array $attributes) {
            return [
                'dispute_flag' => true,
            ];
        });
    }


}
