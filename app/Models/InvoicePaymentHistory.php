<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoicePaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'invoice_payment_history';
    
    protected $fillable = [
        'id_sales_invoice',
        'tanggal_bayar',
        'jumlah_bayar',
        'jenis_pembayaran',
        'nama_bank',
        'nama_pengirim',
        'penerima',
        'bukti_pembayaran',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns the payment
     */
    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'id_sales_invoice', 'id_sales_invoice');
    }

    /**
     * Get the user who created this payment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the full URL for bukti pembayaran
     */
    public function getBuktiPembayaranUrlAttribute()
    {
        if ($this->bukti_pembayaran) {
            return asset('storage/' . $this->bukti_pembayaran);
        }
        return null;
    }
}
