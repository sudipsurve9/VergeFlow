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
        Schema::table('addresses', function (Blueprint $table) {
            // Add new Amazon-style fields
            $table->string('label')->nullable()->after('type');
            $table->string('landmark')->nullable()->after('address_line2');
            $table->boolean('is_default_shipping')->default(false)->after('postal_code');
            $table->boolean('is_default_billing')->default(false)->after('is_default_shipping');
            $table->text('delivery_instructions')->nullable()->after('is_default_billing');
            $table->enum('address_type', ['shipping', 'billing', 'both'])->default('both')->after('delivery_instructions');
            $table->boolean('is_verified')->default(false)->after('address_type');
            
            // Modify existing fields
            $table->enum('type', ['home', 'work', 'other'])->default('home')->change();
            
            // Remove old field if it exists
            $table->dropColumn('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'label',
                'landmark', 
                'is_default_shipping',
                'is_default_billing',
                'delivery_instructions',
                'address_type',
                'is_verified'
            ]);
            
            // Restore old field
            $table->boolean('is_default')->default(false);
        });
    }
};
