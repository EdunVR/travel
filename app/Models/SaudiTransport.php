<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaudiTransport extends Model
{
    use HasFactory;

    protected $table = 'saudi_transports';

    protected $fillable = [
        'transport_code', 'transport_name', 'transport_type',
        'route_from', 'route_to', 'operator',
        'price_per_person', 'seller_name', 'seller_phone',
        'notes', 'id_outlet'
    ];

    protected $casts = [
        'price_per_person' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function getTypeLabel(): string
    {
        return match($this->transport_type) {
            'kereta_cepat' => 'Kereta Cepat (Haramain)',
            'bus' => 'Bus',
            default => 'Lainnya',
        };
    }
}
