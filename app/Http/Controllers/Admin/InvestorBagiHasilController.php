<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestorProfitShare;
use App\Models\Investor;
use App\Models\Outlet;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Carbon\Carbon;

class InvestorBagiHasilController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:investor.bagi-hasil.view')->only(['index', 'show']);
        $this->middleware('permission:investor.bagi-hasil.create')->only(['create', 'store']);
        $this->middleware('permission:investor.bagi-hasil.edit')->only(['edit', 'update']);
        $this->middleware('permission:investor.bagi-hasil.delete')->only(['destroy']);
        $this->middleware('permission:investor.bagi-hasil.approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $query = InvestorProfitShare::with(['investor', 'outlet', 'approvedBy', 'paidBy']);

            // Apply filters
            if ($request->filled('outlet_filter') && $request->outlet_filter !== 'ALL') {
                $query->where('outlet_id', $request->outlet_filter);
            }

            if ($request->filled('investor_filter') && $request->investor_filter !== 'ALL') {
                $query->where('investor_id', $request->investor_filter);
            }

            if ($request->filled('status_filter') && $request->status_filter !== 'ALL') {
                $query->where('status', $request->status_filter);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('investor', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    })->orWhere('period_month', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortKey = $request->get('sort_key', 'period_month');
            $sortDir = $request->get('sort_dir', 'desc');
            
            switch ($sortKey) {
                case 'investor_name':
                    $query->join('investors', 'investor_profit_shares.investor_id', '=', 'investors.id')
                          ->orderBy('investors.name', $sortDir)
                          ->select('investor_profit_shares.*');
                    break;
                case 'outlet_name':
                    $query->join('outlets', 'investor_profit_shares.outlet_id', '=', 'outlets.id')
                          ->orderBy('outlets.nama_outlet', $sortDir)
                          ->select('investor_profit_shares.*');
                    break;
                default:
                    $query->orderBy($sortKey, $sortDir);
                    break;
            }

            $bagiHasil = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'outlet_id' => $item->outlet_id,
                    'outlet_name' => $item->outlet ? $item->outlet->nama_outlet : '-',
                    'investor_id' => $item->investor_id,
                    'investor_name' => $item->investor ? $item->investor->name : '-',
                    'period_month' => $item->period_month,
                    'period_formatted' => $item->formatted_period,
                    'total_revenue' => $item->total_revenue ?? 0,
                    'total_expense' => $item->total_expense ?? 0,
                    'net_profit' => $item->net_profit ?? 0,
                    'investor_share_percentage' => $item->investor_share_percentage ?? 0,
                    'investor_share_amount' => $item->investor_share_amount ?? 0,
                    'management_share_amount' => $item->management_share_amount ?? 0,
                    'status' => $item->status ?? 'draft',
                    'notes' => $item->notes ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $bagiHasil
            ]);
        }

        $outlets = Outlet::orderBy('nama_outlet')->get();
        $investors = Investor::orderBy('name')->get();
        
        // Generate period options (last 12 months)
        $periods = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $periods[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y')
            ];
        }

        return view('admin.investor.bagi-hasil.index', compact('outlets', 'investors', 'periods'));
    }

    public function create()
    {
        $outlets = Outlet::orderBy('nama_outlet')->get();
        $investors = Investor::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.investor.bagi-hasil.create', compact('outlets', 'investors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'outlet_id' => 'required|exists:outlets,id',
            'period_month' => 'required|date_format:Y-m',
            'total_revenue' => 'required|numeric|min:0',
            'total_expense' => 'required|numeric|min:0',
            'investor_share_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // Check if record already exists
        $exists = InvestorProfitShare::where('investor_id', $validated['investor_id'])
            ->where('outlet_id', $validated['outlet_id'])
            ->where('period_month', $validated['period_month'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false, 
                'message' => 'Bagi hasil untuk investor dan periode ini sudah ada'
            ], 422);
        }

        // Calculate profit sharing
        $netProfit = $validated['total_revenue'] - $validated['total_expense'];
        $investorShareAmount = ($netProfit * $validated['investor_share_percentage']) / 100;
        $managementShareAmount = $netProfit - $investorShareAmount;

        $validated['net_profit'] = $netProfit;
        $validated['investor_share_amount'] = $investorShareAmount;
        $validated['management_share_amount'] = $managementShareAmount;

        InvestorProfitShare::create($validated);

        return response()->json(['success' => true, 'message' => 'Bagi hasil berhasil ditambahkan']);
    }

    public function show($id)
    {
        $profitShare = InvestorProfitShare::with(['investor', 'outlet', 'approvedBy', 'paidBy'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $profitShare]);
    }

    public function edit($id)
    {
        $profitShare = InvestorProfitShare::findOrFail($id);
        
        if ($profitShare->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya bagi hasil dengan status draft yang dapat diedit'], 422);
        }

        $outlets = Outlet::orderBy('nama_outlet')->get();
        $investors = Investor::where('status', 'active')->orderBy('name')->get();
        
        return response()->json(['success' => true, 'data' => $profitShare, 'outlets' => $outlets, 'investors' => $investors]);
    }

    public function update(Request $request, $id)
    {
        $profitShare = InvestorProfitShare::findOrFail($id);
        
        if ($profitShare->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya bagi hasil dengan status draft yang dapat diedit'], 422);
        }

        $validated = $request->validate([
            'total_revenue' => 'required|numeric|min:0',
            'total_expense' => 'required|numeric|min:0',
            'investor_share_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // Recalculate profit sharing
        $netProfit = $validated['total_revenue'] - $validated['total_expense'];
        $investorShareAmount = ($netProfit * $validated['investor_share_percentage']) / 100;
        $managementShareAmount = $netProfit - $investorShareAmount;

        $validated['net_profit'] = $netProfit;
        $validated['investor_share_amount'] = $investorShareAmount;
        $validated['management_share_amount'] = $managementShareAmount;

        $profitShare->update($validated);

        return response()->json(['success' => true, 'message' => 'Bagi hasil berhasil diperbarui']);
    }

    public function approve($id)
    {
        $profitShare = InvestorProfitShare::findOrFail($id);
        
        if ($profitShare->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya bagi hasil dengan status draft yang dapat disetujui'], 422);
        }

        $profitShare->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Bagi hasil berhasil disetujui']);
    }

    public function markAsPaid($id)
    {
        $profitShare = InvestorProfitShare::findOrFail($id);
        
        if ($profitShare->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya bagi hasil yang sudah disetujui yang dapat ditandai sebagai dibayar'], 422);
        }

        $profitShare->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Bagi hasil berhasil ditandai sebagai dibayar']);
    }

    public function destroy($id)
    {
        $profitShare = InvestorProfitShare::findOrFail($id);
        
        if ($profitShare->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya bagi hasil dengan status draft yang dapat dihapus'], 422);
        }

        $profitShare->delete();

        return response()->json(['success' => true, 'message' => 'Bagi hasil berhasil dihapus']);
    }

    public function export(Request $request)
    {
        $query = InvestorProfitShare::with(['investor', 'outlet'])
            ->when($request->outlet_id, function ($q) use ($request) {
                return $q->where('outlet_id', $request->outlet_id);
            })
            ->when($request->investor_id, function ($q) use ($request) {
                return $q->where('investor_id', $request->investor_id);
            })
            ->when($request->period_month, function ($q) use ($request) {
                return $q->where('period_month', $request->period_month);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->orderBy('period_month', 'desc');

        $profitShares = $query->get();

        $filename = 'investor_bagi_hasil_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($profitShares) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'ID',
                'Outlet',
                'Investor',
                'Periode',
                'Total Pendapatan',
                'Total Pengeluaran',
                'Laba Bersih',
                'Persentase Investor',
                'Bagian Investor',
                'Bagian Pengelola',
                'Status',
                'Tanggal Disetujui',
                'Tanggal Dibayar'
            ]);

            // Data
            foreach ($profitShares as $share) {
                fputcsv($file, [
                    $share->id,
                    $share->outlet ? $share->outlet->nama_outlet : '',
                    $share->investor ? $share->investor->name : '',
                    $share->formatted_period,
                    $share->total_revenue,
                    $share->total_expense,
                    $share->net_profit,
                    $share->investor_share_percentage . '%',
                    $share->investor_share_amount,
                    $share->management_share_amount,
                    ucfirst($share->status),
                    $share->approved_at ? $share->approved_at->format('Y-m-d H:i:s') : '',
                    $share->paid_at ? $share->paid_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}