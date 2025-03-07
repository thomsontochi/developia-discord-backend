<?php

namespace Database\Factories;

use App\Models\Dispute;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dispute>
 */
class DisputeFactory extends Factory
{
     protected $model = Dispute::class;
     
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'under_review', 'resolved', 'closed'];
        $reasons = [
            'Product not received',
            'Product damaged',
            'Wrong item received',
            'Item not as described',
            'Quality issues',
        ];

        return [
            'order_id' => Order::factory(),
            'reason' => $this->faker->randomElement($reasons),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'resolved_at' => function (array $attributes) {
                return $attributes['status'] === 'resolved' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
            'resolution_notes' => function (array $attributes) {
                return $attributes['status'] === 'resolved' ? $this->faker->text() : null;
            },
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}

