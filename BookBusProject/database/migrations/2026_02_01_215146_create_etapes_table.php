<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapes', function (Blueprint $table) {
            $table->id();
            $table->integer('ordre');
            $table->time('heure_passage');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('gare_id')->constrained('gares')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapes');
    }
};