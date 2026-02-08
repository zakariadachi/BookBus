<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;

$request = new Request([
    'departure_city' => 1,
    'arrival_city' => 2,
    'date' => date('Y-m-d'),
    'passengers' => 1
]);

$controller = new SearchController();
$response = $controller->search($request);
$html = $response->render();

if (strpos($html, 'trajets trouvés') !== false) {
    echo "TEXT FOUND\n";
    preg_match('/<span class="font-bold text-gray-900">(\d+)<\/span> trajets trouvés/', $html, $matches);
    if ($matches) {
        echo "Nombre trouvé: " . $matches[1] . "\n";
    }
} else {
    echo "TEXT NOT FOUND\n";
    echo "HTML snippet:\n";
    echo substr($html, 0, 500);
}
