<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'account_id',
        'reconciliation_date',
        'period_month',
        'bank_statement_balance',
        'book_balance',
        'adjusted_balance',
        'difference',
        'status',
        'notes',
        'reconciled_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'bank_statement_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'adjusted_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'account_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BankReconciliationItem::class, 'reconciliation_id');
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriod($query, $periodMonth)
    {
        return $query->where('period_month', $periodMonth);
    }
}
