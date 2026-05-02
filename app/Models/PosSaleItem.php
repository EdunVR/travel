<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSaleItem extends Model
{
    use HasFactory;

    protected $table = 'pos_sale_items';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'pos_sale_id',
        'id_produk',
        'nama_produk',
        'sku',
        'kuantitas',
        'satuan',
        'harga',
        'subtotal',
        'tipe',
    ];

    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function posSale()
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
