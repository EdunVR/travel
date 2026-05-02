<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorCustomer extends Model
{
    protected $table = 'investor_customers';
    
    protected $fillable = [
        'investor_id',
        'id_member',
        'biaya',
        'status'
    ];

    protected $casts = [
        'biaya' => 'decimal:2'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }
}