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
        // Validation
        $request->validate([
            'departure_city' => 'required|exists:villes,id',
            'arrival_city' => 'required|exists:villes,id|different:departure_city',
            'date' => 'required|date|after_or_equal:today',
            'passengers' => 'required|integer|min:1|max:10',
        ]);

        $depCityId = $request->departure_city;
        $arrCityId = $request->arrival_city;
        $date = $request->date;
        $passengers = $request->passengers;

        // Get Gares for Cities
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
            $results->push((object)[
                'type' => 'Direct',
                'segments' => [$segment],
                'departureGare' => $segment->departureGare,
                'arrivalGare' => $segment->arrivalGare,
                'heure_depart' => $segment->heure_depart,
                'heure_arrivee' => $segment->heure_arrivee,
                'tarif' => $segment->tarif,
                'bus' => $segment->bus,
                'is_direct' => true
            ]);
        }
        // Find segments A -> X
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
                $results->push((object)[
                    'type' => 'Connexion',
                    'segments' => [$leg1, $leg2],
                    'departureGare' => $leg1->departureGare,
                    'arrivalGare' => $leg2->arrivalGare,
                    'heure_depart' => $leg1->heure_depart,
                    'heure_arrivee' => $leg2->heure_arrivee,
                    'tarif' => $leg1->tarif + $leg2->tarif,
                    'bus' => $leg1->bus,
                    'is_direct' => false
                ]);
            }
        }

        // Sort by price then time
        $results = $results->sortBy([
            ['tarif', 'asc'],
            ['heure_depart', 'asc']
        ]);
        
        // Pass data to results view
        return view('search.results', [
            'results' => $results,
            'searchParams' => $request->all()
        ]);
    }
}
