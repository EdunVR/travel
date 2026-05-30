<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoPaketData extends Model
{
    protected $table = 'info_paket_data';

    protected $fillable = [
        'id_travel_package',
        'id_keberangkatan',
        'group_name',
        'tour_leader_name',
        'adult_count',
        'child_count',
        'infant_count',
        'itinerary_rows',
        'rawdah_rows',
    ];

    protected $casts = [
        'itinerary_rows' => 'array',
        'rawdah_rows' => 'array',
        'adult_count' => 'integer',
        'child_count' => 'integer',
        'infant_count' => 'integer',
    ];

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }
}
