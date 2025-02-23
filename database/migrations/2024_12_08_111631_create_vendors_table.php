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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('store_name')->nullable();
            $table->text('store_description')->nullable();
            $table->string('business_category')->nullable();
            $table->string('address')->nullable();
            $table->json('business_hours')->nullable();
            $table->json('payment_details')->nullable();
            $table->string('store_logo')->nullable();
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending');
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->json('verification_documents')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('has_completed_onboarding')->default(false);
            $table->json('onboarding_step')->default(json_encode(['current_step' => 1, 'completed_steps' => []]));
           
            $table->integer('total_products')->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('fulfillment_rate', 5, 2)->default(0);
            $table->decimal('return_rate', 5, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->decimal('pending_payout', 10, 2)->default(0);
            $table->timestamps();



         
           
          
          
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
