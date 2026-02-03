<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'programme_id',
        'departure_gare_id',
        'arrival_gare_id',
        'heure_depart',
        'heure_arrivee',
        'tarif',
        'distance_km'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function departureGare()
    {
        return $this->belongsTo(Gare::class, 'departure_gare_id');
    }

    public function arrivalGare()
    {
        return $this->belongsTo(Gare::class, 'arrival_gare_id');
    }
}
