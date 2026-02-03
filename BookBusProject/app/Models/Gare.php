<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gare extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'adresse', 'ville_id'];

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function etapes()
    {
        return $this->hasMany(Etape::class);
    }
}
