<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AffiliateVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_affiliator',
        'code',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_transaction',
        'usage_limit',
        'usage_count',
        'valid_from',
        'valid_until',
        'is_active',
        'description',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class, 'id_affiliator');
    }

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class, 'id_voucher');
    }

    public function bookings()
    {
        return $this->hasMany(JamaahBooking::class, 'id_voucher');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where(function($q) use ($now) {
            $q->where(function($subQ) use ($now) {
                $subQ->whereNull('valid_from')
                     ->orWhere('valid_from', '<=', $now);
            })->where(function($subQ) use ($now) {
                $subQ->whereNull('valid_until')
                     ->orWhere('valid_until', '>=', $now);
            });
        });
    }

    public function scopeAvailable($query)
    {
        return $query->active()->valid()->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('usage_count < usage_limit');
        });
    }

    // Methods
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeUsed($transactionAmount)
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($transactionAmount < $this->min_transaction) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;
            
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            
            return $discount;
        }

        // Fixed discount
        return min($this->discount_value, $amount);
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function getRemainingUsageAttribute()
    {
        if (!$this->usage_limit) {
            return null; // Unlimited
        }

        return max(0, $this->usage_limit - $this->usage_count);
    }

    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }

        if (!$this->isValid()) {
            return 'Tidak Valid';
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return 'Habis';
        }

        return 'Aktif';
    }

    public function getDiscountTextAttribute()
    {
        if ($this->discount_type === 'percentage') {
            $text = $this->discount_value . '%';
            if ($this->max_discount) {
                $text .= ' (Max Rp ' . number_format($this->max_discount, 0, ',', '.') . ')';
            }
            return $text;
        }

        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }
}
