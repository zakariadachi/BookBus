<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 
        'nom_complet', 
        'cin', 
        'date_naissance', 
        'type', 
        'insurance', 
        'snack_box', 
        'premium_seat'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
