<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamaahHotelBooking extends Model
{
    protected $table = 'jamaah_hotel_bookings';

    protected $fillable = [
        'id_jamaah_booking', 'id_hotel', 'city_type', 'room_type',
        'check_in_date', 'check_out_date', 'nights', 'price_per_night',
        'is_charged', 'notes', 'sort_order',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'price_per_night' => 'decimal:2',
        'is_charged' => 'boolean',
        'nights' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    public function getTotalCostAttribute(): float
    {
        return (float)$this->price_per_night * $this->nights;
    }
}
