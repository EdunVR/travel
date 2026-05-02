<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HppProduk extends Model
{
    use HasFactory;

    protected $table = 'hpp_produk';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];


    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function product()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function scopeAvailableStock($query)
    {
        return $query->where('stok', '>', 0);
    }

    /**
     * Scope untuk mengurutkan berdasarkan FIFO
     */
    public function scopeFifo($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope untuk mengurutkan berdasarkan LIFO
     */
    public function scopeLifo($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
