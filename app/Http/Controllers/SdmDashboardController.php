<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Models\Outlet;
use App\Models\Recruitment;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\KontrakKerja;
use App\Models\PerformanceAppraisal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SdmDashboardController extends Controller
{
    use HasOutletFilter;

    public function index()
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get();
        
        return view('admin.sdm.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        try {
            // Get accessible outlets for current user
            $accessibleOutletIds = $this->getAccessibleOutletIds();
            
            $outletIds = $request->input('outlet_ids', []);
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            // Validate outlet access if specific outlets requested
            if (!empty($outletIds)) {
                $invalidOutlets = array_diff($outletIds, $accessibleOutletIds);
                if (!empty($invalidOutlets)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke beberapa outlet yang dipilih'
                    ], 403);
                }
                // Use only the requested outlets that user has access to
                $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
            } else {
                // If no specific outlets requested, use all accessible outlets
                $filterOutletIds = $accessibleOutletIds;
            }

            $data = [
                'kpi' => $this->getKPI($filterOutletIds, $startDate, $endDate),
                'employee_summary' => $this->getEmployeeSummary($filterOutletIds),
                'attendance_summary' => $this->getAttendanceSummary($filterOutletIds, $startDate, $endDate),
                'payroll_summary' => $this->getPayrollSummary($filterOutletIds, $startDate, $endDate),
                'recent_activities' => $this->getRecentActivities($filterOutletIds, 10),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading SDM dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getKPI($filterOutletIds, $startDate, $endDate)
    {
        // Total Active Employees
        $totalEmployees = Recruitment::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'active')
            ->count();

        // Total Departments (unique positions)
        $totalDepartments = Recruitment::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'active')
            ->distinct('position')
            ->count('position');

        // Today's Attendance
        $todayAttendance = Attendance::whereIn('outlet_id', $filterOutletIds)
            ->whereDate('date', Carbon::today())
            ->count();

        // Total Payroll for the period
        $totalPayroll = Payroll::whereIn('outlet_id', $filterOutletIds)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('net_salary');

        // Active Contracts
        $activeContracts = KontrakKerja::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'aktif')
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'total_departments' => $totalDepartments,
            'today_attendance' => $todayAttendance,
            'total_payroll' => $totalPayroll,
            'active_contracts' => $activeContracts,
        ];
    }

    private function getEmployeeSummary($filterOutletIds)
    {
        $summary = [];
        
        $employees = Recruitment::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'active')
            ->with('outlet')
            ->get();

        foreach ($employees as $employee) {
            $outletName = $employee->outlet->nama_outlet ?? 'Unknown';
            if (!isset($summary[$outletName])) {
                $summary[$outletName] = [
                    'name' => $outletName,
                    'total' => 0,
                    'positions' => []
                ];
            }
            $summary[$outletName]['total']++;
            
            $position = $employee->position ?? 'Unknown';
            if (!isset($summary[$outletName]['positions'][$position])) {
                $summary[$outletName]['positions'][$position] = 0;
            }
            $summary[$outletName]['positions'][$position]++;
        }

        return array_values($summary);
    }

    private function getAttendanceSummary($filterOutletIds, $startDate, $endDate)
    {
        $attendances = Attendance::whereIn('outlet_id', $filterOutletIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'overtime_hours' => $attendances->sum('overtime_hours'),
        ];

        return $summary;
    }

    private function getPayrollSummary($filterOutletIds, $startDate, $endDate)
    {
        $payrolls = Payroll::whereIn('outlet_id', $filterOutletIds)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_employees_paid' => $payrolls->count(),
            'total_gross_salary' => $payrolls->sum('gross_salary'),
            'total_deductions' => $payrolls->sum('deduction') + $payrolls->sum('late_penalty') + $payrolls->sum('absent_penalty') + $payrolls->sum('loan_deduction') + $payrolls->sum('tax'),
            'total_net_salary' => $payrolls->sum('net_salary'),
            'average_salary' => $payrolls->count() > 0 ? $payrolls->avg('net_salary') : 0,
        ];

        return $summary;
    }

    private function getRecentActivities($filterOutletIds, $limit = 10)
    {
        $activities = [];

        // Recent Recruitments
        $recentRecruitments = Recruitment::whereIn('outlet_id', $filterOutletIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->with('outlet')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentRecruitments as $recruitment) {
            $activities[] = [
                'type' => 'recruitment',
                'title' => 'Karyawan Baru: ' . $recruitment->name,
                'description' => 'Posisi: ' . $recruitment->position . ' di ' . ($recruitment->outlet->nama_outlet ?? 'Unknown'),
                'date' => $recruitment->created_at,
                'icon' => 'bx-user-plus',
                'color' => 'green'
            ];
        }

        // Recent Contracts
        $recentContracts = KontrakKerja::whereIn('outlet_id', $filterOutletIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->with(['recruitment', 'outlet'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentContracts as $contract) {
            $activities[] = [
                'type' => 'contract',
                'title' => 'Kontrak Baru: ' . ($contract->recruitment->name ?? 'Unknown'),
                'description' => 'Nomor: ' . $contract->nomor_kontrak . ' di ' . ($contract->outlet->nama_outlet ?? 'Unknown'),
                'date' => $contract->created_at,
                'icon' => 'bx-file',
                'color' => 'blue'
            ];
        }

        // Sort by date and limit
        usort($activities, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        
        return array_slice($activities, 0, $limit);
    }
}