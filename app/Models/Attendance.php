<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'recruitment_id',
        'employee_name',
        'fingerprint_id',
        'rfid_uid',
        'date',
        'clock_in',
        'clock_in_photo',
        'break_out',
        'break_out_photo',
        'break_in',
        'break_in_photo',
        'clock_out',
        'clock_out_photo',
        'overtime_in',
        'overtime_in_photo',
        'overtime_out',
        'overtime_out_photo',
        'status',
        'work_hours',
        'overtime_hours',
        'hours_worked',
        'late_minutes',
        'early_minutes',
        'notes',
        'source',
        'latitude',
        'longitude',
        'location_address',
        'device_info',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'work_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'hours_worked' => 'decimal:2',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function employee()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }

    public function workSchedule()
    {
        return $this->hasOneThrough(
            WorkSchedule::class,
            Recruitment::class,
            'id', // Foreign key on recruitments table
            'recruitment_id', // Foreign key on work_schedules table
            'recruitment_id', // Local key on attendances table
            'id' // Local key on recruitments table
        );
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Auto-calculate work hours (Total Jam Kerja)
    public function calculateWorkHours()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        try {
            // Normalize time fields to HH:MM:SS format only
            $clockIn = $this->normalizeTimeField($this->clock_in);
            $clockOut = $this->normalizeTimeField($this->clock_out);
            
            if (!$clockIn || !$clockOut) {
                return 0;
            }
            
            // Parse times using a fixed date to avoid timezone issues
            $start = Carbon::parse('2000-01-01 ' . $clockIn);
            $end = Carbon::parse('2000-01-01 ' . $clockOut);
            
            // Handle overnight shifts
            if ($end->lt($start)) {
                $end->addDay();
            }
            
            $totalMinutes = 0;
            
            // Apply the correct formula: [(break_in - clock_in) + (clock_out - break_out)] + (overtime_out - overtime_in)
            if ($this->break_in && $this->break_out) {
                $breakIn = $this->normalizeTimeField($this->break_in);   // mulai istirahat
                $breakOut = $this->normalizeTimeField($this->break_out); // selesai istirahat
                
                if ($breakIn && $breakOut) {
                    $breakStart = Carbon::parse('2000-01-01 ' . $breakIn);
                    $breakEnd = Carbon::parse('2000-01-01 ' . $breakOut);
                    
                    // Handle overnight break
                    if ($breakEnd->lt($breakStart)) {
                        $breakEnd->addDay();
                    }
                    
                    // Waktu kerja sebelum istirahat: break_in - clock_in
                    $beforeBreakMinutes = $start->diffInMinutes($breakStart);
                    
                    // Waktu kerja setelah istirahat: clock_out - break_out
                    $afterBreakMinutes = $breakEnd->diffInMinutes($end);
                    
                    $totalMinutes = $beforeBreakMinutes + $afterBreakMinutes;
                } else {
                    // Jika break time tidak valid, hitung tanpa break
                    $totalMinutes = $start->diffInMinutes($end);
                }
            } else {
                // Tidak ada waktu istirahat: clock_out - clock_in
                $totalMinutes = $start->diffInMinutes($end);
            }
            
            // Tambahkan jam lembur jika ada: overtime_out - overtime_in
            if ($this->overtime_in && $this->overtime_out) {
                $overtimeIn = $this->normalizeTimeField($this->overtime_in);
                $overtimeOut = $this->normalizeTimeField($this->overtime_out);
                
                if ($overtimeIn && $overtimeOut) {
                    $overtimeStart = Carbon::parse('2000-01-01 ' . $overtimeIn);
                    $overtimeEnd = Carbon::parse('2000-01-01 ' . $overtimeOut);
                    
                    // Handle overnight overtime
                    if ($overtimeEnd->lt($overtimeStart)) {
                        $overtimeEnd->addDay();
                    }
                    
                    $overtimeMinutes = $overtimeStart->diffInMinutes($overtimeEnd);
                    if ($overtimeMinutes > 0) {
                        $totalMinutes += $overtimeMinutes;
                    }
                }
            }
            
            return round($totalMinutes / 60, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating work hours: ' . $e->getMessage() . ' - Data: ' . json_encode([
                'clock_in' => $this->clock_in,
                'clock_out' => $this->clock_out,
                'break_in' => $this->break_in,
                'break_out' => $this->break_out,
                'overtime_in' => $this->overtime_in,
                'overtime_out' => $this->overtime_out
            ]));
            return 0;
        }
    }
    
    /**
     * Normalize time field to HH:MM:SS format
     */
    private function normalizeTimeField($timeValue)
    {
        if (empty($timeValue)) {
            return null;
        }
        
        $timeValue = trim($timeValue);
        
        // Handle malformed strings like "2025-12-12 00:00:00 05:01:00" (double time specification)
        if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}\s+(\d{2}:\d{2}:\d{2})$/', $timeValue, $matches)) {
            return $matches[1];
        }
        
        // If it contains date and time (YYYY-MM-DD HH:MM:SS), extract only time
        if (preg_match('/^\d{4}-\d{2}-\d{2}\s+(\d{2}:\d{2}:\d{2})$/', $timeValue, $matches)) {
            return $matches[1];
        }
        
        // If it's HH:MM format, add seconds
        if (preg_match('/^\d{2}:\d{2}$/', $timeValue)) {
            return $timeValue . ':00';
        }
        
        // If it's already HH:MM:SS format
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $timeValue)) {
            return $timeValue;
        }
        
        // Handle other malformed formats - extract any valid time pattern
        if (preg_match('/(\d{2}:\d{2}:\d{2})/', $timeValue, $matches)) {
            return $matches[1];
        }
        
        // If format is not recognized, log and return null
        \Log::warning("Unknown time format: {$timeValue}");
        return null;
    }

    public function calculateLateMinutes($scheduleClockIn = null)
    {
        if (!$this->clock_in) {
            return 0;
        }

        try {
            // Get schedule from work_schedule if not provided
            if (!$scheduleClockIn) {
                $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
                $scheduleClockIn = $workSchedule ? $workSchedule->clock_in : '08:00:00';
            }

            // Normalize schedule time
            $scheduleTime = $this->normalizeTimeField($scheduleClockIn);
            if (!$scheduleTime) {
                return 0;
            }

            // Normalize clock_in time
            $clockInTime = $this->normalizeTimeField($this->clock_in);
            if (!$clockInTime) {
                return 0;
            }

            // Parse times using a fixed date to avoid timezone issues
            $scheduled = Carbon::parse('2000-01-01 ' . $scheduleTime);
            $actual = Carbon::parse('2000-01-01 ' . $clockInTime);
            
            if ($actual->greaterThan($scheduled)) {
                return $actual->diffInMinutes($scheduled);
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating late minutes for attendance ' . $this->id . ': ' . $e->getMessage());
            \Log::error('Data: date=' . $this->date . ', clock_in=' . $this->clock_in . ', scheduleClockIn=' . ($scheduleClockIn ?? 'null'));
            return 0;
        }
    }

    // Calculate early leave minutes (Pulang Cepat)
    public function calculateEarlyMinutes($scheduleClockOut = null)
    {
        if (!$this->clock_out) {
            return 0;
        }

        try {
            // Get schedule from work_schedule if not provided
            if (!$scheduleClockOut) {
                $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
                $scheduleClockOut = $workSchedule ? $workSchedule->clock_out : '17:00:00';
            }

            // Normalize schedule time
            $scheduleTime = $this->normalizeTimeField($scheduleClockOut);
            if (!$scheduleTime) {
                return 0;
            }

            // Normalize clock_out time
            $clockOutTime = $this->normalizeTimeField($this->clock_out);
            if (!$clockOutTime) {
                return 0;
            }

            // Parse times using a fixed date to avoid timezone issues
            $scheduled = Carbon::parse('2000-01-01 ' . $scheduleTime);
            $actual = Carbon::parse('2000-01-01 ' . $clockOutTime);
            
            // Handle overnight shifts
            if ($actual->lt($scheduled)) {
                return $scheduled->diffInMinutes($actual);
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating early minutes: ' . $e->getMessage());
            return 0;
        }
    }

    // Calculate overtime minutes (Lembur)
    public function calculateOvertimeMinutes($scheduleClockOut = null)
    {
        if (!$this->clock_out) {
            return 0;
        }

        try {
            // Get schedule from work_schedule if not provided
            if (!$scheduleClockOut) {
                $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
                $scheduleClockOut = $workSchedule ? $workSchedule->clock_out : '17:00:00';
            }

            // Normalize schedule time
            $scheduleTime = $this->normalizeTimeField($scheduleClockOut);
            if (!$scheduleTime) {
                return 0;
            }

            // Normalize clock_out time
            $clockOutTime = $this->normalizeTimeField($this->clock_out);
            if (!$clockOutTime) {
                return 0;
            }

            // Parse times using a fixed date to avoid timezone issues
            $scheduled = Carbon::parse('2000-01-01 ' . $scheduleTime);
            $actual = Carbon::parse('2000-01-01 ' . $clockOutTime);
            
            // Handle overnight shifts
            if ($actual->lt($scheduled)) {
                $actual->addDay();
            }
            
            if ($actual->greaterThan($scheduled)) {
                return $actual->diffInMinutes($scheduled);
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating overtime minutes: ' . $e->getMessage());
            return 0;
        }
    }

    // Auto-calculate overtime hours (from overtime_in and overtime_out)
    public function calculateOvertimeHours()
    {
        if (!$this->overtime_in || !$this->overtime_out) {
            return 0;
        }

        try {
            // Normalize overtime times
            $overtimeIn = $this->normalizeTimeField($this->overtime_in);
            $overtimeOut = $this->normalizeTimeField($this->overtime_out);
            
            if (!$overtimeIn || !$overtimeOut) {
                return 0;
            }

            // Parse times using a fixed date to avoid timezone issues
            $start = Carbon::parse('2000-01-01 ' . $overtimeIn);
            $end = Carbon::parse('2000-01-01 ' . $overtimeOut);
            
            // Handle overnight overtime
            if ($end->lt($start)) {
                $end->addDay();
            }
            
            return round($end->diffInMinutes($start) / 60, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating overtime hours: ' . $e->getMessage());
            return 0;
        }
    }

    // Auto-determine status
    public function determineStatus()
    {
        if (!$this->clock_in) {
            return 'absent';
        }

        // Check if late more than 15 minutes
        $lateMinutes = $this->calculateLateMinutes();
        
        if ($lateMinutes > 15) {
            return 'late';
        }
        
        return 'present';
    }

    // Auto-calculate all fields
    public function autoCalculate()
    {
        // Get work schedule
        $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
        $scheduleClockIn = $workSchedule ? $workSchedule->clock_in : '08:00:00';
        $scheduleClockOut = $workSchedule ? $workSchedule->clock_out : '17:00:00';

        // Calculate all metrics
        $this->hours_worked = $this->calculateWorkHours();
        $this->work_hours = $this->hours_worked; // Keep both for compatibility
        $this->late_minutes = $this->calculateLateMinutes($scheduleClockIn);
        $this->early_minutes = $this->calculateEarlyMinutes($scheduleClockOut);
        
        // Calculate overtime from schedule (not from overtime_in/out)
        $overtimeMinutes = $this->calculateOvertimeMinutes($scheduleClockOut);
        
        // If there's also overtime_in/out, add those hours
        $overtimeFromFields = $this->calculateOvertimeHours();
        $this->overtime_hours = round(($overtimeMinutes / 60) + $overtimeFromFields, 2);
        
        // Only auto-determine status if not manually set to leave/sick/permission
        if (!in_array($this->status, ['leave', 'sick', 'permission', 'absent'])) {
            $this->status = $this->determineStatus();
        }
        
        return $this;
    }

    // Get schedule times for display
    public function getScheduleTimes()
    {
        $workSchedule = WorkSchedule::where('recruitment_id', $this->recruitment_id)->first();
        
        return [
            'clock_in' => $workSchedule ? date('H:i', strtotime($workSchedule->clock_in)) : '08:00',
            'clock_out' => $workSchedule ? date('H:i', strtotime($workSchedule->clock_out)) : '17:00',
        ];
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByEmployee($query, $recruitmentId)
    {
        return $query->where('recruitment_id', $recruitmentId);
    }

    public function getFormattedTime($timeField)
    {
        if (!$this->$timeField) {
            return null;
        }
        
        try {
            return substr($this->$timeField, 0, 5);
        } catch (\Exception $e) {
            return $this->$timeField;
        }
    }

    public function setTimeField($field, $time)
    {
        if (empty($time)) {
            $this->attributes[$field] = null;
            return;
        }
        
        // If time doesn't have seconds, add them
        if (strlen($time) === 5 && strpos($time, ':') !== false) {
            $this->attributes[$field] = $time . ':00';
        } else {
            $this->attributes[$field] = $time;
        }
    }

    public function getClockInFormattedAttribute()
    {
        return $this->getFormattedTime('clock_in');
    }

    public function getClockOutFormattedAttribute()
    {
        return $this->getFormattedTime('clock_out');
    }

    public function getBreakOutFormattedAttribute()
    {
        return $this->getFormattedTime('break_out');
    }

    public function getBreakInFormattedAttribute()
    {
        return $this->getFormattedTime('break_in');
    }

    public function getOvertimeInFormattedAttribute()
    {
        return $this->getFormattedTime('overtime_in');
    }

    public function getOvertimeOutFormattedAttribute()
    {
        return $this->getFormattedTime('overtime_out');
    }

    // Di model Attendance, tambahkan mutator:
    public function setClockInAttribute($value)
    {
        $this->attributes['clock_in'] = $this->cleanTimeValue($value);
    }

    public function setClockOutAttribute($value)
    {
        $this->attributes['clock_out'] = $this->cleanTimeValue($value);
    }

    public function setBreakOutAttribute($value)
    {
        $this->attributes['break_out'] = $this->cleanTimeValue($value);
    }

    public function setBreakInAttribute($value)
    {
        $this->attributes['break_in'] = $this->cleanTimeValue($value);
    }

    public function setOvertimeInAttribute($value)
    {
        $this->attributes['overtime_in'] = $this->cleanTimeValue($value);
    }

    public function setOvertimeOutAttribute($value)
    {
        $this->attributes['overtime_out'] = $this->cleanTimeValue($value);
    }

    private function cleanTimeValue($value)
    {
        if (empty($value)) {
            return null;
        }
        
        $value = trim($value);
        
        // Jika ada spasi (mungkin ada tanggal), ambil hanya waktu
        if (strpos($value, ' ') !== false) {
            $parts = explode(' ', $value);
            $value = end($parts);
        }
        
        // Pastikan format HH:MM:SS
        if (strlen($value) === 5 && strpos($value, ':') !== false) {
            $value .= ':00';
        }
        
        return $value;
    }
}
