<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keberangkatan extends Model
{
    use HasFactory;

    protected $table = 'keberangkatan';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'keberangkatan_code',
        'keberangkatan_name',
        'id_travel_package',
        'departure_date',
        'return_date',
        'total_jamaah',
        'status',
        'manifest_order',
        'id_rab',
        'id_outlet'
    ];

    protected $casts = [
        'departure_date' => 'immutable_date',
        'return_date' => 'immutable_date',
        'total_jamaah' => 'integer',
        'manifest_order' => 'array',
    ];

    /**
     * Boot method to auto-sync dates with package
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new keberangkatan, auto-sync dates from package
        static::creating(function ($keberangkatan) {
            if ($keberangkatan->id_travel_package && !$keberangkatan->departure_date) {
                $package = TravelPackage::find($keberangkatan->id_travel_package);
                if ($package) {
                    $keberangkatan->departure_date = $package->departure_date;
                    $keberangkatan->return_date = $package->return_date;
                    \Log::info('Auto-synced keberangkatan dates from package', [
                        'package_id' => $package->id,
                        'departure_date' => $package->departure_date,
                        'return_date' => $package->return_date
                    ]);
                }
            }
        });
    }

    /**
     * Relationship to outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Relationship to travel package
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Relationship to jamaah bookings
     */
    public function jamaahBookings()
    {
        return $this->hasMany(JamaahBooking::class, 'id_keberangkatan');
    }

    /**
     * Relationship to flight bookings
     */
    public function flightBookings()
    {
        return $this->hasMany(FlightBooking::class, 'id_keberangkatan');
    }

    /**
     * Relationship to hotel bookings
     */
    public function hotelBookings()
    {
        return $this->hasMany(HotelBooking::class, 'id_keberangkatan');
    }

    /**
     * Relationship to equipment checklists
     */
    public function equipmentChecklists()
    {
        return $this->hasMany(EquipmentChecklist::class, 'id_keberangkatan');
    }

    /**
     * Relationship to RAB (Budget Plan)
     */
    public function rab()
    {
        return $this->belongsTo(RabTemplate::class, 'id_rab', 'id_rab');
    }

    /**
     * Get confirmed jamaah count (termasuk anggota keluarga)
     * 
     * @return int
     */
    public function getConfirmedJamaahCount()
    {
        $bookings = $this->jamaahBookings()
            ->with('jamaah')
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $total = 0;
        foreach ($bookings as $booking) {
            $total++; // jamaah utama
            // Hitung anggota keluarga
            $familyMembers = $booking->jamaah->family_members ?? [];
            if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
            if (is_array($familyMembers)) $total += count($familyMembers);
        }
        return $total;
    }

    /**
     * Get available capacity
     * 
     * @return int
     */
    public function getAvailableCapacity()
    {
        return $this->total_jamaah - $this->getConfirmedJamaahCount();
    }

    /**
     * Check if keberangkatan is full
     * 
     * @return bool
     */
    public function isFull()
    {
        return $this->getAvailableCapacity() <= 0;
    }

    /**
     * Update total jamaah count from bookings
     * 
     * @return void
     */
    public function updateTotalJamaah()
    {
        $this->total_jamaah = $this->getConfirmedJamaahCount();
        $this->save();
    }

    /**
     * Check if all jamaah have confirmed flight tickets
     * 
     * @return bool
     */
    public function hasAllFlightTicketsConfirmed()
    {
        $totalJamaah = $this->getConfirmedJamaahCount();
        
        if ($totalJamaah === 0) {
            return false;
        }

        $confirmedTickets = $this->flightBookings()
            ->where('status', 'confirmed')
            ->sum('seat_count');

        return $confirmedTickets >= $totalJamaah;
    }

    /**
     * Check if all jamaah have approved documents
     * 
     * @return bool
     */
    public function hasAllDocumentsApproved()
    {
        $jamaahBookings = $this->jamaahBookings()
            ->whereNotIn('status', ['cancelled'])
            ->get();

        if ($jamaahBookings->isEmpty()) {
            return false;
        }

        foreach ($jamaahBookings as $booking) {
            $requiredDocTypes = ['passport', 'visa', 'ticket', 'insurance', 'health_certificate'];
            
            foreach ($requiredDocTypes as $docType) {
                $hasApprovedDoc = $booking->documents()
                    ->where('document_type', $docType)
                    ->where('status', 'approved')
                    ->exists();

                if (!$hasApprovedDoc) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if all equipment is shipped
     * 
     * @return bool
     */
    public function hasAllEquipmentShipped()
    {
        $equipmentItems = $this->equipmentChecklists;

        if ($equipmentItems->isEmpty()) {
            return true; // No equipment required
        }

        return $equipmentItems->every(function ($item) {
            return $item->status === 'shipped';
        });
    }

    /**
     * Scope to search keberangkatan
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('keberangkatan_code', 'like', "%{$term}%")
              ->orWhere('keberangkatan_name', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to filter by outlet
     */
    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by travel package
     */
    public function scopeForPackage($query, $packageId)
    {
        return $query->where('id_travel_package', $packageId);
    }

    /**
     * Scope to get upcoming departures
     */
    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now())
                    ->orderBy('departure_date', 'asc');
    }

    /**
     * Scope to get departures by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('departure_date', [$startDate, $endDate]);
    }
}

