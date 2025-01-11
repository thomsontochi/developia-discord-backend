<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'store_name' => $this->faker->company(),
            'store_description' => $this->faker->paragraph(),
            'business_category' => $this->faker->randomElement(['Electronics', 'Fashion', 'Food', 'Home & Garden', 'Health & Beauty']),
            'address' => $this->faker->address(),
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '17:00'],
                'tuesday' => ['open' => '09:00', 'close' => '17:00'],
                'wednesday' => ['open' => '09:00', 'close' => '17:00'],
                'thursday' => ['open' => '09:00', 'close' => '17:00'],
                'friday' => ['open' => '09:00', 'close' => '17:00'],
                'saturday' => ['open' => '10:00', 'close' => '15:00'],
                'sunday' => ['open' => 'closed', 'close' => 'closed'],
            ],
            'payment_details' => [
                'bank_name' => $this->faker->company(),
                'account_number' => $this->faker->bankAccountNumber(),
                'account_name' => $this->faker->name(),
            ],
            'store_logo' => null,
            'is_verified' => $this->faker->boolean(70),
            'is_approved' => $this->faker->boolean(80),
            'email_verified_at' => $this->faker->randomElement([now(), null]),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'email_verified_at' => null,
        ]);
    }

    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }
}
