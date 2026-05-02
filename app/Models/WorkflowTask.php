<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_travel_package',
        'id_workflow_stage',
        'task_name',
        'task_description',
        'assigned_to_team',
        'assigned_to_user',
        'due_date',
        'status',
        'completed_at',
        'completed_by',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the travel package this task belongs to
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Get the workflow stage this task belongs to
     */
    public function workflowStage()
    {
        return $this->belongsTo(WorkflowStage::class, 'id_workflow_stage');
    }

    /**
     * Get the team this task is assigned to
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'assigned_to_team', 'team_code');
    }

    /**
     * Get the user this task is assigned to
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user');
    }

    /**
     * Get the user who completed this task
     */
    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope to get pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get in progress tasks
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get tasks for a specific team
     */
    public function scopeForTeam($query, $teamCode)
    {
        return $query->where('assigned_to_team', $teamCode);
    }

    /**
     * Scope to get tasks for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_to_user', $userId);
    }

    /**
     * Scope to get tasks for a specific package
     */
    public function scopeForPackage($query, $packageId)
    {
        return $query->where('id_travel_package', $packageId);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue()
    {
        return $this->due_date < now() && !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Mark task as completed
     */
    public function markAsCompleted($userId, $notes = null)
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->completed_by = $userId;
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }
}
