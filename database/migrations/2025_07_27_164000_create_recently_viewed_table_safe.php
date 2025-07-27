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
                $table->string('session_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->timestamp('viewed_at');
                $table->timestamps();
                
                // Add foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                
                // Add indexes for better performance
                $table->index(['user_id', 'viewed_at']);
                $table->index(['session_id', 'viewed_at']);
                $table->index('product_id');
            });
        } else {
            // Table exists, ensure all required columns are present
            Schema::table('recently_viewed', function (Blueprint $table) {
                if (!Schema::hasColumn('recently_viewed', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('recently_viewed', 'session_id')) {
                    $table->string('session_id')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('recently_viewed', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->after('session_id');
                }
                if (!Schema::hasColumn('recently_viewed', 'viewed_at')) {
                    $table->timestamp('viewed_at')->after('product_id');
                }
                if (!Schema::hasColumn('recently_viewed', 'created_at')) {
                    $table->timestamps();
                }
            });
            
            // Add foreign keys if they don't exist (simplified approach)
            try {
                Schema::table('recently_viewed', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
            
            try {
                Schema::table('recently_viewed', function (Blueprint $table) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        if (Schema::hasTable('recently_viewed')) {
            Schema::dropIfExists('recently_viewed');
        }
    }
    

};
