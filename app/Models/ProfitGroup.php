<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'product_id',
        'total_quota',
    ];

    protected $casts = [
        'total_quota' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id_produk');
    }

    public function investors()
    {
        return $this->hasMany(ProfitGroupInvestor::class, 'group_id');
    }

    public function getTotalInvestmentAttribute()
    {
        return $this->investors->sum('investment_amount');
    }
}