<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_code',
        'stage_name',
        'stage_order',
        'description',
        'responsible_team',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stage_order' => 'integer'
    ];

    /**
     * Get tasks associated with this workflow stage
     */
    public function tasks()
    {
        return $this->hasMany(WorkflowTask::class, 'id_workflow_stage');
    }

    /**
     * Get the team responsible for this stage
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'responsible_team', 'team_code');
    }

    /**
     * Scope to get only active stages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get stages in order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('stage_order');
    }

    /**
     * Get the next workflow stage
     */
    public function getNextStage()
    {
        return static::where('stage_order', '>', $this->stage_order)
            ->where('is_active', true)
            ->orderBy('stage_order')
            ->first();
    }

    /**
     * Get the previous workflow stage
     */
    public function getPreviousStage()
    {
        return static::where('stage_order', '<', $this->stage_order)
            ->where('is_active', true)
            ->orderBy('stage_order', 'desc')
            ->first();
    }
}
