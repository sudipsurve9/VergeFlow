<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if table doesn't exist
        if (!Schema::hasTable('products')) {
            return;
        }
        
        // Check if column already exists (either 'featured' or 'is_featured')
        if (Schema::hasColumn('products', 'featured') || Schema::hasColumn('products', 'is_featured')) {
            return;
        }
        
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('sale_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'featured')) {
                $table->dropColumn('featured');
            }
        });
    }
};
