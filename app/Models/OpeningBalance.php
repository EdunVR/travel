<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpeningBalance extends Model
{
    use HasFactory;

    protected $table = 'opening_balances';
    
    protected $fillable = [
        'outlet_id',
        'book_id',
        'account_id',
        'debit',
        'credit',
        'effective_date',
        'description',
        'status',
        'created_by'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'effective_date' => 'date'
    ];

    // Relasi ke outlet
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    // Relasi ke accounting book
    public function accountingBook(): BelongsTo
    {
        return $this->belongsTo(AccountingBook::class, 'book_id');
    }

    // Relasi ke chart of account
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // Relasi ke user yang membuat
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk filter berdasarkan outlet
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    // Scope untuk filter berdasarkan book
    public function scopeByBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    // Scope untuk filter berdasarkan account
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriod($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('effective_date', [$startDate, $endDate]);
        }
        return $query->where('effective_date', '>=', $startDate);
    }

    // Scope untuk saldo aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Method untuk mendapatkan saldo netto
    public function getBalanceAttribute(): float
    {
        return $this->debit - $this->credit;
    }

    // Method untuk mengecek apakah saldo seimbang
    public function getIsBalancedAttribute(): bool
    {
        return $this->debit === $this->credit;
    }

    // Method untuk validasi saldo
    public function validateBalance(): array
    {
        $errors = [];

        // Validasi account exists dan aktif
        if (!$this->account || $this->account->status !== 'active') {
            $errors[] = 'Akun tidak ditemukan atau tidak aktif';
        }

        // Validasi outlet exists
        if (!$this->outlet) {
            $errors[] = 'Outlet tidak ditemukan';
        }

        // Validasi book exists dan aktif
        if (!$this->accountingBook || $this->accountingBook->status !== 'active') {
            $errors[] = 'Buku akuntansi tidak ditemukan atau tidak aktif';
        }

        // Validasi tidak boleh debit dan credit sama-sama > 0
        if ($this->debit > 0 && $this->credit > 0) {
            $errors[] = 'Tidak boleh mengisi debit dan kredit secara bersamaan';
        }

        return $errors;
    }

    // Method untuk posting saldo awal ke journal entries
    public function postToJournal(): bool
    {
        try {
            // Cek apakah sudah diposting
            if ($this->status === 'posted') {
                return true;
            }

            // Buat journal entry
            $journalEntry = new JournalEntry();
            $journalEntry->book_id = $this->book_id;
            $journalEntry->outlet_id = $this->outlet_id;
            $journalEntry->transaction_number = JournalEntry::generateTransactionNumber($this->book_id);
            $journalEntry->transaction_date = $this->effective_date;
            $journalEntry->description = 'Saldo Awal - ' . $this->account->name . ($this->description ? ' - ' . $this->description : '');
            $journalEntry->reference_type = 'opening_balance';
            $journalEntry->reference_number = 'OB-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $journalEntry->total_debit = $this->debit;
            $journalEntry->total_credit = $this->credit;
            $journalEntry->status = 'posted';
            $journalEntry->posted_at = now();
            $journalEntry->save();

            // Buat journal entry detail
            $journalDetail = new JournalEntryDetail();
            $journalDetail->journal_entry_id = $journalEntry->id;
            $journalDetail->account_id = $this->account_id;
            $journalDetail->debit = $this->debit;
            $journalDetail->credit = $this->credit;
            $journalDetail->description = $journalEntry->description;
            $journalDetail->reference_type = 'opening_balance';
            $journalDetail->reference_number = $journalEntry->reference_number;
            $journalDetail->save();

            // Update status saldo awal
            $this->status = 'posted';
            $this->save();

            // Update accounting book entries count
            $this->accountingBook->incrementEntries();
            $this->accountingBook->calculateClosingBalance();

            return true;

        } catch (\Exception $e) {
            \Log::error('Error posting opening balance to journal: ' . $e->getMessage());
            return false;
        }
    }

    // Static method untuk validasi keseluruhan saldo awal
    public static function validateTotalBalance($outletId, $bookId, $effectiveDate): array
    {
        $totalDebit = self::where('outlet_id', $outletId)
            ->where('book_id', $bookId)
            ->where('effective_date', $effectiveDate)
            ->where('status', 'active')
            ->sum('debit');

        $totalCredit = self::where('outlet_id', $outletId)
            ->where('book_id', $bookId)
            ->where('effective_date', $effectiveDate)
            ->where('status', 'active')
            ->sum('credit');

        $balance = $totalDebit - $totalCredit;

        return [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balance' => $balance,
            'is_balanced' => abs($balance) < 0.01, // Allow small rounding difference
            'difference' => abs($balance)
        ];
    }

}