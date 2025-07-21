<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFeaturedToIsFeaturedInProductsTable extends Migration
{
    public function up()
    {
        // No operation needed. Migration intentionally left blank to avoid MariaDB renameColumn issues.
    }

    public function down()
    {
        // No operation needed. Migration intentionally left blank.
    }
} 