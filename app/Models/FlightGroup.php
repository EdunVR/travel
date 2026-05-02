<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlightGroup extends Model
{
    use HasFactory;

    protected $table = 'flight_groups';
    
    protected $fillable = [
        'group_name',
        'description',
        'id_outlet'
    ];

    /**
     * Relationship to outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Relationship to flight group items
     */
    public function items()
    {
        return $this->hasMany(FlightGroupItem::class, 'id_flight_group')->orderBy('sequence');
    }

    /**
     * Relationship to flights through items
     */
    public function flights()
    {
        return $this->belongsToMany(Flight::class, 'flight_group_items', 'id_flight_group', 'id_flight')
            ->withPivot('flight_type', 'sequence')
            ->withTimestamps()
            ->orderBy('flight_group_items.sequence');
    }

    /**
     * Get departure flight
     */
    public function getDepartureFlight()
    {
        return $this->items()->where('flight_type', 'departure')->with('flight')->first()?->flight;
    }

    /**
     * Get return flight
     */
    public function getReturnFlight()
    {
        return $this->items()->where('flight_type', 'return')->with('flight')->first()?->flight;
    }

    /**
     * Get transit flights
     */
    public function getTransitFlights()
    {
        return $this->items()->where('flight_type', 'transit')->with('flight')->get()->pluck('flight');
    }

    /**
     * Get formatted route
     */
    public function getFormattedRoute()
    {
        $departure = $this->getDepartureFlight();
        $return = $this->getReturnFlight();
        
        if ($departure && $return) {
            return $departure->departure_airport . ' → ' . $departure->arrival_airport . ' → ' . $return->arrival_airport;
        } elseif ($departure) {
            return $departure->departure_airport . ' → ' . $departure->arrival_airport;
        }
        
        return '-';
    }
}
