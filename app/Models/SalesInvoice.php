<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $table = 'sales_invoice';
    protected $primaryKey = 'id_sales_invoice';
    
    protected $fillable = [
        'no_invoice',
        'tanggal',
        'id_member',
        'id_prospek',
        'id_outlet',
        'id_customer_price',
        'id_user',
        'id_penjualan',
        'total',
        'total_dibayar',
        'sisa_tagihan',
        'status',
        'due_date',
        'keterangan',
        'jenis_pembayaran',
        'penerima',
        'tanggal_pembayaran',
        'catatan_pembayaran',
        'bukti_transfer', 
        'nama_bank', 
        'nama_pengirim', 
        'jumlah_transfer', 
        'total_diskon',
        'subtotal',
        'id_ongkir',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'due_date' => 'date',
        'tanggal_pembayaran' => 'datetime',
        'total' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'jumlah_transfer' => 'decimal:2'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function prospek()
    {
        return $this->belongsTo(Prospek::class, 'id_prospek');
    }

    public function customerPrice()
    {
        return $this->belongsTo(CustomerPrice::class, 'id_customer_price');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'id_sales_invoice');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function journalEntries()
    {
        return $this->hasMany(Journal::class, 'reference_id')->where('module', 'penjualan');
    }

    public function paymentHistory()
    {
        return $this->hasMany(InvoicePaymentHistory::class, 'id_sales_invoice', 'id_sales_invoice')
                    ->orderBy('tanggal_bayar', 'desc');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan', 'id_penjualan');
    }

    // Scope untuk filter status
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Scope untuk filter outlet
    public function scopeOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'all') {
            return $query->whereHas('penjualan', function($q) use ($outletId) {
                $q->where('id_outlet', $outletId);
            });
        }
        return $query;
    }

    // Scope untuk filter tanggal
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        return $query;
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum(function($item) {
            return ($item->harga_normal * $item->kuantitas);
        });
        
        $totalDiskon = $this->items->sum(function($item) {
            return ($item->diskon * $item->kuantitas);
        });
        
        $total = $subtotal - $totalDiskon;
        
        $this->update([
            'subtotal' => $subtotal,
            'total_diskon' => $totalDiskon,
            'total' => $total
        ]);
    }

    public function ongkosKirim()
    {
        return $this->belongsTo(OngkosKirim::class, 'id_ongkir');
    }

    public function hasBuktiTransfer()
    {
        return !empty($this->bukti_transfer);
    }

    /**
     * Get URL bukti transfer
     */
    public function getBuktiTransferUrl()
    {
        if ($this->bukti_transfer) {
            return asset('storage/bukti-transfer/' . $this->bukti_transfer);
        }
        return null;
    }

    /**
     * Get path bukti transfer
     */
    public function getBuktiTransferPath()
    {
        if ($this->bukti_transfer) {
            return storage_path('app/public/bukti-transfer/' . $this->bukti_transfer);
        }
        return null;
    }

    /**
     * Accessor untuk sisa tagihan yang selalu akurat
     * Rumus: total - total_dibayar
     */
    public function getSisaTagihanAttribute($value)
    {
        // Jika ada nilai di database, gunakan itu
        // Tapi pastikan konsisten dengan perhitungan
        $calculated = $this->attributes['total'] - ($this->attributes['total_dibayar'] ?? 0);
        
        // Return calculated value untuk memastikan selalu akurat
        return $calculated;
    }

    /**
     * Check if invoice is partially paid
     */
    public function isPartiallyPaid()
    {
        return $this->total_dibayar > 0 && $this->sisa_tagihan > 0;
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid()
    {
        return $this->sisa_tagihan <= 0 && $this->total_dibayar >= $this->total;
    }
}