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
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing role enum
            $table->dropColumn('role');
            // Add the new role enum with updated values
            $table->enum('role', ['super_admin', 'admin', 'moderator', 'customer'])->default('customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->enum('role', ['admin', 'customer'])->default('customer');
        });
    }
};
