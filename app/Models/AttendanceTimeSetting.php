<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceTimeSetting extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get current time period based on current time
     */
    public static function getCurrentTimePeriod($currentTime = null)
    {
        if (!$currentTime) {
            $currentTime = now()->format('H:i:s');
        }

        $settings = self::where('is_active', true)->get();
        
        foreach ($settings as $setting) {
            if (self::isTimeInRange($currentTime, $setting->start_time, $setting->end_time)) {
                return $setting->name;
            }
        }

        return null; // Outside all defined periods
    }

    /**
     * Check if time is within range (handles overnight ranges)
     */
    public static function isTimeInRange($currentTime, $startTime, $endTime)
    {
        // Extract just the time part if it's a full datetime
        if (strlen($startTime) > 8) {
            $startTime = substr($startTime, 11, 8); // Extract HH:MM:SS from datetime
        }
        if (strlen($endTime) > 8) {
            $endTime = substr($endTime, 11, 8); // Extract HH:MM:SS from datetime
        }
        
        $current = Carbon::createFromFormat('H:i:s', $currentTime);
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);

        // Handle overnight range (e.g., 18:01 - 03:30)
        if ($end->lt($start)) {
            // If end time is less than start time, it crosses midnight
            return $current->gte($start) || $current->lte($end);
        } else {
            // Normal range within same day
            return $current->gte($start) && $current->lte($end);
        }
    }

    /**
     * Get time setting by name
     */
    public static function getByName($name)
    {
        return self::where('name', $name)->where('is_active', true)->first();
    }

    /**
     * Get all active time settings
     */
    public static function getActiveSettings()
    {
        return self::where('is_active', true)->orderBy('start_time')->get();
    }

    /**
     * Determine next action based on current attendance and time period
     * Simplified: only check_in and check_out periods
     */
    public static function determineNextAction($attendance, $currentTimePeriod, $currentTime = null)
    {
        switch ($currentTimePeriod) {
            case 'check_in':
                // Dalam range jam masuk → selalu clock_in
                return 'clock_in';

            case 'check_out':
                // Dalam range jam pulang
                // Jika belum clock_in, isi clock_in dulu
                if (!$attendance || !$attendance->clock_in) {
                    return 'clock_in';
                }
                // Sudah clock_in → clock_out
                return 'clock_out';

            default:
                // Di luar range yang ditentukan
                if (!$attendance || !$attendance->clock_in) {
                    return 'clock_in';
                }
                return 'clock_out';
        }
    }

    /**
     * Get human readable description for action
     */
    public static function getActionDescription($action)
    {
        $descriptions = [
            'clock_in'  => 'Masuk kerja',
            'clock_out' => 'Pulang kerja',
        ];

        return $descriptions[$action] ?? $action;
    }
}