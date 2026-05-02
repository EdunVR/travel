<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightGroupItem extends Model
{
    protected $table = 'flight_group_items';
    
    protected $fillable = [
        'id_flight_group',
        'id_flight',
        'flight_type',
        'sequence'
    ];

    /**
     * Relationship to flight group
     */
    public function flightGroup()
    {
        return $this->belongsTo(FlightGroup::class, 'id_flight_group');
    }

    /**
     * Relationship to flight
     */
    public function flight()
    {
        return $this->belongsTo(Flight::class, 'id_flight');
    }
}
