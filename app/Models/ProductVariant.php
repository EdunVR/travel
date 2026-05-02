<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'nama_varian', 'sku', 'deskripsi', 'harga', 'stok', 'is_default'
    ];
    
    protected $casts = [
        'stok' => 'integer',
        'is_default' => 'boolean'
    ];
    
    public function product()
    {
        return $this->belongsTo(Produk::class);
    }
    
}
