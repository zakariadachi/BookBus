<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status', 'nb_passagers'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function segments()
    {
        return $this->belongsToMany(Segment::class, 'booking_segment');
    }
}
