<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_type'); // shiprocket, delhivery, other
            $table->string('endpoint'); // The API endpoint that was called
            $table->string('method')->default('GET'); // HTTP method (GET, POST, etc.)
            $table->json('request_data')->nullable(); // Request payload/parameters
            $table->json('response_data')->nullable(); // Response data
            $table->integer('status_code')->nullable(); // HTTP status code
            $table->string('status')->default('pending'); // success, failed, error
            $table->text('error_message')->nullable(); // Error message if any
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->string('user_agent')->nullable(); // User agent if applicable
            $table->string('ip_address')->nullable(); // IP address of the request
            $table->string('created_by')->nullable(); // Who triggered the API call
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['api_type', 'status']);
            $table->index(['created_at']);
            $table->index(['status_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_logs');
    }
}
