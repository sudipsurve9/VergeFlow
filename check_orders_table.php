<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

Config::set('database.connections.client_1', [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'vergeflow_client_1',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
]);

echo "Orders table structure:\n";
$columns = DB::connection('client_1')->select('DESCRIBE orders');
foreach($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}
