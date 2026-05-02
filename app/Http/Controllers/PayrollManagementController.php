<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Recruitment;
use App\Models\PayrollCoaSetting;
use App\Models\ChartOfAccount;
use App\Models\Attendance;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use App\Traits\HasOutletFilter;
use App\Services\PayrollJournalService;
use Carbon\Carbon;

class PayrollManagementController extends Controller
{
    use HasOutletFilter;

    protected $journalService;

    public function __construct(PayrollJournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();
        return view('admin.sdm.payroll.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        $outletFilter = $request->get('outlet_filter', 'all');
        $periodFilter = $request->get('period_filter', date('Y-m'));
        $statusFilter = $request->get('status_filter', 'all');
        $search = $request->get('search', '');

        $query = Payroll::with(['outlet', 'employee']);

        // Apply outlet filter
        $query = $this->applyOutletFilter($query, 'outlet_id');

        if ($outletFilter !== 'all') {
            $query->where('outlet_id', $outletFilter);
        }

        if ($periodFilter) {
            $query->where('period', $periodFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $payrolls = $query->orderBy('payment_date', 'desc')->get();

        $data = $payrolls->map(function($payroll) {
            return [
                'id' => $payroll->id,
                'outlet_name' => $payroll->outlet ? $payroll->outlet->nama_outlet : '-',
                'employee_name' => $payroll->employee->name,
                'employee_position' => $payroll->employee->position,
                'period' => $payroll->period,
                'period_formatted' => Carbon::parse($payroll->period . '-01')->format('F Y'),
                'payment_date' => $payroll->payment_date->format('d/m/Y'),
                'basic_salary' => $payroll->basic_salary,
                'basic_salary_formatted' => 'Rp ' . number_format($payroll->basic_salary, 0, ',', '.'),
                'gross_salary' => $payroll->gross_salary,
                'gross_salary_formatted' => 'Rp ' . number_format($payroll->gross_salary, 0, ',', '.'),
                'net_salary' => $payroll->net_salary,
                'net_salary_formatted' => 'Rp ' . number_format($payroll->net_salary, 0, ',', '.'),
                'status' => $payroll->status,
                'status_label' => $this->getStatusLabel($payroll->status),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEmployees(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        $query = Recruitment::where('status', 'active');
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        } else {
            // Apply outlet filter
            $query = $this->applyOutletFilter($query, 'outlet_id');
        }

        $employees = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $employees->map(function($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                    'position' => $emp->position,
                    'salary' => $emp->salary,
                    'hourly_rate' => $emp->hourly_rate,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'recruitment_id' => 'required|exists:recruitments,id',
            'period' => 'required|date_format:Y-m',
            'payment_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:0',
            'present_days' => 'required|integer|min:0',
            'absent_days' => 'nullable|integer|min:0',
            'late_days' => 'nullable|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'late_penalty' => 'nullable|numeric|min:0',
            'absent_penalty' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($request->outlet_id);

        // Check duplicate
        $exists = Payroll::where('recruitment_id', $request->recruitment_id)
            ->where('period', $request->period)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll untuk karyawan ini pada periode tersebut sudah ada'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $payroll = new Payroll($request->all());
            $payroll->autoCalculate();
            $payroll->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll berhasil ditambahkan',
                'data' => $payroll
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating payroll: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $payroll = Payroll::with(['outlet', 'employee', 'approvedBy', 'paidBy'])->findOrFail($id);

            // Validate outlet access
            $this->validateOutletAccess($payroll->outlet_id);

            return response()->json([
                'success' => true,
                'data' => $payroll
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
        $payroll = Payroll::findOrFail($id);

        // Only draft can be edited
        if ($payroll->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya payroll dengan status draft yang bisa diedit'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'recruitment_id' => 'required|exists:recruitments,id',
            'period' => 'required|date_format:Y-m',
            'payment_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'working_days' => 'required|integer|min:0',
            'present_days' => 'required|integer|min:0',
            'absent_days' => 'nullable|integer|min:0',
            'late_days' => 'nullable|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'late_penalty' => 'nullable|numeric|min:0',
            'absent_penalty' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($request->outlet_id);
        $this->validateOutletAccess($payroll->outlet_id);

        try {
            DB::beginTransaction();

            $payroll->fill($request->all());
            $payroll->autoCalculate();
            $payroll->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll berhasil diupdate',
                'data' => $payroll
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating payroll: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);

            // Only draft can be deleted
            if ($payroll->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya payroll dengan status draft yang bisa dihapus'
                ], 422);
            }

            // Validate outlet access
            $this->validateOutletAccess($payroll->outlet_id);

            DB::beginTransaction();
            $payroll->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting payroll: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);

            if ($payroll->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payroll sudah diapprove atau dibayar'
                ], 422);
            }

            // Validate outlet access
            $this->validateOutletAccess($payroll->outlet_id);

            DB::beginTransaction();

            // Create journal entry for approval
            try {
                $this->journalService->createApprovalJournal($payroll);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat jurnal: ' . $e->getMessage()
                ], 500);
            }

            $payroll->status = 'approved';
            $payroll->approved_by = auth()->id();
            $payroll->approved_at = now();
            $payroll->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll berhasil diapprove dan jurnal telah dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving payroll: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pay($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);

            if ($payroll->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payroll sudah dibayar'
                ], 422);
            }

            // Validate outlet access
            $this->validateOutletAccess($payroll->outlet_id);

            DB::beginTransaction();

            // Create journal entry for payment
            try {
                $this->journalService->createPaymentJournal($payroll);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat jurnal: ' . $e->getMessage()
                ], 500);
            }

            $payroll->status = 'paid';
            $payroll->paid_by = auth()->id();
            $payroll->paid_at = now();
            $payroll->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll berhasil dibayar dan jurnal telah dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error paying payroll: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printSlip($id)
    {
        $payroll = Payroll::with(['outlet', 'employee'])->findOrFail($id);

        // Validate outlet access
        $this->validateOutletAccess($payroll->outlet_id);

        $pdf = Pdf::loadView('admin.sdm.payroll.slip', compact('payroll'));
        return $pdf->download('slip-gaji-' . $payroll->employee->name . '-' . $payroll->period . '.pdf');
    }

    public function exportPdf(Request $request)
    {
        $outletFilter = $request->get('outlet_filter', 'all');
        $periodFilter = $request->get('period_filter', date('Y-m'));
        $statusFilter = $request->get('status_filter', 'all');

        $query = Payroll::with(['outlet', 'employee']);
        $query = $this->applyOutletFilter($query, 'outlet_id');

        if ($outletFilter !== 'all') {
            $query->where('outlet_id', $outletFilter);
        }

        if ($periodFilter) {
            $query->where('period', $periodFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $payrolls = $query->orderBy('payment_date', 'desc')->get();

        $pdf = Pdf::loadView('admin.sdm.payroll.pdf', [
            'payrolls' => $payrolls,
            'period' => $periodFilter,
            'title' => 'Laporan Payroll'
        ]);

        return $pdf->download('payroll-' . $periodFilter . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $outletFilter = $request->get('outlet_filter', 'all');
        $periodFilter = $request->get('period_filter', date('Y-m'));
        $statusFilter = $request->get('status_filter', 'all');

        return Excel::download(
            new PayrollExport($outletFilter, $periodFilter, $statusFilter, $this->getUserOutletIds()), 
            'payroll-' . $periodFilter . '.xlsx'
        );
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'Draft',
            'approved' => 'Approved',
            'paid' => 'Dibayar'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get attendance summary for payroll
     */
    public function getAttendanceSummary(Request $request)
    {
        try {
            $employeeId = $request->get('employee_id');
            $period = $request->get('period'); // Format: YYYY-MM
            
            if (!$employeeId || !$period) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee ID dan periode harus diisi'
                ], 422);
            }

            list($year, $month) = explode('-', $period);
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            // Get employee
            $employee = Recruitment::findOrFail($employeeId);
            
            // Get work schedule
            $schedule = $employee->workSchedule ?? WorkSchedule::getOrCreateForRecruitment($employeeId);
            $scheduleIn = $schedule ? date('H:i', strtotime($schedule->clock_in)) : '08:00';
            $scheduleOut = $schedule ? date('H:i', strtotime($schedule->clock_out)) : '17:00';

            // Get all attendances for this period
            $attendances = Attendance::where('recruitment_id', $employeeId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            // Calculate summary
            $totalPresent = 0;
            $totalAbsent = 0;
            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;
            $totalOvertimeMinutes = 0;
            $totalHours = 0;
            $lateDays = 0;

            foreach ($attendances as $attendance) {
                // Count present days
                if (in_array($attendance->status, ['present', 'late'])) {
                    $totalPresent++;
                }
                
                // Count absent days
                if (in_array($attendance->status, ['absent', 'leave', 'sick', 'permission'])) {
                    $totalAbsent++;
                }
                
                // Calculate hours worked
                if ($attendance->clock_in && $attendance->clock_out) {
                    $start = Carbon::parse($attendance->clock_in);
                    $end = Carbon::parse($attendance->clock_out);
                    $minutes = abs($end->diffInMinutes($start));
                    
                    if ($attendance->break_out && $attendance->break_in) {
                        $breakStart = Carbon::parse($attendance->break_out);
                        $breakEnd = Carbon::parse($attendance->break_in);
                        $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
                        $minutes = max(0, $minutes - $breakMinutes);
                    }
                    
                    $totalHours += $minutes / 60;
                }
                
                // Calculate late minutes
                if ($attendance->clock_in && $scheduleIn) {
                    $actual = Carbon::parse($attendance->clock_in);
                    $scheduled = Carbon::parse($scheduleIn);
                    if ($actual->gt($scheduled)) {
                        $lateMinutes = abs($actual->diffInMinutes($scheduled));
                        $totalLateMinutes += $lateMinutes;
                        if ($lateMinutes > 0) {
                            $lateDays++;
                        }
                    }
                }
                
                // Calculate early minutes
                if ($attendance->clock_out && $scheduleOut) {
                    $actual = Carbon::parse($attendance->clock_out);
                    $scheduled = Carbon::parse($scheduleOut);
                    if ($actual->lt($scheduled)) {
                        $totalEarlyMinutes += abs($scheduled->diffInMinutes($actual));
                    }
                }
                
                // Calculate overtime
                if ($attendance->overtime_in && $attendance->overtime_out) {
                    $overtimeStart = Carbon::parse($attendance->overtime_in);
                    $overtimeEnd = Carbon::parse($attendance->overtime_out);
                    if ($overtimeEnd->gt($overtimeStart)) {
                        $totalOvertimeMinutes += abs($overtimeEnd->diffInMinutes($overtimeStart));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_present' => $totalPresent,
                    'total_absent' => $totalAbsent,
                    'total_hours' => round($totalHours, 2),
                    'total_late_minutes' => $totalLateMinutes,
                    'late_days' => $lateDays,
                    'total_early_minutes' => $totalEarlyMinutes,
                    'overtime_hours' => round($totalOvertimeMinutes / 60, 2),
                    'overtime_minutes' => $totalOvertimeMinutes,
                    'working_days' => $startDate->daysInMonth,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting attendance summary: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data absensi: ' . $e->getMessage()
            ], 500);
        }
    }
}
