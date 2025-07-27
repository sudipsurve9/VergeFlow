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
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('title');
                $table->text('review');
                $table->integer('rating');
                $table->json('images')->nullable();
                $table->boolean('is_verified_purchase')->default(false);
                $table->boolean('is_approved')->default(true);
                $table->integer('helpful_votes')->default(0);
                $table->timestamps();
                
                // Add foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
                
                // Add indexes
                $table->index(['product_id', 'is_approved']);
                $table->index(['user_id', 'created_at']);
                $table->index('rating');
            });
        } else {
            // Table exists, ensure all required columns are present
            Schema::table('product_reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('product_reviews', 'title')) {
                    $table->string('title')->after('order_id');
                }
                if (!Schema::hasColumn('product_reviews', 'images')) {
                    $table->json('images')->nullable()->after('rating');
                }
                if (!Schema::hasColumn('product_reviews', 'is_verified_purchase')) {
                    $table->boolean('is_verified_purchase')->default(false)->after('images');
                }
                if (!Schema::hasColumn('product_reviews', 'is_approved')) {
                    $table->boolean('is_approved')->default(true)->after('is_verified_purchase');
                }
                if (!Schema::hasColumn('product_reviews', 'helpful_votes')) {
                    $table->integer('helpful_votes')->default(0)->after('is_approved');
                }
                if (!Schema::hasColumn('product_reviews', 'order_id')) {
                    $table->unsignedBigInteger('order_id')->nullable()->after('product_id');
                }
            });
            
            // Add foreign keys if they don't exist (simplified approach)
            try {
                Schema::table('product_reviews', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
            
            try {
                Schema::table('product_reviews', function (Blueprint $table) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
            
            try {
                Schema::table('product_reviews', function (Blueprint $table) {
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (Schema::hasTable('product_reviews')) {
            Schema::dropIfExists('product_reviews');
        }
    }
    

};
