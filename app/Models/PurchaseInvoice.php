<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $table = 'purchase_invoice';
    protected $primaryKey = 'id_purchase_invoice';
    
    protected $fillable = [
        'no_invoice',
        'id_purchase_order',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'subtotal',
        'total_pajak',
        'total_diskon',
        'total',
        'status',
        'metode_pembayaran',
        'keterangan',
        'tanggal_bayar'
    ];
    
    protected $casts = [
        'tanggal_invoice' => 'datetime',
        'tanggal_jatuh_tempo' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'subtotal' => 'decimal:2',
        'total_pajak' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'total' => 'decimal:2'
    ];
    
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_purchase_order');
    }
    
    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'id_purchase_invoice');
    }
    
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class, 'id_purchase_invoice');
    }
    
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'draft' => 'Draft',
            'diproses' => 'Diproses',
            'dibayar' => 'Dibayar',
            'jatuh_tempo' => 'Jatuh Tempo',
            'dibatalkan' => 'Dibatalkan'
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }
    
    public function getSisaHariAttribute()
    {
        if (!in_array($this->status, ['draft', 'diproses']) || !$this->tanggal_jatuh_tempo) {
            return null;
        }
        
        $now = now();
        $dueDate = $this->tanggal_jatuh_tempo;
        return $now->diffInDays($dueDate, false);
    }
    
    public function getTotalDibayarAttribute()
    {
        return $this->payments()->where('status', 'selesai')->sum('jumlah_bayar');
    }
    
    public function getSisaPembayaranAttribute()
    {
        return $this->total - $this->total_dibayar;
    }
    
    public function isLunas()
    {
        return $this->sisa_pembayaran <= 0;
    }
}