<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InterOutletSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'outlet_asal',
        'outlet_tujuan',
        'id_user',
        'subtotal',
        'diskon_persen',
        'diskon_nominal',
        'total_diskon',
        'ppn',
        'total',
        'status',
        'catatan',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'diskon_nominal' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'ppn' => 'decimal:2',
        'total' => 'decimal:2',
        'outlet_asal' => 'integer',
        'outlet_tujuan' => 'integer',
        'id_user' => 'integer',
        'approved_by' => 'integer',
    ];

    /**
     * Generate transaction number with improved uniqueness and race condition handling
     */
    public static function generateTransactionNumber($outletId, $transactionDate = null)
    {
        $outlet = Outlet::find($outletId);
        $prefix = $outlet ? $outlet->kode_outlet : 'OUT';
        
        // Use provided date or current date
        $date = $transactionDate ? \Carbon\Carbon::parse($transactionDate) : now();
        $dateStr = $date->format('Ymd');
        
        // Use database transaction with locking to prevent race conditions
        return DB::transaction(function () use ($outletId, $dateStr, $prefix, $date) {
            // Get the last transaction for this outlet and date with lock
            $lastTransaction = self::where('outlet_asal', $outletId)
                ->whereDate('tanggal', $date->format('Y-m-d'))
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();
            
            $sequence = 1;
            if ($lastTransaction) {
                $lastNumber = $lastTransaction->no_transaksi;
                // Extract sequence from the last 4 characters
                $lastSequence = (int) substr($lastNumber, -4);
                $sequence = $lastSequence + 1;
            }
            
            // Generate transaction number
            $transactionNumber = "IOS-{$prefix}-{$dateStr}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            // Double check for uniqueness (extra safety)
            $maxAttempts = 10;
            $attempt = 0;
            
            while ($attempt < $maxAttempts) {
                $exists = self::where('no_transaksi', $transactionNumber)->exists();
                if (!$exists) {
                    break;
                }
                
                // If exists, increment sequence and try again
                $sequence++;
                $transactionNumber = "IOS-{$prefix}-{$dateStr}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                $attempt++;
            }
            
            if ($attempt >= $maxAttempts) {
                throw new \Exception("Unable to generate unique transaction number after {$maxAttempts} attempts");
            }
            
            return $transactionNumber;
        });
    }

    /**
     * Relationship with outlet asal
     */
    public function outletAsal()
    {
        return $this->belongsTo(Outlet::class, 'outlet_asal', 'id_outlet');
    }

    /**
     * Relationship with outlet tujuan
     */
    public function outletTujuan()
    {
        return $this->belongsTo(Outlet::class, 'outlet_tujuan', 'id_outlet');
    }

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relationship with approved by user
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship with items
     */
    public function items()
    {
        return $this->hasMany(InterOutletSaleItem::class);
    }

    /**
     * Scope by outlet (asal atau tujuan)
     */
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId === 'all') {
            return $query;
        }
        
        return $query->where(function($q) use ($outletId) {
            $q->where('outlet_asal', $outletId)
              ->orWhere('outlet_tujuan', $outletId);
        });
    }

    /**
     * Scope by status
     */
    public function scopeStatus($query, $status)
    {
        if ($status === 'all') {
            return $query;
        }
        
        return $query->where('status', $status);
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }
        
        return $query;
    }
}