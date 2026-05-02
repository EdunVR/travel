<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action_type',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Prevent modification of audit logs
    public static function boot()
    {
        parent::boot();
        
        // Prevent updates to audit logs
        static::updating(function ($model) {
            return false;
        });
        
        // Prevent deletion of audit logs
        static::deleting(function ($model) {
            return false;
        });
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable()
    {
        return $this->morphTo('model');
    }

    /**
     * Scope to filter by action type
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by model type
     */
    public function scopeByModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Get formatted old values
     */
    public function getFormattedOldValuesAttribute()
    {
        if (!$this->old_values) {
            return null;
        }
        
        return collect($this->old_values)->map(function ($value, $key) {
            return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
        })->implode(', ');
    }

    /**
     * Get formatted new values
     */
    public function getFormattedNewValuesAttribute()
    {
        if (!$this->new_values) {
            return null;
        }
        
        return collect($this->new_values)->map(function ($value, $key) {
            return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
        })->implode(', ');
    }
}
