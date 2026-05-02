<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceInvoice extends Model
{
    use HasFactory;

    protected $table = 'service_invoices';
    protected $primaryKey = 'id_service_invoice';
    protected $fillable = [
        'no_invoice', 'tanggal', 'tanggal_mulai_service', 'tanggal_selesai_service', 'id_member', 'id_mesin_customer', 
        'jenis_service', 'jumlah_teknisi', 'jumlah_jam', 'biaya_teknisi', 
        'is_garansi', 'diskon', 'total_sebelum_diskon', 'total', 
        'status', 'due_date', 'catatan',
        'tanggal_service_berikutnya', 'id_invoice_sebelumnya', 'service_lanjutan_ke',
        'jenis_pembayaran', 'penerima', 'tanggal_pembayaran', 'catatan_pembayaran', 'id_user', 'keterangan_service',
        'outlet_id', // Add outlet_id to fillable
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai_service' => 'date',
        'tanggal_selesai_service' => 'date',
        'due_date' => 'datetime',
        'tanggal_pembayaran' => 'datetime',
        'is_garansi' => 'boolean',
        'diskon' => 'decimal:0',
        'total_sebelum_diskon' => 'decimal:0',
        'tanggal_service_berikutnya' => 'date'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function mesinCustomer()
    {
        return $this->belongsTo(MesinCustomer::class, 'id_mesin_customer');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function invoiceSebelumnya()
    {
        return $this->belongsTo(ServiceInvoice::class, 'id_invoice_sebelumnya');
    }

    public function serviceLanjutan()
    {
        return $this->hasMany(ServiceInvoice::class, 'id_invoice_sebelumnya');
    }

    public function items()
    {
        return $this->hasMany(ServiceInvoiceItem::class, 'id_service_invoice');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function getUrutanServiceLanjutanAttribute()
    {
        if ($this->service_lanjutan_ke > 0) {
            return $this->service_lanjutan_ke;
        }
        
        // Jika tidak ada invoice sebelumnya, ini adalah service pertama
        if (!$this->id_invoice_sebelumnya) {
            return 0;
        }
        
        // Hitung dari invoice sebelumnya
        $invoiceSebelumnya = $this->invoiceSebelumnya;
        return $invoiceSebelumnya ? $invoiceSebelumnya->service_lanjutan_ke + 1 : 0;
    }

    // Scope untuk filter status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk invoice yang masih aktif (bukan gagal)
    public function scopeAktif($query)
    {
        return $query->where('status', '!=', 'gagal');
    }

    // Hitung sisa hari jatuh tempo
    public function getSisaHariAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        
        $now = now();
        $dueDate = $this->due_date;
        
        if ($dueDate < $now) {
            return -$dueDate->diffInDays($now);
        }
        
        return $dueDate->diffInDays($now);
    }

    // Cek apakah sudah lewat jatuh tempo
    public function getIsTerlambatAttribute()
    {
        return $this->due_date && $this->due_date < now();
    }

    public function getMemberCodeWithPrefix()
    {
        $member = $this->member;
        if (!$member || !$member->kode_member) {
            return null;
        }

        // Get closing type from the first mesin customer's produk
        $closingType = 'jual_putus'; // default
        
        if ($this->mesinCustomer && $this->mesinCustomer->produk->isNotEmpty()) {
            $firstProduk = $this->mesinCustomer->produk->first();
            $closingType = $firstProduk->pivot->closing_type ?? 'jual_putus';
        }

        // Determine prefix
        $prefix = 'J'; // Jual Putus
        if ($closingType === 'deposit') {
            $prefix = 'D';
        }

        return $prefix . '-' . $member->kode_member;
    }

    /**
     * Get closing type for display
     */
    public function getClosingTypeDisplay()
    {
        if ($this->mesinCustomer && $this->mesinCustomer->produk->isNotEmpty()) {
            $closingTypes = $this->mesinCustomer->produk->pluck('pivot.closing_type')->unique();
            
            if ($closingTypes->contains('deposit') && $closingTypes->contains('jual_putus')) {
                return 'Mixed';
            } elseif ($closingTypes->contains('deposit')) {
                return 'Deposit';
            }
        }
        
        return 'Jual Putus';
    }
}