<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCouponsTableMainDb extends Migration
{
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('description');
            }
            if (!Schema::hasColumn('coupons', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            }
            if (!Schema::hasColumn('coupons', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('discount_value');
            }
            if (!Schema::hasColumn('coupons', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('expires_at');
            }
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('coupons', 'discount_value')) {
                $table->dropColumn('discount_value');
            }
            if (Schema::hasColumn('coupons', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('coupons', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
} 