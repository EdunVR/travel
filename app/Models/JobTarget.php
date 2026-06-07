<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTarget extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description',
        'target_percent', 'realisasi_percent',
        'period', 'due_date', 'created_by',
    ];

    protected $casts = [
        'target_percent'     => 'float',
        'realisasi_percent'  => 'float',
        'due_date'           => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Progress % aktual terhadap target (max 100) */
    public function getProgressPercent(): float
    {
        if ($this->target_percent <= 0) return 0;
        return min(100, round(($this->realisasi_percent / $this->target_percent) * 100, 1));
    }
}
