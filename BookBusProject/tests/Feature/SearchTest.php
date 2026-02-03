<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Gare;
use App\Models\Programme;
use App\Models\Route;
use App\Models\Segment;
use App\Models\Ville;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_direct_search_results()
    {
        // Setup Data
        $v1 = Ville::create(['nom' => 'Ville A']);
        $v2 = Ville::create(['nom' => 'Ville B']);
        
        $g1 = Gare::create(['nom' => 'Gare A', 'adresse' => 'A', 'ville_id' => $v1->id]);
        $g2 = Gare::create(['nom' => 'Gare B', 'adresse' => 'B', 'ville_id' => $v2->id]);

        $bus = Bus::create(['immatriculation' => 'TEST', 'capacite' => 50, 'classe' => 'VIP']);
        
        $route = Route::create(['nom' => 'R1', 'description' => 'D1']);
        $date = Carbon::tomorrow()->toDateString();
        
        $prog = Programme::create([
            'route_id' => $route->id,
            'jour_depart' => $date,
            'heure_depart' => '08:00',
            'heure_arrivee' => '10:00'
        ]);

        Segment::create([
            'bus_id' => $bus->id,
            'programme_id' => $prog->id,
            'departure_gare_id' => $g1->id,
            'arrival_gare_id' => $g2->id,
            'heure_depart' => '08:00',
            'heure_arrivee' => '10:00',
            'tarif' => 100,
            'distance_km' => 100
        ]);

        // Search
        $response = $this->get("/search?departure_city={$v1->id}&arrival_city={$v2->id}&date={$date}&passengers=1");

        $response->assertStatus(200);
        $response->assertViewHas('results');
        $results = $response->viewData('results');
        
        $this->assertCount(1, $results);
        $this->assertEquals('Direct', $results->first()->type);
        $this->assertEquals(100, $results->first()->tarif);
    }

    public function test_indirect_search_results()
    {
        // A -> B -> C
        $vA = Ville::create(['nom' => 'A']);
        $vB = Ville::create(['nom' => 'B']);
        $vC = Ville::create(['nom' => 'C']);

        $gA = Gare::create(['nom' => 'GA', 'adresse'=>'A', 'ville_id' => $vA->id]);
        $gB = Gare::create(['nom' => 'GB', 'adresse'=>'B', 'ville_id' => $vB->id]);
        $gC = Gare::create(['nom' => 'GC', 'adresse'=>'C', 'ville_id' => $vC->id]);

        $bus = Bus::create(['immatriculation' => 'TEST', 'capacite' => 50, 'classe' => 'VIP']);
        $route = Route::create(['nom' => 'R1', 'description' => 'D1']);
        $date = Carbon::tomorrow()->toDateString();
        $prog = Programme::create(['route_id'=>$route->id, 'jour_depart'=>$date, 'heure_depart'=>'08:00', 'heure_arrivee'=>'20:00']);

        // Leg 1: A -> B (08:00 -> 10:00)
        Segment::create([
            'bus_id' => $bus->id, 'programme_id' => $prog->id,
            'departure_gare_id' => $gA->id, 'arrival_gare_id' => $gB->id,
            'heure_depart' => '08:00', 'heure_arrivee' => '10:00',
            'tarif' => 50, 'distance_km' => 50
        ]);

        // Leg 2: B -> C (11:00 -> 13:00) - 1 hour wait OK
        Segment::create([
            'bus_id' => $bus->id, 'programme_id' => $prog->id,
            'departure_gare_id' => $gB->id, 'arrival_gare_id' => $gC->id,
            'heure_depart' => '11:00', 'heure_arrivee' => '13:00',
            'tarif' => 60, 'distance_km' => 50
        ]);

        // Search A -> C
        $response = $this->get("/search?departure_city={$vA->id}&arrival_city={$vC->id}&date={$date}&passengers=1");
        
        $response->assertStatus(200);
        $results = $response->viewData('results');

        $this->assertCount(1, $results); // Should find 1 connection
        $this->assertEquals('Connexion', $results->first()->type);
        $this->assertEquals(110, $results->first()->tarif); // 50 + 60
    }
}
