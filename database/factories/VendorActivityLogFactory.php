<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\VendorActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorActivityLogFactory extends Factory
{
    protected $model = VendorActivityLog::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'action' => $this->faker->randomElement([
                'account_created',
                'profile_updated',
                'store_updated',
                'product_added',
                'order_fulfilled',
                'status_updated',
                'document_uploaded',
                'payment_received',
                'settings_changed',
                'login_detected'
            ]),
            'details' => [
                'ip_address' => $this->faker->ipv4,
                'user_agent' => $this->faker->userAgent,
                'metadata' => [
                    'location' => $this->faker->city,
                    'device' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
                    'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari'])
                ]
            ],
            'created_at' => $this->faker->dateTimeThisMonth(),
            'updated_at' => $this->faker->dateTimeThisMonth(),
        ];
    }
}