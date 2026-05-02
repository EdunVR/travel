<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OutletExport;
use App\Imports\OutletImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class OutletController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inventaris.outlet.view')->only(['index', 'data', 'show', 'getCities']);
        $this->middleware('permission:inventaris.outlet.create')->only(['store', 'getNewKode']);
        $this->middleware('permission:inventaris.outlet.edit')->only(['update']);
        $this->middleware('permission:inventaris.outlet.delete')->only(['destroy']);
        $this->middleware('permission:inventaris.outlet.export')->only(['exportPdf', 'exportExcel', 'downloadTemplate']);
        $this->middleware('permission:inventaris.outlet.import')->only(['importExcel']);
    }

    public function index()
    {
        Log::info('Loading Outlet Index Page');
        return view('admin.inventaris.outlet.index');
    }

    public function data(Request $request)
    {
        Log::info('Fetching Outlet Data with filters', $request->all());

        $query = Outlet::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter kota
        if ($request->has('kota_filter') && $request->kota_filter !== 'ALL') {
            $query->where('kota', $request->kota_filter);
        }

        // Filter status
        if ($request->has('status_filter') && $request->status_filter !== 'ALL') {
            $query->where('is_active', $request->status_filter === 'ACTIVE');
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'id_outlet';
        $sortDirection = $request->sort_dir ?? 'desc';
        
        $columnMapping = [
            'code' => 'kode_outlet',
            'name' => 'nama_outlet', 
            'city' => 'kota',
            'is_active' => 'is_active'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $outlets = $query->get();

        return datatables()
            ->of($outlets)
            ->addIndexColumn()
            ->addColumn('code', function ($outlet) {
                return $outlet->kode_outlet;
            })
            ->addColumn('name', function ($outlet) {
                return $outlet->nama_outlet;
            })
            ->addColumn('city', function ($outlet) {
                return $outlet->kota;
            })
            ->addColumn('address', function ($outlet) {
                return $outlet->alamat;
            })
            ->addColumn('phone', function ($outlet) {
                return $outlet->telepon;
            })
            ->addColumn('is_active', function ($outlet) {
                return $outlet->is_active;
            })
            ->addColumn('note', function ($outlet) {
                return $outlet->catatan;
            })
            ->addColumn('aksi', function ($outlet) {
                return '
                    <div class="flex justify-end gap-2">
                        <button onclick="editForm(`'. route('outlet.update', $outlet->id_outlet) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button onclick="deleteData(`'. route('outlet.destroy', $outlet->id_outlet) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
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
            'kode_outlet' => 'required|unique:outlets,kode_outlet',
            'nama_outlet' => 'required',
            'kota' => 'required',
            'alamat' => 'nullable',
            'telepon' => 'nullable',
            'is_active' => 'boolean',
            'catatan' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kodeOutlet = Outlet::generateKodeOutlet();

        $outlet = Outlet::create([
            'kode_outlet' => $kodeOutlet,
            'nama_outlet' => $request->nama_outlet,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'telepon' => $request->telepon,
            'is_active' => $request->is_active ?? true,
            'catatan' => $request->catatan
        ]);

        return response()->json(['message' => 'Data berhasil disimpan', 'data' => $outlet], 200);
    }

    // Method untuk mendapatkan kode outlet baru
    public function getNewKode()
    {
        try {
            $kodeOutlet = Outlet::generateKodeOutlet();
            return response()->json(['kode_outlet' => $kodeOutlet]);
        } catch (\Exception $e) {
            \Log::error('Error generating outlet code: ' . $e->getMessage());
            return response()->json(['kode_outlet' => 'OUT-001']); // Fallback
        }
    }

    public function show($id)
    {
        $outlet = Outlet::find($id);
        if (!$outlet) {
            return response()->json(['error' => 'Outlet tidak ditemukan'], 404);
        }

        // Format response sesuai frontend
        return response()->json([
            'id' => $outlet->id_outlet,
            'code' => $outlet->kode_outlet,
            'name' => $outlet->nama_outlet,
            'address' => $outlet->alamat,
            'city' => $outlet->kota,
            'phone' => $outlet->telepon,
            'is_active' => $outlet->is_active,
            'note' => $outlet->catatan
        ]);
    }

    public function update(Request $request, $id)
    {
        $outlet = Outlet::find($id);
        if (!$outlet) {
            return response()->json(['error' => 'Outlet tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_outlet' => 'required|unique:outlets,kode_outlet,' . $id . ',id_outlet',
            'nama_outlet' => 'required',
            'kota' => 'required',
            'alamat' => 'nullable',
            'telepon' => 'nullable',
            'is_active' => 'boolean',
            'catatan' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $outlet->update([
            'kode_outlet' => $request->kode_outlet,
            'nama_outlet' => $request->nama_outlet,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'telepon' => $request->telepon,
            'is_active' => $request->is_active ?? true,
            'catatan' => $request->catatan
        ]);

        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $outlet], 200);
    }

    public function destroy($id)
    {
        $outlet = Outlet::find($id);
        if (!$outlet) {
            return response()->json(['error' => 'Outlet tidak ditemukan'], 404);
        }

        $outlet->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $query = Outlet::query();

        // Apply filters jika ada
        if ($request->has('kota') && $request->kota !== 'ALL') {
            $query->where('kota', $request->kota);
        }

        if ($request->has('status') && $request->status !== 'ALL') {
            $query->where('is_active', $request->status === 'ACTIVE');
        }

        $outlets = $query->get();

        $pdf = PDF::loadView('admin.inventaris.outlet.outlet-pdf', [
            'outlets' => $outlets,
            'filterKota' => $request->kota,
            'filterStatus' => $request->status
        ]);

        return $pdf->download('outlets-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new OutletExport($request), 'outlets-' . date('Y-m-d') . '.xlsx');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new OutletImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    // Download Template Excel
    public function downloadTemplate()
    {
        return Excel::download(new OutletExport(true), 'template-import-outlet.xlsx');
    }

    // Get unique cities for filter
    public function getCities()
    {
        $cities = Outlet::select('kota')
            ->whereNotNull('kota')
            ->where('kota', '!=', '')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        return response()->json($cities);
    }
}