<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = ['route_id', 'jour_depart', 'heure_depart', 'heure_arrivee'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function segments()
    {
        return $this->hasMany(Segment::class);
    }
}
