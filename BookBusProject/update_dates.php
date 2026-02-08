<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;
use Carbon\Carbon;

$today = Carbon::today();

echo "Updating programme dates to be relative to today ({$today->toDateString()})...\n";

$programmes = Programme::all();
foreach ($programmes as $index => $p) {
    // Spread them over today and the next 2 days
    $newDate = $today->copy()->addDays($index % 3);
    $p->update(['jour_depart' => $newDate->toDateString()]);
    echo "Updated Programme ID {$p->id} to {$newDate->toDateString()}\n";
}

echo "Done.\n";
