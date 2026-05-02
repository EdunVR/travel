<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelRoomAssignment extends Model
{
    use HasFactory;

    protected $table = 'hotel_room_assignments';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_hotel_booking',
        'id_jamaah_booking',
        'room_number',
        'room_type',
        'bed_number',
        'notes'
    ];

    protected $casts = [
        'bed_number' => 'integer'
    ];

    /**
     * Relationship to hotel booking
     */
    public function hotelBooking()
    {
        return $this->belongsTo(HotelBooking::class, 'id_hotel_booking');
    }

    /**
     * Relationship to jamaah booking
     */
    public function jamaahBooking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    /**
     * Get jamaah member details
     */
    public function jamaah()
    {
        return $this->hasOneThrough(
            Member::class,
            JamaahBooking::class,
            'id', // Foreign key on jamaah_bookings table
            'id', // Foreign key on member table
            'id_jamaah_booking', // Local key on hotel_room_assignments table
            'id_member' // Local key on jamaah_bookings table
        );
    }
}
