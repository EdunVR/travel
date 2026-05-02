<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoPenjualanDetail extends Model
{
    protected $table = 'po_penjualan_detail';
    protected $primaryKey = 'id_po_penjualan_detail';
    
    protected $fillable = [
        'id_po_penjualan',
        'id_produk',
        'harga_jual',
        'jumlah',
        'diskon',
        'subtotal',
        'hpp',
        'id_hpp',
        'tipe_item' // 'produk' atau 'ongkir'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function poPenjualan()
    {
        return $this->belongsTo(PoPenjualan::class, 'id_po_penjualan');
    }
}