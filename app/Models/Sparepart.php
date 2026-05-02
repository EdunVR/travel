<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'spareparts';
    protected $primaryKey = 'id_sparepart';
    
    protected $fillable = [
        'outlet_id',
        'kode_sparepart',
        'nama_sparepart',
        'merk',
        'spesifikasi',
        'harga',
        'stok',
        'stok_minimum',
        'satuan',
        'is_active',
        'keterangan'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationship
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    // Scope untuk sparepart aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk filter by outlet
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId) {
            return $query->where('outlet_id', $outletId);
        }
        return $query;
    }

    // Scope untuk stok tersedia
    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0);
    }

    // Cek apakah stok mencukupi
    public function isStokMencukupi($jumlah)
    {
        return $this->stok >= $jumlah;
    }

    // Kurangi stok
    public function kurangiStok($jumlah)
    {
        if ($this->isStokMencukupi($jumlah)) {
            $this->stok -= $jumlah;
            return $this->save();
        }
        return false;
    }

    // Tambah stok
    public function tambahStok($jumlah)
    {
        $this->stok += $jumlah;
        return $this->save();
    }

    // Cek stok minimum
    public function isStokMinimum()
    {
        return $this->stok <= $this->stok_minimum;
    }

    public function logs()
    {
        return $this->hasMany(SparepartLog::class, 'id_sparepart', 'id_sparepart');
    }

    public function stokLogs()
    {
        return $this->hasMany(SparepartLog::class, 'id_sparepart', 'id_sparepart')->stok();
    }

    public function hargaLogs()
    {
        return $this->hasMany(SparepartLog::class, 'id_sparepart', 'id_sparepart')->harga();
    }
}