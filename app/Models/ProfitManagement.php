<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProfitManagement extends Model
{
    protected $table = 'profit_managements';
    protected $fillable = [
        'period',
        'total_profit',
        'distribution_date',
        'status',
        'category',
        'notes',
        'proof_file',
        'remaining_profit',
        'use_custom_percentage', 
        'custom_percentage'
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'total_profit' => 'decimal:2'
    ];

    public function distributions()
    {
        return $this->hasMany(AccountInvestment::class, 'management_id')
            ->where('type', 'deposit');
    }

    public function addToRemainingProfit($amount)
    {
        $this->remaining_profit += $amount;
        $this->save();
    }

    public function useRemainingProfit($amount)
    {
        if ($amount > $this->remaining_profit) {
            throw new \Exception('Jumlah melebihi keuntungan tersisa');
        }
        
        $this->remaining_profit -= $amount;
        $this->save();
    }
}