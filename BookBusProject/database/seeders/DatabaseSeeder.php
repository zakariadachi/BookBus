<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Etape;
use App\Models\Gare;
use App\Models\Programme;
use App\Models\Route;
use App\Models\Segment;
use App\Models\Ville;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Villes
        $casa = Ville::create(['nom' => 'Casablanca']);
        $marrakech = Ville::create(['nom' => 'Marrakech']);
        $rabat = Ville::create(['nom' => 'Rabat']);

        // Create Gares
        $gareCasa = Gare::create(['nom' => 'Gare Routière Ouled Ziane', 'adresse' => 'Ouled Ziane', 'ville_id' => $casa->id]);
        $gareMarrakech = Gare::create(['nom' => 'Gare Routière Bab Doukkala', 'adresse' => 'Bab Doukkala', 'ville_id' => $marrakech->id]);
        $gareRabat = Gare::create(['nom' => 'Gare Routière Kamra', 'adresse' => 'Kamra', 'ville_id' => $rabat->id]);

        // Create Buses
        $bus1 = Bus::create(['immatriculation' => '1234-A-50', 'capacite' => 50, 'classe' => 'Premium']);
        $bus2 = Bus::create(['immatriculation' => '5678-B-40', 'capacite' => 40, 'classe' => 'Standard']);
        $bus3 = Bus::create(['immatriculation' => '9012-C-45', 'capacite' => 45, 'classe' => 'Confort']);

        // Create Routes
        // Casablanca -> Marrakech
        $route1 = Route::create([
            'nom' => 'Casablanca - Marrakech Express',
            'description' => 'Trajet direct via autoroute'
        ]);

        Etape::create(['route_id' => $route1->id, 'gare_id' => $gareCasa->id, 'ordre' => 1, 'heure_passage' => '00:00:00']); // Départ
        Etape::create(['route_id' => $route1->id, 'gare_id' => $gareMarrakech->id, 'ordre' => 2, 'heure_passage' => '03:30:00']); // Arrivée

        // Route 2: Rabat -> Casablanca -> Marrakech
        $route2 = Route::create([
            'nom' => 'Nord - Sud',
            'description' => 'Rabat vers Marrakech via Casablanca'
        ]);

        Etape::create(['route_id' => $route2->id, 'gare_id' => $gareRabat->id, 'ordre' => 1, 'heure_passage' => '00:00:00']);
        Etape::create(['route_id' => $route2->id, 'gare_id' => $gareCasa->id, 'ordre' => 2, 'heure_passage' => '01:15:00']);
        Etape::create(['route_id' => $route2->id, 'gare_id' => $gareMarrakech->id, 'ordre' => 3, 'heure_passage' => '04:45:00']);

        // Programmes & Segments

        $dates = [Carbon::today(), Carbon::tomorrow()];

        foreach ($dates as $date) {
            $prog1 = Programme::create([
                'route_id' => $route1->id,
                'jour_depart' => $date->toDateString(),
                'heure_depart' => '08:00:00',
                'heure_arrivee' => '11:30:00',
            ]);

            Segment::create([
                'bus_id' => $bus1->id,
                'programme_id' => $prog1->id,
                'departure_gare_id' => $gareCasa->id,
                'arrival_gare_id' => $gareMarrakech->id,
                'heure_depart' => '08:00:00',
                'heure_arrivee' => '11:30:00',
                'tarif' => 120.00,
                'distance_km' => 240
            ]);

            // Programme for Route 2 (Rabat -> Casa -> Marrakech)
            $prog2 = Programme::create([
                'route_id' => $route2->id,
                'jour_depart' => $date->toDateString(),
                'heure_depart' => '14:00:00',
                'heure_arrivee' => '18:45:00',
            ]);

            // Segment Rabat -> Casa
            Segment::create([
                'bus_id' => $bus2->id,
                'programme_id' => $prog2->id,
                'departure_gare_id' => $gareRabat->id,
                'arrival_gare_id' => $gareCasa->id,
                'heure_depart' => '14:00:00',
                'heure_arrivee' => '15:15:00',
                'tarif' => 45.00,
                'distance_km' => 87
            ]);

            // Segment Casa -> Marrakech
            Segment::create([
                'bus_id' => $bus2->id,
                'programme_id' => $prog2->id,
                'departure_gare_id' => $gareCasa->id,
                'arrival_gare_id' => $gareMarrakech->id,
                'heure_depart' => '15:30:00',
                'heure_arrivee' => '18:45:00',
                'tarif' => 100.00,
                'distance_km' => 240
            ]);

            // Indirect Segment
            Segment::create([
                'bus_id' => $bus2->id,
                'programme_id' => $prog2->id,
                'departure_gare_id' => $gareRabat->id,
                'arrival_gare_id' => $gareMarrakech->id,
                'heure_depart' => '14:00:00',
                'heure_arrivee' => '18:45:00',
                'tarif' => 140.00,
                'distance_km' => 327
            ]);

            Segment::create([
                'bus_id' => $bus3->id,
                'programme_id' => $prog1->id,
                'departure_gare_id' => $gareCasa->id,
                'arrival_gare_id' => $gareMarrakech->id,
                'heure_depart' => '10:00:00',
                'heure_arrivee' => '13:30:00',
                'tarif' => 135.00,
                'distance_km' => 240
            ]);
        }
    }
}
