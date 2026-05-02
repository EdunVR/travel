<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComponent extends Model
{
    protected $fillable = [
        'product_id',
        'component_id', // Tambahkan ini
        'qty',
        'subtotal'
    ];
    
    // Relasi ke produk utama
    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }
    
    // Relasi ke produk komponen
    public function component()
    {
        return $this->belongsTo(Produk::class, 'component_id');
    }
}
