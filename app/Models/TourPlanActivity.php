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
        'order'
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    public function tourPlan()
    {
        return $this->belongsTo(TourPlan::class);
    }
}
