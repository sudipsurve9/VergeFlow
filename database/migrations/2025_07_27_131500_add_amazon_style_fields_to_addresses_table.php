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
            // Add new Amazon-style fields only if they don't exist
            if (!Schema::hasColumn('addresses', 'label')) {
                $table->string('label')->nullable()->after('type');
            }
            if (!Schema::hasColumn('addresses', 'landmark')) {
                $table->string('landmark')->nullable()->after('address_line2');
            }
            if (!Schema::hasColumn('addresses', 'is_default_shipping')) {
                $table->boolean('is_default_shipping')->default(false)->after('postal_code');
            }
            if (!Schema::hasColumn('addresses', 'is_default_billing')) {
                $table->boolean('is_default_billing')->default(false)->after('is_default_shipping');
            }
            if (!Schema::hasColumn('addresses', 'delivery_instructions')) {
                $table->text('delivery_instructions')->nullable()->after('is_default_billing');
            }
            if (!Schema::hasColumn('addresses', 'address_type')) {
                $table->enum('address_type', ['shipping', 'billing', 'both'])->default('both')->after('delivery_instructions');
            }
            if (!Schema::hasColumn('addresses', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('address_type');
            }
            
            // Ensure phone column exists
            if (!Schema::hasColumn('addresses', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }
        });
        
        // Handle type field modification separately
        if (Schema::hasColumn('addresses', 'type')) {
            // Check current type column definition and modify if needed
            Schema::table('addresses', function (Blueprint $table) {
                $table->enum('type', ['home', 'work', 'other'])->default('home')->change();
            });
        }
        
        // Remove old field if it exists
        if (Schema::hasColumn('addresses', 'is_default')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->dropColumn('is_default');
            });
        }
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
