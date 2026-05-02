<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OngkosKirim extends Model
{
    protected $table = 'ongkos_kirim';
    protected $primaryKey = 'id_ongkir';

    protected $fillable = [
        'id_outlet',
        'daerah',
        'harga',
        'nama_tujuan',
        'biaya'
    ];

    protected $attributes = [
        'nama_tujuan' => '-',
        'biaya' => 0
    ];

    protected $casts = [
        'id_outlet' => 'integer',
        'harga' => 'decimal:2'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    public static function getByOutlet($outletId)
    {
        return static::byOutlet($outletId)->get();
    }
}