<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use DB;

class InvestorProfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:investor.profil.view')->only(['index', 'show']);
        $this->middleware('permission:investor.profil.create')->only(['create', 'store']);
        $this->middleware('permission:investor.profil.edit')->only(['edit', 'update']);
        $this->middleware('permission:investor.profil.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $query = Investor::with(['outlet']);

            // Apply filters
            if ($request->filled('outlet_filter') && $request->outlet_filter !== 'ALL') {
                $query->where('outlet_id', $request->outlet_filter);
            }

            if ($request->filled('status_filter') && $request->status_filter !== 'ALL') {
                $query->where('status', $request->status_filter);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%")
                      ->orWhereHas('outlet', function ($oq) use ($search) {
                          $oq->where('nama_outlet', 'like', "%{$search}%");
                      });
                });
            }

            // Apply sorting
            $sortKey = $request->get('sort_key', 'name');
            $sortDir = $request->get('sort_dir', 'asc');
            
            switch ($sortKey) {
                case 'outlet':
                    $query->join('outlets', 'investors.outlet_id', '=', 'outlets.id')
                          ->orderBy('outlets.nama_outlet', $sortDir)
                          ->select('investors.*');
                    break;
                case 'total_investment':
                    $query->orderBy('total_investment', $sortDir);
                    break;
                case 'join_date':
                    $query->orderBy('join_date', $sortDir);
                    break;
                default:
                    $query->orderBy($sortKey, $sortDir);
                    break;
            }

            $investors = $query->get()->map(function ($investor) {
                return [
                    'id' => $investor->id,
                    'outlet_id' => $investor->outlet_id,
                    'outlet_name' => $investor->outlet ? $investor->outlet->nama_outlet : '-',
                    'name' => $investor->name,
                    'email' => $investor->email,
                    'phone' => $investor->phone,
                    'jenis_kelamin' => $investor->jenis_kelamin,
                    'category' => $investor->category,
                    'status' => $investor->status,
                    'join_date' => $investor->join_date,
                    'initial_investment' => $investor->initial_investment ?? 0,
                    'total_investment' => $investor->total_investment ?? $investor->initial_investment ?? 0,
                    'address' => $investor->address,
                    'notes' => $investor->notes,
                    'tempo' => $investor->tempo,
                    'bank' => $investor->bank,
                    'rekening' => $investor->rekening,
                    'atas_nama' => $investor->atas_nama,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $investors
            ]);
        }

        $outlets = Outlet::orderBy('nama_outlet')->get();
        return view('admin.investor.profil.index', compact('outlets'));
    }

    public function create()
    {
        $outlets = Outlet::orderBy('nama_outlet')->get();
        return view('admin.investor.profil.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email',
            'phone' => 'required|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'category' => 'required|in:syirkah,investama,sukuk,internal,eksternal,aktif,pasif',
            'status' => 'required|in:active,inactive',
            'join_date' => 'required|date',
            'initial_investment' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tempo' => 'nullable|date',
            'bank' => 'nullable|string|max:255',
            'rekening' => 'nullable|string',
            'atas_nama' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('investor-photos', 'public');
        }

        $validated['total_investment'] = $validated['initial_investment'];

        DB::beginTransaction();
        try {
            $investor = Investor::create($validated);

            // Create initial account if investment > 0
            if ($request->initial_investment > 0) {
                $investor->accounts()->create([
                    'account_number' => 'INV-' . $investor->outlet_id . '-' . str_pad($investor->id, 4, '0', STR_PAD_LEFT),
                    'bank_name' => 'Investasi Awal',
                    'account_name' => $investor->name,
                    'initial_balance' => $request->initial_investment,
                    'current_balance' => $request->initial_investment,
                    'profit_percentage' => 50, // default 50%
                    'date' => $request->join_date,
                    'tempo' => $request->tempo,
                    'status' => 'active'
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Investor berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan investor: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $investor = Investor::with(['outlet', 'accounts.investments', 'documents'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $investor]);
    }

    public function edit($id)
    {
        $investor = Investor::findOrFail($id);
        $outlets = Outlet::orderBy('nama_outlet')->get();
        return response()->json(['success' => true, 'data' => $investor, 'outlets' => $outlets]);
    }

    public function update(Request $request, $id)
    {
        $investor = Investor::findOrFail($id);

        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email,' . $investor->id,
            'phone' => 'required|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'category' => 'required|in:syirkah,investama,sukuk,internal,eksternal,aktif,pasif',
            'status' => 'required|in:active,inactive',
            'join_date' => 'required|date',
            'initial_investment' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tempo' => 'nullable|date',
            'bank' => 'nullable|string|max:255',
            'rekening' => 'nullable|string',
            'atas_nama' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($investor->photo) {
                Storage::disk('public')->delete($investor->photo);
            }
            $validated['photo'] = $request->file('photo')->store('investor-photos', 'public');
        }

        $investor->update($validated);

        return response()->json(['success' => true, 'message' => 'Data investor berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $investor = Investor::findOrFail($id);

        // Delete photo if exists
        if ($investor->photo) {
            Storage::disk('public')->delete($investor->photo);
        }

        $investor->delete();

        return response()->json(['success' => true, 'message' => 'Investor berhasil dihapus']);
    }

    public function export(Request $request)
    {
        $query = Investor::with(['outlet'])
            ->when($request->outlet_id, function ($q) use ($request) {
                return $q->where('outlet_id', $request->outlet_id);
            });

        $investors = $query->get();

        $filename = 'investor_profil_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($investors) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'ID',
                'Outlet',
                'Nama',
                'Email',
                'Telepon',
                'Jenis Kelamin',
                'Kategori',
                'Status',
                'Tanggal Bergabung',
                'Total Investasi',
                'Bank',
                'Rekening',
                'Atas Nama',
                'Alamat',
                'Catatan'
            ]);

            // Data
            foreach ($investors as $investor) {
                fputcsv($file, [
                    $investor->id,
                    $investor->outlet ? $investor->outlet->nama_outlet : '',
                    $investor->name,
                    $investor->email,
                    $investor->phone,
                    $investor->jenis_kelamin,
                    $investor->category,
                    $investor->status,
                    $investor->join_date ? $investor->join_date->format('Y-m-d') : '',
                    $investor->total_investment,
                    $investor->bank,
                    $investor->rekening,
                    $investor->atas_nama,
                    $investor->address,
                    $investor->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}