<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

try {
    // Set up client database connection manually
    Config::set('database.connections.client_1', [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => 'vergeflow_client_1',
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
    ]);
    
    echo "🔍 Inspecting addresses table structure in vergeflow_client_1\n";
    echo "=========================================================\n";
    
    // Get complete table structure
    $columns = DB::connection('client_1')->select('DESCRIBE addresses');
    
    echo "📋 Complete addresses table structure:\n";
    foreach ($columns as $column) {
        $null = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column->Default ? "DEFAULT '{$column->Default}'" : '';
        echo sprintf("   %-25s %-20s %-10s %s\n", 
            $column->Field, 
            $column->Type, 
            $null, 
            $default
        );
    }
    
    // Check if table has any data
    $count = DB::connection('client_1')->table('addresses')->count();
    echo "\n📊 Current records in addresses table: {$count}\n";
    
    // Show sample data if any exists
    if ($count > 0) {
        $sample = DB::connection('client_1')->table('addresses')->first();
        echo "\n📝 Sample record:\n";
        foreach ($sample as $field => $value) {
            echo "   {$field}: {$value}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
