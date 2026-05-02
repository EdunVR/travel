<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitGroupDistribution extends Model
{
    protected $fillable = [
        'history_id',
        'investor_id',
        'account_id',
        'investment_amount',
        'profit_share',
        'profit_percentage'
    ];

    public function history()
    {
        return $this->belongsTo(ProfitGroupHistory::class, 'history_id');
    }

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
        return $this->hasOneThrough(
            ProfitGroup::class,
            ProfitGroupHistory::class,
            'id', // Foreign key on histories table
            'id', // Foreign key on groups table
            'history_id', // Local key on distributions table
            'group_id' // Local key on histories table
        );
    }
}