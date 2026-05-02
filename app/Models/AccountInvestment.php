<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountInvestment extends Model
{
    protected $fillable = [
        'account_id',
        'date',
        'type',
        'amount',
        'description',
        'document'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date'
    ];

    protected static function booted()
    {
        static::saved(function ($investment) {
            $investment->account->updateBalance();
        });

        static::deleted(function ($investment) {
            $investment->account->updateBalance();
        });
    }

    public function account()
    {
        return $this->belongsTo(InvestorAccount::class, 'account_id');
    }

    public function management()
    {
        return $this->belongsTo(ProfitManagement::class, 'management_id');
    }
}