<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $table = 'purchase_payment';
    protected $primaryKey = 'id_purchase_payment';
    
    protected $fillable = [
        'id_purchase_invoice',
        'tanggal_bayar',
        'metode_bayar',
        'jumlah_bayar',
        'kode_bank',
        'no_referensi',
        'keterangan',
        'status',
        'bukti_bayar'
    ];
    
    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar' => 'decimal:2'
    ];
    
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'id_purchase_invoice');
    }
    
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'draft' => 'Draft',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            'gagal' => 'Gagal'
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }
}