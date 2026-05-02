<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestorWithdrawal;
use App\Models\Investor;
use App\Models\Outlet;
use Illuminate\Http\Request;
use DataTables;
use DB;

class InvestorPencairanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:investor.pencairan.view')->only(['index', 'show']);
        $this->middleware('permission:investor.pencairan.create')->only(['create', 'store']);
        $this->middleware('permission:investor.pencairan.edit')->only(['edit', 'update']);
        $this->middleware('permission:investor.pencairan.delete')->only(['destroy']);
        $this->middleware('permission:investor.pencairan.approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $query = InvestorWithdrawal::with(['investor', 'outlet', 'approvedBy', 'paidBy']);

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

            if ($request->filled('type_filter') && $request->type_filter !== 'ALL') {
                $query->where('type', $request->type_filter);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('investor', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    })->orWhere('withdrawal_number', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortKey = $request->get('sort_key', 'request_date');
            $sortDir = $request->get('sort_dir', 'desc');
            
            switch ($sortKey) {
                case 'investor_name':
                    $query->join('investors', 'investor_withdrawals.investor_id', '=', 'investors.id')
                          ->orderBy('investors.name', $sortDir)
                          ->select('investor_withdrawals.*');
                    break;
                case 'outlet_name':
                    $query->join('outlets', 'investor_withdrawals.outlet_id', '=', 'outlets.id')
                          ->orderBy('outlets.nama_outlet', $sortDir)
                          ->select('investor_withdrawals.*');
                    break;
                default:
                    $query->orderBy($sortKey, $sortDir);
                    break;
            }

            $pencairan = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'outlet_id' => $item->outlet_id,
                    'outlet_name' => $item->outlet ? $item->outlet->nama_outlet : '-',
                    'investor_id' => $item->investor_id,
                    'investor_name' => $item->investor ? $item->investor->name : '-',
                    'withdrawal_number' => $item->withdrawal_number ?? '-',
                    'request_date' => $item->request_date,
                    'amount' => $item->amount ?? 0,
                    'type' => $item->type ?? '',
                    'type_label' => $item->type_label ?? $this->getTypeLabel($item->type),
                    'status' => $item->status ?? 'pending',
                    'reason' => $item->reason ?? '',
                    'notes' => $item->notes ?? '',
                    'payment_method' => $item->payment_method ?? '',
                    'rejection_reason' => $item->rejection_reason ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $pencairan
            ]);
        }

        $outlets = Outlet::orderBy('nama_outlet')->get();
        $investors = Investor::orderBy('name')->get();

        return view('admin.investor.pencairan.index', compact('outlets', 'investors'));
    }

    private function getTypeLabel($type)
    {
        switch ($type) {
            case 'profit_share': return 'Bagi Hasil';
            case 'investment': return 'Investasi';
            case 'both': return 'Keduanya';
            default: return '-';
        }
    }

    public function create()
    {
        $outlets = Outlet::orderBy('nama_outlet')->get();
        $investors = Investor::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.investor.pencairan.create', compact('outlets', 'investors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'outlet_id' => 'required|exists:outlets,id',
            'request_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:profit_share,investment,both',
            'reason' => 'nullable|string',
        ]);

        InvestorWithdrawal::create($validated);

        return response()->json(['success' => true, 'message' => 'Permintaan pencairan berhasil ditambahkan']);
    }

    public function show($id)
    {
        $withdrawal = InvestorWithdrawal::with(['investor', 'outlet', 'approvedBy', 'paidBy'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $withdrawal]);
    }

    public function edit($id)
    {
        $withdrawal = InvestorWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya pencairan dengan status pending yang dapat diedit'], 422);
        }

        $outlets = Outlet::orderBy('nama_outlet')->get();
        $investors = Investor::where('status', 'active')->orderBy('name')->get();
        
        return response()->json(['success' => true, 'data' => $withdrawal, 'outlets' => $outlets, 'investors' => $investors]);
    }

    public function update(Request $request, $id)
    {
        $withdrawal = InvestorWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya pencairan dengan status pending yang dapat diedit'], 422);
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:profit_share,investment,both',
            'reason' => 'nullable|string',
        ]);

        $withdrawal->update($validated);

        return response()->json(['success' => true, 'message' => 'Permintaan pencairan berhasil diperbarui']);
    }

    public function approve(Request $request, $id)
    {
        $withdrawal = InvestorWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya pencairan dengan status pending yang dapat disetujui'], 422);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $withdrawal->update([
            'status' => 'approved',
            'notes' => $validated['notes'] ?? null,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Permintaan pencairan berhasil disetujui']);
    }

    public function reject(Request $request, $id)
    {
        $withdrawal = InvestorWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya pencairan dengan status pending yang dapat ditolak'], 422);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Permintaan pencairan berhasil ditolak']);
    }

    public function markAsPaid(Request $request, $id)
    {
        $withdrawal = InvestorWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya pencairan yang sudah disetujui yang dapat ditandai sebagai dibayar'], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        $withdrawal->update([
            'status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'paid_at' => now(),
            'paid_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Pencairan berhasil ditandai sebagai dibayar']);
    }

    public function destroy($id)
    {
        $withdrawal = InvestorWithdrawal::findOrFail($id);
        
        if (!in_array($withdrawal->status, ['pending', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Hanya pencairan dengan status pending atau rejected yang dapat dihapus'], 422);
        }

        $withdrawal->delete();

        return response()->json(['success' => true, 'message' => 'Permintaan pencairan berhasil dihapus']);
    }

    public function export(Request $request)
    {
        $query = InvestorWithdrawal::with(['investor', 'outlet'])
            ->when($request->outlet_id, function ($q) use ($request) {
                return $q->where('outlet_id', $request->outlet_id);
            })
            ->when($request->investor_id, function ($q) use ($request) {
                return $q->where('investor_id', $request->investor_id);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->orderBy('request_date', 'desc');

        $withdrawals = $query->get();

        $filename = 'investor_pencairan_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($withdrawals) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'No Pencairan',
                'Outlet',
                'Investor',
                'Tanggal Permintaan',
                'Jumlah',
                'Tipe',
                'Alasan',
                'Status',
                'Catatan',
                'Alasan Penolakan',
                'Metode Pembayaran',
                'Referensi Pembayaran',
                'Tanggal Disetujui',
                'Tanggal Dibayar'
            ]);

            // Data
            foreach ($withdrawals as $withdrawal) {
                fputcsv($file, [
                    $withdrawal->withdrawal_number,
                    $withdrawal->outlet ? $withdrawal->outlet->nama_outlet : '',
                    $withdrawal->investor ? $withdrawal->investor->name : '',
                    $withdrawal->request_date->format('Y-m-d'),
                    $withdrawal->amount,
                    $withdrawal->type_label,
                    $withdrawal->reason,
                    ucfirst($withdrawal->status),
                    $withdrawal->notes,
                    $withdrawal->rejection_reason,
                    $withdrawal->payment_method,
                    $withdrawal->payment_reference,
                    $withdrawal->approved_at ? $withdrawal->approved_at->format('Y-m-d H:i:s') : '',
                    $withdrawal->paid_at ? $withdrawal->paid_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}