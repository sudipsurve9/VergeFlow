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
        Schema::connection('main')->table('clients', function (Blueprint $table) {
            $table->string('status', 50)->default('active')->after('theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('main')->table('clients', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
