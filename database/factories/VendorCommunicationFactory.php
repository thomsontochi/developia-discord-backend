<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\VendorCommunication;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorCommunicationFactory extends Factory
{
    protected $model = VendorCommunication::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['email', 'notification', 'message']);
        $created_at = $this->faker->dateTimeThisMonth();
        
        return [
            'vendor_id' => Vendor::factory(),
            'type' => $type,
            'subject' => $this->getSubjectByType($type),
            'content' => $this->faker->paragraph(),
            'read_at' => $this->faker->randomElement([null, $created_at]),
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ];
    }

    private function getSubjectByType($type): string
    {
        $subjects = [
            'email' => [
                'Welcome to Our Platform',
                'Account Verification Required',
                'Order Update',
                'Payment Processed',
                'Important Notice'
            ],
            'notification' => [
                'New Order Received',
                'Stock Alert',
                'Payment Received',
                'Customer Review',
                'Account Alert'
            ],
            'message' => [
                'Support Request',
                'Account Question',
                'Order Inquiry',
                'Payment Question',
                'General Inquiry'
            ]
        ];

        return $this->faker->randomElement($subjects[$type]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null
        ]);
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'email'
        ]);
    }

    public function notification(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'notification'
        ]);
    }

    public function message(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'message'
        ]);
    }
}