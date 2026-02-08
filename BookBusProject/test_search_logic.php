<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ville;
use App\Models\Gare;
use App\Models\Segment;
use Illuminate\Http\Request;

// Mock request
$request = new Request([
    'departure_city' => 1, // Casablanca
    'arrival_city' => 2,   // Marrakech (guessing ID based on table count)
    'date' => '2026-02-06',
    'passengers' => 1
]);

$depCityId = $request->departure_city;
$arrCityId = $request->arrival_city;
$date = $request->date;

$depGares = Gare::where('ville_id', $depCityId)->pluck('id');
$arrGares = Gare::where('ville_id', $arrCityId)->pluck('id');

echo "Dep Gares: " . implode(',', $depGares->toArray()) . "\n";
echo "Arr Gares: " . implode(',', $arrGares->toArray()) . "\n";

$directSegments = Segment::with(['bus', 'departureGare.ville', 'arrivalGare.ville'])
    ->whereIn('departure_gare_id', $depGares)
    ->whereIn('arrival_gare_id', $arrGares)
    ->whereHas('programme', function($q) use ($date) {
        $q->whereDate('jour_depart', $date);
    })
    ->get();

echo "Direct segments found: " . $directSegments->count() . "\n";

foreach ($directSegments as $s) {
    echo "- Segment ID: {$s->id} | Bus: {$s->bus->id} | Classe: {$s->bus->classe} | Cap: {$s->bus->capacite}\n";
}
