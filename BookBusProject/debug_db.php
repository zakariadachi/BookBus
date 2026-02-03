<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Default DB: " . Config::get('database.default') . "\n";
echo "Env DB_CONNECTION: " . env('DB_CONNECTION') . "\n";

try {
    echo "Driver Name: " . DB::connection()->getDriverName() . "\n";
    echo "Database Name: " . DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "Connection Error: " . $e->getMessage() . "\n";
}
