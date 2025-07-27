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
        // Only create table if it doesn't exist
        if (!Schema::hasTable('recently_viewed')) {
            Schema::create('recently_viewed', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->string('session_id')->nullable();
                $table->timestamp('viewed_at')->useCurrent();
                $table->timestamps();
                
                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                
                // Indexes for performance
                $table->index(['user_id', 'viewed_at']);
                $table->index(['session_id', 'viewed_at']);
                $table->index(['product_id']);
                
                // Unique constraint to prevent duplicates
                $table->unique(['user_id', 'product_id'], 'unique_user_product');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recently_viewed');
    }
};
