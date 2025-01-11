<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 verified and approved vendors
        Vendor::factory()
            ->count(20)
            ->create();

        // Create 5 unverified vendors
        Vendor::factory()
            ->count(5)
            ->unverified()
            ->create();

        // Create 5 verified but unapproved vendors
        Vendor::factory()
            ->count(5)
            ->unapproved()
            ->create();

    }
}
