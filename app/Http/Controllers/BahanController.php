<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\Bahan;
use App\Models\BahanDetail;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BahanExport;
use App\Imports\BahanImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasOutletFilter;

class BahanController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('permission:inventaris.bahan.view')->only(['index', 'data', 'show', 'getOutlets', 'getSatuan']);
        $this->middleware('permission:inventaris.bahan.create')->only(['store', 'getNewKode']);
        $this->middleware('permission:inventaris.bahan.edit')->only(['update', 'updateHarga', 'editHarga', 'edit']);
        $this->middleware('permission:inventaris.bahan.delete')->only(['destroy', 'destroyHarga', 'deleteSelected']);
        $this->middleware('permission:inventaris.bahan.export')->only(['exportPdf', 'exportExcel', 'downloadTemplate']);
        $this->middleware('permission:inventaris.bahan.import')->only(['importExcel']);
        $this->middleware('permission:inventaris.bahan.edit-stock')->only(['storeStock', 'updateStock']);
        $this->middleware('permission:inventaris.bahan.edit-price')->only(['updateHargaBeli']);
    }

    public function index()
    {
        Log::info('Loading Bahan Index Page');
        return view('admin.inventaris.bahan.index');
    }

    public function data(Request $request)
    {   
        Log::info('Fetching Bahan Data with filters', $request->all());
        
        $query = Bahan::with(['outlet', 'satuan', 'hargaBahan'])
            ->withSum('hargaBahan', 'stok');

        // Apply outlet filter using trait
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter outlet
        if ($request->has('outlet_filter') && $request->outlet_filter !== 'ALL') {
            $query->where('id_outlet', $request->outlet_filter);
        }

        // Filter satuan
        if ($request->has('unit_filter') && $request->unit_filter !== 'ALL') {
            $query->whereHas('satuan', function($q) use ($request) {
                $q->where('nama_satuan', $request->unit_filter);
            });
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'nama_bahan';
        $sortDirection = $request->sort_dir ?? 'asc';
        
        $columnMapping = [
            'name' => 'nama_bahan',
            'outlet' => 'id_outlet', 
            'brand' => 'merk',
            'stock' => 'harga_bahan_sum_stok',
            'unit' => 'id_satuan'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $bahan = $query->get();

        return datatables()
            ->of($bahan)
            ->addIndexColumn()
            ->addColumn('code', function ($bahan) {
                return $bahan->kode_bahan;
            })
            ->addColumn('name', function ($bahan) {
                return $bahan->nama_bahan;
            })
            ->addColumn('outlet', function ($bahan) {
                return $bahan->outlet ? $bahan->outlet->nama_outlet : '-';
            })
            ->addColumn('brand', function ($bahan) {
                return $bahan->merk ?: '-';
            })
            ->addColumn('stock', function ($bahan) {
                return $bahan->harga_bahan_sum_stok ?? 0;
            })
            ->addColumn('unit', function ($bahan) {
                return $bahan->satuan ? $bahan->satuan->nama_satuan : '-';
            })
            ->addColumn('note', function ($bahan) {
                return $bahan->catatan ?: '';
            })
            ->addColumn('is_active', function ($bahan) {
                return $bahan->is_active ? 'ACTIVE' : 'INACTIVE';
            })
            ->addColumn('harga_bahan', function ($bahan) {
                return $bahan->hargaBahan->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'harga_beli' => $detail->harga_beli,
                        'stok' => $detail->stok,
                        'created_at' => $detail->created_at,
                    ];
                });
            })
            ->addColumn('aksi', function ($bahan) {
                return '
                <div class="flex justify-end gap-2">
                    <button onclick="showDetail(`'. route('admin.inventaris.bahan.show', $bahan->id_bahan) .'`)" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 text-white px-3 py-1.5 hover:bg-emerald-700 text-sm">
                        <i class="bx bx-show"></i> Harga Beli
                    </button>
                    <button onclick="editForm(`'. route('admin.inventaris.bahan.update', $bahan->id_bahan) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                        <i class="bx bx-edit-alt"></i> Edit
                    </button>
                    <button onclick="deleteData(`'. route('admin.inventaris.bahan.destroy', $bahan->id_bahan) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
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
            'nama_bahan' => 'required',
            'id_outlet' => 'required',
            'id_satuan' => 'required',
            'merk' => 'nullable',
            'catatan' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate kode bahan otomatis
        $kodeBahan = Bahan::generateKodeBahan();

        $bahan = Bahan::create([
            'kode_bahan' => $kodeBahan,
            'nama_bahan' => $request->nama_bahan,
            'id_outlet' => $request->id_outlet ?? auth()->user()->akses_outlet[0],
            'id_satuan' => $request->id_satuan,
            'merk' => $request->merk,
            'catatan' => $request->catatan,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan', 
            'data' => $bahan,
            'generated_code' => $kodeBahan
        ], 200);
    }

    public function show($id)
    {
        $detail = BahanDetail::with('bahan')->where('id_bahan', $id)->orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($detail) {
                return tanggal_indonesia($detail->created_at, false);
            })
            ->addColumn('harga_beli', function ($detail) {
                return format_uang($detail->harga_beli);
            })
            ->addColumn('stok', function ($detail) {
                return $detail->stok;
            })
            ->addColumn('aksi', function ($detail) {
                $updateUrl = $detail->id ? route('admin.inventaris.bahan.edit_harga', $detail->id) : '#';
                $deleteUrl = $detail->id ? route('admin.inventaris.bahan.destroy_harga', $detail->id) : '#';
            
                return '
                <div class="btn-group">
                    <button onclick="editForm_harga(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData_harga(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })            
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function edit($id)
    {
        $bahan = Bahan::with(['outlet', 'satuan', 'hargaBahan'])->find($id);

        if (!$bahan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Format response sesuai frontend
        return response()->json([
            'id' => $bahan->id_bahan,
            'code' => $bahan->kode_bahan,
            'name' => $bahan->nama_bahan,
            'outlet' => $bahan->id_outlet,
            'brand' => $bahan->merk,
            'stock' => $bahan->harga_bahan_sum_stok ?? 0,
            'unit' => $bahan->id_satuan,
            'note' => $bahan->catatan,
            'is_active' => $bahan->is_active,
            'harga_bahan' => $bahan->hargaBahan->map(function($detail) {
                return [
                    'id' => $detail->id,
                    'harga_beli' => $detail->harga_beli,
                    'stok' => $detail->stok,
                    'created_at' => $detail->created_at,
                ];
            })
        ]);
    }

    public function editHarga($id)
    {
        $bahanDetail = BahanDetail::find($id);

        if (!$bahanDetail) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($bahanDetail);
    }

    public function update(Request $request, string $id)
    {
        $bahan = Bahan::find($id);
        if (!$bahan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required',
            'id_outlet' => 'required',
            'id_satuan' => 'required',
            'merk' => 'nullable',
            'catatan' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bahan->update([
            'nama_bahan' => $request->nama_bahan,
            'id_outlet' => $request->id_outlet ?? auth()->user()->akses_outlet[0],
            'id_satuan' => $request->id_satuan,
            'merk' => $request->merk,
            'catatan' => $request->catatan,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json(['message' => 'Data berhasil diperbarui'], 200);
    }

    public function updateHarga(Request $request, string $id)
    {
        $bahan_detail = BahanDetail::find($id);
        try {
            $bahan_detail->update($request->all());
            return response()->json(['message' => 'Data berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $bahan = Bahan::find($id);
        if (!$bahan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $bahan->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    public function destroyHarga(string $id)
    {
        BahanDetail::find($id)->delete();
        return response(null, 204);
    }

    public function storeStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_bahan' => 'required|exists:bahan,id_bahan',
            'harga_beli' => 'required|numeric|min:0',
            'stok' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            BahanDetail::create([
                'id_bahan' => $request->id_bahan,
                'harga_beli' => $request->harga_beli,
                'stok' => $request->stok
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan stok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stok' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bahanDetail = BahanDetail::findOrFail($id);
            $bahanDetail->update([
                'stok' => $request->stok
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateHargaBeli(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'harga_beli' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bahanDetail = BahanDetail::findOrFail($id);
            $bahanDetail->update([
                'harga_beli' => $request->harga_beli
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Harga beli berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui harga beli: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $query = Bahan::with(['outlet', 'satuan'])
            ->withSum('hargaBahan', 'stok');

        // Apply outlet filter using trait
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Apply filters jika ada
        if ($request->has('outlet') && $request->outlet !== 'ALL') {
            $query->where('id_outlet', $request->outlet);
        }

        if ($request->has('unit') && $request->unit !== 'ALL') {
            $query->whereHas('satuan', function($q) use ($request) {
                $q->where('nama_satuan', $request->unit);
            });
        }

        $bahan = $query->get();

        $pdf = PDF::loadView('admin.inventaris.bahan.export_pdf', [
            'bahan' => $bahan,
            'filterOutlet' => $request->outlet,
            'filterUnit' => $request->unit
        ]);

        return $pdf->download('bahan-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new BahanExport($request), 'bahan-' . date('Y-m-d') . '.xlsx');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new BahanImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    // Download Template Excel
    public function downloadTemplate()
    {
        return Excel::download(new BahanExport(true), 'template-import-bahan.xlsx');
    }

    // Get outlets for filter
    public function getOutlets()
    {
        $outlets = $this->getUserOutlets()
            ->map(function($outlet) {
                return [
                    'id' => $outlet->id_outlet,
                    'name' => $outlet->nama_outlet
                ];
            })
            ->values();

        return response()->json($outlets);
    }

    // Get satuan for filter
    public function getSatuan()
    {
        $satuan = Satuan::all()->pluck('nama_satuan', 'id_satuan');
        return response()->json($satuan);
    }

    // Get new kode bahan
    public function getNewKode()
    {
        $kodeBahan = Bahan::generateKodeBahan();
        return response()->json(['kode_bahan' => $kodeBahan]);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_bahan as $id) {
            $bahan = Bahan::find($id);
            $bahan->delete();
        }

        return response(null, 204);
    }
}