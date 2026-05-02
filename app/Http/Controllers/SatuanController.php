<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satuan;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SatuanExport;
use App\Imports\SatuanImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class SatuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inventaris.satuan.view')->only(['index', 'data', 'show', 'getSatuanUtama']);
        $this->middleware('permission:inventaris.satuan.create')->only(['store', 'getNewKode']);
        $this->middleware('permission:inventaris.satuan.edit')->only(['update']);
        $this->middleware('permission:inventaris.satuan.delete')->only(['destroy']);
        $this->middleware('permission:inventaris.satuan.export')->only(['exportPdf', 'exportExcel', 'downloadTemplate']);
        $this->middleware('permission:inventaris.satuan.import')->only(['importExcel']);
    }

    public function index()
    {
        return view('admin.inventaris.satuan.index');
    }

    public function data(Request $request)
    {
        Log::info('Fetching Satuan Data with filters', $request->all());

        $query = Satuan::with('satuanUtama');

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter status
        if ($request->has('status_filter') && $request->status_filter !== 'ALL') {
            $query->where('is_active', $request->status_filter === 'ACTIVE');
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'nama_satuan';
        $sortDirection = $request->sort_dir ?? 'asc';
        
        $columnMapping = [
            'code' => 'kode_satuan',
            'name' => 'nama_satuan', 
            'symbol' => 'simbol'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $satuan = $query->get();

        return datatables()
            ->of($satuan)
            ->addIndexColumn()
            ->addColumn('code', function ($satuan) {
                return $satuan->kode_satuan;
            })
            ->addColumn('name', function ($satuan) {
                return $satuan->nama_satuan;
            })
            ->addColumn('symbol', function ($satuan) {
                return $satuan->simbol;
            })
            ->addColumn('desc', function ($satuan) {
                return $satuan->deskripsi;
            })
            ->addColumn('is_active', function ($satuan) {
                return $satuan->is_active ? 'ACTIVE' : 'INACTIVE';
            })
            ->addColumn('nilai_konversi', function ($satuan) {
                return $satuan->nilai_konversi;
            })
            ->addColumn('satuan_utama_id', function ($satuan) {
                return $satuan->satuan_utama_id;
            })
            ->addColumn('satuan_utama_name', function ($satuan) {
                return $satuan->satuanUtama ? $satuan->satuanUtama->nama_satuan : null;
            })
            ->addColumn('satuan_utama_simbol', function ($satuan) {
                return $satuan->satuanUtama ? $satuan->satuanUtama->simbol : null;
            })
            ->addColumn('konversi_display', function ($satuan) {
                if ($satuan->nilai_konversi && $satuan->satuanUtama) {
                    return "1 {$satuan->simbol} = {$satuan->nilai_konversi} {$satuan->satuanUtama->simbol}";
                }
                return '-';
            })
            ->addColumn('aksi', function ($satuan) {
                return '
                    <div class="flex justify-end gap-2">
                        <button onclick="editForm(`'. route('admin.inventaris.satuan.update', $satuan->id_satuan) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button onclick="deleteData(`'. route('admin.inventaris.satuan.destroy', $satuan->id_satuan) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi', 'konversi_display'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_satuan' => 'required',
            'simbol' => 'nullable',
            'deskripsi' => 'nullable',
            'is_active' => 'boolean',
            'nilai_konversi' => 'nullable|numeric|min:0',
            'satuan_utama_id' => 'nullable|exists:satuan,id_satuan'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate kode satuan otomatis
        $kodeSatuan = Satuan::generateKodeSatuan();

        $satuan = Satuan::create([
            'kode_satuan' => $kodeSatuan,
            'nama_satuan' => $request->nama_satuan,
            'simbol' => $request->simbol,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->is_active ?? true,
            'nilai_konversi' => $request->nilai_konversi,
            'satuan_utama_id' => $request->satuan_utama_id
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan', 
            'data' => $satuan,
            'generated_code' => $kodeSatuan
        ], 200);
    }

    public function show($id)
    {
        $satuan = Satuan::with('satuanUtama')->find($id);
        if (!$satuan) {
            return response()->json(['error' => 'Satuan tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $satuan->id_satuan,
            'code' => $satuan->kode_satuan,
            'name' => $satuan->nama_satuan,
            'symbol' => $satuan->simbol,
            'desc' => $satuan->deskripsi,
            'is_active' => $satuan->is_active,
            'nilai_konversi' => $satuan->nilai_konversi ? (float) $satuan->nilai_konversi : null,
            'satuan_utama_id' => $satuan->satuan_utama_id,
            'satuan_utama_name' => $satuan->satuanUtama ? $satuan->satuanUtama->nama_satuan : null,
            'satuan_utama_simbol' => $satuan->satuanUtama ? $satuan->satuanUtama->simbol : null
        ]);
    }

    public function update(Request $request, $id)
    {
        $satuan = Satuan::find($id);
        if (!$satuan) {
            return response()->json(['error' => 'Satuan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_satuan' => 'required',
            'simbol' => 'nullable',
            'deskripsi' => 'nullable',
            'is_active' => 'boolean',
            'nilai_konversi' => 'nullable|numeric|min:0',
            'satuan_utama_id' => 'nullable|exists:satuan,id_satuan'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $satuan->update([
            'nama_satuan' => $request->nama_satuan,
            'simbol' => $request->simbol,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->is_active ?? true,
            'nilai_konversi' => $request->nilai_konversi,
            'satuan_utama_id' => $request->satuan_utama_id
        ]);

        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $satuan], 200);
    }

    public function destroy($id)
    {
        $satuan = Satuan::find($id);
        if (!$satuan) {
            return response()->json(['error' => 'Satuan tidak ditemukan'], 404);
        }

        // Cek apakah satuan digunakan sebagai satuan utama
        $digunakan = Satuan::where('satuan_utama_id', $id)->exists();
        if ($digunakan) {
            return response()->json(['error' => 'Satuan tidak dapat dihapus karena digunakan sebagai satuan utama'], 422);
        }

        $satuan->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $query = Satuan::with('satuanUtama');

        if ($request->has('status') && $request->status !== 'ALL') {
            $query->where('is_active', $request->status === 'ACTIVE');
        }

        $satuan = $query->get();

        $pdf = PDF::loadView('admin.inventaris.satuan.export_pdf', [
            'satuan' => $satuan,
            'filterStatus' => $request->status
        ]);

        return $pdf->download('satuan-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new SatuanExport($request), 'satuan-' . date('Y-m-d') . '.xlsx');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new SatuanImport, $request->file('file'));
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    // Download Template Excel
    public function downloadTemplate()
    {
        return Excel::download(new SatuanExport(true), 'template-import-satuan.xlsx');
    }

    // Get new kode satuan
    public function getNewKode()
    {
        $kodeSatuan = Satuan::generateKodeSatuan();
        return response()->json(['kode_satuan' => $kodeSatuan]);
    }

    // Get satuan utama untuk dropdown konversi
    public function getSatuanUtama()
    {
        $satuanUtama = Satuan::whereNull('satuan_utama_id')
            ->where('is_active', true)
            ->get(['id_satuan', 'nama_satuan', 'simbol']);
            
        return response()->json($satuanUtama);
    }
}