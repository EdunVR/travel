<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_travel_package',
        'id_member',
        'communication_method',
        'communication_date',
        'notes',
        'follow_up_status',
        'next_follow_up_date',
        'contacted_by'
    ];

    protected $casts = [
        'communication_date' => 'datetime',
        'next_follow_up_date' => 'date'
    ];

    /**
     * Get the travel package associated with this communication
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Get the member (jamaah/customer) associated with this communication
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    /**
     * Get the user who made the contact
     */
    public function contactedByUser()
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }

    /**
     * Scope to get communications ordered chronologically
     */
    public function scopeChronological($query)
    {
        return $query->orderBy('communication_date', 'asc');
    }

    /**
     * Scope to get pending follow-ups
     */
    public function scopePendingFollowUps($query)
    {
        return $query->where('follow_up_status', 'pending')
                    ->whereNotNull('next_follow_up_date')
                    ->where('next_follow_up_date', '<=', now());
    }

    /**
     * Scope to get communications by member
     */
    public function scopeByMember($query, $memberId)
    {
        return $query->where('id_member', $memberId);
    }

    /**
     * Scope to get communications by package
     */
    public function scopeByPackage($query, $packageId)
    {
        return $query->where('id_travel_package', $packageId);
    }

    /**
     * Check if follow-up is overdue
     */
    public function isFollowUpOverdue()
    {
        if (!$this->next_follow_up_date || $this->follow_up_status !== 'pending') {
            return false;
        }

        return $this->next_follow_up_date < now()->toDateString();
    }

    /**
     * Get communication method label
     */
    public function getCommunicationMethodLabelAttribute()
    {
        $labels = [
            'phone_call' => 'Phone Call',
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            'in_person' => 'In Person',
            'other' => 'Other'
        ];

        return $labels[$this->communication_method] ?? $this->communication_method;
    }

    /**
     * Get follow-up status label
     */
    public function getFollowUpStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'contacted' => 'Contacted',
            'responded' => 'Responded',
            'no_response' => 'No Response'
        ];

        return $labels[$this->follow_up_status] ?? $this->follow_up_status;
    }
}
