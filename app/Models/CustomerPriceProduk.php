<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPriceProduk extends Model
{
    use HasFactory;

    protected $table = 'customer_price_produk';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_customer_price',
        'id_produk',
        'harga_khusus'
    ];

    protected $casts = [
        'id_customer_price' => 'integer',
        'id_produk' => 'integer',
        'harga_khusus' => 'decimal:2'
    ];

    /**
     * Relationship ke CustomerPrice
     */
    public function customerPrice()
    {
        return $this->belongsTo(CustomerPrice::class, 'id_customer_price', 'id_customer_price');
    }

    /**
     * Relationship ke Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Scope untuk filter berdasarkan customer price
     */
    public function scopeByCustomerPrice($query, $customerPriceId)
    {
        return $query->where('id_customer_price', $customerPriceId);
    }

    /**
     * Scope untuk filter berdasarkan produk
     */
    public function scopeByProduk($query, $produkId)
    {
        return $query->where('id_produk', $produkId);
    }

    /**
     * Get harga khusus dengan format
     */
    public function getFormattedHargaKhususAttribute()
    {
        return number_format($this->harga_khusus, 0, ',', '.');
    }
}
