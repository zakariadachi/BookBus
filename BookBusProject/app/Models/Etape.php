<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etape extends Model
{
    use HasFactory;

    protected $fillable = ['route_id', 'gare_id', 'ordre', 'heure_passage'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function gare()
    {
        return $this->belongsTo(Gare::class);
    }
}
