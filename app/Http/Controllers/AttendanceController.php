<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Recruitment;
use App\Models\Payroll;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Log;
use App\Models\WorkSchedule;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Get active tab
        $activeTab = $request->input('tab', 'daily');
        
        // Initialize variables
        $weeklyYear = date('Y');
        $weeklyMonth = date('m');
        $selectedWeek = Carbon::now()->weekOfMonth;
        $monthlyMonth = date('Y-m');

        // Initialize variables
        $selectedDate = date('Y-m-d');
        $selectedYear = date('Y');
        $selectedMonth = date('m');

        $dailyAttendances = [];
        $monthlyData = [];
        $employees = Recruitment::with('workSchedule')->get();

        $monthlyData = [
            'dates' => [],
            'employees' => [],
            'startDate' => now()->startOfMonth(),
            'endDate' => now()->endOfMonth()
        ];
        
        // Monthly Tab
        if ($activeTab === 'monthly' || $request->has('monthly_filter')) {
            $monthlyMonth = $request->input('monthly_month', date('Y-m'));
            $monthlyData = $this->getMonthlyAttendances($monthlyMonth, $employees); // Kirim $employees ke method
        } else {
            $monthlyData = $this->getMonthlyAttendances($monthlyMonth, $employees); // Kirim $employees ke method
        }
        
        // For daily tab
        if ($activeTab === 'daily') {
            $selectedDate = $request->input('date', date('Y-m-d'));
            $dailyAttendances = $this->getDailyAttendances($selectedDate);
        }
        
        $dayNames = [
            0 => 'Min',
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab'
        ];


        if ($request->ajax()) {
            return view('hrm.attendance.index', [
                'activeTab' => $request->input('tab', 'daily'),
                'monthlyMonth' => $monthlyMonth,
                'dailyAttendances' => $dailyAttendances,
                'monthlyData' => $monthlyData,
                'dayNames' => $dayNames,
                'selectedDate' => $selectedDate,
                'employees' => $employees
            ]);
        }
        
        // Jika request normal, kembalikan full page
        return view('hrm.attendance.index', [
            'activeTab' => $request->input('tab', 'daily'),
                'monthlyMonth' => $monthlyMonth,
                'dailyAttendances' => $dailyAttendances,
                'monthlyData' => $monthlyData,
                'dayNames' => $dayNames,
                'selectedDate' => $selectedDate,
                'employees' => $employees
        ]);
    
    }

    private function getDailyAttendances($date)
    {
        $attendances = Attendance::with('recruitment')
            ->whereDate('date', $date)
            ->get()
            ->map(function ($attendance) {
                $attendance->late_minutes = $this->calculateLateMinutes($attendance);
                $attendance->early_minutes = $this->calculateEarlyMinutes($attendance);
                $attendance->overtime_minutes = $this->calculateOvertimeMinutes($attendance);
                return $attendance;
            });
        
        return $attendances;
    }


    private function getMonthlyAttendances($month, $employeesX = null)
    {
        $defaultData = [
            'dates' => [],
            'employees' => [],
            'startDate' => now()->startOfMonth(),
            'endDate' => now()->endOfMonth()
        ];

        try {
            // Validasi input month
            if (empty($month)) {
                return $defaultData;
            }
    
            // Parse input month
            $month = Carbon::parse($month)->format('Y-m');
            
            // Get start and end date of the month
            $startDate = Carbon::parse($month)->startOfMonth();
            $endDate = Carbon::parse($month)->endOfMonth();
            
            // Get all dates in the month
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get all employees
        $employees = $employeesX ?: Recruitment::with('workSchedule')->get();
        
        // Get attendances for this month
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('recruitment_id');
        
            $monthlyData = [];
            foreach ($employees as $employee) {
                $employeeData = [
                    'fingerprint_id' => $employee->fingerprint_id,
                    'work_schedule' => [
                        'clock_in' => $employee->workSchedule->clock_in ?? '08:00',
                        'clock_out' => $employee->workSchedule->clock_out ?? '17:00'
                    ],
                    'name' => $employee->name,
                    'position' => $employee->position ?? '-',
                    'attendances' => [],
                    'total_present' => 0,
                    'total_absent' => 0,
                    'total_late' => 0,
                    'total_early' => 0,
                    'total_overtime' => 0
                ];
                
                foreach ($dates as $date) {
                    $dateKey = $date->format('Y-m-d');
                    $attendance = null;
                    
                    if (isset($attendances[$employee->id])) {
                        $attendance = $attendances[$employee->id]->firstWhere('date', $dateKey);
                    }
                    
                    if ($attendance) {
                        $employeeData['attendances'][$dateKey] = [
                            'status' => '✓',
                            'clock_in' => $attendance->clock_in,
                            'clock_out' => $attendance->clock_out,
                            'late' => $this->calculateLateMinutes($attendance),
                            'early' => $this->calculateEarlyMinutes($attendance),
                            'overtime' => $this->calculateOvertimeMinutes($attendance)
                        ];
                        $employeeData['total_present']++;
                        $employeeData['total_late'] += $this->calculateLateMinutes($attendance);
                        $employeeData['total_early'] += $this->calculateEarlyMinutes($attendance);
                        $employeeData['total_overtime'] += $this->calculateOvertimeMinutes($attendance);
                    } else {
                        $employeeData['attendances'][$dateKey] = [
                            'status' => '-',
                            'clock_in' => null,
                            'clock_out' => null,
                            'late' => 0,
                            'early' => 0,
                            'overtime' => 0
                        ];
                        $employeeData['total_absent']++;
                    }
                }
                
                $monthlyData[] = $employeeData;
            }
            
            return [
                'dates' => $dates,
                'employees' => $monthlyData,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];
    
        } catch (\Exception $e) {
            \Log::error('Error in getMonthlyAttendances: ' . $e->getMessage());
            return $defaultData;
        }
    }

    private function getDayName($dayOfWeek)
    {
        $days = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        return $days[$dayOfWeek] ?? '';
    }

    private function calculateLateMinutes($attendance)
    {
        // Use the model's calculation method which handles normalization properly
        return $attendance->calculateLateMinutes();
    }

    private function calculateEarlyMinutes($attendance)
    {
        // Use the model's calculation method which handles normalization properly
        return $attendance->calculateEarlyMinutes();
    }

    private function calculateOvertimeMinutes($attendance)
    {
        // Use the model's calculation method which handles normalization properly
        return $attendance->calculateOvertimeMinutes();
    }

    public function create()
    {
        $recruitments = Recruitment::all();
        return view('hrm.attendance.create', compact('recruitments'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id', // recruitment_id wajib diisi
            'date' => 'required|date',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
        ]);

        // Hitung total jam kerja
        $clockIn = Carbon::parse($request->clock_in);
        $clockOut = $request->clock_out ? Carbon::parse($request->clock_out) : null;
        $hoursWorked = $clockOut ? $clockIn->diffInHours($clockOut) : 0;

        // Simpan data absensi
        $attendance = Attendance::create([
            'recruitment_id' => $request->recruitment_id,
            'date' => $request->date,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'hours_worked' => $hoursWorked,
        ]);

        // Ambil bulan dan tahun dari tanggal absensi
        $month = date('Y-m', strtotime($attendance->date));

        // Cek apakah sudah ada data payroll untuk recruitment_id dan bulan ini
        $payroll = Payroll::where('recruitment_id', $attendance->recruitment_id)
            ->whereYear('created_at', date('Y', strtotime($attendance->date)))
            ->whereMonth('created_at', date('m', strtotime($attendance->date)))
            ->first();

        if (!$payroll) {
            Payroll::create([
                'recruitment_id' => $attendance->recruitment_id,
                'salary' => $attendance->recruitment->salary, // Ambil gaji dari tabel recruitments
                'benefit' => '',
                'hourly_rate' => $attendance->recruitment->hourly_rate, // Ambil harga per jam dari tabel recruitments
                'created_at' => $attendance->date, // Set created_at sesuai tanggal absensi
            ]);
        }

        return redirect()->route('hrm.attendance.index')->with('success', 'Absensi berhasil dicatat.');
    }

    public function storeApi(Request $request)
    {
        // Validasi input
        $request->validate([
            'fingerprint_id' => 'required|integer', // fingerprint_id wajib diisi
            'date' => 'required|date',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'condition' => 'required|in:datang,pulang', // Tambahkan validasi untuk condition
        ]);

        // Cari recruitment_id berdasarkan fingerprint_id
        $recruitment = Recruitment::where('fingerprint_id', $request->fingerprint_id)->first();

        if (!$recruitment) {
            return response()->json(['error' => 'Karyawan dengan fingerprint_id tersebut tidak ditemukan.'], 404);
        }

        $recruitment_id = $recruitment->id;

        // Cek apakah sudah ada data absensi pada tanggal yang sama
        $attendance = Attendance::where('recruitment_id', $recruitment_id)
            ->whereDate('date', $request->date)
            ->first();

        if ($request->condition == 'datang') {
            // Jika kondisi "datang" dan sudah ada data, kembalikan error
            if ($attendance) {
                return response()->json(['error' => 'Absensi datang sudah dicatat.'], 400);
            }

            // Buat data absensi baru
            $attendance = Attendance::create([
                'recruitment_id' => $recruitment_id,
                'date' => $request->date,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_in, // clock_out sama dengan clock_in
                'hours_worked' => 0, // Jam kerja dihitung nanti saat pulang
            ]);

            // Ambil bulan dan tahun dari tanggal absensi
            $month = date('Y-m', strtotime($attendance->date));

            // Cek apakah sudah ada data payroll untuk recruitment_id dan bulan ini
            $payroll = Payroll::where('recruitment_id', $attendance->recruitment_id)
                ->whereYear('created_at', date('Y', strtotime($attendance->date)))
                ->whereMonth('created_at', date('m', strtotime($attendance->date)))
                ->first();

            if (!$payroll) {
                Payroll::create([
                    'recruitment_id' => $attendance->recruitment_id,
                    'salary' => $attendance->recruitment->salary, // Ambil gaji dari tabel recruitments
                    'benefit' => '',
                    'hourly_rate' => $attendance->recruitment->hourly_rate, // Ambil harga per jam dari tabel recruitments
                    'created_at' => $attendance->date, // Set created_at sesuai tanggal absensi
                ]);
            }

            return response()->json(['message' => 'Absensi datang berhasil dicatat.'], 200);
        } elseif ($request->condition == 'pulang') {
            // Jika kondisi "pulang" dan tidak ada data, kembalikan error
            if (!$attendance) {
                return response()->json(['error' => 'Absensi datang belum dicatat.'], 400);
            }

            // Update clock_out dan hitung total jam kerja
            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($request->clock_out);
            $hoursWorked = $clockIn->diffInHours($clockOut);

            $attendance->update([
                'clock_out' => $request->clock_out,
                'hours_worked' => $hoursWorked,
            ]);

            return response()->json(['message' => 'Absensi pulang berhasil dicatat.'], 200);
        }



        // Jika kondisi tidak valid
        return response()->json(['error' => 'Kondisi tidak valid.'], 400);
    }

    public function edit(Attendance $attendance)
    {
        // Ambil data recruitments untuk dropdown
        $recruitments = Recruitment::all();
        return view('hrm.attendance.edit', compact('recruitments', 'attendance'));
    }

    public function update(Request $request, Attendance $attendance)
    {

        // Hitung total jam kerja
        $clockIn = \Carbon\Carbon::parse($request->clock_in);
        $clockOut = \Carbon\Carbon::parse($request->clock_out);
        $hoursWorked = $clockIn->diffInHours($clockOut);

        // Update data absensi
        $attendance->update([
            'recruitment_id' => $request->recruitment_id, // Gunakan recruitment_id
            'date' => $request->date,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'hours_worked' => $hoursWorked, // Update total jam kerja
        ]);

        return redirect()->route('hrm.attendance.index')->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('hrm.attendance.index')->with('success', 'Attendance deleted successfully.');
    }

    public function getWeeksInMonth(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        
        $date = Carbon::create($year, $month, 1);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Calculate weeks in month (considering partial weeks)
        $weeks = [];
        $currentWeek = $startOfMonth->copy();
        
        while ($currentWeek <= $endOfMonth) {
            $weeks[] = $currentWeek->copy();
            $currentWeek->addWeek();
        }
        
        $totalWeeks = count($weeks);
        
        return response()->json([
            'totalWeeks' => $totalWeeks
        ]);
    }

    public function setWorkHours(Request $request)
    {
        $request->validate([
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i|after:clock_in',
            'apply_to_all' => 'sometimes|boolean'
        ]);

        if ($request->apply_to_all) {
            // Update semua karyawan
            $employees = Recruitment::all();
            foreach ($employees as $employee) {
                WorkSchedule::updateOrCreate(
                    ['recruitment_id' => $employee->id],
                    [
                        'clock_in' => $request->clock_in,
                        'clock_out' => $request->clock_out
                    ]
                );
            }
        } else {
            // Pastikan employee_id ada jika tidak apply_to_all
            if (!$request->employee_id) {
                return redirect()->back()->with('error', 'Pilih karyawan atau centang "Terapkan untuk semua"');
            }
            
            // Update karyawan tertentu
            WorkSchedule::updateOrCreate(
                ['recruitment_id' => $request->employee_id],
                [
                    'clock_in' => $request->clock_in,
                    'clock_out' => $request->clock_out
                ]
            );
        }

        return redirect()->back()->with('success', 'Jam kerja berhasil diupdate');
    }
}