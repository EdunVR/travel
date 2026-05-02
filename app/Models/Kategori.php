<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'kelompok',
        'id_outlet',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Method untuk generate kode kategori otomatis
    public static function generateKodeKategori()
    {
        $prefix = 'CTG';
        $lastKategori = self::where('kode_kategori', 'like', $prefix . '-%')
            ->orderBy('kode_kategori', 'desc')
            ->first();

        if ($lastKategori) {
            $lastNumber = (int) substr($lastKategori->kode_kategori, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKelompok($query, $kelompok)
    {
        if ($kelompok && $kelompok !== 'ALL') {
            return $query->where('kelompok', $kelompok);
        }
        return $query;
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
            $q->where('nama_kategori', 'like', "%{$search}%")
              ->orWhere('kode_kategori', 'like', "%{$search}%")
              ->orWhere('kelompok', 'like', "%{$search}%")
              ->orWhereHas('outlet', function($q) use ($search) {
                  $q->where('nama_outlet', 'like', "%{$search}%");
              });
        });
    }

    // Relationships
    public function inventori()
    {
        return $this->hasMany(Inventori::class, 'id_kategori', 'id_kategori');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public static function getKelompokOptions()
    {
        return ['Produk', 'Bahan', 'Aset', 'Lainnya'];
    }
}