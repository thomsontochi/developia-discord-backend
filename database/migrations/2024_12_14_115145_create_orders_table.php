<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('status')->default('pending');
            $table->decimal('total_amount', 10,2);
            $table->boolean('dispute_flag')->default(false);
            $table->string('billing_address')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_method');
            $table->string('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
