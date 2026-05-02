<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_package_id',
        'day_number',
        'day_title',
        'day_date',
        'description',
        'order'
    ];

    protected $casts = [
        'day_number' => 'integer',
        'day_date' => 'date:Y-m-d',
        'order' => 'integer'
    ];

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'travel_package_id');
    }

    public function activities()
    {
        return $this->hasMany(TourPlanActivity::class)->orderBy('order');
    }
}
