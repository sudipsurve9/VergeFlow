<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                if (!Schema::hasColumn('coupons', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable()->after('id');
                    $table->index('client_id');
                }
                if (!Schema::hasColumn('coupons', 'name')) {
                    $table->string('name')->nullable()->after('code');
                }
                if (!Schema::hasColumn('coupons', 'description')) {
                    $table->text('description')->nullable()->after('name');
                }
                if (!Schema::hasColumn('coupons', 'type')) {
                    $table->enum('type', ['percentage', 'fixed', 'free_shipping'])->default('percentage')->after('description');
                }
                if (!Schema::hasColumn('coupons', 'value')) {
                    $table->decimal('value', 10, 2)->default(0)->after('type');
                }
                if (!Schema::hasColumn('coupons', 'minimum_amount')) {
                    $table->decimal('minimum_amount', 10, 2)->default(0)->after('value');
                }
                if (!Schema::hasColumn('coupons', 'maximum_discount')) {
                    $table->decimal('maximum_discount', 10, 2)->nullable()->after('minimum_amount');
                }
                if (!Schema::hasColumn('coupons', 'usage_limit')) {
                    $table->integer('usage_limit')->nullable()->after('maximum_discount');
                }
                if (!Schema::hasColumn('coupons', 'usage_limit_per_user')) {
                    $table->integer('usage_limit_per_user')->default(1)->after('usage_limit');
                }
                if (!Schema::hasColumn('coupons', 'used_count')) {
                    $table->integer('used_count')->default(0)->after('usage_limit_per_user');
                }
                if (!Schema::hasColumn('coupons', 'start_date')) {
                    $table->date('start_date')->nullable()->after('used_count');
                }
                if (!Schema::hasColumn('coupons', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('coupons', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('end_date');
                }
                if (!Schema::hasColumn('coupons', 'applicable_categories')) {
                    $table->json('applicable_categories')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('coupons', 'applicable_products')) {
                    $table->json('applicable_products')->nullable()->after('applicable_categories');
                }
                if (!Schema::hasColumn('coupons', 'excluded_products')) {
                    $table->json('excluded_products')->nullable()->after('applicable_products');
                }
                if (!Schema::hasColumn('coupons', 'first_time_only')) {
                    $table->boolean('first_time_only')->default(false)->after('excluded_products');
                }
                if (!Schema::hasColumn('coupons', 'status')) {
                    $table->string('status')->default('active')->after('first_time_only');
                }
            });
        }
    }

    public function down(): void
    {
        // No destructive down: we don't drop columns to avoid losing data.
    }
};
