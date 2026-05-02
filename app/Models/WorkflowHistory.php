<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowHistory extends Model
{
    use HasFactory;

    protected $table = 'workflow_history';

    protected $fillable = [
        'id_travel_package',
        'from_stage',
        'to_stage',
        'transitioned_at',
        'transitioned_by',
        'notes'
    ];

    protected $casts = [
        'transitioned_at' => 'datetime'
    ];

    /**
     * Get the travel package this history belongs to
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Get the user who performed the transition
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'transitioned_by');
    }

    /**
     * Get the from stage details
     */
    public function fromStageDetails()
    {
        return $this->belongsTo(WorkflowStage::class, 'from_stage', 'stage_code');
    }

    /**
     * Get the to stage details
     */
    public function toStageDetails()
    {
        return $this->belongsTo(WorkflowStage::class, 'to_stage', 'stage_code');
    }

    /**
     * Scope to get history for a specific package
     */
    public function scopeForPackage($query, $packageId)
    {
        return $query->where('id_travel_package', $packageId);
    }

    /**
     * Scope to get history ordered by transition time
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('transitioned_at', 'desc');
    }

    /**
     * Scope to get recent transitions
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('transitioned_at', '>=', now()->subDays($days));
    }
}
