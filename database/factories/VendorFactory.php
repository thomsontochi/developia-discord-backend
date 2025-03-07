<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'approved', 'suspended', 'rejected']);
        $created_at = $this->faker->dateTimeBetween('-1 year', 'now');
        
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
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
            'status' => $status,
            'commission_rate' => $this->faker->randomFloat(2, 5, 15),
            'verification_documents' => [
                'business_license' => 'documents/license.pdf',
                'tax_certificate' => 'documents/tax.pdf',
                'id_proof' => 'documents/id.pdf'
            ],
            'is_verified' => $status === 'approved',
            'email_verified_at' => $status === 'approved' ? now() : null,
            'approved_at' => $status === 'approved' ? now() : null,
            'rejection_reason' => $status === 'rejected' ? $this->faker->sentence() : null,
            'has_completed_onboarding' => $status === 'approved',
            'onboarding_step' => [
                'current_step' => $status === 'approved' ? 4 : $this->faker->numberBetween(1, 3),
                'completed_steps' => $status === 'approved' ? [1,2,3,4] : []
            ],
            
            // Performance metrics
            'total_products' => $this->faker->numberBetween(0, 100),
            'total_sales' => $this->faker->randomFloat(2, 0, 100000),
            'average_rating' => $this->faker->randomFloat(1, 3.0, 5.0),
            'total_reviews' => $this->faker->numberBetween(0, 500),
            'fulfillment_rate' => $this->faker->randomFloat(2, 80, 100),
            'return_rate' => $this->faker->randomFloat(2, 0, 10),
            'current_balance' => $this->faker->randomFloat(2, 0, 10000),
            'pending_payout' => $this->faker->randomFloat(2, 0, 5000),
            
            'created_at' => $created_at,
            'updated_at' => $this->faker->dateTimeBetween($created_at, 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'is_verified' => false,
            'email_verified_at' => null,
            'approved_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'is_verified' => true,
            'email_verified_at' => now(),
            'approved_at' => now(),
            'has_completed_onboarding' => true,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}