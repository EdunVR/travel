<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorAccount extends Model
{
    protected $fillable = [
        'investor_id',
        'account_number',
        'bank_name',
        'account_name',
        'initial_balance',
        'current_balance',
        'saldo_tertahan',
        'profit_percentage',
        'status',
        'date',
        'tempo'
    ];

    protected $dates = [
        'date',
        'tempo',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'saldo_tertahan' => 'decimal:2',
        'profit_percentage' => 'decimal:2'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function investments()
    {
        return $this->hasMany(AccountInvestment::class, 'account_id');
    }


    public function updateBalance()
    {
        $totalInvestments = $this->investments()
            ->where('type', 'deposit')
            ->sum('amount');

        $totalWithdrawals = $this->investments()
            ->where('type', 'withdrawal')
            ->sum('amount');

        $this->current_balance = $totalInvestments - $totalWithdrawals;
        $this->save();
    }

    public function getTotalInvestmentAttribute()
    {
        $totalInvestments = $this->investments()
            ->where('type', 'investment')
            ->sum('amount');

        $totalPenarikan = $this->investments()
            ->where('type', 'penarikan')
            ->sum('amount');

        return $totalInvestments - $totalPenarikan;
        
    }

    public function getTotalProfitAttribute()
    {
        return $this->investments()
                ->where('type', 'deposit')
                ->sum('amount') ?? 0;
    }

    public function getTotalWithdrawalsAttribute()
    {
        return $this->investments()
                ->where('type', 'withdrawal')
                ->sum('amount') ?? 0;
    }

    public function getProfitBalanceAttribute()
    {
        return $this->total_profit - $this->total_withdrawals;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function profitDistributions()
    {
        return $this->hasMany(AccountInvestment::class, 'account_id')
            ->where('type', 'deposit');
    }
    public function withdrawals()
    {
        return $this->hasMany(InvestorWithdrawal::class, 'account_id');
    }
}