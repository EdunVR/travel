<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlightBooking extends Model
{
    use HasFactory;

    protected $table = 'flight_bookings';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_flight',
        'id_keberangkatan',
        'seat_count',
        'status',
        'booking_reference',
        'confirmation_code',
        'ticket_document_path',
        'booking_date',
        'confirmed_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'confirmed_at' => 'datetime',
        'seat_count' => 'integer'
    ];

    /**
     * Relationship to flight
     */
    public function flight()
    {
        return $this->belongsTo(Flight::class, 'id_flight');
    }

    /**
     * Relationship to keberangkatan
     */
    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }
}
