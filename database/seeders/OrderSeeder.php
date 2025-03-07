<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users and vendors if they don't exist
        $users = User::count() < 5 ? User::factory(5)->create() : User::all();
        $vendors = Vendor::count() < 3 ? Vendor::factory(3)->create() : Vendor::all();

        // Create regular orders
        Order::factory(50)->recycle($users)->recycle($vendors)->create();

        // Create some disputed orders
        Order::factory(10)
            ->disputed()
            ->recycle($users)
            ->recycle($vendors)
            ->create();

        // Create some completed orders
        Order::factory(20)
            ->completed()
            ->recycle($users)
            ->recycle($vendors)
            ->create();
    }

}
