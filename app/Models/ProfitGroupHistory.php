<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitGroupHistory extends Model
{
    protected $fillable = [
        'group_id',
        'total_profit',
        'distribution_date',
        'status',
        'proof_file',
        'remaining_profit',
        'use_custom_percentage',
        'custom_percentage',
        'period'
    ];
    
    protected $casts = [
        'distribution_date' => 'date'
    ];
    
    public function group()
    {
        return $this->belongsTo(ProfitGroup::class);
    }
    
    public function distributions()
    {
        return $this->hasMany(ProfitGroupDistribution::class, 'history_id');
    }
    
    public function investors()
    {
        return $this->hasManyThrough(
            Investor::class,
            ProfitGroupDistribution::class,
            'history_id', // Foreign key on distributions table
            'id', // Foreign key on investors table
            'id', // Local key on histories table
            'investor_id' // Local key on distributions table
        );
    }
}