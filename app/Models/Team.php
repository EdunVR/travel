<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_code',
        'team_name',
        'description',
        'responsibilities',
        'is_active'
    ];

    protected $casts = [
        'responsibilities' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get team members (users)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id')
            ->select('users.*');
    }

    /**
     * Get tasks assigned to this team
     */
    public function tasks()
    {
        return $this->hasMany(WorkflowTask::class, 'assigned_to_team', 'team_code');
    }

    /**
     * Get workflow stages this team is responsible for
     */
    public function workflowStages()
    {
        return $this->hasMany(WorkflowStage::class, 'responsible_team', 'team_code');
    }

    /**
     * Scope to get only active teams
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
