<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventori extends Model
{
    use HasFactory;

    protected $table = 'inventori';
    protected $primaryKey = 'id_inventori';
    protected $guarded = [];

    protected $fillable = [
        'kode_inventori',
        'nama_barang',
        'id_kategori',
        'id_outlet',
        'penanggung_jawab',
        'stok',
        'lokasi_penyimpanan',
        'status',
        'catatan',
        'is_active'
    ];

    protected $casts = [
        'stok' => 'integer',
        'is_active' => 'boolean'
    ];

    // Method untuk generate kode inventori otomatis
    public static function generateKodeInventori()
    {
        $prefix = 'INV';
        $lastInventori = self::where('kode_inventori', 'like', $prefix . '-%')
            ->orderBy('kode_inventori', 'desc')
            ->first();

        if ($lastInventori) {
            $lastNumber = (int) substr($lastInventori->kode_inventori, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Scopes untuk filter
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

    public function scopeByKategori($query, $kategoriId)
    {
        if ($kategoriId && $kategoriId !== 'ALL') {
            return $query->where('id_kategori', $kategoriId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'ALL') {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_barang', 'like', "%{$search}%")
              ->orWhere('kode_inventori', 'like', "%{$search}%")
              ->orWhere('penanggung_jawab', 'like', "%{$search}%")
              ->orWhere('lokasi_penyimpanan', 'like', "%{$search}%");
        });
    }

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function details()
    {
        return $this->hasMany(InventoriDetail::class, 'id_inventori');
    }
}