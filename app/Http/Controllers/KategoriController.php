<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Outlet;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KategoriExport;
use App\Imports\KategoriImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasOutletFilter;

class KategoriController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('permission:inventaris.kategori.view')->only(['index', 'data', 'show', 'getGroups', 'getOutlets']);
        $this->middleware('permission:inventaris.kategori.create')->only(['store', 'getNewKode']);
        $this->middleware('permission:inventaris.kategori.edit')->only(['update']);
        $this->middleware('permission:inventaris.kategori.delete')->only(['destroy']);
        $this->middleware('permission:inventaris.kategori.export')->only(['exportPdf', 'exportExcel', 'downloadTemplate']);
        $this->middleware('permission:inventaris.kategori.import')->only(['importExcel']);
    }

    public function index()
    {
        Log::info('Loading Kategori Index Page');
        return view('admin.inventaris.kategori.index');
    }

    public function data(Request $request)
    {
        Log::info('Fetching Kategori Data with filters', $request->all());
        
        $query = Kategori::with('outlet');

        // Apply outlet filter using trait
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter kelompok
        if ($request->has('kelompok_filter') && $request->kelompok_filter !== 'ALL') {
            $query->where('kelompok', $request->kelompok_filter);
        }

        // Filter status
        if ($request->has('status_filter') && $request->status_filter !== 'ALL') {
            $query->where('is_active', $request->status_filter === 'ACTIVE');
        }

        // Filter outlet
        if ($request->has('outlet_filter') && $request->outlet_filter !== 'ALL') {
            $query->where('id_outlet', $request->outlet_filter);
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'nama_kategori';
        $sortDirection = $request->sort_dir ?? 'asc';
        
        $columnMapping = [
            'code' => 'kode_kategori',
            'name' => 'nama_kategori',
            'group' => 'kelompok',
            'outlet' => 'id_outlet'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $kategori = $query->get();

        return datatables()
            ->of($kategori)
            ->addIndexColumn()
            ->addColumn('code', function ($kategori) {
                return $kategori->kode_kategori;
            })
            ->addColumn('name', function ($kategori) {
                return $kategori->nama_kategori;
            })
            ->addColumn('group', function ($kategori) {
                return $kategori->kelompok;
            })
            ->addColumn('outlet', function ($kategori) {
                return $kategori->outlet ? $kategori->outlet->nama_outlet : '-';
            })
            ->addColumn('desc', function ($kategori) {
                return $kategori->deskripsi;
            })
            ->addColumn('is_active', function ($kategori) {
                return $kategori->is_active ? 'ACTIVE' : 'INACTIVE';
            })
            ->addColumn('aksi', function ($kategori) {
                return '
                    <div class="flex justify-end gap-2">
                        <button onclick="editForm(`'. route('admin.inventaris.kategori.update', $kategori->id_kategori) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button onclick="deleteData(`'. route('admin.inventaris.kategori.destroy', $kategori->id_kategori) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',
            'kelompok' => 'required|in:Produk,Bahan,Aset,Lainnya',
            'id_outlet' => 'nullable|exists:outlets,id_outlet',
            'deskripsi' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userOutlets = auth()->user()->akses_outlet ?? [];
        $idOutlet = $request->id_outlet;

        // Validasi akses outlet
        if ($idOutlet && $userOutlets && !in_array($idOutlet, $userOutlets)) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke outlet ini'], 403);
        }

        // Jika tidak ada outlet yang dipilih, gunakan outlet pertama user
        if (!$idOutlet && $userOutlets) {
            $idOutlet = $userOutlets[0];
        }

        $kategori = Kategori::create([
            'kode_kategori' => Kategori::generateKodeKategori(),
            'nama_kategori' => $request->nama_kategori,
            'kelompok' => $request->kelompok,
            'id_outlet' => $idOutlet,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan', 
            'data' => $kategori
        ], 200);
    }

    public function show($id)
    {
        $kategori = Kategori::with('outlet')->find($id);
        
        if (!$kategori) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }

        // Validasi akses outlet
        $userOutlets = auth()->user()->akses_outlet ?? [];
        if ($userOutlets && !in_array($kategori->id_outlet, $userOutlets)) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke data ini'], 403);
        }

        return response()->json([
            'id' => $kategori->id_kategori,
            'code' => $kategori->kode_kategori,
            'name' => $kategori->nama_kategori,
            'group' => $kategori->kelompok,
            'outlet' => $kategori->outlet ? $kategori->outlet->nama_outlet : null,
            'outlet_id' => $kategori->id_outlet,
            'desc' => $kategori->deskripsi,
            'is_active' => $kategori->is_active
        ]);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);
        
        if (!$kategori) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }

        // Validasi akses outlet
        $userOutlets = auth()->user()->akses_outlet ?? [];
        if ($userOutlets && !in_array($kategori->id_outlet, $userOutlets)) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke data ini'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',
            'kelompok' => 'required|in:Produk,Bahan,Aset,Lainnya',
            'id_outlet' => 'nullable|exists:outlets,id_outlet',
            'deskripsi' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idOutlet = $request->id_outlet;

        // Validasi akses outlet baru
        if ($idOutlet && $userOutlets && !in_array($idOutlet, $userOutlets)) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke outlet ini'], 403);
        }

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'kelompok' => $request->kelompok,
            'id_outlet' => $idOutlet,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $kategori], 200);
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        
        if (!$kategori) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }

        // Validasi akses outlet
        $userOutlets = auth()->user()->akses_outlet ?? [];
        if ($userOutlets && !in_array($kategori->id_outlet, $userOutlets)) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke data ini'], 403);
        }

        $kategori->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $query = Kategori::with('outlet');

        // Apply outlet filter using trait
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Apply filters
        if ($request->has('kelompok') && $request->kelompok !== 'ALL') {
            $query->where('kelompok', $request->kelompok);
        }

        if ($request->has('status') && $request->status !== 'ALL') {
            $query->where('is_active', $request->status === 'ACTIVE');
        }

        $kategori = $query->get();

        $pdf = PDF::loadView('admin.inventaris.kategori.kategori-pdf', [
            'kategori' => $kategori,
            'filterKelompok' => $request->kelompok,
            'filterStatus' => $request->status
        ]);

        return $pdf->download('kategori-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new KategoriExport($request), 'kategori-' . date('Y-m-d') . '.xlsx');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new KategoriImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    // Download Template Excel
    public function downloadTemplate()
    {
        return Excel::download(new KategoriExport(true), 'template-import-kategori.xlsx');
    }

    // Get unique groups for filter
    public function getGroups()
    {
        // Gunakan enum values dari model instead of query database
        $groups = Kategori::getKelompokOptions();

        return response()->json($groups);
    }

    // Get kode kategori baru
    public function getNewKode()
    {
        $kodeKategori = Kategori::generateKodeKategori();
        return response()->json(['kode_kategori' => $kodeKategori]);
    }

    // Get outlets for dropdown
    public function getOutlets()
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            $outlets = Outlet::select('id_outlet', 'nama_outlet')
                ->where('is_active', true)
                ->get();
        } else {
            // Get user's accessible outlets
            $outlets = $user->outlets()
                ->select('outlets.id_outlet', 'outlets.nama_outlet')
                ->where('outlets.is_active', true)
                ->get();
        }

        return response()->json($outlets);
    }
}