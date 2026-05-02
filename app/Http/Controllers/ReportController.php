<?php

namespace App\Http\Controllers;

use App\Models\JamaahBooking;
use App\Models\Keberangkatan;
use App\Models\TravelPackage;
use App\Models\WorkflowTask;
use App\Models\WorkflowHistory;
use App\Models\JamaahPayment;
use App\Models\JamaahDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DepartureSummaryExport;
use App\Exports\FinancialReportExport;
use App\Exports\OperationalReportExport;
use App\Exports\TeamPerformanceExport;
use App\Traits\HasOutletFilter;

class ReportController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.report.view')->only(['index', 'departureSummary', 'financial', 'operational', 'teamPerformance']);
        $this->middleware('permission:travel.report.export')->only(['exportDepartureSummary', 'exportFinancial', 'exportOperational', 'exportTeamPerformance']);
        $this->middleware('permission:travel.report.dashboard')->only(['dashboard']);
    }
    
    /**
     * Display report selection page
     */
    public function index()
    {
        return view('admin.travel.report.index');
    }

    /**
     * Generate departure summary report
     */
    public function departureSummary(Request $request)
    {
        $query = Keberangkatan::with(['travelPackage', 'jamaahBookings.payments']);

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('id_outlet')) {
            $query->where('id_outlet', $request->id_outlet);
        }

        $keberangkatans = $query->get();

        $reportData = $keberangkatans->map(function ($keberangkatan) {
            $bookings = $keberangkatan->jamaahBookings;
            $revenue = $bookings->sum('total_price');
            $expenses = $keberangkatan->travelPackage->hpp * $bookings->count();
            $profit = $revenue - $expenses;

            return [
                'keberangkatan_code' => $keberangkatan->keberangkatan_code,
                'keberangkatan_name' => $keberangkatan->keberangkatan_name,
                'departure_date' => $keberangkatan->departure_date,
                'jamaah_count' => $bookings->count(),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'profit_margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0
            ];
        });

        return view('admin.travel.report.departure-summary', [
            'reportData' => $reportData,
            'filters' => $request->all()
        ]);
    }

    /**
     * Generate financial report
     */
    public function financial(Request $request)
    {
        // Mode: per keberangkatan atau per paket
        $mode = $request->get('mode', 'package'); // 'package' | 'keberangkatan'

        if ($mode === 'keberangkatan') {
            $query = \App\Models\Keberangkatan::with([
                'travelPackage.hppCalculation',
                'jamaahBookings.addons',
                'jamaahBookings.hotelBookings',
                'outlet',
            ]);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('id_outlet')) {
                $query->where('id_outlet', $request->id_outlet);
            }
            if ($request->filled('id_keberangkatan')) {
                $query->where('id', $request->id_keberangkatan);
            }

            $keberangkatans = $query->get();

            $reportData = $keberangkatans->map(function ($k) {
                $bookings = $k->jamaahBookings;
                $revenue  = $bookings->sum('total_price');
                $hppCalc  = $k->travelPackage?->hppCalculation;
                $hpp      = $hppCalc ? $hppCalc->getHppPerPerson() : 0;
                $costs    = $hpp * $bookings->count();
                $profit   = $revenue - $costs;

                // Add-ons HPP
                $addonHpp = $bookings->flatMap(fn($b) => $b->addons ?? collect())
                    ->where('masuk_hpp', true)
                    ->sum(fn($a) => $a->harga * $a->qty);

                $costs  += $addonHpp;
                $profit -= $addonHpp;

                // Terapkan penyesuaian laporan jika sudah disesuaikan
                $adjustment = 0;
                if ($hppCalc && (bool) $hppCalc->laporan_disesuaikan) {
                    $adjustment = (float) ($hppCalc->laporan_adjustment ?? 0);
                    // Surplus (+): kurangi costs (efisiensi), Defisit (-): tambah costs
                    $costs  -= $adjustment;
                    $profit  = $revenue - $costs;
                }

                // RAB realisasi & hutang dari component_realisasi
                $rabRealisasi = 0;
                $rabHutang    = 0;
                $totalBudget  = 0;
                if ($hppCalc) {
                    $realisasiMap2 = $hppCalc->component_realisasi ?? [];
                    $payStatus2    = $hppCalc->component_payment_status ?? [];
                    $hutangAmt2    = $hppCalc->component_hutang_amount ?? [];
                    $dasarKeys2    = ['flight_cost','transportation_cost','meal_cost','visa_cost','guide_cost','insurance_cost','operational_overhead','contingency'];
                    foreach ($dasarKeys2 as $dk) {
                        $up = (float) ($hppCalc->{$dk} ?? 0);
                        if ($up <= 0) continue;
                        $tot = $up * $bookings->count();
                        $totalBudget += $tot;
                        $st  = $payStatus2[$dk] ?? 'lunas';
                        $rel = isset($realisasiMap2[$dk]) ? (float)$realisasiMap2[$dk] : (($st === 'lunas') ? $tot : 0);
                        $rabRealisasi += $rel;
                        $rabHutang    += (float) ($hutangAmt2[$dk] ?? 0);
                    }
                }

                return [
                    'keberangkatan_code' => $k->keberangkatan_code,
                    'keberangkatan_name' => $k->keberangkatan_name,
                    'package_name'       => $k->travelPackage ? $k->travelPackage->package_name : '-',
                    'package_type'       => $k->travelPackage ? $k->travelPackage->package_type : '-',
                    'departure_date'     => $k->departure_date,
                    'jamaah_count'       => $bookings->count(),
                    'revenue'            => $revenue,
                    'costs'              => $costs,
                    'profit'             => $profit,
                    'profit_margin'      => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                    'hpp_per_person'     => $hpp,
                    'addon_hpp'          => $addonHpp,
                    'outlet_name'        => $k->outlet ? $k->outlet->nama_outlet : '-',
                    'rab_realisasi'      => $rabRealisasi,
                    'rab_hutang'         => $rabHutang,
                    'surplus_defisit'    => $totalBudget - $rabRealisasi,
                    'laporan_disesuaikan'=> $hppCalc?->laporan_disesuaikan ?? false,
                    'laporan_adjustment' => (float) ($hppCalc?->laporan_adjustment ?? 0),
                    'keberangkatan_id'   => $k->id,
                ];
            });

            $totals = [
                'total_revenue'         => $reportData->sum('revenue'),
                'total_costs'           => $reportData->sum('costs'),
                'total_profit'          => $reportData->sum('profit'),
                'average_profit_margin' => $reportData->avg('profit_margin'),
            ];

            $allKeberangkatan = \App\Models\Keberangkatan::orderBy('departure_date', 'desc')->get(['id', 'keberangkatan_code', 'keberangkatan_name']);

            return view('admin.travel.report.financial', [
                'reportData'        => $reportData,
                'totals'            => $totals,
                'filters'           => $request->all(),
                'mode'              => 'keberangkatan',
                'allKeberangkatan'  => $allKeberangkatan,
            ]);
        }

        // Default: per paket
        $query = TravelPackage::with(['jamaahBookings.payments', 'hppCalculation']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('package_type')) {
            $query->where('package_type', $request->package_type);
        }
        if ($request->filled('id_outlet')) {
            $query->where('id_outlet', $request->id_outlet);
        }

        $packages = $query->get();

        $reportData = $packages->map(function ($package) {
            $bookings = $package->jamaahBookings;
            $revenue  = $bookings->sum('total_price');
            $costs    = $package->hpp * $bookings->count();
            $profit   = $revenue - $costs;

            return [
                'package_code'   => $package->package_code,
                'package_name'   => $package->package_name,
                'package_type'   => $package->package_type,
                'departure_date' => $package->departure_date,
                'jamaah_count'   => $bookings->count(),
                'revenue'        => $revenue,
                'costs'          => $costs,
                'profit'         => $profit,
                'profit_margin'  => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                'hpp_per_person' => $package->hpp,
                'price_per_person' => $package->price,
            ];
        });

        $totals = [
            'total_revenue'         => $reportData->sum('revenue'),
            'total_costs'           => $reportData->sum('costs'),
            'total_profit'          => $reportData->sum('profit'),
            'average_profit_margin' => $reportData->avg('profit_margin'),
        ];

        $allKeberangkatan = \App\Models\Keberangkatan::orderBy('departure_date', 'desc')->get(['id', 'keberangkatan_code', 'keberangkatan_name']);

        return view('admin.travel.report.financial', [
            'reportData'       => $reportData,
            'totals'           => $totals,
            'filters'          => $request->all(),
            'mode'             => 'package',
            'allKeberangkatan' => $allKeberangkatan,
        ]);
    }

    /**
     * Generate operational report (workflow stage completion times)
     */
    public function operational(Request $request)
    {
        $query = TravelPackage::with(['workflowHistory']);

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('id_outlet')) {
            $query->where('id_outlet', $request->id_outlet);
        }

        $packages = $query->get();

        // Calculate average time spent in each workflow stage
        $stageData = [];
        
        foreach ($packages as $package) {
            $history = $package->workflowHistory->sortBy('transitioned_at');
            
            $previousTransition = null;
            foreach ($history as $transition) {
                if ($previousTransition) {
                    $stage = $previousTransition->to_stage;
                    $duration = $transition->transitioned_at->diffInHours($previousTransition->transitioned_at);
                    
                    if (!isset($stageData[$stage])) {
                        $stageData[$stage] = [
                            'stage_name' => $stage,
                            'total_duration' => 0,
                            'count' => 0
                        ];
                    }
                    
                    $stageData[$stage]['total_duration'] += $duration;
                    $stageData[$stage]['count']++;
                }
                $previousTransition = $transition;
            }
        }

        // Calculate averages
        $reportData = collect($stageData)->map(function ($data) {
            return [
                'stage_name' => $data['stage_name'],
                'average_duration_hours' => $data['count'] > 0 ? $data['total_duration'] / $data['count'] : 0,
                'package_count' => $data['count']
            ];
        })->values();

        return view('admin.travel.report.operational', [
            'reportData' => $reportData,
            'filters' => $request->all()
        ]);
    }

    /**
     * Generate team performance report
     */
    public function teamPerformance(Request $request)
    {
        $query = WorkflowTask::with(['team', 'workflowStage']);

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('team_code')) {
            $query->where('assigned_to_team', $request->team_code);
        }

        $tasks = $query->get();

        // Group by team
        $teamData = $tasks->groupBy('assigned_to_team')->map(function ($teamTasks, $teamCode) {
            $completed = $teamTasks->where('status', 'completed')->count();
            $total = $teamTasks->count();
            $overdue = $teamTasks->filter(function ($task) {
                return $task->isOverdue();
            })->count();

            // Calculate average completion time
            $completedTasks = $teamTasks->where('status', 'completed');
            $avgCompletionTime = 0;
            if ($completedTasks->count() > 0) {
                $totalTime = $completedTasks->sum(function ($task) {
                    return $task->completed_at->diffInHours($task->created_at);
                });
                $avgCompletionTime = $totalTime / $completedTasks->count();
            }

            return [
                'team_code' => $teamCode,
                'team_name' => $teamTasks->first()->team->team_name ?? $teamCode,
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'pending_tasks' => $teamTasks->where('status', 'pending')->count(),
                'in_progress_tasks' => $teamTasks->where('status', 'in_progress')->count(),
                'overdue_tasks' => $overdue,
                'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0,
                'average_completion_hours' => $avgCompletionTime
            ];
        })->values();

        return view('admin.travel.report.team-performance', [
            'reportData' => $teamData,
            'filters' => $request->all()
        ]);
    }

    /**
     * Get dashboard data
     */
    public function dashboard()
    {
        // Total jamaah count
        $totalJamaah = JamaahBooking::whereNotIn('status', ['cancelled'])->count();
        
        // Jamaah trend (last 6 months)
        $jamaahTrend = JamaahBooking::select(
            DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('booking_date', '>=', now()->subMonths(6))
        ->whereNotIn('status', ['cancelled'])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Upcoming departures (next 30 days)
        $upcomingDepartures = Keberangkatan::with(['travelPackage', 'jamaahBookings'])
            ->whereBetween('departure_date', [now(), now()->addDays(30)])
            ->orderBy('departure_date')
            ->get();

        // Pending payments
        $pendingPayments = JamaahBooking::with(['jamaah', 'travelPackage'])
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->get();
        
        $totalPendingAmount = $pendingPayments->sum('remaining_amount');

        // Incomplete documents
        $incompleteDocuments = JamaahBooking::with(['jamaah', 'documents'])
            ->whereHas('keberangkatan', function ($query) {
                $query->where('departure_date', '>=', now());
            })
            ->get()
            ->filter(function ($booking) {
                $requiredDocs = ['passport', 'visa', 'ticket', 'insurance'];
                $approvedDocs = $booking->documents->where('status', 'approved')->pluck('document_type')->toArray();
                return count(array_diff($requiredDocs, $approvedDocs)) > 0;
            });

        // Booking volume trend (last 12 months)
        $bookingVolumeTrend = JamaahBooking::select(
            DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_price) as revenue')
        )
        ->where('booking_date', '>=', now()->subMonths(12))
        ->whereNotIn('status', ['cancelled'])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Profit margin by package type
        $profitMargins = TravelPackage::with(['jamaahBookings'])
            ->get()
            ->groupBy('package_type')
            ->map(function ($packages, $type) {
                $totalRevenue = 0;
                $totalCost = 0;
                
                foreach ($packages as $package) {
                    $bookingCount = $package->jamaahBookings->whereNotIn('status', ['cancelled'])->count();
                    $totalRevenue += $package->price * $bookingCount;
                    $totalCost += $package->hpp * $bookingCount;
                }
                
                $profit = $totalRevenue - $totalCost;
                $margin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
                
                return [
                    'type' => $type,
                    'revenue' => $totalRevenue,
                    'cost' => $totalCost,
                    'profit' => $profit,
                    'margin' => $margin
                ];
            });

        return view('admin.travel.report.dashboard', compact(
            'totalJamaah',
            'jamaahTrend',
            'upcomingDepartures',
            'pendingPayments',
            'totalPendingAmount',
            'incompleteDocuments',
            'bookingVolumeTrend',
            'profitMargins'
        ));
    }

    /**
     * Export departure summary to PDF
     */
    public function exportDepartureSummaryPdf(Request $request)
    {
        $query = Keberangkatan::with(['travelPackage', 'jamaahBookings.payments']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('id_outlet')) {
            $query->where('id_outlet', $request->id_outlet);
        }

        $keberangkatans = $query->get();

        $reportData = $keberangkatans->map(function ($keberangkatan) {
            $bookings = $keberangkatan->jamaahBookings;
            $revenue = $bookings->sum('total_price');
            $expenses = $keberangkatan->travelPackage->hpp * $bookings->count();
            $profit = $revenue - $expenses;

            return [
                'keberangkatan_code' => $keberangkatan->keberangkatan_code,
                'keberangkatan_name' => $keberangkatan->keberangkatan_name,
                'departure_date' => $keberangkatan->departure_date,
                'jamaah_count' => $bookings->count(),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'profit_margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0
            ];
        });

        $pdf = Pdf::loadView('admin.travel.report.pdf.departure-summary', [
            'reportData' => $reportData,
            'filters' => $request->all(),
            'generatedAt' => now()
        ]);

        return $pdf->download('laporan-ringkasan-keberangkatan-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export departure summary to Excel
     */
    public function exportDepartureSummaryExcel(Request $request)
    {
        return Excel::download(
            new DepartureSummaryExport($request->all()),
            'laporan-ringkasan-keberangkatan-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export financial report to PDF
     */
    public function exportFinancialPdf(Request $request)
    {
        $query = TravelPackage::with(['jamaahBookings.payments', 'hppCalculation']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('package_type')) {
            $query->where('package_type', $request->package_type);
        }

        if ($request->filled('id_outlet')) {
            $query->where('id_outlet', $request->id_outlet);
        }

        $packages = $query->get();

        $reportData = $packages->map(function ($package) {
            $bookings = $package->jamaahBookings;
            $revenue = $bookings->sum('total_price');
            $costs = $package->hpp * $bookings->count();
            $profit = $revenue - $costs;

            return [
                'package_code' => $package->package_code,
                'package_name' => $package->package_name,
                'package_type' => $package->package_type,
                'departure_date' => $package->departure_date,
                'jamaah_count' => $bookings->count(),
                'revenue' => $revenue,
                'costs' => $costs,
                'profit' => $profit,
                'profit_margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                'hpp_per_person' => $package->hpp,
                'price_per_person' => $package->price
            ];
        });

        $totals = [
            'total_revenue' => $reportData->sum('revenue'),
            'total_costs' => $reportData->sum('costs'),
            'total_profit' => $reportData->sum('profit'),
            'average_profit_margin' => $reportData->avg('profit_margin')
        ];

        $pdf = Pdf::loadView('admin.travel.report.pdf.financial', [
            'reportData' => $reportData,
            'totals' => $totals,
            'filters' => $request->all(),
            'generatedAt' => now()
        ]);

        return $pdf->download('laporan-keuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export financial report to Excel
     */
    public function exportFinancialExcel(Request $request)
    {
        return Excel::download(
            new FinancialReportExport($request->all()),
            'laporan-keuangan-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export operational report to PDF
     */
    public function exportOperationalPdf(Request $request)
    {
        $query = TravelPackage::with(['workflowHistory']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('id_outlet')) {
            $query->where('id_outlet', $request->id_outlet);
        }

        $packages = $query->get();

        $stageData = [];
        
        foreach ($packages as $package) {
            $history = $package->workflowHistory->sortBy('transitioned_at');
            
            $previousTransition = null;
            foreach ($history as $transition) {
                if ($previousTransition) {
                    $stage = $previousTransition->to_stage;
                    $duration = $transition->transitioned_at->diffInHours($previousTransition->transitioned_at);
                    
                    if (!isset($stageData[$stage])) {
                        $stageData[$stage] = [
                            'stage_name' => $stage,
                            'total_duration' => 0,
                            'count' => 0
                        ];
                    }
                    
                    $stageData[$stage]['total_duration'] += $duration;
                    $stageData[$stage]['count']++;
                }
                $previousTransition = $transition;
            }
        }

        $reportData = collect($stageData)->map(function ($data) {
            return [
                'stage_name' => $data['stage_name'],
                'average_duration_hours' => $data['count'] > 0 ? $data['total_duration'] / $data['count'] : 0,
                'package_count' => $data['count']
            ];
        })->values();

        $pdf = Pdf::loadView('admin.travel.report.pdf.operational', [
            'reportData' => $reportData,
            'filters' => $request->all(),
            'generatedAt' => now()
        ]);

        return $pdf->download('laporan-operasional-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export operational report to Excel
     */
    public function exportOperationalExcel(Request $request)
    {
        return Excel::download(
            new OperationalReportExport($request->all()),
            'laporan-operasional-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export team performance to PDF
     */
    public function exportTeamPerformancePdf(Request $request)
    {
        $query = WorkflowTask::with(['team', 'workflowStage']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('team_code')) {
            $query->where('assigned_to_team', $request->team_code);
        }

        $tasks = $query->get();

        $teamData = $tasks->groupBy('assigned_to_team')->map(function ($teamTasks, $teamCode) {
            $completed = $teamTasks->where('status', 'completed')->count();
            $total = $teamTasks->count();
            $overdue = $teamTasks->filter(function ($task) {
                return $task->isOverdue();
            })->count();

            $completedTasks = $teamTasks->where('status', 'completed');
            $avgCompletionTime = 0;
            if ($completedTasks->count() > 0) {
                $totalTime = $completedTasks->sum(function ($task) {
                    return $task->completed_at->diffInHours($task->created_at);
                });
                $avgCompletionTime = $totalTime / $completedTasks->count();
            }

            return [
                'team_code' => $teamCode,
                'team_name' => $teamTasks->first()->team->team_name ?? $teamCode,
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'pending_tasks' => $teamTasks->where('status', 'pending')->count(),
                'in_progress_tasks' => $teamTasks->where('status', 'in_progress')->count(),
                'overdue_tasks' => $overdue,
                'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0,
                'average_completion_hours' => $avgCompletionTime
            ];
        })->values();

        $pdf = Pdf::loadView('admin.travel.report.pdf.team-performance', [
            'reportData' => $teamData,
            'filters' => $request->all(),
            'generatedAt' => now()
        ]);

        return $pdf->download('laporan-kinerja-tim-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export team performance to Excel
     */
    public function exportTeamPerformanceExcel(Request $request)
    {
        return Excel::download(
            new TeamPerformanceExport($request->all()),
            'laporan-kinerja-tim-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
