<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('delivery_status');
            }
            if (!Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'courier_url')) {
                $table->string('courier_url')->nullable()->after('courier_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $columns[] = 'tracking_number';
            }
            if (Schema::hasColumn('orders', 'courier_name')) {
                $columns[] = 'courier_name';
            }
            if (Schema::hasColumn('orders', 'courier_url')) {
                $columns[] = 'courier_url';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
