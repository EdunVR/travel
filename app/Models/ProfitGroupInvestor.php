<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitGroupInvestor extends Model
{
    protected $fillable = [
        'group_id',
        'investor_id',
        'account_id',
        'investment_amount'
    ];

    protected $casts = [
        'investment_amount' => 'decimal:2'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function account()
    {
        return $this->belongsTo(InvestorAccount::class);
    }


    public function group()
    {
        return $this->belongsTo(ProfitGroup::class);
    }
}