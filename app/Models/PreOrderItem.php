<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_order_id',
        'produk_id',
        'deskripsi',
        'qty',
        'harga',
        'subtotal',
        'material_instalasi_biaya',
        'material_instalasi_satuan',
        'material_instalasi_keterangan',
        'pemasangan_pelatihan_biaya',
        'pemasangan_pelatihan_satuan',
        'pemasangan_pelatihan_keterangan',
        'ongkos_kirim_biaya',
        'ongkos_kirim_satuan',
        'ongkos_kirim_komponen',
        'total_biaya_tambahan'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'material_instalasi_biaya' => 'decimal:2',
        'pemasangan_pelatihan_biaya' => 'decimal:2',
        'ongkos_kirim_biaya' => 'decimal:2',
        'total_biaya_tambahan' => 'decimal:2',
        'ongkos_kirim_komponen' => 'array'
    ];

    public function preOrder()
    {
        return $this->belongsTo(PreOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }

    public function getProductImageAttribute()
    {
        if ($this->product && $this->product->gambar) {
            return asset('storage/' . $this->product->gambar);
        }
        
        return null;
    }

    public function getProductSpecificationsAttribute()
    {
        if ($this->product && $this->product->spesifikasi) {
            // Handle JSON specifications
            if (is_string($this->product->spesifikasi)) {
                $decoded = json_decode($this->product->spesifikasi, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            
            return $this->product->spesifikasi;
        }
        
        return null;
    }

    public function calculateTotalBiayaTambahan()
    {
        return $this->material_instalasi_biaya + $this->pemasangan_pelatihan_biaya + $this->ongkos_kirim_biaya;
    }

    public function getTotalWithAdditionalCostsAttribute()
    {
        return $this->subtotal + $this->calculateTotalBiayaTambahan();
    }

    public function getFormattedOngkosKirimKomponenAttribute()
    {
        if (!$this->ongkos_kirim_komponen) {
            return [];
        }
        
        return collect($this->ongkos_kirim_komponen)->map(function ($komponen) {
            return [
                'nama' => $komponen['nama'] ?? '',
                'biaya' => $komponen['biaya'] ?? 0,
                'formatted_biaya' => 'Rp ' . number_format($komponen['biaya'] ?? 0, 0, ',', '.')
            ];
        })->toArray();
    }
}