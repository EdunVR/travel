<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelRoomType extends Model
{
    use HasFactory;

    protected $table = 'hotel_room_types';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_hotel',
        'room_type_name',
        'capacity',
        'total_rooms',
        'price_per_night'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'total_rooms' => 'integer',
        'price_per_night' => 'decimal:2'
    ];

    /**
     * Relationship to hotel
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    /**
     * Get available rooms for this room type
     * 
     * @return int
     */
    public function getAvailableRooms()
    {
        // This would need to be implemented when HotelBooking model is created
        // For now, return total rooms
        return $this->total_rooms;
    }
}
