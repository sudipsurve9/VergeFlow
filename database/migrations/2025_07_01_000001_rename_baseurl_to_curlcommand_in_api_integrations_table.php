<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('api_integrations', function (Blueprint $table) {
            if (!Schema::hasColumn('api_integrations', 'curl_command')) {
                $table->text('curl_command')->nullable()->after('password');
            }
            if (Schema::hasColumn('api_integrations', 'base_url')) {
                $table->dropColumn('base_url');
            }
        });
    }

    public function down()
    {
        Schema::table('api_integrations', function (Blueprint $table) {
            if (!Schema::hasColumn('api_integrations', 'base_url')) {
                $table->string('base_url')->nullable()->after('password');
            }
            if (Schema::hasColumn('api_integrations', 'curl_command')) {
                $table->dropColumn('curl_command');
            }
        });
    }
}; 