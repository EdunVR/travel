<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'assigned_to',
        'category',
        'attachment_notes',
        'realisasi_pct',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date'      => 'date',
        'realisasi_pct' => 'float',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The user this task is assigned to.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The user who created this task.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Local Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope: tasks that are overdue.
     * Overdue = has a due_date, the date is in the past, and status is not 'done'.
     */
    public function scopeOverdue($query)
    {
        return $query
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->where('status', '!=', 'done');
    }
}
