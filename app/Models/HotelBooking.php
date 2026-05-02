<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelBooking extends Model
{
    use HasFactory;

    protected $table = 'hotel_bookings';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_hotel',
        'id_keberangkatan',
        'check_in_date',
        'check_out_date',
        'room_count',
        'room_type',
        'seller_name',
        'seller_phone',
        'status',
        'booking_reference',
        'notes'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'room_count' => 'integer'
    ];

    /**
     * Relationship to hotel
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    /**
     * Relationship to keberangkatan
     */
    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    /**
     * Relationship to room assignments
     */
    public function roomAssignments()
    {
        return $this->hasMany(HotelRoomAssignment::class, 'id_hotel_booking');
    }

    /**
     * Get total assigned jamaah count
     * 
     * @return int
     */
    public function getAssignedJamaahCount()
    {
        return $this->roomAssignments()->count();
    }

    /**
     * Check if all jamaah are assigned to rooms
     * 
     * @return bool
     */
    public function isFullyAssigned()
    {
        $totalJamaah = $this->keberangkatan->getConfirmedJamaahCount();
        $assignedCount = $this->getAssignedJamaahCount();
        
        return $assignedCount >= $totalJamaah;
    }

    /**
     * Get unassigned jamaah for this booking
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnassignedJamaah()
    {
        \Log::info("=== GET UNASSIGNED JAMAAH START ===");
        \Log::info("Hotel Booking ID: {$this->id}");
        \Log::info("Keberangkatan ID: {$this->id_keberangkatan}");
        
        // Get already assigned jamaah IDs
        $assignedJamaahIds = $this->roomAssignments()->pluck('id_jamaah_booking')->toArray();
        
        \Log::info("Already Assigned Jamaah IDs:", $assignedJamaahIds);
        \Log::info("Assigned Count: " . count($assignedJamaahIds));
        
        // Query jamaah bookings
        $query = $this->keberangkatan->jamaahBookings()
            ->whereNotIn('id', $assignedJamaahIds)
            ->whereNotIn('status', ['cancelled']);
            
        \Log::info("Query SQL: " . $query->toSql());
        \Log::info("Query Bindings:", $query->getBindings());
        
        $unassigned = $query->get();
        
        \Log::info("Unassigned Jamaah Found: " . $unassigned->count());
        
        foreach ($unassigned as $jamaah) {
            \Log::info("Unassigned Jamaah Detail:", [
                'id' => $jamaah->id,
                'booking_code' => $jamaah->booking_code,
                'id_jamaah' => $jamaah->id_jamaah,
                'id_keberangkatan' => $jamaah->id_keberangkatan,
                'room_type' => $jamaah->room_type,
                'status' => $jamaah->status
            ]);
        }
        
        \Log::info("=== GET UNASSIGNED JAMAAH END ===");
        
        return $unassigned;
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by keberangkatan
     */
    public function scopeForKeberangkatan($query, $keberangkatanId)
    {
        return $query->where('id_keberangkatan', $keberangkatanId);
    }
}
