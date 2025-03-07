<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorActivityLog;
use App\Models\VendorCommunication;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Create approved vendors
        Vendor::factory()
            ->count(15)
            ->approved()
            ->create()
            ->each(function ($vendor) {
                // Add activity logs
                VendorActivityLog::factory()->count(5)->create([
                    'vendor_id' => $vendor->id
                ]);

                // Add communications
                VendorCommunication::factory()->count(3)->create([
                    'vendor_id' => $vendor->id
                ]);
            });

        // Create pending vendors
        Vendor::factory()
            ->count(8)
            ->pending()
            ->create()
            ->each(function ($vendor) {
                VendorActivityLog::factory()->count(2)->create([
                    'vendor_id' => $vendor->id
                ]);
            });

        // Create suspended vendors
        Vendor::factory()
            ->count(5)
            ->suspended()
            ->create()
            ->each(function ($vendor) {
                VendorActivityLog::factory()->count(3)->create([
                    'vendor_id' => $vendor->id
                ]);
            });
    }
}