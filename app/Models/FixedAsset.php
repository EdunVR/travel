<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedAsset extends Model
{
    protected $fillable = [
        'outlet_id',
        'book_id',
        'code',
        'name',
        'category',
        'location',
        'acquisition_date',
        'acquisition_cost',
        'salvage_value',
        'useful_life',
        'depreciation_method',
        'asset_account_id',
        'depreciation_expense_account_id',
        'accumulated_depreciation_account_id',
        'payment_account_id',
        'accumulated_depreciation',
        'book_value',
        'status',
        'disposal_date',
        'disposal_value',
        'disposal_notes',
        'description',
        'created_by'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'disposal_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'disposal_value' => 'decimal:2',
    ];

    // Relationships
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(AccountingBook::class, 'book_id');
    }

    public function assetAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'asset_account_id');
    }

    public function depreciationExpenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function accumulatedDepreciationAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account_id');
    }

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(FixedAssetDepreciation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Methods
    public function calculateMonthlyDepreciation(): float
    {
        $realTimeBookValue = $this->getRealTimeBookValue();
        
        if ($realTimeBookValue <= $this->salvage_value) {
            return 0;
        }

        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;

        switch ($this->depreciation_method) {
            case 'straight_line':
                $monthlyDepreciation = $depreciableAmount / $this->useful_life / 12;
                break;

            case 'declining_balance':
                $rate = 1.5 / $this->useful_life;
                $monthlyDepreciation = $realTimeBookValue * $rate / 12;
                break;

            case 'double_declining':
                $rate = 2 / $this->useful_life;
                $monthlyDepreciation = $realTimeBookValue * $rate / 12;
                break;

            default:
                $monthlyDepreciation = $depreciableAmount / $this->useful_life / 12;
        }

        // Ensure depreciation doesn't exceed depreciable amount
        $remainingDepreciable = $realTimeBookValue - $this->salvage_value;
        return min($monthlyDepreciation, $remainingDepreciable);
    }

    public function calculateRemainingLife(): float
    {
        $realTimeAccumulatedDepreciation = $this->getRealTimeAccumulatedDepreciation();
        
        if ($realTimeAccumulatedDepreciation == 0) {
            return $this->useful_life;
        }

        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;
        $depreciationRate = $realTimeAccumulatedDepreciation / $depreciableAmount;
        
        return max(0, $this->useful_life * (1 - $depreciationRate));
    }

    public function canBeDeleted(): bool
    {
        // Cannot delete if has posted journal entries
        $hasPostedDepreciation = $this->depreciations()
            ->where('status', 'posted')
            ->exists();

        return !$hasPostedDepreciation;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'draft';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function updateBookValue(): void
    {
        $this->book_value = $this->acquisition_cost - $this->accumulated_depreciation;
        $this->save();
    }

    /**
     * Get real-time accumulated depreciation including draft entries
     */
    public function getRealTimeAccumulatedDepreciation(): float
    {
        return $this->depreciations()->sum('amount');
    }

    /**
     * Get real-time book value including draft depreciation entries
     */
    public function getRealTimeBookValue(): float
    {
        return $this->acquisition_cost - $this->getRealTimeAccumulatedDepreciation();
    }

    public static function generateCode($outletId): string
    {
        $date = now()->format('Ym');
        $prefix = "AST-{$date}-";
        
        $lastAsset = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}