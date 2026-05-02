<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';
    protected $primaryKey = 'id_bahan';
    protected $guarded = [];

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'id_outlet',
        'id_satuan',
        'merk',
        'catatan',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Method untuk generate kode bahan otomatis
    public static function generateKodeBahan()
    {
        $prefix = 'MAT';
        $lastBahan = self::where('kode_bahan', 'like', $prefix . '-%')
            ->orderBy('kode_bahan', 'desc')
            ->first();

        if ($lastBahan) {
            $lastNumber = (int) substr($lastBahan->kode_bahan, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function hargaBahan()
    {
        return $this->hasMany(BahanDetail::class, 'id_bahan', 'id_bahan');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function getHargaBahanSumStokAttribute()
    {
        return $this->hargaBahan()->sum('stok');
    }

    public function produksiDetails()
    {
        return $this->hasMany(ProduksiDetail::class, 'id_bahan', 'id_bahan');
    }

    // Scope untuk filter
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'ALL') {
            return $query->where('id_outlet', $outletId);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_bahan', 'like', "%{$search}%")
              ->orWhere('kode_bahan', 'like', "%{$search}%")
              ->orWhere('merk', 'like', "%{$search}%");
        });
    }
}