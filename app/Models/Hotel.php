<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'hotels';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'hotel_name',
        'location',
        'city',
        'country',
        'star_rating',
        'total_rooms',
        'contact_person',
        'phone',
        'email',
        'address',
        'seller_name',
        'seller_phone',
        'id_outlet'
    ];

    protected $casts = [
        'star_rating' => 'integer',
        'total_rooms' => 'integer'
    ];

    /**
     * Relationship to outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Relationship to room types
     */
    public function roomTypes()
    {
        return $this->hasMany(HotelRoomType::class, 'id_hotel');
    }

    /**
     * Relationship to hotel bookings
     */
    public function bookings()
    {
        return $this->hasMany(HotelBooking::class, 'id_hotel');
    }

    /**
     * Get total available rooms across all room types
     * 
     * @return int
     */
    public function getAvailableRooms()
    {
        $bookedRooms = $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->sum('room_count');
        
        return $this->total_rooms - $bookedRooms;
    }

    /**
     * Scope to search hotels
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('hotel_name', 'like', "%{$term}%")
              ->orWhere('location', 'like', "%{$term}%")
              ->orWhere('city', 'like', "%{$term}%")
              ->orWhere('country', 'like', "%{$term}%");
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
     * Scope to filter by city
     */
    public function scopeByCity($query, $city)
    {
        if ($city && $city !== 'ALL') {
            return $query->where('city', $city);
        }
        return $query;
    }

    /**
     * Scope to filter by star rating
     */
    public function scopeByStarRating($query, $rating)
    {
        if ($rating && $rating !== 'ALL') {
            return $query->where('star_rating', $rating);
        }
        return $query;
    }
}
