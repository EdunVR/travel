<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingBook extends Model
{
    use HasFactory;

    protected $table = 'accounting_books';
    
    protected $fillable = [
        'outlet_id',
        'code',
        'name',
        'type',
        'description',
        'currency',
        'start_date',
        'end_date',
        'opening_balance',
        'closing_balance',
        'total_entries',
        'status',
        'is_locked',
        'locked_at',
        'closed_at',
        'closed_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    protected $appends = ['period'];

    // Relasi ke outlet
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    // Scope untuk filter aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Scope untuk filter berdasarkan outlet
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    // Scope untuk filter berdasarkan tipe
    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type', $type);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Scope untuk buku yang sedang berjalan (periode aktif)
    public function scopeCurrentPeriod($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    // Method untuk mendapatkan nama tipe dalam bahasa Indonesia
    public function getTypeNameAttribute(): string
    {
        $types = [
            'general' => 'Umum',
            'cash' => 'Kas',
            'bank' => 'Bank',
            'sales' => 'Penjualan',
            'purchase' => 'Pembelian',
            'inventory' => 'Persediaan',
            'payroll' => 'Penggajian'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Method untuk mendapatkan nama status dalam bahasa Indonesia
    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'draft' => 'Draft',
            'closed' => 'Ditutup'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Method untuk mendapatkan periode dalam format yang readable
    public function getPeriodAttribute(): string
    {
        // Pastikan start_date dan end_date adalah Carbon instance
        $start = \Carbon\Carbon::parse($this->start_date)->translatedFormat('M Y');
        $end = \Carbon\Carbon::parse($this->end_date)->translatedFormat('M Y');
        return "{$start} - {$end}";
    }

    // Tambahkan accessor untuk created_at yang diformat
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at->translatedFormat('d M Y');
    }

    // Method untuk mengecek apakah buku bisa di-edit
    public function getCanEditAttribute(): bool
    {
        return !$this->is_locked && $this->status !== 'closed';
    }

    // Method untuk mengecek apakah buku bisa dihapus
    public function getCanDeleteAttribute(): bool
    {
        return !$this->is_locked && $this->status === 'draft' && $this->total_entries === 0;
    }

    public static function generateBookCode($outletId, $type = 'general'): string
    {
        try {
            $outlet = Outlet::find($outletId);
            
            if (!$outlet) {
                \Log::warning('Outlet not found for ID: ' . $outletId);
                $outletPrefix = '001';
            } else {
                // Ambil 3 digit terakhir dari kode outlet
                $outletCode = $outlet->kode_outlet;
                $outletPrefix = substr($outletCode, -3);
                
                // Jika panjang tidak cukup, pad dengan 0
                if (strlen($outletPrefix) < 3) {
                    $outletPrefix = str_pad($outletPrefix, 3, '0', STR_PAD_LEFT);
                }
            }
            
            $typePrefix = self::getTypePrefix($type);
            
            // Cari buku terakhir dengan type yang sama di outlet ini
            $lastBook = self::where('outlet_id', $outletId)
                ->where('type', $type)
                ->orderBy('code', 'desc')
                ->first();

            $newNumber = 1;
            if ($lastBook) {
                // Extract number dari kode terakhir (format: BB-001-001)
                $codeParts = explode('-', $lastBook->code);
                
                // Debug log
                \Log::info('Last book code: ' . $lastBook->code);
                \Log::info('Code parts: ' . json_encode($codeParts));
                
                if (count($codeParts) >= 3) {
                    $lastNumber = intval($codeParts[2]);
                    $newNumber = $lastNumber + 1;
                } else {
                    // Fallback: cari berdasarkan pattern
                    $lastBookSameType = self::where('outlet_id', $outletId)
                        ->where('type', $type)
                        ->where('code', 'like', $typePrefix . '-%')
                        ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
                        ->first();
                        
                    if ($lastBookSameType) {
                        $lastNumber = intval(substr($lastBookSameType->code, -3));
                        $newNumber = $lastNumber + 1;
                    }
                }
            }

            $generatedCode = "{$typePrefix}-{$outletPrefix}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            
            \Log::info('Generated book code: ' . $generatedCode . ' for outlet: ' . $outletId . ', type: ' . $type);
            
            return $generatedCode;

        } catch (\Exception $e) {
            \Log::error('Error in generateBookCode: ' . $e->getMessage());
            // Fallback code
            return 'BB-001-001';
        }
    }

    private static function getTypePrefix($type): string
    {
        $prefixes = [
            'general' => 'BB', // Buku Besar
            'cash' => 'BK',    // Buku Kas
            'bank' => 'BBK',   // Buku Bank
            'sales' => 'BP',   // Buku Penjualan
            'purchase' => 'BPM', // Buku Pembelian
            'inventory' => 'BPS', // Buku Persediaan
            'payroll' => 'BGA' // Buku Gaji
        ];

        return $prefixes[$type] ?? 'BUK';
    }

    // Method untuk update saldo closing
    public function updateClosingBalance(): bool
    {
        // Logika untuk menghitung saldo closing berdasarkan entri jurnal
        // Untuk sementara, kita set sama dengan opening balance
        $this->closing_balance = $this->opening_balance;
        return $this->save();
    }

    // Method untuk lock buku
    public function lock(): bool
    {
        $this->is_locked = true;
        $this->locked_at = now();
        return $this->save();
    }

    // Method untuk unlock buku
    public function unlock(): bool
    {
        $this->is_locked = false;
        $this->locked_at = null;
        return $this->save();
    }

    // Relasi ke journal entries
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'book_id');
    }

    // Method untuk menambah total entries
    public function incrementEntries(): bool
    {
        $this->total_entries++;
        return $this->save();
    }

    public function calculateClosingBalance(): bool
    {
        $totalBalance = $this->journalEntries()
            ->where('status', 'posted')
            ->get()
            ->sum(function($entry) {
                return $entry->total_debit - $entry->total_credit;
            });
        
        $this->closing_balance = $this->opening_balance + $totalBalance;
        return $this->save();
    }
}