<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Notification types
    const TYPE_TASK_ASSIGNED = 'task_assigned';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_DOCUMENT_UPLOADED = 'document_uploaded';
    const TYPE_DEADLINE_APPROACHING = 'deadline_approaching';
    const TYPE_WORKFLOW_STAGE_COMPLETED = 'workflow_stage_completed';
    const TYPE_GENERAL = 'general';

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get notifications by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope to get recent notifications (within 90 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(90));
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->notification_type) {
            self::TYPE_TASK_ASSIGNED => 'fa-tasks',
            self::TYPE_PAYMENT_RECEIVED => 'fa-money',
            self::TYPE_DOCUMENT_UPLOADED => 'fa-file',
            self::TYPE_DEADLINE_APPROACHING => 'fa-clock-o',
            self::TYPE_WORKFLOW_STAGE_COMPLETED => 'fa-check-circle',
            default => 'fa-bell'
        };
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute()
    {
        return match($this->notification_type) {
            self::TYPE_TASK_ASSIGNED => 'info',
            self::TYPE_PAYMENT_RECEIVED => 'success',
            self::TYPE_DOCUMENT_UPLOADED => 'primary',
            self::TYPE_DEADLINE_APPROACHING => 'warning',
            self::TYPE_WORKFLOW_STAGE_COMPLETED => 'success',
            default => 'default'
        };
    }
}
