<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorWithdrawal extends Model
{
    protected $fillable = [
        'investor_id',
        'account_id',
        'amount',
        'notes',
        'status',
        'requested_at',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function account()
    {
        return $this->belongsTo(InvestorAccount::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}