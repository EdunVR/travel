<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'service_invoice_items';
    protected $primaryKey = 'id_service_invoice_item';
    protected $fillable = [
        'id_service_invoice', 'id_produk', 'deskripsi', 'keterangan', 
        'kuantitas', 'satuan', 'harga', 'subtotal', 'tipe',
        'is_sparepart', 'jenis_kendaraan', 'kode_sparepart', 'diskon', 'harga_setelah_diskon', 'id_sparepart'
    ];

    public function invoice()
    {
        return $this->belongsTo(ServiceInvoice::class, 'id_service_invoice');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function sparepart()
    {
        return $this->belongsTo(\App\Models\Sparepart::class, 'id_sparepart', 'id_sparepart');
    }
}