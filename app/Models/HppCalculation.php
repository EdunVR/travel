<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HppCalculation extends Model
{
    use HasFactory;

    protected $table = 'hpp_calculations';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_travel_package',
        'flight_cost',
        'hotel_cost',
        'transportation_cost',
        'meal_cost',
        'visa_cost',
        'guide_cost',
        'insurance_cost',
        'operational_overhead',
        'contingency',
        'total_hpp',
        'is_locked',
        'locked_at',
        'locked_by',
        'custom_components',
        'component_payment_status',
        'component_hutang_amount',
        'component_realisasi',
        'laporan_adjustment',
        'laporan_disesuaikan',
        'laporan_disesuaikan_at',
    ];

    protected $casts = [
        'flight_cost' => 'decimal:2',
        'hotel_cost' => 'decimal:2',
        'transportation_cost' => 'decimal:2',
        'meal_cost' => 'decimal:2',
        'visa_cost' => 'decimal:2',
        'guide_cost' => 'decimal:2',
        'insurance_cost' => 'decimal:2',
        'operational_overhead' => 'decimal:2',
        'contingency' => 'decimal:2',
        'total_hpp' => 'decimal:2',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'custom_components' => 'array',
        'component_payment_status' => 'array',
        'component_hutang_amount' => 'array',
        'component_realisasi' => 'array',
        'laporan_adjustment' => 'decimal:2',
        'laporan_disesuaikan' => 'boolean',
        'laporan_disesuaikan_at' => 'datetime',
    ];

    /**
     * Relationship to travel package
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Relationship to user who locked the calculation
     */
    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Calculate total HPP Dasar from all cost components (excluding hotel).
     * Hotel HPP is calculated per jamaah booking.
     * Transportation cost now includes all saudi_transports from the package.
     * Hotels cost now includes all hotels from the package.
     *
     * @return float
     */
    public function calculateTotal()
    {
        $package = $this->travelPackage;
        $capacity = $package ? $package->capacity : 1;

        // Calculate total transport cost from all saudi_transports
        $totalTransportCost = $this->transportation_cost;
        if ($package && $package->saudi_transports) {
            $saudiTransports = $package->saudi_transports;
            if (is_array($saudiTransports)) {
                foreach (['makkah', 'madinah'] as $city) {
                    if (isset($saudiTransports[$city]) && is_array($saudiTransports[$city])) {
                        foreach ($saudiTransports[$city] as $transport) {
                            $totalTransportCost += (float)($transport['price'] ?? 0);
                        }
                    }
                }
            }
        }

        // Calculate total hotel cost from all hotels
        $totalHotelCost = $this->hotel_cost;
        if ($package && $package->hotels) {
            $hotels = $package->hotels;
            if (is_array($hotels)) {
                foreach ($hotels as $hotel) {
                    // Get room type price per night
                    $roomTypeId = $hotel['id_room_type'] ?? null;
                    $nights = $hotel['nights'] ?? 0;
                    
                    if ($roomTypeId && $nights > 0) {
                        $roomType = \App\Models\HotelRoomType::find($roomTypeId);
                        if ($roomType) {
                            $totalHotelCost += (float)$roomType->price_per_night * $nights;
                        }
                    }
                }
            }
        }

        // HPP Dasar includes all costs
        $this->total_hpp = ($this->flight_cost +
                           $totalHotelCost +
                           $totalTransportCost +
                           $this->meal_cost +
                           $this->visa_cost +
                           $this->guide_cost +
                           $this->insurance_cost +
                           $this->operational_overhead +
                           $this->contingency) * $capacity;

        return $this->total_hpp;
    }

    /**
     * Get HPP Dasar per person — includes hotel_cost from all hotels.
     * Transportation cost now includes all saudi_transports from the package.
     * Hotels cost now includes all hotels from the package.
     *
     * @return float
     */
    public function getHppPerPerson()
    {
        $package = $this->travelPackage;
        
        // Calculate total transport cost from all saudi_transports
        $totalTransportCost = $this->transportation_cost;
        if ($package && $package->saudi_transports) {
            $saudiTransports = $package->saudi_transports;
            if (is_array($saudiTransports)) {
                foreach (['makkah', 'madinah'] as $city) {
                    if (isset($saudiTransports[$city]) && is_array($saudiTransports[$city])) {
                        foreach ($saudiTransports[$city] as $transport) {
                            $totalTransportCost += (float)($transport['price'] ?? 0);
                        }
                    }
                }
            }
        }

        // Calculate total hotel cost from all hotels
        $totalHotelCost = $this->hotel_cost;
        if ($package && $package->hotels) {
            $hotels = $package->hotels;
            if (is_array($hotels)) {
                foreach ($hotels as $hotel) {
                    // Get room type price per night
                    $roomTypeId = $hotel['id_room_type'] ?? null;
                    $nights = $hotel['nights'] ?? 0;
                    
                    if ($roomTypeId && $nights > 0) {
                        $roomType = \App\Models\HotelRoomType::find($roomTypeId);
                        if ($roomType) {
                            $totalHotelCost += (float)$roomType->price_per_night * $nights;
                        }
                    }
                }
            }
        }

        return $this->flight_cost +
               $totalHotelCost +
               $totalTransportCost +
               $this->meal_cost +
               $this->visa_cost +
               $this->guide_cost +
               $this->insurance_cost +
               $this->operational_overhead +
               $this->contingency;
    }

    /**
     * Lock the HPP calculation
     * 
     * @param int $userId
     * @return bool
     */
    public function lock($userId)
    {
        if ($this->is_locked) {
            return false;
        }

        $this->is_locked = true;
        $this->locked_at = now();
        $this->locked_by = $userId;
        
        return $this->save();
    }

    /**
     * Check if HPP calculation is locked
     * 
     * @return bool
     */
    public function isLocked()
    {
        return $this->is_locked;
    }

    /**
     * Get cost breakdown as array
     * 
     * @return array
     */
    public function getCostBreakdown()
    {
        return [
            'flight_cost' => $this->flight_cost,
            'hotel_cost' => $this->hotel_cost,
            'transportation_cost' => $this->transportation_cost,
            'meal_cost' => $this->meal_cost,
            'visa_cost' => $this->visa_cost,
            'guide_cost' => $this->guide_cost,
            'insurance_cost' => $this->insurance_cost,
            'operational_overhead' => $this->operational_overhead,
            'contingency' => $this->contingency,
            'total_hpp' => $this->total_hpp,
            'is_locked' => $this->is_locked,
            'custom_components' => $this->custom_components ?? [],
            'component_payment_status' => $this->component_payment_status ?? [],
            'component_hutang_amount' => $this->component_hutang_amount ?? [],
        ];
    }

    /**
     * Get payment status for a component (default: lunas)
     */
    public function getComponentStatus(string $key): string
    {
        $statuses = $this->component_payment_status ?? [];
        return $statuses[$key] ?? 'lunas';
    }

    /**
     * Get hutang amount for a component
     */
    public function getComponentHutang(string $key): float
    {
        $amounts = $this->component_hutang_amount ?? [];
        return (float) ($amounts[$key] ?? 0);
    }

    /**
     * Check if component is paid (lunas)
     */
    public function isComponentLunas(string $key): bool
    {
        return $this->getComponentStatus($key) === 'lunas';
    }

    /**
     * Get realisasi value for a component
     */
    public function getComponentRealisasi(string $key): float
    {
        $realisasi = $this->component_realisasi ?? [];
        return (float) ($realisasi[$key] ?? 0);
    }
}
