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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained(); // This requires vendors table to exist
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('sizes')->nullable();
            $table->json('colors')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->datetime('discount_ends_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->json('specifications')->nullable();
            $table->json('shipping_info')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->timestamp('last_sold_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
