<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'sales_invoice_item';
    protected $primaryKey = 'id_sales_invoice_item';
    
    protected $fillable = [
        'id_sales_invoice',
        'id_produk',
        'deskripsi',
        'keterangan',
        'kuantitas',
        'satuan',
        'harga',
        'subtotal',
        'tipe',
        'diskon',
        'harga_normal'
    ];

    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'id_sales_invoice');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function ongkosKirim(): BelongsTo
    {
        return $this->belongsTo(OngkosKirim::class, 'id_ongkir', 'id_ongkir');
    }
}