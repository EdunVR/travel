<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POPaymentHistory extends Model
{
    protected $table = 'po_payment_history';
    protected $primaryKey = 'id_payment';
    
    protected $fillable = [
        'id_purchase_order',
        'tanggal_pembayaran',
        'jumlah_pembayaran',
        'jenis_pembayaran',
        'bukti_pembayaran',
        'penerima',
        'catatan',
    ];
    
    protected $casts = [
        'jumlah_pembayaran' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
    ];
    
    /**
     * Get the purchase order that owns the payment
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_purchase_order', 'id_purchase_order');
    }
}
