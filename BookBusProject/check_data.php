<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Villes: " . App\Models\Ville::count() . "\n";
echo "Gares: " . App\Models\Gare::count() . "\n";
echo "Programmes: " . App\Models\Programme::count() . "\n";
echo "Segments: " . App\Models\Segment::count() . "\n";
echo "Buses: " . App\Models\Bus::count() . "\n";

$firstProg = App\Models\Programme::orderBy('jour_depart')->first();
if ($firstProg) {
    echo "Première date de programme: " . $firstProg->jour_depart . "\n";
} else {
    echo "Aucun programme trouvé.\n";
}

$villes = App\Models\Ville::all();
foreach ($villes as $v) {
    echo "Ville: {$v->nom} (ID: {$v->id})\n";
}
