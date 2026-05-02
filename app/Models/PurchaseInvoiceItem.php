<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $table = 'purchase_invoice_item';
    protected $primaryKey = 'id_purchase_invoice_item';
    
    protected $fillable = [
        'id_purchase_invoice',
        'id_purchase_order_item',
        'deskripsi',
        'kuantitas',
        'satuan',
        'harga',
        'diskon',
        'pajak',
        'subtotal'
    ];
    
    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'id_purchase_invoice');
    }
    
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'id_purchase_order_item');
    }
    
    public function produk()
    {
        return $this->hasOneThrough(Produk::class, PurchaseOrderItem::class, 'id_purchase_order_item', 'id_produk', 'id_purchase_order_item', 'id_produk');
    }
}