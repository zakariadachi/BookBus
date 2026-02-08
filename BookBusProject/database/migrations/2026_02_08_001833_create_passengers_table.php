<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('nom_complet');
            $table->string('cin');
            $table->date('date_naissance');
            $table->string('type')->default('Adulte');
            $table->boolean('insurance')->default(false);
            $table->boolean('snack_box')->default(false);
            $table->boolean('premium_seat')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
