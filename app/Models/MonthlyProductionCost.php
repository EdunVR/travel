<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyProductionCost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monthly_production_costs';

    protected $fillable = [
        'outlet_id',
        'month',
        'year',
        'electricity_cost',
        'water_cost',
        'fuel_cost',
        'office_salary_cost',
        'other_costs',
        'total_cost',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'electricity_cost' => 'decimal:2',
        'water_cost' => 'decimal:2',
        'fuel_cost' => 'decimal:2',
        'office_salary_cost' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getElectricityCostFormattedAttribute()
    {
        return 'Rp ' . number_format($this->electricity_cost, 0, ',', '.');
    }

    public function getWaterCostFormattedAttribute()
    {
        return 'Rp ' . number_format($this->water_cost, 0, ',', '.');
    }

    public function getFuelCostFormattedAttribute()
    {
        return 'Rp ' . number_format($this->fuel_cost, 0, ',', '.');
    }

    public function getOfficeSalaryCostFormattedAttribute()
    {
        return 'Rp ' . number_format($this->office_salary_cost, 0, ',', '.');
    }

    public function getOtherCostsFormattedAttribute()
    {
        return 'Rp ' . number_format($this->other_costs, 0, ',', '.');
    }

    public function getTotalCostFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_cost, 0, ',', '.');
    }

    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$this->month] ?? '';
    }

    public function getPeriodAttribute()
    {
        return $this->month_name . ' ' . $this->year;
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'ALL') {
            return $query->where('outlet_id', $outletId);
        }
        return $query;
    }

    public function scopeByPeriod($query, $month = null, $year = null)
    {
        if ($month) {
            $query->where('month', $month);
        }
        if ($year) {
            $query->where('year', $year);
        }
        return $query;
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('year', 'desc')->orderBy('month', 'desc');
    }

    // Static methods
    public static function getCurrentPeriodCost($outletId = null)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $query = self::byPeriod($currentMonth, $currentYear);
        
        if ($outletId && $outletId !== 'ALL') {
            $query->byOutlet($outletId);
        }

        return $query->first();
    }

    public static function getLatestCost($outletId = null)
    {
        $query = self::latest();
        
        if ($outletId && $outletId !== 'ALL') {
            $query->byOutlet($outletId);
        }

        return $query->first();
    }

    // Mutators
    public function setElectricityCostAttribute($value)
    {
        $this->attributes['electricity_cost'] = $this->cleanCurrency($value);
    }

    public function setWaterCostAttribute($value)
    {
        $this->attributes['water_cost'] = $this->cleanCurrency($value);
    }

    public function setFuelCostAttribute($value)
    {
        $this->attributes['fuel_cost'] = $this->cleanCurrency($value);
    }

    public function setOfficeSalaryCostAttribute($value)
    {
        $this->attributes['office_salary_cost'] = $this->cleanCurrency($value);
    }

    public function setOtherCostsAttribute($value)
    {
        $this->attributes['other_costs'] = $this->cleanCurrency($value);
    }

    private function cleanCurrency($value)
    {
        if (is_string($value)) {
            // Remove currency symbols and formatting
            $value = preg_replace('/[^\d,.-]/', '', $value);
            $value = str_replace(',', '', $value);
        }
        return (float) $value;
    }

    // Calculate total cost automatically
    public function calculateTotalCost()
    {
        $this->total_cost = $this->electricity_cost + $this->water_cost + 
                           $this->fuel_cost + $this->office_salary_cost + 
                           $this->other_costs;
        return $this->total_cost;
    }
}