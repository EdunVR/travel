<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Inventori;
use App\Models\Kategori;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoriExport;
use App\Imports\InventoriImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class InventoriController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        Log::info('Loading Inventori Index Page');
        return view('admin.inventaris.inventori.index');
    }

    public function data(Request $request)
    {
        Log::info('Fetching Inventori Data with filters', $request->all());

        $query = Inventori::with(['outlet', 'kategori']);

        // Filter by accessible outlets
        if (!$this->isSuperAdmin()) {
            $accessibleOutletIds = $this->getAccessibleOutletIds();
            $query->whereIn('id_outlet', $accessibleOutletIds);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter outlet
        if ($request->has('outlet_filter') && $request->outlet_filter !== 'ALL') {
            // Validate outlet access
            if (!$this->isSuperAdmin()) {
                $this->validateOutletAccess($request->outlet_filter);
            }
            $query->where('id_outlet', $request->outlet_filter);
        }

        // Filter kategori
        if ($request->has('category_filter') && $request->category_filter !== 'ALL') {
            $query->where('id_kategori', $request->category_filter);
        }

        // Filter status
        if ($request->has('status_filter') && $request->status_filter !== 'ALL') {
            $query->where('status', $request->status_filter);
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'nama_barang';
        $sortDirection = $request->sort_dir ?? 'asc';
        
        $columnMapping = [
            'name' => 'nama_barang',
            'category' => 'id_kategori',
            'outlet' => 'id_outlet',
            'pic' => 'penanggung_jawab',
            'stock' => 'stok'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $inventori = $query->get();

        return datatables()
            ->of($inventori)
            ->addIndexColumn()
            ->addColumn('code', function ($inventori) {
                return $inventori->kode_inventori;
            })
            ->addColumn('name', function ($inventori) {
                return $inventori->nama_barang;
            })
            ->addColumn('category', function ($inventori) {
                return $inventori->kategori ? $inventori->kategori->nama_kategori : '-';
            })
            ->addColumn('outlet', function ($inventori) {
                return $inventori->outlet ? $inventori->outlet->nama_outlet : '-';
            })
            ->addColumn('pic', function ($inventori) {
                return $inventori->penanggung_jawab;
            })
            ->addColumn('stock', function ($inventori) {
                return $inventori->stok;
            })
            ->addColumn('location', function ($inventori) {
                return $inventori->lokasi_penyimpanan;
            })
            ->addColumn('status', function ($inventori) {
                return $inventori->status;
            })
            ->addColumn('note', function ($inventori) {
                return $inventori->catatan;
            })
            ->addColumn('is_active', function ($inventori) {
                return $inventori->is_active;
            })
            ->addColumn('aksi', function ($inventori) {
                return '
                    <div class="flex justify-end gap-2">
                        <button onclick="editForm(`'. route('admin.inventaris.inventori.update', $inventori->id_inventori) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button onclick="deleteData(`'. route('admin.inventaris.inventori.destroy', $inventori->id_inventori) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
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
            'nama_barang' => 'required',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'penanggung_jawab' => 'required',
            'stok' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required',
            'status' => 'required|in:tersedia,tidak tersedia',
            'catatan' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate outlet access
        if (!$this->isSuperAdmin()) {
            $this->validateOutletAccess($request->id_outlet);
        }

        // Generate kode inventori otomatis
        $kodeInventori = Inventori::generateKodeInventori();

        $inventori = Inventori::create([
            'kode_inventori' => $kodeInventori,
            'nama_barang' => $request->nama_barang,
            'id_kategori' => $request->id_kategori,
            'id_outlet' => $request->id_outlet,
            'penanggung_jawab' => $request->penanggung_jawab,
            'stok' => $request->stok,
            'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan', 
            'data' => $inventori,
            'generated_code' => $kodeInventori
        ], 200);
    }

    public function show($id)
    {
        $inventori = Inventori::with(['outlet', 'kategori'])->find($id);
        if (!$inventori) {
            return response()->json(['error' => 'Inventori tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $inventori->id_inventori,
            'code' => $inventori->kode_inventori,
            'name' => $inventori->nama_barang,
            'category' => $inventori->kategori ? $inventori->kategori->nama_kategori : '',
            'category_id' => $inventori->id_kategori,
            'outlet' => $inventori->outlet ? $inventori->outlet->nama_outlet : '',
            'outlet_id' => $inventori->id_outlet,
            'pic' => $inventori->penanggung_jawab,
            'stock' => $inventori->stok,
            'location' => $inventori->lokasi_penyimpanan,
            'status' => $inventori->status,
            'note' => $inventori->catatan,
            'is_active' => $inventori->is_active
        ]);
    }

    public function update(Request $request, $id)
    {
        $inventori = Inventori::find($id);
        if (!$inventori) {
            return response()->json(['error' => 'Inventori tidak ditemukan'], 404);
        }

        // Validate outlet access for existing data
        if (!$this->isSuperAdmin()) {
            $this->validateOutletAccess($inventori->id_outlet);
        }

        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'penanggung_jawab' => 'required',
            'stok' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required',
            'status' => 'required|in:tersedia,tidak tersedia',
            'catatan' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate new outlet access if changed
        if (!$this->isSuperAdmin() && $request->id_outlet != $inventori->id_outlet) {
            $this->validateOutletAccess($request->id_outlet);
        }

        $inventori->update([
            'nama_barang' => $request->nama_barang,
            'id_kategori' => $request->id_kategori,
            'id_outlet' => $request->id_outlet,
            'penanggung_jawab' => $request->penanggung_jawab,
            'stok' => $request->stok,
            'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $inventori], 200);
    }

    public function destroy($id)
    {
        $inventori = Inventori::find($id);
        if (!$inventori) {
            return response()->json(['error' => 'Inventori tidak ditemukan'], 404);
        }

        $inventori->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    // Generate kode baru
    public function getNewKode()
    {
        $kodeInventori = Inventori::generateKodeInventori();
        return response()->json(['kode_inventori' => $kodeInventori]);
    }

    // Get outlets untuk filter
    public function getOutlets()
    {
        $outlets = $this->getAccessibleOutlets();
        return response()->json($outlets);
    }

    // Get categories untuk filter
    public function getCategories()
    {
        $categories = Kategori::select('id_kategori', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        return response()->json($categories);
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $query = Inventori::with(['outlet', 'kategori']);

        // Filter by accessible outlets
        if (!$this->isSuperAdmin()) {
            $accessibleOutletIds = $this->getAccessibleOutletIds();
            $query->whereIn('id_outlet', $accessibleOutletIds);
        }

        // Apply filters jika ada
        if ($request->has('outlet') && $request->outlet !== 'ALL') {
            // Validate outlet access
            if (!$this->isSuperAdmin()) {
                $this->validateOutletAccess($request->outlet);
            }
            $query->where('id_outlet', $request->outlet);
        }

        if ($request->has('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        $inventori = $query->get();

        $pdf = PDF::loadView('admin.inventaris.inventori.export_pdf', [
            'inventori' => $inventori,
            'filterOutlet' => $request->outlet,
            'filterStatus' => $request->status
        ]);

        return $pdf->download('inventori-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new InventoriExport($request), 'inventori-' . date('Y-m-d') . '.xlsx');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new InventoriImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    // Download Template Excel
    public function downloadTemplate()
    {
        return Excel::download(new InventoriExport(true), 'template-import-inventori.xlsx');
    }
}