<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_item';
    protected $primaryKey = 'id_purchase_order_item';
    
    protected $fillable = [
        'id_purchase_order',
        'tipe_item',
        'id_produk',
        'id_bahan',
        'deskripsi',
        'keterangan',
        'kuantitas',
        'satuan',
        'harga',
        'diskon',
        'subtotal'
    ];
    
    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_purchase_order');
    }
    
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
    
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}