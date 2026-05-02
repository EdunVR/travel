<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'outlets';
    protected $primaryKey = 'id_outlet';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    // Tambahkan fillable untuk mass assignment
    protected $fillable = [
        'kode_outlet',
        'nama_outlet', 
        'alamat',
        'kota',
        'telepon',
        'is_active',
        'catatan'
    ];

    // Casting untuk tipe data
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function generateKodeOutlet()
    {
        $prefix = 'OUT';
        $lastOutlet = self::where('kode_outlet', 'like', $prefix . '-%')
            ->orderBy('kode_outlet', 'desc')
            ->first();

        if ($lastOutlet) {
            $lastNumber = (int) substr($lastOutlet->kode_outlet, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Scope untuk filter aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByKota($query, $kota)
    {
        if ($kota && $kota !== 'ALL') {
            return $query->where('kota', $kota);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_outlet', 'like', "%{$search}%")
              ->orWhere('kode_outlet', 'like', "%{$search}%")
              ->orWhere('kota', 'like', "%{$search}%")
              ->orWhere('telepon', 'like', "%{$search}%");
        });
    }

    // Relasi ke Produk
    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_outlet', 'id_outlet');
    }

    // Relasi ke Bahan
    public function bahans()
    {
        return $this->hasMany(Bahan::class, 'id_outlet', 'id_outlet');
    }

    // Relasi ke Inventori
    public function inventoris()
    {
        return $this->hasMany(Inventori::class, 'id_outlet', 'id_outlet');
    }

    // Relasi ke Permintaan Pengiriman (outlet asal)
    public function permintaanPengirimanAsal()
    {
        return $this->hasMany(PermintaanPengiriman::class, 'id_outlet_asal', 'id_outlet');
    }

    // Relasi ke Permintaan Pengiriman (outlet tujuan)
    public function permintaanPengirimanTujuan()
    {
        return $this->hasMany(PermintaanPengiriman::class, 'id_outlet_tujuan', 'id_outlet');
    }

    // Relasi ke Tipe Customer
    public function tipes()
    {
        return $this->hasMany(Tipe::class, 'id_outlet', 'id_outlet');
    }
    
}