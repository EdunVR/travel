<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSale extends Model
{
    use HasFactory;

    protected $table = 'pos_sales';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'id_outlet',
        'id_member',
        'id_user',
        'subtotal',
        'diskon_persen',
        'diskon_nominal',
        'total_diskon',
        'ppn',
        'total',
        'jenis_pembayaran',
        'jumlah_bayar',
        'kembalian',
        'status',
        'catatan',
        'is_bon',
        'id_penjualan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'diskon_nominal' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'ppn' => 'decimal:2',
        'total' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'is_bon' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(PosSaleItem::class, 'pos_sale_id');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan', 'id_penjualan');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'pos');
    }

    /**
     * Generate nomor transaksi POS dengan prefix outlet
     */
    public static function generateTransactionNumber($outletId)
    {
        $date = now();
        $month = $date->format('m');
        $year = $date->format('Y');
        
        // Get outlet info for prefix
        $outlet = Outlet::find($outletId);
        $outletPrefix = $outlet ? strtoupper(substr($outlet->nama_outlet, 0, 3)) : 'OUT';
        
        // Use database lock to prevent race conditions
        return \DB::transaction(function() use ($outletId, $year, $month, $outletPrefix) {
            // Get last number for this outlet and month with lock
            $lastSale = static::where('id_outlet', $outletId)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->lockForUpdate() // This prevents race conditions
                ->orderBy('id', 'desc')
                ->first();
            
            // Extract sequence number from last transaction
            $sequence = 1;
            if ($lastSale && $lastSale->no_transaksi) {
                // Extract number from format: 0001/PBU/POS/12/2025
                $parts = explode('/', $lastSale->no_transaksi);
                if (count($parts) >= 1) {
                    $sequence = intval($parts[0]) + 1;
                }
            }
            
            // Double-check for uniqueness (additional safety)
            $attempts = 0;
            do {
                $transactionNumber = sprintf('%04d/%s/POS/%s/%s', $sequence, $outletPrefix, $month, $year);
                $exists = static::where('no_transaksi', $transactionNumber)->exists();
                
                if ($exists) {
                    $sequence++;
                    $attempts++;
                    
                    // Prevent infinite loop
                    if ($attempts > 100) {
                        throw new \Exception('Unable to generate unique transaction number after 100 attempts');
                    }
                }
            } while ($exists);
            
            return $transactionNumber;
        });
    }

    /**
     * Scope untuk filter outlet
     */
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'all') {
            return $query->where('id_outlet', $outletId);
        }
        return $query;
    }

    /**
     * Scope untuk filter tanggal (inclusive)
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereDate('tanggal', '>=', $startDate)
                        ->whereDate('tanggal', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope untuk filter status
     */
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }
}
