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
     * Following the new algorithm logic
     */
    public static function determineNextAction($attendance, $currentTimePeriod, $currentTime = null)
    {
        switch ($currentTimePeriod) {
            case 'check_in':
                // RANGE JAM MASUK: Selalu isi clock_in (replace jika sudah ada)
                return 'clock_in';

            case 'break':
                // RANGE JAM ISTIRAHAT
                // Jika clock_in belum ada, isi clock_in terlebih dahulu
                if (!$attendance || !$attendance->clock_in) {
                    return 'clock_in';
                }
                
                // Jika break_in belum ada, isi break_in
                if (!$attendance->break_in) {
                    return 'break_in';
                } else {
                    // Jika break_in sudah ada, isi break_out (replace jika sudah ada)
                    return 'break_out';
                }

            case 'check_out':
                // RANGE JAM PULANG
                // Jika clock_in belum ada, isi clock_in terlebih dahulu
                if (!$attendance || !$attendance->clock_in) {
                    return 'clock_in';
                }
                
                // Jika break_in belum ada, isi break_in
                if (!$attendance->break_in) {
                    return 'break_in';
                }
                
                // Jika break_out belum ada, isi break_out
                if (!$attendance->break_out) {
                    return 'break_out';
                }
                
                // Semua field sebelumnya sudah terisi, isi clock_out (replace jika sudah ada)
                return 'clock_out';

            case 'overtime':
                // RANGE JAM LEMBUR
                // Jika clock_in belum ada, isi clock_in terlebih dahulu
                if (!$attendance || !$attendance->clock_in) {
                    return 'clock_in';
                }
                
                // Jika break_in belum ada, isi break_in
                if (!$attendance->break_in) {
                    return 'break_in';
                }
                
                // Jika break_out belum ada, isi break_out
                if (!$attendance->break_out) {
                    return 'break_out';
                }
                
                // Jika clock_out belum ada, isi clock_out
                if (!$attendance->clock_out) {
                    return 'clock_out';
                }
                
                // Jika overtime_in belum ada, isi overtime_in
                if (!$attendance->overtime_in) {
                    return 'overtime_in';
                } else {
                    // Jika overtime_in sudah ada, isi overtime_out (replace jika sudah ada)
                    return 'overtime_out';
                }

            default:
                // Outside defined periods - follow sequential logic
                if (!$attendance || !$attendance->clock_in) {
                    return 'clock_in';
                }
                
                // If clock_in exists but break_in doesn't, and it's reasonable time for break
                if (!$attendance->break_in && $currentTime) {
                    $currentHour = (int) substr($currentTime, 0, 2);
                    // If it's between 10 AM and 3 PM, could be break time
                    if ($currentHour >= 10 && $currentHour <= 15) {
                        return 'break_in';
                    }
                }
                
                // If break_in exists but break_out doesn't
                if ($attendance->break_in && !$attendance->break_out) {
                    return 'break_out';
                }
                
                // If break is complete but no clock_out
                if ($attendance->break_in && $attendance->break_out && !$attendance->clock_out) {
                    return 'clock_out';
                }
                
                // If all basic attendance is complete, could be overtime
                if ($attendance->clock_out && !$attendance->overtime_in && $currentTime) {
                    $currentHour = (int) substr($currentTime, 0, 2);
                    // If it's after 6 PM, could be overtime
                    if ($currentHour >= 18) {
                        return 'overtime_in';
                    }
                }
                
                // If overtime_in exists but overtime_out doesn't
                if ($attendance->overtime_in && !$attendance->overtime_out) {
                    return 'overtime_out';
                }
                
                // Default fallback
                return 'clock_in';
        }
    }

    /**
     * Get human readable description for action
     */
    public static function getActionDescription($action)
    {
        $descriptions = [
            'clock_in' => 'Masuk kerja',
            'clock_out' => 'Pulang kerja',
            'break_in' => 'Mulai istirahat',
            'break_out' => 'Selesai istirahat',
            'overtime_in' => 'Mulai lembur',
            'overtime_out' => 'Selesai lembur'
        ];

        return $descriptions[$action] ?? $action;
    }
}