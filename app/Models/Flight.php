<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flight extends Model
{
    use HasFactory;

    protected $table = 'flights';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'airline_name',
        'flight_number',
        'flight_group_code',
        'flight_direction',
        'departure_airport',
        'arrival_airport',
        'departure_time',
        'arrival_time',
        'transit_info',
        'capacity',
        'aircraft_type',
        'price_per_person',
        'seller_name',
        'seller_phone',
        'id_outlet'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'transit_info' => 'array',
        'capacity' => 'integer',
        'price_per_person' => 'decimal:2'
    ];

    /**
     * Relationship to outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Relationship to flight bookings
     */
    public function bookings()
    {
        return $this->hasMany(FlightBooking::class, 'id_flight');
    }

    /**
     * Get available seats for this flight
     * 
     * @return int
     */
    public function getAvailableSeats()
    {
        $bookedSeats = $this->bookings()
            ->whereIn('status', ['confirmed', 'ticketed'])
            ->sum('seat_count');
        
        return $this->capacity - $bookedSeats;
    }

    /**
     * Scope to search flights
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('airline_name', 'like', "%{$term}%")
              ->orWhere('flight_number', 'like', "%{$term}%")
              ->orWhere('departure_airport', 'like', "%{$term}%")
              ->orWhere('arrival_airport', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to filter by outlet
     */
    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    /**
     * Get total transit duration in minutes
     * 
     * @return int
     */
    public function getTotalTransitDuration()
    {
        if (empty($this->transit_info) || !is_array($this->transit_info)) {
            return 0;
        }

        $totalMinutes = 0;
        foreach ($this->transit_info as $transit) {
            $totalMinutes += $transit['duration_minutes'] ?? 0;
        }

        return $totalMinutes;
    }

    /**
     * Get formatted transit info for display
     * 
     * @return string
     */
    public function getFormattedTransitInfo()
    {
        if (empty($this->transit_info) || !is_array($this->transit_info)) {
            return 'Direct Flight';
        }

        $count = count($this->transit_info);
        $airports = array_map(function($transit) {
            return $transit['airport'] ?? '';
        }, $this->transit_info);

        return $count . ' Transit (' . implode(', ', $airports) . ')';
    }

    /**
     * Check if flight has transit
     * 
     * @return bool
     */
    public function hasTransit()
    {
        return !empty($this->transit_info) && is_array($this->transit_info) && count($this->transit_info) > 0;
    }

    /**
     * Get paired flight (return if this is departure, departure if this is return)
     * 
     * @return Flight|null
     */
    public function getPairedFlight()
    {
        if (!$this->flight_group_code) {
            return null;
        }

        $oppositeDirection = $this->flight_direction === 'departure' ? 'return' : 'departure';
        
        return static::where('flight_group_code', $this->flight_group_code)
            ->where('flight_direction', $oppositeDirection)
            ->first();
    }

    /**
     * Scope to get flight groups (only departure flights with group code)
     */
    public function scopeFlightGroups($query)
    {
        return $query->whereNotNull('flight_group_code')
            ->where('flight_direction', 'departure')
            ->orderBy('flight_group_code');
    }
}
