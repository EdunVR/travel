<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPlanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_plan_id',
        'activity_time',
        'activity_title',
        'activity_description',
        'order',
        'is_transport_info',
        'transport_from',
        'transport_to',
        'transport_remark',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_transport_info' => 'boolean',
    ];

    public function tourPlan()
    {
        return $this->belongsTo(TourPlan::class);
    }
}
