<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Penjualan;
use App\Models\Inventori;
use App\Models\Production;
use App\Models\Attendance;
use App\Models\Recruitment;

class AdminDashboardController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        return view('admin.dashboard');
    }

    public function getOverviewStats(Request $request)
    {
        $outletIds = $request->input('outlet_ids', []);
        if (empty($outletIds)) {
            $outletIds = [$request->input('outlet_id', session('outlet_id'))];
        }
        $outletIds = array_filter($outletIds); // Remove null values
        
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Total Penjualan Bulan Ini
        $currentSales = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');

        $lastMonthSales = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [$startDate->copy()->subMonth(), $endDate->copy()->subMonth()])
            ->sum('total_harga');

        $salesGrowth = $lastMonthSales > 0 ? (($currentSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;

        // Pesanan Diproses (count transaksi bulan ini)
        $ordersProcessed = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $lastMonthOrders = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [$startDate->copy()->subMonth(), $endDate->copy()->subMonth()])
            ->count();

        $ordersGrowth = $lastMonthOrders > 0 ? (($ordersProcessed - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;

        // Retur & Cancel (piutang yang belum lunas)
        $returns = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('piutang', function($q) {
                $q->where('status', '!=', 'lunas');
            })
            ->count();

        $lastMonthReturns = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [$startDate->copy()->subMonth(), $endDate->copy()->subMonth()])
            ->whereHas('piutang', function($q) {
                $q->where('status', '!=', 'lunas');
            })
            ->count();

        $returnsGrowth = $lastMonthReturns > 0 ? (($returns - $lastMonthReturns) / $lastMonthReturns) * 100 : 0;

        return response()->json([
            'sales' => [
                'value' => $currentSales,
                'growth' => round($salesGrowth, 1)
            ],
            'orders' => [
                'value' => $ordersProcessed,
                'growth' => round($ordersGrowth, 1)
            ],
            'returns' => [
                'value' => $returns,
                'growth' => round($returnsGrowth, 1)
            ]
        ]);
    }

    public function getSalesTrend(Request $request)
    {
        $outletIds = $request->input('outlet_ids', []);
        if (empty($outletIds)) {
            $outletIds = [$request->input('outlet_id', session('outlet_id'))];
        }
        $outletIds = array_filter($outletIds); // Remove null values
        $days = $request->input('days', 7);

        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->whereDate('created_at', $date)
                ->sum('total_harga');

            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'value' => (float) $sales
            ];
        }

        return response()->json($trend);
    }

    public function getInventoryStatus(Request $request)
    {
        $outletIds = $request->input('outlet_ids', []);
        if (empty($outletIds)) {
            $outletIds = [$request->input('outlet_id', session('outlet_id'))];
        }
        $outletIds = array_filter($outletIds); // Remove null values

        $query = Inventori::with('kategori');
        if (!empty($outletIds)) {
            $query->whereIn('id_outlet', $outletIds);
        }

        $inventory = $query->orderBy('stok', 'asc')
            ->limit(10)
            ->get();

        // If no inventory data, return empty but valid structure
        if ($inventory->isEmpty()) {
            return response()->json([
                'items' => [],
                'stats' => [
                    'safe' => 0,
                    'low' => 0,
                    'critical' => 0
                ]
            ]);
        }

        $stats = [
            'safe' => Inventori::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->where('stok', '>=', 50)
                ->count(),
            'low' => Inventori::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->whereBetween('stok', [20, 49])
                ->count(),
            'critical' => Inventori::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->where('stok', '<', 20)
                ->count()
        ];

        return response()->json([
            'items' => $inventory->map(function ($item) {
                $maxStock = 100; // Default max
                $percentage = min(100, round(($item->stok / $maxStock) * 100, 0));
                $status = $item->stok >= 50 ? 'safe' : ($item->stok >= 20 ? 'low' : 'critical');
                
                return [
                    'name' => $item->nama_barang,
                    'stock' => $item->stok,
                    'max' => $maxStock,
                    'percentage' => $percentage,
                    'status' => $status
                ];
            }),
            'stats' => $stats
        ]);
    }

    public function getProductionEfficiency(Request $request)
    {
        $outletIds = $request->input('outlet_ids', []);
        if (empty($outletIds)) {
            $outletIds = [$request->input('outlet_id', session('outlet_id'))];
        }
        $outletIds = array_filter($outletIds); // Remove null values
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $query = Production::whereBetween('start_date', [$startDate, $endDate]);
        if (!empty($outletIds)) {
            $query->whereIn('outlet_id', $outletIds);
        }

        $totalProductions = $query->count();
        $completedProductions = $query->where('status', 'completed')->count();

        $targetAchievement = $totalProductions > 0 ? ($completedProductions / $totalProductions) * 100 : 0;

        // Calculate efficiency based on realized vs target
        $efficiency = Production::whereBetween('start_date', [$startDate, $endDate])
            ->when(!empty($outletIds), fn($q) => $q->whereIn('outlet_id', $outletIds))
            ->where('status', 'completed')
            ->where('target_quantity', '>', 0)
            ->get()
            ->avg(function($prod) {
                return ($prod->realized_quantity / $prod->target_quantity) * 100;
            }) ?? 0;

        return response()->json([
            'target_achievement' => round($targetAchievement, 0),
            'efficiency' => round($efficiency, 0)
        ]);
    }

    public function getEmployeePerformance(Request $request)
    {
        $outletIds = $request->input('outlet_ids', []);
        if (empty($outletIds)) {
            $outletIds = [$request->input('outlet_id', session('outlet_id'))];
        }
        $outletIds = array_filter($outletIds); // Remove null values
        $startDate = Carbon::now()->startOfQuarter();
        $endDate = Carbon::now()->endOfQuarter();

        try {
            // Check if Recruitment table exists and has data
            if (!Recruitment::count()) {
                return response()->json([]);
            }

            $employees = Recruitment::with(['attendances' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }])
                ->when(!empty($outletIds), fn($q) => $q->whereIn('outlet_id', $outletIds))
                ->where('status', 'active')
                ->limit(5)
                ->get()
                ->map(function($emp) {
                    $totalDays = $emp->attendances->count();
                    $presentDays = $emp->attendances->whereIn('status', ['present', 'hadir'])->count();
                    $attendance = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
                    
                    // Performance score based on attendance and punctuality
                    $lateCount = $emp->attendances->where('late_minutes', '>', 0)->count();
                    $performance = $totalDays > 0 ? (($presentDays - ($lateCount * 0.5)) / $totalDays) * 100 : 0;
                    $performance = max(0, min(100, $performance));
                    
                    return [
                        'name' => $emp->nama_lengkap ?? 'Unknown',
                        'performance' => round($performance, 0),
                        'attendance' => round($attendance, 0)
                    ];
                });

            return response()->json($employees->values()->toArray());
        } catch (\Exception $e) {
            \Log::error('Error in getEmployeePerformance: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function getInsights(Request $request)
    {
        $outletIds = $request->input('outlet_ids', []);
        if (empty($outletIds)) {
            $outletIds = [$request->input('outlet_id', session('outlet_id'))];
        }
        $outletIds = array_filter($outletIds); // Remove null values
        $insights = [];

        // Check low stock items
        $lowStock = Inventori::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->where('stok', '<', 20)
            ->count();

        if ($lowStock > 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Stok Menipis',
                'message' => "Ada {$lowStock} produk dengan stok di bawah 20. Segera lakukan pemesanan ulang.",
                'action' => 'Lihat Detail',
                'route' => 'admin.inventaris.inventori.index'
            ];
        }

        // Check pending invoices (piutang belum lunas)
        $pendingInvoices = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereHas('piutang', function($q) {
                $q->where('status', '!=', 'lunas')
                  ->where('created_at', '<', Carbon::now()->subDays(7));
            })
            ->count();

        if ($pendingInvoices > 0) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Piutang Tertunda',
                'message' => "Ada {$pendingInvoices} piutang yang belum dibayar lebih dari 7 hari.",
                'action' => 'Lihat Piutang',
                'route' => 'admin.finance.piutang.index'
            ];
        }

        // Sales prediction
        $currentMonthSales = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->sum('total_harga');

        $daysInMonth = Carbon::now()->daysInMonth;
        $daysPassed = Carbon::now()->day;
        $predictedSales = $daysPassed > 0 ? ($currentMonthSales / $daysPassed) * $daysInMonth : 0;

        $lastMonthSales = Penjualan::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
            ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('total_harga');

        if ($lastMonthSales > 0) {
            if ($predictedSales > $lastMonthSales * 1.1) {
                $growth = round((($predictedSales - $lastMonthSales) / $lastMonthSales) * 100, 1);
                $insights[] = [
                    'type' => 'success',
                    'title' => 'Prediksi Penjualan Positif',
                    'message' => "Berdasarkan tren saat ini, penjualan bulan ini diprediksi naik {$growth}% dari bulan lalu.",
                    'action' => 'Lihat Laporan',
                    'route' => 'admin.penjualan.laporan.index'
                ];
            } elseif ($predictedSales < $lastMonthSales * 0.9) {
                $decline = round((($lastMonthSales - $predictedSales) / $lastMonthSales) * 100, 1);
                $insights[] = [
                    'type' => 'danger',
                    'title' => 'Penjualan Menurun',
                    'message' => "Penjualan bulan ini diprediksi turun {$decline}%. Pertimbangkan strategi promosi.",
                    'action' => 'Analisis Penjualan',
                    'route' => 'admin.penjualan.dashboard.index'
                ];
            }
        }

        return response()->json($insights);
    }
}
