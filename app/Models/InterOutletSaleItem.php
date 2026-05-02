<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterOutletSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inter_outlet_sale_id',
        'id_produk',
        'kuantitas',
        'harga',
        'subtotal',
        'data_hpp',
    ];

    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'data_hpp' => 'array',
    ];

    /**
     * Relationship with inter outlet sale
     */
    public function interOutletSale()
    {
        return $this->belongsTo(InterOutletSale::class);
    }

    /**
     * Relationship with produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}