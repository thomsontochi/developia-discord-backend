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
        Schema::table('vendors', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('verification_documents')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->boolean('has_completed_onboarding')->default(false);
            $table->json('onboarding_step')->default(json_encode(['current_step' => 1, 'completed_steps' => []]));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'verification_documents',
                'approved_at',
                'rejection_reason',
                'has_completed_onboarding',
                'onboarding_step'
            ]);
        });
    }
};
