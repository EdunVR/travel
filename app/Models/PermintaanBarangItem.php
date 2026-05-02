<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanBarangItem extends Model
{
    protected $table = 'permintaan_barang_items';
    
    protected $fillable = [
        'permintaan_barang_id',
        'tipe_item',
        'produk_id',
        'bahan_id',
        'nama_item',
        'spesifikasi',
        'qty',
        'satuan',
        'estimasi_harga',
        'total_estimasi',
        'catatan'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'estimasi_harga' => 'decimal:2',
        'total_estimasi' => 'decimal:2'
    ];

    // Relationships
    public function permintaanBarang(): BelongsTo
    {
        return $this->belongsTo(PermintaanBarang::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class);
    }

    // Accessors
    public function getTipeItemBadgeAttribute()
    {
        $badges = [
            'produk' => 'bg-blue-100 text-blue-800',
            'bahan' => 'bg-green-100 text-green-800',
            'custom' => 'bg-purple-100 text-purple-800'
        ];

        return $badges[$this->tipe_item] ?? 'bg-gray-100 text-gray-800';
    }

    // Methods
    public function calculateTotal()
    {
        $this->total_estimasi = $this->qty * $this->estimasi_harga;
        return $this->total_estimasi;
    }
}
