<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Segment;
use App\Models\Ville;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function index()
    {
        $villes = Ville::orderBy('nom')->get();
        return view('search.form', compact('villes'));
    }

    public function search(Request $request)
    {
        // Basic Validation
        $request->validate([
            'departure_city' => 'required|exists:villes,id',
            'arrival_city' => 'required|exists:villes,id|different:departure_city',
            'date' => 'required|date|after_or_equal:today',
            'classes' => 'nullable|array',
            'departure_time' => 'nullable|string',
            'max_price' => 'nullable|numeric|min:50|max:500',
            'sort_by' => 'nullable|string',
        ]);

        $depCityId = $request->departure_city;
        $arrCityId = $request->arrival_city;
        $date = $request->date;

        // Get Gares
        $depGares = \App\Models\Gare::where('ville_id', $depCityId)->pluck('id');
        $arrGares = \App\Models\Gare::where('ville_id', $arrCityId)->pluck('id');

        $results = collect();

        // Direct Segments
        $directSegments = Segment::with(['bus', 'departureGare.ville', 'arrivalGare.ville'])
            ->whereIn('departure_gare_id', $depGares)
            ->whereIn('arrival_gare_id', $arrGares)
            ->whereHas('programme', function($q) use ($date) {
                $q->whereDate('jour_depart', $date);
            })
            ->get();

        foreach ($directSegments as $segment) {
            $tarifUnitaire = $segment->tarif;
            $class = strtolower(trim($segment->bus->classe));
            if ($class === 'confort') {
                $tarifUnitaire *= 1.1;
            } elseif ($class === 'premium') {
                $tarifUnitaire *= 1.2;
            }

            $results->push((object)[
                'type' => 'Direct',
                'segments' => [$segment],
                'departureGare' => $segment->departureGare,
                'arrivalGare' => $segment->arrivalGare,
                'heure_depart' => $segment->heure_depart,
                'heure_arrivee' => $segment->heure_arrivee,
                'tarif' => $tarifUnitaire,
                'bus' => $segment->bus,
                'is_direct' => true,
                'duration_minutes' => \Carbon\Carbon::parse($segment->heure_depart)->diffInMinutes(\Carbon\Carbon::parse($segment->heure_arrivee))
            ]);
        }

        // Connexions A -> X -> B
        $possibleFirstLegs = Segment::whereIn('departure_gare_id', $depGares)
            ->whereHas('programme', function($q) use ($date) {
                $q->whereDate('jour_depart', $date);
            })
            ->with(['bus', 'departureGare.ville', 'arrivalGare.ville'])
            ->get();

        foreach ($possibleFirstLegs as $leg1) {
            if ($arrGares->contains($leg1->arrival_gare_id)) continue;

            $arrivalAtX = \Carbon\Carbon::parse($leg1->heure_arrivee);
            $minDepFromX = $arrivalAtX->copy()->addMinutes(15);

            $connectingLegs = Segment::where('departure_gare_id', $leg1->arrival_gare_id)
                ->whereIn('arrival_gare_id', $arrGares)
                ->whereHas('programme', function($q) use ($date) {
                    $q->whereDate('jour_depart', $date);
                })
                ->whereTime('heure_depart', '>=', $minDepFromX->format('H:i:s'))
                ->with(['bus', 'departureGare.ville', 'arrivalGare.ville'])
                ->get();
            
            foreach ($connectingLegs as $leg2) {
                $totalTarif = $leg1->tarif + $leg2->tarif;
                
                $class = strtolower(trim($leg1->bus->classe));
                if ($class === 'confort') {
                    $totalTarif *= 1.1;
                } elseif ($class === 'premium') {
                    $totalTarif *= 1.2;
                }

                $results->push((object)[
                    'type' => 'Connexion',
                    'segments' => [$leg1, $leg2],
                    'departureGare' => $leg1->departureGare,
                    'arrivalGare' => $leg2->arrivalGare,
                    'heure_depart' => $leg1->heure_depart,
                    'heure_arrivee' => $leg2->heure_arrivee,
                    'tarif' => $totalTarif,
                    'bus' => $leg1->bus,
                    'is_direct' => false,
                    'duration_minutes' => \Carbon\Carbon::parse($leg1->heure_depart)->diffInMinutes(\Carbon\Carbon::parse($leg2->heure_arrivee))
                ]);
            }
        }

        $allResults = clone $results;

        // FILTERS
        if ($request->filled('classes')) {
            $results = $results->filter(function($item) use ($request) {
                $itemClass = strtolower(trim($item->bus->classe));
                $requestedClasses = array_map(function($c) { return strtolower(trim($c)); }, $request->classes);
                return in_array($itemClass, $requestedClasses);
            });
        }

        if ($request->filled('departure_time')) {
            $results = $results->filter(function($item) use ($request) {
                $hour = (int)\Carbon\Carbon::parse($item->heure_depart)->format('H');
                switch ($request->departure_time) {
                    case 'matin':
                        return $hour >= 5 && $hour < 12;
                    case 'apres-midi':
                        return $hour >= 12 && $hour < 18;
                    case 'soir':
                        return $hour >= 18 || $hour < 5;
                    default:
                        return true;
                }
            });
        }

        if ($request->filled('max_price')) {
            $results = $results->filter(function($item) use ($request) {
                return $item->tarif <= $request->max_price;
            });
        }

        // SORTING
        $sortBy = $request->get('sort_by', 'price_asc');
        switch ($sortBy) {
            case 'price_asc':
                $results = $results->sortBy('tarif');
                break;
            case 'price_desc':
                $results = $results->sortByDesc('tarif');
                break;
            case 'time_asc':
                $results = $results->sortBy('heure_depart');
                break;
            case 'duration_asc':
                $results = $results->sortBy('duration_minutes');
                break;
            default:
                $results = $results->sortBy('tarif');
        }

        // Results view data
        $viewData = [
            'results' => $results,
            'searchParams' => $request->all(),
            'availableClasses' => $allResults->pluck('bus.classe')->unique()->values()->toArray()
        ];

        if ($results->isEmpty()) {
            // Find next available date with ANY trips for these cities
            $nextDate = Segment::whereIn('departure_gare_id', $depGares)
                ->whereIn('arrival_gare_id', $arrGares)
                ->whereHas('programme', function($q) use ($date) {
                    $q->whereDate('jour_depart', '>', $date);
                })
                ->join('programmes', 'segments.programme_id', '=', 'programmes.id')
                ->orderBy('programmes.jour_depart')
                ->value('programmes.jour_depart');
            
            $viewData['suggested_date'] = $nextDate;
        }

        return view('search.results', $viewData);
    }
}
