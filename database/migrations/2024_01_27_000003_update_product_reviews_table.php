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
        Schema::table('product_reviews', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('product_reviews', 'title')) {
                $table->string('title')->after('rating');
            }
            
            if (!Schema::hasColumn('product_reviews', 'is_verified_purchase')) {
                $table->boolean('is_verified_purchase')->default(false)->after('review');
            }
            
            if (!Schema::hasColumn('product_reviews', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('is_verified_purchase');
            }
            
            if (!Schema::hasColumn('product_reviews', 'helpful_count')) {
                $table->integer('helpful_count')->default(0)->after('is_approved');
            }
            
            if (!Schema::hasColumn('product_reviews', 'images')) {
                $table->json('images')->nullable()->after('helpful_count');
            }
            
            if (!Schema::hasColumn('product_reviews', 'order_id')) {
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null')->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn(['title', 'is_verified_purchase', 'is_approved', 'helpful_count', 'images', 'order_id']);
        });
    }
};
