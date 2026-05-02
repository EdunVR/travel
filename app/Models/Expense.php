<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'outlet_id',
        'book_id',
        'rab_id',
        'realisasi_id',
        'is_auto_generated',
        'account_id',
        'cash_account_id',
        'reference_number',
        'expense_date',
        'category',
        'description',
        'amount',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'attachment'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'is_auto_generated' => 'boolean'
    ];

    /**
     * Get the outlet that owns the expense
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    /**
     * Get the accounting book for this expense
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\Models\AccountingBook::class, 'book_id');
    }

    /**
     * Get the RAB template for this expense
     */
    public function rab(): BelongsTo
    {
        return $this->belongsTo(\App\Models\RabTemplate::class, 'rab_id', 'id_rab');
    }

    /**
     * Get the chart of account for this expense
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    /**
     * Get the cash/bank account for this expense
     */
    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_account_id');
    }

    /**
     * Get the user who approved this expense
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the RAB realisasi history for this expense
     */
    public function realisasi(): BelongsTo
    {
        return $this->belongsTo(\App\Models\RabRealisasiHistory::class, 'realisasi_id');
    }

    /**
     * Get journal entries related to this expense
     */
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference_number', 'reference_number');
    }

    /**
     * Generate reference number
     */
    public static function generateReferenceNumber(): string
    {
        $lastExpense = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastExpense ? (int) substr($lastExpense->reference_number, -4) + 1 : 1;
        
        return 'EXP-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
