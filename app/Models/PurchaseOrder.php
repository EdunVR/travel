<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_order';
    protected $primaryKey = 'id_purchase_order';
    
    protected $fillable = [
        'no_po',
        'tanggal',
        'id_supplier',
        'id_outlet',
        'id_user',
        'subtotal',
        'total_diskon',
        'total',
        'total_dibayar',
        'sisa_pembayaran',
        'status',
        'due_date',
        'keterangan',
        'metode_pengiriman',
        'alamat_pengiriman',
        'catatan_status',
        'tanggal_dibayar',
        'tanggal_selesai',
        // Tambahan kolom baru
        'tanggal_payment',
        'metode_payment',
        'no_referensi_payment',
        'catatan_payment',
        'tanggal_vendor_bill',
        'no_vendor_bill',
        'catatan_vendor_bill',
        'tanggal_penerimaan',
        'penerima_barang',
        'catatan_penerimaan',
        'tanggal_quotation',
        'no_quotation',
        'catatan_quotation'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'due_date' => 'datetime',
        'tanggal_dibayar' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'tanggal_payment' => 'datetime',
        'tanggal_vendor_bill' => 'datetime',
        'tanggal_penerimaan' => 'datetime',
        'tanggal_quotation' => 'datetime',
        'subtotal' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2'
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
    
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'id_purchase_order');
    }
    
    public function invoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'id_purchase_order');
    }
    
    public function paymentHistory()
    {
        return $this->hasMany(POPaymentHistory::class, 'id_purchase_order', 'id_purchase_order');
    }

    public function hutang()
    {
        return $this->hasOne(Hutang::class, 'id_pembelian', 'id_purchase_order');
    }

    public function getStatusTextAttribute()
    {
        $statusMap = [
            'permintaan_pembelian' => 'Permintaan Pembelian',
            'request_quotation' => 'Request Quotation', 
            'purchase_order' => 'Purchase Order',
            'penerimaan_barang' => 'Penerimaan Barang',
            'vendor_bill' => 'Vendor Bill',
            'payment' => 'Payment',
            'dibatalkan' => 'Dibatalkan'
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }

    public function isPaid()
    {
        return $this->status === 'dibayar';
    }

    public function canBeProcessed()
    {
        return $this->status === 'dibayar';
    }

    public function canBePaid()
    {
        return $this->status === 'draft' && $this->invoices->count() > 0;
    }

    public static function generateDraftNumber()
    {
        $maxAttempts = 10;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $datePart = date('Ymd');
            $sequence = self::where('no_po', 'like', "DRAFT/{$datePart}/%")
                ->count() + 1;
                
            $draftNumber = "DRAFT/{$datePart}/" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            
            // Check if this number already exists
            $exists = self::where('no_po', $draftNumber)->exists();
            
            if (!$exists) {
                return $draftNumber;
            }
            
            $attempt++;
        }
        
        // Fallback dengan timestamp jika masih gagal
        return "DRAFT/" . date('YmdHis') . "/" . uniqid();
    }
}