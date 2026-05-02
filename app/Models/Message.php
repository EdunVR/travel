<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'mode',
        'content',
        'is_read',
        'read_at',
        'outlet_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Set the content attribute with XSS prevention
     * Strip all HTML tags and trim whitespace
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = strip_tags(trim($value));
    }

    /**
     * Get the content attribute with proper escaping for display
     * This is already handled by Blade's {{ }} syntax, but we ensure it here too
     */
    public function getContentAttribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    // Relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByMode($query, $mode)
    {
        return $query->where('mode', $mode);
    }
}
