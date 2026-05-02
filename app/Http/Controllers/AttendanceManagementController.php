<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Attendance;
use App\Models\Recruitment;
use App\Models\WorkSchedule;
use App\Models\AttendanceTimeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Carbon\Carbon;

class AttendanceManagementController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        return view('admin.sdm.attendance.index');
    }

    public function getData(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $status = $request->get('status');

        $query = Attendance::with('employee');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($employeeId) {
            $query->where('recruitment_id', $employeeId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $data = $attendances->map(function($att, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $att->id,
                'date' => Carbon::parse($att->date)->format('d/m/Y'),
                'employee_name' => $att->employee ? $att->employee->name : '-',
                'check_in' => $att->check_in ?? '-',
                'check_out' => $att->check_out ?? '-',
                'status' => '<span class="badge badge-' . $this->getStatusBadge($att->status) . '">' . $this->getStatusLabel($att->status) . '</span>',
                'work_hours' => number_format($att->work_hours, 2) . ' jam',
                'overtime_hours' => number_format($att->overtime_hours, 2) . ' jam',
                'notes' => $att->notes ?? '-',
                'action' => '
                    <button class="btn btn-sm btn-info" onclick="editAttendance(' . $att->id . ')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteAttendance(' . $att->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                '
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEmployees(Request $request)
    {
        $outletIds = $request->get('outlet_ids', []);
        
        $employeesQuery = Recruitment::where('status', 'active');
        
        // Apply outlet filtering if provided
        if (!empty($outletIds) && is_array($outletIds)) {
            $employeesQuery->whereIn('outlet_id', $outletIds);
        }
        
        $employees = $employeesQuery->orderBy('name')
            ->get()
            ->map(function($emp) {
                return [
                    'id' => $emp->id,
                    'nama' => $emp->name,
                    'jabatan' => $emp->position ?? '-',
                    'departemen' => $emp->department ?? '-'
                ];
            });

        return response()->json($employees);
    }

    /**
     * Custom validation rule for time format (accepts both HH:MM and HH:MM:SS)
     */
    private function validateTimeFormat($value) {
        if (empty($value)) {
            return true; // nullable fields
        }
        
        // Accept both HH:MM and HH:MM:SS formats
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $value);
    }
    
    /**
     * Normalize time to HH:MM format for database storage
     */
    private function normalizeTimeToHHMM($time) {
        if (empty($time)) {
            return null;
        }
        
        // If HH:MM:SS format, remove seconds
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $time)) {
            return substr($time, 0, 5);
        }
        
        // If HH:MM format, ensure proper padding
        if (preg_match('/^([0-9]{1,2}):([0-9]{2})$/', $time, $matches)) {
            return sprintf('%02d:%02d', (int)$matches[1], (int)$matches[2]);
        }
        
        return $time;
    }

    public function store(Request $request)
    {
        // Custom time format validation
        $timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];
        foreach ($timeFields as $field) {
            if ($request->has($field) && !empty($request->$field)) {
                if (!$this->validateTimeFormat($request->$field)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => [$field => ['Format waktu harus HH:MM atau HH:MM:SS (24 jam)']]
                    ], 422);
                }
                // Normalize to HH:MM for database storage
                $request->merge([$field => $this->normalizeTimeToHHMM($request->$field)]);
            }
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:recruitments,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|string',
            'clock_out' => 'nullable|string',
            'break_in' => 'nullable|string',
            'break_out' => 'nullable|string',
            'overtime_in' => 'nullable|string',
            'overtime_out' => 'nullable|string',
            'status' => 'required|in:present,late,absent,leave,sick,permission',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check duplicate
        $exists = Attendance::where('recruitment_id', $request->employee_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $employee = Recruitment::findOrFail($request->employee_id);
            
            // Get user's first outlet or default
            $outletId = auth()->user()->outlets()->first()->id_outlet ?? 1;

            $attendance = new Attendance([
                'outlet_id' => $outletId,
                'recruitment_id' => $request->employee_id,
                'employee_name' => $employee->name,
                'fingerprint_id' => $employee->fingerprint_id,
                'date' => $request->date,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'break_out' => $request->break_out,
                'break_in' => $request->break_in,
                'overtime_in' => $request->overtime_in,
                'overtime_out' => $request->overtime_out,
                'status' => $request->status,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // Auto-calculate all metrics
            $attendance->autoCalculate();
            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil ditambahkan',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating attendance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $attendance = Attendance::with('employee')->findOrFail($id);

            return response()->json([
                'id' => $attendance->id,
                'employee_id' => $attendance->recruitment_id,
                'date' => $attendance->date,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
                'break_out' => $attendance->break_out,
                'break_in' => $attendance->break_in,
                'overtime_in' => $attendance->overtime_in,
                'overtime_out' => $attendance->overtime_out,
                'status' => $attendance->status,
                'notes' => $attendance->notes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // Custom time format validation
        $timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];
        foreach ($timeFields as $field) {
            if ($request->has($field) && !empty($request->$field)) {
                if (!$this->validateTimeFormat($request->$field)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => [$field => ['Format waktu harus HH:MM atau HH:MM:SS (24 jam)']]
                    ], 422);
                }
                // Normalize to HH:MM for database storage
                $request->merge([$field => $this->normalizeTimeToHHMM($request->$field)]);
            }
        }

        $validator = Validator::make($request->all(), [
            'clock_in' => 'nullable|string',
            'clock_out' => 'nullable|string',
            'break_in' => 'nullable|string',
            'break_out' => 'nullable|string',
            'overtime_in' => 'nullable|string',
            'overtime_out' => 'nullable|string',
            'status' => 'required|in:present,late,absent,leave,sick,permission',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $attendance->clock_in = $request->clock_in;
            $attendance->clock_out = $request->clock_out;
            $attendance->break_out = $request->break_out;
            $attendance->break_in = $request->break_in;
            $attendance->overtime_in = $request->overtime_in;
            $attendance->overtime_out = $request->overtime_out;
            $attendance->status = $request->status;
            $attendance->notes = $request->notes;

            // Auto-calculate all metrics
            $attendance->autoCalculate();
            $attendance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil diupdate',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating attendance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);

            DB::beginTransaction();
            $attendance->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting attendance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            'cuti' => 'Cuti',
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Alpha',
            'leave' => 'Izin',
            'sick' => 'Sakit',
            'permission' => 'Izin Khusus'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
            'cuti' => 'primary',
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            'leave' => 'warning',
            'sick' => 'info',
            'permission' => 'warning'
        ];

        return $badges[$status] ?? 'secondary';
    }

    /**
     * Get daily attendance table data
     */
    public function getDailyTable(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            $outletIds = $request->get('outlet_ids', []);
            $search = $request->get('search', '');

            // Base query for employees
            $employeesQuery = Recruitment::where('status', 'active');
            
            // Apply outlet filtering if provided
            if (!empty($outletIds) && is_array($outletIds)) {
                $employeesQuery->whereIn('outlet_id', $outletIds);
            }

            // Apply search filter
            if (!empty($search)) {
                $employeesQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('fingerprint_id', 'like', "%{$search}%");
                });
            }

            $employees = $employeesQuery->get();

            $attendanceData = [];

            foreach ($employees as $employee) {
                // Get attendance for this employee on the specified date
                $attendance = Attendance::where('recruitment_id', $employee->id)
                    ->where('date', $date)
                    ->first();

                // Get work schedule for this employee
                $schedule = WorkSchedule::where('recruitment_id', $employee->id)->first();

                $attendanceData[] = [
                    'id' => $attendance->id ?? null,
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'fingerprint_id' => $employee->fingerprint_id,
                    'position' => $employee->position ?? '-',
                    'schedule_in' => $schedule && $schedule->clock_in ? \Carbon\Carbon::parse($schedule->clock_in)->format('H:i') : null,
                    'schedule_out' => $schedule && $schedule->clock_out ? \Carbon\Carbon::parse($schedule->clock_out)->format('H:i') : null,
                    'status' => $attendance->status ?? 'absent',
                    'clock_in' => $attendance->clock_in ?? null,
                    'clock_in_photo' => $attendance->clock_in_photo ?? null,
                    'clock_out' => $attendance->clock_out ?? null,
                    'clock_out_photo' => $attendance->clock_out_photo ?? null,
                    'break_in' => $attendance->break_in ?? null,
                    'break_in_photo' => $attendance->break_in_photo ?? null,
                    'break_out' => $attendance->break_out ?? null,
                    'break_out_photo' => $attendance->break_out_photo ?? null,
                    'overtime_in' => $attendance->overtime_in ?? null,
                    'overtime_in_photo' => $attendance->overtime_in_photo ?? null,
                    'overtime_out' => $attendance->overtime_out ?? null,
                    'overtime_out_photo' => $attendance->overtime_out_photo ?? null,
                    'late_minutes' => $attendance->late_minutes ?? 0,
                    'early_minutes' => $attendance->early_minutes ?? 0,
                    'overtime_minutes' => $attendance->overtime_minutes ?? 0,
                    'hours_worked' => $attendance->hours_worked ?? 0,
                    'notes' => $attendance->notes ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $attendanceData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting daily table: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly attendance table data
     */
    public function getMonthlyTable(Request $request)
    {
        try {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
            $outletIds = $request->get('outlet_ids', []);
            $search = $request->get('search', '');

            // Calculate days in month
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            // Base query for employees
            $employeesQuery = Recruitment::where('status', 'active');
            
            // Apply outlet filtering if provided
            if (!empty($outletIds) && is_array($outletIds)) {
                $employeesQuery->whereIn('outlet_id', $outletIds);
            }

            // Apply search filter
            if (!empty($search)) {
                $employeesQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('fingerprint_id', 'like', "%{$search}%");
                });
            }

            $employees = $employeesQuery->get();

            $monthlyData = [];

            foreach ($employees as $employee) {
                $employeeData = [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'position' => $employee->position ?? '-',
                    'total_present' => 0,
                    'total_absent' => 0,
                    'total_hours' => 0,
                    'total_late' => 0,
                    'total_early' => 0,
                    'total_overtime' => 0,
                ];

                // Get attendance for each day of the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day)->format('Y-m-d');
                    
                    $attendance = Attendance::where('recruitment_id', $employee->id)
                        ->where('date', $date)
                        ->first();

                    if ($attendance) {
                        $employeeData["day_{$day}"] = $attendance->status;
                        
                        // Update totals
                        if (in_array($attendance->status, ['present', 'late'])) {
                            $employeeData['total_present']++;
                        } else {
                            $employeeData['total_absent']++;
                        }
                        
                        $employeeData['total_hours'] += $attendance->hours_worked ?? 0;
                        $employeeData['total_late'] += $attendance->late_minutes ?? 0;
                        $employeeData['total_early'] += $attendance->early_minutes ?? 0;
                        $employeeData['total_overtime'] += ($attendance->overtime_minutes ?? 0) / 60; // Convert to hours
                    } else {
                        $employeeData["day_{$day}"] = null;
                        $employeeData['total_absent']++;
                    }
                }

                $monthlyData[] = $employeeData;
            }

            return response()->json([
                'success' => true,
                'data' => $monthlyData,
                'days_in_month' => $daysInMonth
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting monthly table: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $outletIds = $request->get('outlet_ids', []);

            $query = Attendance::whereBetween('date', [$startDate, $endDate]);

            // Apply outlet filtering if provided
            if (!empty($outletIds) && is_array($outletIds)) {
                $query->whereIn('outlet_id', $outletIds);
            }

            $attendances = $query->get();

            $statistics = [
                'hadir' => $attendances->whereIn('status', ['present', 'late'])->count(),
                'terlambat' => $attendances->where('status', 'late')->count(),
                'tidak_hadir' => $attendances->whereIn('status', ['absent', 'leave', 'sick', 'permission'])->count(),
                'avg_hours' => $attendances->avg('hours_worked') ?? 0,
            ];

            return response()->json($statistics);

        } catch (\Exception $e) {
            \Log::error('Error getting statistics: ' . $e->getMessage());
            return response()->json([
                'hadir' => 0,
                'terlambat' => 0,
                'tidak_hadir' => 0,
                'avg_hours' => 0,
            ]);
        }
    }

    /**
     * Set work hours for employees
     */
    public function setWorkHours(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'clock_in' => 'required|string',
                'clock_out' => 'required|string',
                'employee_id' => 'nullable|exists:recruitments,id',
                'apply_to_all' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate time format
            if (!$this->validateTimeFormat($request->clock_in) || !$this->validateTimeFormat($request->clock_out)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format waktu harus HH:MM atau HH:MM:SS (24 jam)'
                ], 422);
            }

            DB::beginTransaction();

            $clockIn = $this->normalizeTimeToHHMM($request->clock_in);
            $clockOut = $this->normalizeTimeToHHMM($request->clock_out);

            if ($request->apply_to_all || !$request->employee_id) {
                // Apply to all active employees
                $employees = Recruitment::where('status', 'active')->get();
                
                foreach ($employees as $employee) {
                    WorkSchedule::updateOrCreate(
                        ['recruitment_id' => $employee->id],
                        [
                            'clock_in' => $clockIn,
                            'clock_out' => $clockOut,
                            'updated_by' => auth()->id()
                        ]
                    );
                }

                $message = 'Jadwal kerja berhasil diterapkan ke semua karyawan aktif';
            } else {
                // Apply to specific employee
                WorkSchedule::updateOrCreate(
                    ['recruitment_id' => $request->employee_id],
                    [
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'updated_by' => auth()->id()
                    ]
                );

                $employee = Recruitment::find($request->employee_id);
                $message = "Jadwal kerja berhasil disimpan untuk {$employee->name}";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error setting work hours: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jadwal kerja'
            ], 500);
        }
    }

    /**
     * Get time settings for RFID
     */
    public function getTimeSettings()
    {
        try {
            $settings = AttendanceTimeSetting::all();

            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting time settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat pengaturan waktu'
            ], 500);
        }
    }

    /**
     * Update time settings for RFID
     */
    public function updateTimeSettings(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'settings' => 'required|array',
                'settings.*.id' => 'required|exists:attendance_time_settings,id',
                'settings.*.start_time' => 'required|string',
                'settings.*.end_time' => 'required|string',
                'settings.*.is_active' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            foreach ($request->settings as $settingData) {
                // Validate time format
                if (!$this->validateTimeFormat($settingData['start_time']) || !$this->validateTimeFormat($settingData['end_time'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format waktu harus HH:MM atau HH:MM:SS (24 jam)'
                    ], 422);
                }

                $setting = AttendanceTimeSetting::find($settingData['id']);
                if ($setting) {
                    $setting->start_time = $this->normalizeTimeToHHMM($settingData['start_time']);
                    $setting->end_time = $this->normalizeTimeToHHMM($settingData['end_time']);
                    $setting->is_active = $settingData['is_active'];
                    $setting->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan waktu berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating time settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan pengaturan waktu'
            ], 500);
        }
    }

    /**
     * Test time period for RFID
     */
    public function testTimePeriod(Request $request)
    {
        try {
            $time = $request->get('time');
            
            if (!$time || !$this->validateTimeFormat($time)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format waktu tidak valid'
                ], 422);
            }

            $normalizedTime = $this->normalizeTimeToHHMM($time);
            $timeCarbon = Carbon::createFromFormat('H:i', $normalizedTime);

            // Get active time settings
            $settings = AttendanceTimeSetting::where('is_active', true)->get();

            $result = [
                'time_period' => 'Diluar jam kerja',
                'action_description' => 'Tidak ada aksi'
            ];

            foreach ($settings as $setting) {
                $startTime = Carbon::createFromFormat('H:i', $setting->start_time);
                $endTime = Carbon::createFromFormat('H:i', $setting->end_time);

                // Handle overnight periods
                if ($endTime->lt($startTime)) {
                    if ($timeCarbon->gte($startTime) || $timeCarbon->lte($endTime)) {
                        $result = [
                            'time_period' => $setting->name,
                            'action_description' => $setting->description
                        ];
                        break;
                    }
                } else {
                    if ($timeCarbon->gte($startTime) && $timeCarbon->lte($endTime)) {
                        $result = [
                            'time_period' => $setting->name,
                            'action_description' => $setting->description
                        ];
                        break;
                    }
                }
            }

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Error testing time period: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat test periode waktu'
            ], 500);
        }
    }

    /**
     * Export daily attendance to PDF
     */
    public function exportDailyPdf(Request $request)
        {
            try {
                $date = $request->get('date', now()->format('Y-m-d'));
                $outletIds = $request->get('outlet_ids', []);

                // Get daily data
                $request->merge(['date' => $date, 'outlet_ids' => $outletIds]);
                $response = $this->getDailyTable($request);
                $data = $response->getData(true);

                if (!$data['success']) {
                    return response()->json(['error' => 'Gagal memuat data'], 500);
                }

                $attendances = $data['data'];
                $formattedDate = Carbon::parse($date)->format('d F Y');

                // Format data for PDF view
                $formattedData = array_map(function($row) {
                    return [
                        'fingerprint_id' => $row['fingerprint_id'] ?? '-',
                        'employee_name' => $row['employee_name'] ?? '-',
                        'position' => $row['position'] ?? '-',
                        'schedule_in' => $row['schedule_in'] ?? '-',
                        'schedule_out' => $row['schedule_out'] ?? '-',
                        'status_label' => $this->getStatusLabel($row['status'] ?? 'absent'),
                        'status_class' => $this->getStatusBadge($row['status'] ?? 'absent') === 'success' ? 'success' : ($this->getStatusBadge($row['status'] ?? 'absent') === 'danger' ? 'danger' : 'warning'),
                        'clock_in' => $row['clock_in'] ?? '-',
                        'clock_out' => $row['clock_out'] ?? '-',
                        'break_out' => $row['break_out'] ?? '-',
                        'break_in' => $row['break_in'] ?? '-',
                        'overtime_in' => $row['overtime_in'] ?? '-',
                        'overtime_out' => $row['overtime_out'] ?? '-',
                        'hours_worked' => number_format($row['hours_worked'] ?? 0, 2),
                        'late_minutes' => ($row['late_minutes'] ?? 0) > 0 ? $row['late_minutes'] . ' mnt' : '-',
                        'late_class' => ($row['late_minutes'] ?? 0) > 0 ? 'danger' : '',
                        'early_minutes' => ($row['early_minutes'] ?? 0) > 0 ? $row['early_minutes'] . ' mnt' : '-',
                        'early_class' => ($row['early_minutes'] ?? 0) > 0 ? 'warning' : '',
                        'overtime_minutes' => ($row['overtime_minutes'] ?? 0) > 0 ? number_format($row['overtime_minutes'] / 60, 2) . ' jam' : '-',
                    ];
                }, $attendances);

                $pdf = PDF::loadView('admin.sdm.attendance.daily-pdf', [
                    'data' => $formattedData,
                    'date' => $date,
                    'dateFormatted' => $formattedDate
                ])->setPaper('a4', 'landscape');

                return $pdf->download("Absensi_Harian_{$date}.pdf");

            } catch (\Exception $e) {
                \Log::error('Error exporting daily PDF: ' . $e->getMessage());
                return response()->json(['error' => 'Gagal export PDF'], 500);
            }
        }


    /**
     * Export monthly attendance to PDF
     */
    public function exportMonthlyPdf(Request $request)
        {
            try {
                $month = $request->get('month', now()->month);
                $year = $request->get('year', now()->year);
                $outletIds = $request->get('outlet_ids', []);

                // Get monthly data
                $request->merge(['month' => $month, 'year' => $year, 'outlet_ids' => $outletIds]);
                $response = $this->getMonthlyTable($request);
                $data = $response->getData(true);

                if (!$data['success']) {
                    return response()->json(['error' => 'Gagal memuat data'], 500);
                }

                $monthlyData = $data['data'];
                $daysInMonth = $data['days_in_month'];
                $monthName = Carbon::create($year, $month, 1)->format('F Y');

                // Format data for PDF view
                $formattedData = array_map(function($row) use ($daysInMonth) {
                    $days = [];
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $status = $row["day_{$day}"] ?? null;
                        $symbol = '-';

                        if ($status === 'present' || $status === 'late') {
                            $symbol = 'H';
                        } elseif ($status === 'leave' || $status === 'sick') {
                            $symbol = 'I';
                        } elseif ($status === 'absent') {
                            $symbol = 'A';
                        }

                        $days[$day] = ['symbol' => $symbol];
                    }

                    return [
                        'employee_name' => $row['employee_name'] ?? '-',
                        'position' => $row['position'] ?? '-',
                        'days' => $days,
                        'summary' => [
                            'present' => $row['total_present'] ?? 0,
                            'absent' => $row['total_absent'] ?? 0,
                            'hours' => number_format($row['total_hours'] ?? 0, 2),
                            'late' => $row['total_late'] ?? 0,
                            'early' => $row['total_early'] ?? 0,
                            'overtime' => number_format($row['total_overtime'] ?? 0, 2),
                        ]
                    ];
                }, $monthlyData);

                $pdf = PDF::loadView('admin.sdm.attendance.monthly-pdf', [
                    'data' => $formattedData,
                    'daysInMonth' => $daysInMonth,
                    'monthName' => $monthName
                ])->setPaper('a4', 'landscape');

                return $pdf->download("Absensi_Bulanan_{$year}_{$month}.pdf");

            } catch (\Exception $e) {
                \Log::error('Error exporting monthly PDF: ' . $e->getMessage());
                return response()->json(['error' => 'Gagal export PDF'], 500);
            }
        }


    /**
     * Export attendance to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $outletIds = $request->get('outlet_ids', []);

            return Excel::download(new AttendanceExport($startDate, $endDate, $outletIds), "Absensi_{$startDate}_to_{$endDate}.xlsx");

        } catch (\Exception $e) {
            \Log::error('Error exporting Excel: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export Excel'], 500);
        }
    }

    /**
     * Register RFID card for employee
     */
    public function registerRfidCard(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:recruitment,id',
                'rfid_uid' => 'required|string|unique:recruitment,rfid_uid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $employee = Recruitment::findOrFail($request->employee_id);
            $employee->rfid_uid = $request->rfid_uid;
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'RFID card registered successfully',
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'rfid_uid' => $employee->rfid_uid
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error registering RFID card: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to register RFID card'
            ], 500);
        }
    }

    /**
     * Get current RFID mode
     */
    public function getRfidMode()
    {
        try {
            $mode = \Cache::get('rfid_mode', 'attendance');
            
            return response()->json([
                'success' => true,
                'mode' => $mode
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting RFID mode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get RFID mode'
            ], 500);
        }
    }

    /**
     * Set RFID mode
     */
    public function setRfidMode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mode' => 'required|in:attendance,register'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid mode',
                    'errors' => $validator->errors()
                ], 422);
            }

            \Cache::put('rfid_mode', $request->mode, now()->addHours(24));

            return response()->json([
                'success' => true,
                'message' => 'RFID mode updated successfully',
                'mode' => $request->mode
            ]);

        } catch (\Exception $e) {
            \Log::error('Error setting RFID mode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to set RFID mode'
            ], 500);
        }
    }

    /**
     * Handle RFID card detection with photo capture
     */
    public function handleCardDetected(Request $request)
    {
        // Set maximum execution time to prevent timeout
        set_time_limit(30);
        
        try {
            $validator = Validator::make($request->all(), [
                'uid' => 'required|string',
                'photo' => 'nullable|string' // Base64 encoded photo
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $uid = $request->uid;
            $photoBase64 = $request->photo;
            $currentMode = \Cache::get('rfid_mode', 'attendance');

            // Store detected UID in cache for frontend (non-blocking)
            \Cache::put('detected_rfid_uid', $uid, now()->addMinutes(5));

            // Process photo in background if too large
            if ($photoBase64 && strlen($photoBase64) > 50000) {
                \Log::info("Large photo received for UID: $uid, processing in background");
                // Queue photo processing for later
                dispatch(function() use ($photoBase64, $uid) {
                    $this->saveAttendancePhoto($photoBase64, $uid . '_background');
                })->afterResponse();
                $photoBase64 = null; // Don't process now
            }

            if ($currentMode === 'register') {
                return $this->handleRegistrationMode($uid, $photoBase64);
            } else {
                return $this->handleAttendanceMode($uid, $photoBase64);
            }

        } catch (\Exception $e) {
            \Log::error('Error handling card detection: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process card detection'
            ], 500);
        }
    }

    /**
     * Handle registration mode
     */
    private function handleRegistrationMode($uid, $photoBase64 = null)
    {
        // Check if UID already exists
        $existingEmployee = Recruitment::where('rfid_uid', $uid)->first();
        
        if ($existingEmployee) {
            // Card already registered, switch back to attendance mode
            \Cache::put('rfid_mode', 'attendance', now()->addHours(24));
            
            return response()->json([
                'success' => false,
                'message' => 'RFID card already registered to ' . $existingEmployee->name,
                'action' => 'register',
                'mode_changed_to' => 'attendance',
                'employee' => [
                    'name' => $existingEmployee->name,
                    'position' => $existingEmployee->position
                ]
            ]);
        }

        // Save photo if provided
        $photoPath = null;
        if ($photoBase64) {
            $photoPath = $this->saveAttendancePhoto($photoBase64, $uid . '_register');
        }

        // After successful registration detection, switch back to attendance mode
        \Cache::put('rfid_mode', 'attendance', now()->addHours(24));

        return response()->json([
            'success' => true,
            'message' => 'Card ready for registration. Please assign to employee in admin panel.',
            'action' => 'register',
            'mode_changed_to' => 'attendance',
            'uid' => $uid,
            'photo_path' => $photoPath
        ]);
    }

    /**
     * Handle attendance mode
     */
    private function handleAttendanceMode($uid, $photoBase64 = null)
    {
        try {
            // Find employee by RFID UID
            $employee = Recruitment::where('rfid_uid', $uid)->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'RFID card not registered. Please register first.',
                    'action' => 'register',
                    'mode_changed_to' => 'register'
                ]);
            }

            // Get current time and determine action
            $now = now();
            $currentTime = $now->format('H:i:s');
            $currentDate = $now->format('Y-m-d');

            // Get time period using the model method
            $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
            
            // Get outlet ID - handle both authenticated and unauthenticated requests
            $outletId = 1; // Default outlet
            $createdBy = null;
            
            if (auth()->check()) {
                // Authenticated request (web interface)
                $userOutlet = auth()->user()->outlets()->first();
                $outletId = $userOutlet ? $userOutlet->id_outlet : 1;
                $createdBy = auth()->id();
            } else {
                // Unauthenticated request (ESP32 API)
                // Use employee's outlet if available, otherwise default
                $outletId = $employee->outlet_id ?? 1;
            }
            
            // Find or create attendance record for today
            $attendance = Attendance::firstOrCreate(
                [
                    'recruitment_id' => $employee->id,
                    'date' => $currentDate
                ],
                [
                    'outlet_id' => $outletId,
                    'employee_name' => $employee->name,
                    'fingerprint_id' => $employee->fingerprint_id,
                    'status' => 'present',
                    'notes' => 'Auto-created by RFID system',
                    'created_by' => $createdBy
                ]
            );

            // Save photo if provided
            $photoPath = null;
            if ($photoBase64) {
                $photoPath = $this->saveAttendancePhoto($photoBase64, $employee->id . '_' . $currentDate . '_' . time());
            }

            // Determine next action using the model method
            $nextAction = AttendanceTimeSetting::determineNextAction($attendance, $timePeriod, $currentTime);
            
            // Update attendance based on determined action
            $result = $this->updateAttendanceByAction($attendance, $nextAction, $currentTime, $photoPath);

            // Auto-calculate work hours and other metrics
            $attendance->autoCalculate();
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => "Attendance recorded: {$result['description']}",
                'action' => 'attendance',
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position
                ],
                'attendance' => [
                    'condition' => $result['condition'],
                    'time' => $currentTime,
                    'photo_path' => $photoPath,
                    'time_period' => $timePeriod,
                    'next_action' => $nextAction
                ],
                'type' => $result['condition'], // check_in, break, check_out, etc
                'time' => $now->format('H:i')  // Format jam:menit untuk ditampilkan di TFT
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in handleAttendanceMode: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save attendance photo from base64
     */
    private function saveAttendancePhoto($photoBase64, $filename)
    {
        try {
            // Quick validation
            if (empty($photoBase64) || strlen($photoBase64) < 100) {
                return null;
            }

            // Create directory if not exists
            $directory = storage_path('app/public/attendance_photos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate unique filename
            $filename = 'attendance_' . $filename . '_' . time() . '.jpg';
            $filepath = $directory . '/' . $filename;

            // Decode and save photo with error handling
            $photoData = base64_decode($photoBase64, true);
            if ($photoData === false) {
                \Log::error('Invalid base64 photo data');
                return null;
            }

            // Quick file size check
            if (strlen($photoData) > 2000000) { // 2MB limit
                \Log::warning('Photo too large, skipping save: ' . strlen($photoData) . ' bytes');
                return null;
            }

            $result = file_put_contents($filepath, $photoData);
            if ($result === false) {
                \Log::error('Failed to write photo file: ' . $filepath);
                return null;
            }

            // Return public path
            return 'attendance_photos/' . $filename;

        } catch (\Exception $e) {
            \Log::error('Error saving attendance photo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update attendance based on determined action
     */
    private function updateAttendanceByAction($attendance, $action, $currentTime, $photoPath = null)
    {
        $result = ['condition' => 'unknown', 'description' => 'Unknown action'];

        try {
            switch ($action) {
                case 'clock_in':
                    // Only update if not set, or if we're in check_in period (can replace)
                    $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
                    if (!$attendance->clock_in || $timePeriod === 'check_in') {
                        $attendance->clock_in = $currentTime;
                        if ($photoPath) {
                            $attendance->clock_in_photo = $photoPath;
                        }
                        $result = ['condition' => 'datang', 'description' => 'Clock in recorded'];
                    } else {
                        $result = ['condition' => 'sudah_masuk', 'description' => 'Already clocked in'];
                    }
                    break;

                case 'clock_out':
                    // Only update if not set, or if we're in check_out period (can replace)
                    $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
                    if (!$attendance->clock_out || $timePeriod === 'check_out') {
                        $attendance->clock_out = $currentTime;
                        if ($photoPath) {
                            $attendance->clock_out_photo = $photoPath;
                        }
                        $result = ['condition' => 'pulang', 'description' => 'Clock out recorded'];
                    } else {
                        $result = ['condition' => 'sudah_pulang', 'description' => 'Already clocked out'];
                    }
                    break;

                case 'break_in':
                    // Only update if not set, or if we're in break period (can replace)
                    $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
                    if (!$attendance->break_in || $timePeriod === 'break') {
                        $attendance->break_in = $currentTime;
                        if ($photoPath) {
                            $attendance->break_in_photo = $photoPath;
                        }
                        $result = ['condition' => 'mulai_istirahat', 'description' => 'Break start recorded'];
                    } else {
                        $result = ['condition' => 'sudah_istirahat', 'description' => 'Break already started'];
                    }
                    break;

                case 'break_out':
                    // Only update if not set, or if we're in break period (can replace)
                    $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
                    if (!$attendance->break_out || $timePeriod === 'break') {
                        $attendance->break_out = $currentTime;
                        if ($photoPath) {
                            $attendance->break_out_photo = $photoPath;
                        }
                        $result = ['condition' => 'selesai_istirahat', 'description' => 'Break end recorded'];
                    } else {
                        $result = ['condition' => 'istirahat_selesai', 'description' => 'Break already ended'];
                    }
                    break;

                case 'overtime_in':
                    // Only update if not set, or if we're in overtime period (can replace)
                    $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
                    if (!$attendance->overtime_in || $timePeriod === 'overtime') {
                        $attendance->overtime_in = $currentTime;
                        if ($photoPath) {
                            $attendance->overtime_in_photo = $photoPath;
                        }
                        $result = ['condition' => 'lembur_masuk', 'description' => 'Overtime start recorded'];
                    } else {
                        $result = ['condition' => 'sudah_lembur', 'description' => 'Overtime already started'];
                    }
                    break;

                case 'overtime_out':
                    // Only update if not set, or if we're in overtime period (can replace)
                    $timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
                    if (!$attendance->overtime_out || $timePeriod === 'overtime') {
                        $attendance->overtime_out = $currentTime;
                        if ($photoPath) {
                            $attendance->overtime_out_photo = $photoPath;
                        }
                        $result = ['condition' => 'lembur_keluar', 'description' => 'Overtime end recorded'];
                    } else {
                        $result = ['condition' => 'lembur_selesai', 'description' => 'Overtime already ended'];
                    }
                    break;

                default:
                    // Default to clock_in if action is unknown
                    if (!$attendance->clock_in) {
                        $attendance->clock_in = $currentTime;
                        if ($photoPath) {
                            $attendance->clock_in_photo = $photoPath;
                        }
                        $result = ['condition' => 'datang_default', 'description' => 'Default attendance recorded'];
                    } else {
                        $result = ['condition' => 'tidak_ada_aksi', 'description' => 'No action needed'];
                    }
                    break;
            }

            $attendance->save();
            return $result;

        } catch (\Exception $e) {
            \Log::error('Error updating attendance by action: ' . $e->getMessage());
            return ['condition' => 'error', 'description' => 'Failed to update attendance'];
        }
    }
}
