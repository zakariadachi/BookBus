<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->foreignId('programme_id')->constrained('programmes')->onDelete('cascade');
            $table->foreignId('departure_gare_id')->constrained('gares')->onDelete('cascade');
            $table->foreignId('arrival_gare_id')->constrained('gares')->onDelete('cascade');
            $table->time('heure_depart')->nullable();
            $table->time('heure_arrivee')->nullable();
            $table->decimal('tarif', 8, 2);
            $table->float('distance_km');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};