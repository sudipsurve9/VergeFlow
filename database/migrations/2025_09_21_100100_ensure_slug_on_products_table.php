<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (!Schema::hasColumn('products', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable()->after('is_active');
                    $table->index('client_id');
                }
                if (!Schema::hasColumn('products', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('images');
                }
                if (!Schema::hasColumn('products', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('is_featured');
                }
            });
            // Ensure unique indexes where possible (best effort)
            try {
                $indexes = DB::select('SHOW INDEX FROM `products`');
                $hasSlugUnique = false; $hasSkuUnique = false;
                foreach ($indexes as $idx) {
                    $col = $idx->Column_name ?? '';
                    $non = $idx->Non_unique ?? 1;
                    if ($col === 'slug' && (int)$non === 0) $hasSlugUnique = true;
                    if ($col === 'sku' && (int)$non === 0) $hasSkuUnique = true;
                }
                if (!$hasSlugUnique) {
                    DB::statement('CREATE UNIQUE INDEX products_slug_unique ON products (slug)');
                }
            } catch (\Throwable $e) {
                // ignore if duplicates prevent unique creation; can be fixed manually later
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'slug')) {
                    try { DB::statement('ALTER TABLE products DROP INDEX products_slug_unique'); } catch (\Throwable $e) {}
                    $table->dropColumn('slug');
                }
                // Do not drop client_id/is_featured/is_active to avoid data loss
            });
        }
    }
};
