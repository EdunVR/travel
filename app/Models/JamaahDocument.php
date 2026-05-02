<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JamaahDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jamaah_booking',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'status',
        'verified_by',
        'verified_at',
        'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime'
    ];

    /**
     * Get the jamaah booking that owns the document
     */
    public function jamaahBooking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    /**
     * Get the user who verified the document
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if document is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $daysUntilExpiry = Carbon::now()->diffInDays($this->expiry_date, false);
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return Carbon::now()->isAfter($this->expiry_date);
    }

    /**
     * Check if passport expiry is valid (at least 6 months from departure)
     */
    public function isPassportExpiryValid(): bool
    {
        if ($this->document_type !== 'passport' || !$this->expiry_date) {
            return true;
        }

        $booking = $this->jamaahBooking;
        if (!$booking || !$booking->keberangkatan) {
            return true;
        }

        $departureDate = $booking->keberangkatan->departure_date;
        $monthsUntilExpiry = Carbon::parse($departureDate)->diffInMonths($this->expiry_date, false);
        
        return $monthsUntilExpiry >= 6;
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabel(): string
    {
        return match($this->document_type) {
            'passport' => 'Passport',
            'visa' => 'Visa',
            'ticket' => 'Flight Ticket',
            'insurance' => 'Insurance',
            'health_certificate' => 'Health Certificate',
            default => ucfirst($this->document_type)
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'pending' => 'secondary',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Scope to get documents expiring soon
     */
    public function scopeExpiringSoon($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', Carbon::now())
            ->whereDate('expiry_date', '<=', Carbon::now()->addDays(30));
    }

    /**
     * Scope to get expired documents
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', Carbon::now());
    }

    /**
     * Scope to get documents by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get documents by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}
