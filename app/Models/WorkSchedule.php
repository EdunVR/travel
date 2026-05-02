<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'clock_in',
        'clock_out',
    ];

    protected $casts = [
        'clock_in' => 'datetime:H:i:s',
        'clock_out' => 'datetime:H:i:s',
    ];

    /**
     * Relationship dengan Recruitment (Karyawan)
     */
    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    /**
     * Get formatted clock in time
     */
    public function getClockInFormattedAttribute()
    {
        return $this->clock_in ? date('H:i', strtotime($this->clock_in)) : '08:00';
    }

    /**
     * Get formatted clock out time
     */
    public function getClockOutFormattedAttribute()
    {
        return $this->clock_out ? date('H:i', strtotime($this->clock_out)) : '17:00';
    }

    /**
     * Get or create work schedule for a recruitment
     */
    public static function getOrCreateForRecruitment($recruitmentId)
    {
        return self::firstOrCreate(
            ['recruitment_id' => $recruitmentId],
            [
                'clock_in' => '08:00:00',
                'clock_out' => '17:00:00',
            ]
        );
    }

    /**
     * Set work schedule for all recruitments
     */
    public static function setForAllRecruitments($clockIn, $clockOut)
    {
        $recruitments = Recruitment::where('status', 'active')->get();
        
        foreach ($recruitments as $recruitment) {
            self::updateOrCreate(
                ['recruitment_id' => $recruitment->id],
                [
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                ]
            );
        }
        
        return true;
    }

    /**
     * Set work schedule for specific recruitment
     */
    public static function setForRecruitment($recruitmentId, $clockIn, $clockOut)
    {
        return self::updateOrCreate(
            ['recruitment_id' => $recruitmentId],
            [
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
            ]
        );
    }
}
