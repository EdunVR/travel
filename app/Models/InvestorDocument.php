<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorDocument extends Model
{
    protected $fillable = [
        'investor_id',
        'title',
        'type',
        'file_path',
        'is_custom',
        'content',
        'meta'
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'meta' => 'array'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}