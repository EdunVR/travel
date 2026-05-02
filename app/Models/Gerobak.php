<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gerobak extends Model
{
    use HasFactory;

    protected $table = 'gerobak';
    protected $primaryKey = 'id_gerobak';
    protected $guarded = [];

    protected $fillable = [
        'id_agen',
        'nama_gerobak',
        'kode_gerobak',
        'latitude',
        'longitude',
        'status',
        'id_outlet'
    ];

    /**
     * Relationship with Agen (Member)
     */
    public function agen()
    {
        return $this->belongsTo(Member::class, 'id_agen', 'id_member');
    }

    /**
     * Relationship with Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    /**
     * Relationship with Produk (Many-to-Many)
     */
    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'gerobak_produk', 'id_gerobak', 'id_produk')
            ->withPivot('stok')
            ->withTimestamps();
    }

    /**
     * Get last location update
     */
    public function getLastLocationAttribute()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'updated_at' => $this->updated_at
        ];
    }

    /**
     * Scope for active gerobak
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Update gerobak location
     */
    public function updateLocation($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        return $this->save();
    }
}