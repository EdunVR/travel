<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLaborCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'worker_count',
        'cost_per_worker',
        'total_cost',
        'from_attendance',
        'attendance_date',
        'notes',
    ];

    protected $casts = [
        'cost_per_worker' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'from_attendance' => 'boolean',
        'attendance_date' => 'date',
    ];

    // Relationships
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    // Accessors
    public function getFormattedCostPerWorkerAttribute()
    {
        return 'Rp ' . number_format($this->cost_per_worker, 0, ',', '.');
    }

    public function getFormattedTotalCostAttribute()
    {
        return 'Rp ' . number_format($this->total_cost, 0, ',', '.');
    }
}