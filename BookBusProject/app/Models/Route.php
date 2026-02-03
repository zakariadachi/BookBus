<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description'];

    public function etapes()
    {
        return $this->hasMany(Etape::class)->orderBy('ordre');
    }

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }
}
