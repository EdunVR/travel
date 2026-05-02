<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    protected $table = 'room_assignments';

    protected $fillable = [
        'id_keberangkatan', 'city_type', 'room_number', 'room_type',
        'person_type', 'id_jamaah_booking', 'person_name',
        'family_index', 'room_position', 'sort_order',
    ];

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    public function jamaahBooking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    public static function capacityForType(string $type): int
    {
        return match(strtolower($type)) {
            'single' => 1,
            'double' => 2,
            'triple' => 3,
            'quad'   => 4,
            default  => 2,
        };
    }
}
