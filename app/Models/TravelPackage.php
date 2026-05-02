<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelPackage extends Model
{
    use HasFactory;

    protected $table = 'travel_packages';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'package_code',
        'package_name',
        'package_type',
        'package_subtype',
        'description',
        'ustadz_name',
        'inclusions',
        'image_path',
        'thumbnail_crop_settings',
        'package_photos',
        'price_packages',
        'view_count',
        'booking_count',
        'duration_days',
        'departure_date',
        'return_date',
        'airline',
        'hotel_name',
        'id_flight',
        'id_hotel',
        'id_hotel_room_type',
        // Hotel Makkah
        'id_hotel_makkah',
        'id_hotel_room_type_makkah',
        'makkah_check_in',
        'makkah_check_out',
        // Hotel Madinah
        'id_hotel_madinah',
        'id_hotel_room_type_madinah',
        'madinah_check_in',
        'madinah_check_out',
        // Hotels (JSON - multiple hotels for other cities)
        'hotels',
        // Saudi Transport
        'id_saudi_transport',
        'saudi_transports',
        // Flight Information
        'id_flight_departure',
        'departure_datetime',
        'id_flight_return',
        'return_datetime',
        'capacity',
        'price',
        'hpp',
        'profit_margin',
        'status',
        'is_promo',
        'is_best_seller',
        'current_workflow_stage',
        'id_outlet'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'makkah_check_in' => 'date',
        'makkah_check_out' => 'date',
        'madinah_check_in' => 'date',
        'madinah_check_out' => 'date',
        'departure_datetime' => 'datetime',
        'return_datetime' => 'datetime',
        'duration_days' => 'integer',
        'capacity' => 'integer',
        'view_count' => 'integer',
        'booking_count' => 'integer',
        'price' => 'decimal:2',
        'hpp' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'package_photos' => 'array',
        'price_packages' => 'array',
        'saudi_transports' => 'array',
        'hotels' => 'array',
        'thumbnail_crop_settings' => 'array',
        'is_promo' => 'boolean',
        'is_best_seller' => 'boolean'
    ];

    /**
     * Relationship to outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Relationship to HPP calculation (one-to-one)
     */
    public function hppCalculation()
    {
        return $this->hasOne(HppCalculation::class, 'id_travel_package');
    }

    /**
     * Relationship to flight
     */
    public function flight()
    {
        return $this->belongsTo(Flight::class, 'id_flight');
    }

    /**
     * Relationship to hotel
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    /**
     * Relationship to hotel room type
     */
    public function hotelRoomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'id_hotel_room_type');
    }

    /**
     * Relationship to Makkah hotel
     */
    public function hotelMakkah()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel_makkah');
    }

    /**
     * Relationship to Makkah hotel room type
     */
    public function hotelRoomTypeMakkah()
    {
        return $this->belongsTo(HotelRoomType::class, 'id_hotel_room_type_makkah');
    }

    /**
     * Relationship to Madinah hotel
     */
    public function hotelMadinah()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel_madinah');
    }

    /**
     * Relationship to Madinah hotel room type
     */
    public function hotelRoomTypeMadinah()
    {
        return $this->belongsTo(HotelRoomType::class, 'id_hotel_room_type_madinah');
    }

    /**
     * Relationship to Saudi transport
     */
    public function saudiTransport()
    {
        return $this->belongsTo(\App\Models\SaudiTransport::class, 'id_saudi_transport');
    }

    /**
     * Relationship to departure flight
     */
    public function flightDeparture()
    {
        return $this->belongsTo(Flight::class, 'id_flight_departure');
    }

    /**
     * Relationship to return flight
     */
    public function flightReturn()
    {
        return $this->belongsTo(Flight::class, 'id_flight_return');
    }

    /**
     * Relationship to workflow history
     */
    public function workflowHistory()
    {
        return $this->hasMany(WorkflowHistory::class, 'id_travel_package');
    }

    /**
     * Relationship to jamaah bookings
     */
    public function jamaahBookings()
    {
        return $this->hasMany(JamaahBooking::class, 'id_travel_package');
    }

    /**
     * Relationship to keberangkatan (departure batches)
     */
    public function keberangkatan()
    {
        return $this->hasMany(Keberangkatan::class, 'id_travel_package');
    }

    /**
     * Relationship to design materials
     */
    public function designMaterials()
    {
        return $this->hasMany(DesignMaterial::class, 'id_travel_package');
    }

    /**
     * Relationship to tour plans
     */
    public function tourPlans()
    {
        return $this->hasMany(TourPlan::class, 'travel_package_id')->orderBy('day_number');
    }


    /**
     * Get available seats for this package
     * 
     * @return int
     */
    public function getAvailableSeats()
    {
        $bookings = $this->jamaahBookings()
            ->with('jamaah')
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $totalPax = $bookings->sum(function($b) {
            $fm = $b->jamaah->family_members ?? [];
            if (is_string($fm)) $fm = json_decode($fm, true);
            return 1 + (is_array($fm) ? count($fm) : 0);
        });

        return $this->capacity - $totalPax;
    }

    /**
     * Check if package is full
     * 
     * @return bool
     */
    public function isFull()
    {
        return $this->getAvailableSeats() <= 0;
    }

    /**
     * Calculate profit margin based on HPP and price
     * 
     * @return float|null
     */
    public function calculateProfitMargin()
    {
        if (!$this->hpp || $this->hpp == 0) {
            return null;
        }

        $profit = $this->price - $this->hpp;
        // Profit margin terhadap harga jual (bukan HPP)
        $this->profit_margin = ($this->price > 0) ? ($profit / $this->price) * 100 : 0;
        
        return $this->profit_margin;
    }

    /**
     * Update HPP from calculation
     * 
     * @return void
     */
    public function updateHppFromCalculation()
    {
        if ($this->hppCalculation) {
            // Simpan HPP per orang (bukan total) agar bisa dibandingkan dengan price per orang
            $this->hpp = $this->hppCalculation->getHppPerPerson();
            $this->calculateProfitMargin();
            $this->save();
        }
    }

    /**
     * Scope to search packages
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('package_code', 'like', "%{$term}%")
              ->orWhere('package_name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
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
     * Scope to filter by package type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('package_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by workflow stage
     */
    public function scopeInWorkflowStage($query, $stage)
    {
        return $query->where('current_workflow_stage', $stage);
    }

    /**
     * Scope to get active packages
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get upcoming packages
     */
    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now())
                    ->orderBy('departure_date', 'asc');
    }

    /**
     * Increment view count
     * 
     * @return void
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Update booking count based on confirmed bookings
     * 
     * @return void
     */
    public function updateBookingCount()
    {
        $this->booking_count = $this->jamaahBookings()
            ->whereNotIn('status', ['cancelled'])
            ->count();
        $this->save();
    }

    /**
     * Get popularity score (combination of views and bookings)
     * 
     * @return int
     */
    public function getPopularityScore()
    {
        return ($this->view_count * 1) + ($this->booking_count * 10);
    }

    /**
     * Scope to order by popularity
     */
    public function scopePopular($query)
    {
        return $query->orderByRaw('(view_count * 1) + (booking_count * 10) DESC');
    }

    /**
     * Get inclusions as array
     * 
     * @return array
     */
    public function getInclusionsArray()
    {
        if (!$this->inclusions) {
            return [];
        }
        
        return array_filter(array_map('trim', explode("\n", $this->inclusions)));
    }

    /**
     * Get image URL
     * 
     * @return string|null
     */
    public function getImageUrl()
    {
        if (!$this->image_path) {
            return null;
        }
        
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get package photos URLs
     * 
     * @return array
     */
    public function getPackagePhotosUrls()
    {
        if (!$this->package_photos || !is_array($this->package_photos)) {
            return [];
        }
        
        return array_map(function($photo) {
            return asset('storage/' . $photo);
        }, $this->package_photos);
    }

    /**
     * Add package photo
     * 
     * @param string $photoPath
     * @return void
     */
    public function addPackagePhoto($photoPath)
    {
        $photos = $this->package_photos ?? [];
        $photos[] = $photoPath;
        $this->package_photos = $photos;
        $this->save();
    }

    /**
     * Remove package photo
     * 
     * @param string $photoPath
     * @return void
     */
    public function removePackagePhoto($photoPath)
    {
        $photos = $this->package_photos ?? [];
        $photos = array_filter($photos, function($photo) use ($photoPath) {
            return $photo !== $photoPath;
        });
        $this->package_photos = array_values($photos);
        $this->save();
    }
}
