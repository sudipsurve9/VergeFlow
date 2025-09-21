<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (!Schema::hasColumn('categories', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('image');
                }
            });
            // Try to add a unique index for slug if it doesn't exist yet
            try {
                // Some MySQL versions require raw SQL to check indexes; this is a safe attempt
                $indexes = DB::select("SHOW INDEX FROM `categories`");
                $hasSlugUnique = false;
                foreach ($indexes as $idx) {
                    if (($idx->Column_name ?? '') === 'slug' && ($idx->Non_unique ?? 1) == 0) {
                        $hasSlugUnique = true;
                        break;
                    }
                }
                if (!$hasSlugUnique) {
                    DB::statement('CREATE UNIQUE INDEX categories_slug_unique ON categories (slug)');
                }
            } catch (\Throwable $e) {
                // Ignore if cannot create index (e.g., duplicate slugs). Admin can fix later.
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'slug')) {
                    // Drop unique index if present
                    try {
                        DB::statement('ALTER TABLE categories DROP INDEX categories_slug_unique');
                    } catch (\Throwable $e) {
                        // ignore
                    }
                    $table->dropColumn('slug');
                }
                // Do not drop is_active to avoid losing data
            });
        }
    }
};
