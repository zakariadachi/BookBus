<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dates = App\Models\Programme::select('jour_depart')->distinct()->get();
echo "Dates disponibles:\n";
foreach ($dates as $d) {
    echo "- " . $d->jour_depart . "\n";
}

$segments = App\Models\Segment::with(['departureGare.ville', 'arrivalGare.ville', 'programme'])->get();
echo "\nSegments disponibles:\n";
foreach ($segments as $s) {
    echo "ID: {$s->id} | {$s->departureGare->ville->nom} -> {$s->arrivalGare->ville->nom} | Date: {$s->programme->jour_depart} | Tarif: {$s->tarif}\n";
}
