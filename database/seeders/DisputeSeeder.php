<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Dispute;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DisputeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all orders with dispute_flag = true
        $disputedOrders = Order::where('dispute_flag', true)->get();

        // dd($disputedOrders);

        foreach ($disputedOrders as $order) {
            try {
                Dispute::factory()->create([
                    'order_id' => $order->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create dispute for Order ID: ' . $order->id . ' - ' . $e->getMessage());
            }
        }
    }

    
}
