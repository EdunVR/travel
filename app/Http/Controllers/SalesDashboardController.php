<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Models\Penjualan;
use App\Models\PosSale;
use App\Models\Piutang;
use App\Models\Outlet;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{
    use HasOutletFilter;

    public function index()
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get();
        
        return view('admin.penjualan.index', compact('outlets'));
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

            // Get Invoice data (exclude POS-generated penjualan)
            $posGeneratedPenjualanIds = PosSale::pluck('id_penjualan')->filter()->toArray();
            
            $invoices = Penjualan::with(['outlet', 'member', 'details'])
                ->whereIn('id_outlet', $filterOutletIds)
                ->whereNotIn('id_penjualan', $posGeneratedPenjualanIds)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get POS data
            $posSales = PosSale::with(['outlet', 'member', 'items'])
                ->whereIn('id_outlet', $filterOutletIds)
                ->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate)
                ->orderBy('tanggal', 'desc')
                ->get();

            // Combine data
            $salesData = [];

            // Process Invoices
            foreach ($invoices as $invoice) {
                // Check if there's a SalesInvoice record (new system)
                $salesInvoice = \App\Models\SalesInvoice::where('id_penjualan', $invoice->id_penjualan)->first();
                
                // Determine payment status and amount paid
                $paymentStatus = 'Lunas';
                $totalBayar = $invoice->bayar;
                $sisaPiutang = 0;
                
                if ($salesInvoice) {
                    // Use SalesInvoice data (more accurate)
                    $totalBayar = $salesInvoice->total_dibayar;
                    $sisaPiutang = $salesInvoice->sisa_tagihan;
                    
                    if ($salesInvoice->sisa_tagihan > 0) {
                        if ($salesInvoice->total_dibayar > 0) {
                            $paymentStatus = 'Dibayar Sebagian';
                        } else {
                            $paymentStatus = 'Belum Lunas';
                        }
                    } else {
                        $paymentStatus = 'Lunas';
                    }
                } else {
                    // Fallback to Piutang data (old system)
                    $piutang = Piutang::where('id_penjualan', $invoice->id_penjualan)->first();
                    
                    if ($piutang) {
                        $totalBayar = $piutang->jumlah_dibayar;
                        $sisaPiutang = $piutang->sisa_piutang;
                        
                        if ($piutang->sisa_piutang > 0) {
                            if ($piutang->jumlah_dibayar > 0) {
                                $paymentStatus = 'Dibayar Sebagian';
                            } else {
                                $paymentStatus = 'Belum Lunas';
                            }
                        }
                    }
                }

                $salesData[] = [
                    'id' => 'inv_' . $invoice->id_penjualan,
                    'source' => 'invoice',
                    'source_id' => $invoice->id_penjualan,
                    'no_transaksi' => 'INV-' . str_pad($invoice->id_penjualan, 6, '0', STR_PAD_LEFT),
                    'tanggal' => $invoice->created_at,
                    'outlet' => $invoice->outlet->nama_outlet ?? '-',
                    'customer' => $invoice->member->nama ?? 'Pelanggan Umum',
                    'total_item' => $invoice->total_item ?? 0,
                    'total' => floatval($invoice->total_harga ?? 0), // Use total_harga instead of bayar
                    'dibayar' => floatval($totalBayar),
                    'sisa' => floatval($sisaPiutang),
                    'status' => $paymentStatus,
                ];
            }

            // Process POS
            foreach ($posSales as $pos) {
                // Determine payment status and amount paid for POS
                $paymentStatus = 'Lunas';
                $totalBayar = $pos->jumlah_bayar;
                $sisaPiutang = 0;
                
                if ($pos->is_bon && $pos->id_penjualan) {
                    $piutang = Piutang::where('id_penjualan', $pos->id_penjualan)->first();
                    if ($piutang) {
                        $totalBayar = $piutang->jumlah_dibayar;
                        $sisaPiutang = $piutang->sisa_piutang;
                        
                        // Check payment status
                        if ($piutang->sisa_piutang > 0) {
                            if ($piutang->jumlah_dibayar > 0) {
                                $paymentStatus = 'Dibayar Sebagian';
                            } else {
                                $paymentStatus = 'Belum Lunas';
                            }
                        } else {
                            $paymentStatus = 'Lunas';
                        }
                    }
                } else {
                    // Non-BON: Check if fully paid
                    if ($totalBayar >= $pos->total) {
                        $paymentStatus = 'Lunas';
                    } else if ($totalBayar > 0) {
                        $paymentStatus = 'Dibayar Sebagian';
                    } else {
                        $paymentStatus = 'Belum Lunas';
                    }
                }

                $salesData[] = [
                    'id' => 'pos_' . $pos->id,
                    'source' => 'pos',
                    'source_id' => $pos->id,
                    'no_transaksi' => $pos->no_transaksi,
                    'tanggal' => $pos->tanggal,
                    'outlet' => $pos->outlet->nama_outlet ?? '-',
                    'customer' => $pos->member->nama ?? 'Pelanggan Umum',
                    'total_item' => $pos->items->sum('kuantitas'), // Use eager loaded items
                    'total' => floatval($pos->total ?? 0),
                    'dibayar' => floatval($totalBayar),
                    'sisa' => floatval($sisaPiutang),
                    'status' => $paymentStatus,
                ];
            }

            // Sort by date desc
            usort($salesData, fn($a, $b) => strtotime($b['tanggal']) - strtotime($a['tanggal']));

            // Calculate KPIs
            $kpi = $this->calculateKPI($salesData, $startDate, $endDate, $filterOutletIds);

            // Get outlet summary
            $outletSummary = $this->getOutletSummary($salesData);

            // Get status count
            $statusCount = $this->getStatusCount($salesData);

            // Get daily trend (last 30 days)
            $dailyTrend = $this->getDailyTrend($startDate, $endDate, $filterOutletIds);

            return response()->json([
                'success' => true,
                'data' => [
                    'sales' => $salesData,
                    'kpi' => $kpi,
                    'outlet_summary' => $outletSummary,
                    'status_count' => $statusCount,
                    'daily_trend' => $dailyTrend,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading sales dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateKPI($salesData, $startDate, $endDate, $filterOutletIds)
    {
        $totalTransaksi = count($salesData);
        $totalItem = array_sum(array_column($salesData, 'total_item'));
        $totalPenjualan = array_sum(array_column($salesData, 'total'));
        $totalPiutang = array_sum(array_column($salesData, 'sisa'));
        $totalDibayar = array_sum(array_column($salesData, 'dibayar'));

        // Calculate average
        $avgTransaksi = $totalTransaksi > 0 ? $totalPenjualan / $totalTransaksi : 0;

        // Calculate growth (compare with previous period)
        $periodDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $prevStartDate = Carbon::parse($startDate)->subDays($periodDays)->format('Y-m-d');
        $prevEndDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');

        $prevInvoiceTotal = Penjualan::whereIn('id_outlet', $filterOutletIds)
            ->whereDate('created_at', '>=', $prevStartDate)
            ->whereDate('created_at', '<=', $prevEndDate)
            ->sum('bayar');

        $prevPosTotal = PosSale::whereIn('id_outlet', $filterOutletIds)
            ->whereDate('tanggal', '>=', $prevStartDate)
            ->whereDate('tanggal', '<=', $prevEndDate)
            ->sum('total');

        $prevTotal = floatval($prevInvoiceTotal) + floatval($prevPosTotal);
        $growth = $prevTotal > 0 ? (($totalPenjualan - $prevTotal) / $prevTotal) * 100 : 0;

        return [
            'total_transaksi' => $totalTransaksi,
            'total_item' => $totalItem,
            'total_penjualan' => $totalPenjualan,
            'total_piutang' => $totalPiutang,
            'total_dibayar' => $totalDibayar,
            'avg_transaksi' => $avgTransaksi,
            'growth_percent' => round($growth, 2),
        ];
    }

    private function getOutletSummary($salesData)
    {
        $summary = [];
        
        foreach ($salesData as $sale) {
            $outlet = $sale['outlet'];
            if (!isset($summary[$outlet])) {
                $summary[$outlet] = [
                    'name' => $outlet,
                    'total' => 0,
                    'count' => 0,
                ];
            }
            $summary[$outlet]['total'] += $sale['total'];
            $summary[$outlet]['count']++;
        }

        return array_values($summary);
    }

    private function getStatusCount($salesData)
    {
        $count = [
            'lunas' => 0,
            'dibayar_sebagian' => 0,
            'belum_lunas' => 0,
        ];

        foreach ($salesData as $sale) {
            $status = strtolower($sale['status']);
            if ($status === 'lunas') {
                $count['lunas']++;
            } elseif (str_contains($status, 'sebagian')) {
                $count['dibayar_sebagian']++;
            } else {
                $count['belum_lunas']++;
            }
        }

        return $count;
    }

    private function getDailyTrend($startDate, $endDate, $filterOutletIds)
    {
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $days = min($days, 30); // Max 30 days for trend

        $trend = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::parse($endDate)->subDays($days - 1 - $i)->format('Y-m-d');
            
            $invoiceTotal = Penjualan::whereIn('id_outlet', $filterOutletIds)
                ->whereDate('created_at', $date)
                ->sum('bayar');

            $posTotal = PosSale::whereIn('id_outlet', $filterOutletIds)
                ->whereDate('tanggal', $date)
                ->sum('total');

            $trend[] = [
                'date' => $date,
                'total' => floatval($invoiceTotal) + floatval($posTotal),
            ];
        }

        return $trend;
    }
}
