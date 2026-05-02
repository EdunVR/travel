<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartLog;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\HasOutletFilter;

class SparepartController extends Controller
{
    use HasOutletFilter;
    /**
     * Display sparepart page
     */
    public function index()
    {
        $outlets = $this->getAccessibleOutlets();
        return view('admin.inventaris.sparepart.index', compact('outlets'));
    }

    /**
     * Get sparepart data for DataTables
     */
    public function getData(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Validate outlet access
        if ($outletId && !$this->isSuperAdmin()) {
            $this->validateOutletAccess($outletId);
        }
        
        $query = Sparepart::with('outlet');
        
        // Filter by accessible outlets
        if (!$this->isSuperAdmin()) {
            $accessibleOutletIds = $this->getAccessibleOutletIds();
            $query->whereIn('outlet_id', $accessibleOutletIds);
        }
        
        // Filter by specific outlet if provided
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        // Filter by date range
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="sparepart-checkbox rounded border-slate-300" value="' . $row->id_sparepart . '">';
            })
            ->addColumn('harga_formatted', function ($row) {
                return 'Rp ' . number_format($row->harga, 0, ',', '.');
            })
            ->addColumn('stok_status', function ($row) {
                if ($row->stok <= 0) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Habis</span>';
                } elseif ($row->isStokMinimum()) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Stok Minimum</span>';
                } else {
                    return '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Tersedia</span>';
                }
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->is_active) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Aktif</span>';
                } else {
                    return '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Nonaktif</span>';
                }
            })
            ->addColumn('aksi', function ($row) {
                $canAdjustPrice = auth()->user()->hasRole('superadmin') || auth()->user()->can('inventaris.sparepart.adjust-price');
                $canAdjustStock = auth()->user()->hasRole('superadmin') || auth()->user()->can('inventaris.sparepart.adjust-stock');
                
                $buttons = '<div class="flex gap-1 justify-center">';
                
                $buttons .= '<button onclick="editSparepart(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-yellow-200 text-yellow-700 hover:bg-yellow-50" title="Edit">
                    <i class="bx bx-edit text-lg"></i>
                </button>';
                
                if ($canAdjustStock) {
                    $buttons .= '<button onclick="adjustStok(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50" title="Sesuaikan Stok">
                        <i class="bx bx-package text-lg"></i>
                    </button>';
                }
                
                if ($canAdjustPrice) {
                    $buttons .= '<button onclick="adjustPrice(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-orange-200 text-orange-700 hover:bg-orange-50" title="Sesuaikan Harga">
                        <i class="bx bx-money text-lg"></i>
                    </button>';
                }
                
                $buttons .= '<button onclick="deleteSparepart(' . $row->id_sparepart . ')" class="p-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50" title="Hapus">
                    <i class="bx bx-trash text-lg"></i>
                </button>';
                
                $buttons .= '</div>';
                
                return $buttons;
            })
            ->rawColumns(['checkbox', 'stok_status', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Store new sparepart
     */
    public function store(Request $request)
    {
        // Log the request for debugging
        \Log::info('Sparepart store request', [
            'data' => $request->all(),
            'headers' => $request->headers->all(),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'kode_sparepart' => 'required|string|max:50|unique:spareparts,kode_sparepart',
            'nama_sparepart' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string'
        ]);

        // Validate outlet access
        if (!$this->isSuperAdmin()) {
            $this->validateOutletAccess($request->outlet_id);
        }

        try {
            DB::transaction(function () use ($request) {
                $sparepart = Sparepart::create([
                    'outlet_id' => $request->outlet_id,
                    'kode_sparepart' => $request->kode_sparepart,
                    'nama_sparepart' => $request->nama_sparepart,
                    'merk' => $request->merk,
                    'spesifikasi' => $request->spesifikasi,
                    'harga' => $request->harga,
                    'stok' => $request->stok,
                    'stok_minimum' => $request->stok_minimum,
                    'satuan' => $request->satuan,
                    'is_active' => true,
                    'keterangan' => $request->keterangan
                ]);

                // Log stok awal
                if ($request->stok > 0) {
                    SparepartLog::create([
                        'id_sparepart' => $sparepart->id_sparepart,
                        'id_user' => auth()->id(),
                        'tipe_perubahan' => 'stok',
                        'nilai_lama' => 0,
                        'nilai_baru' => $request->stok,
                        'selisih' => $request->stok,
                        'keterangan' => 'Stok awal'
                    ]);
                }
            });

            \Log::info('Sparepart created successfully', ['user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating sparepart', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sparepart by ID
     */
    public function show($id)
    {
        $sparepart = Sparepart::with(['logs.user'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $sparepart
        ]);
    }

    /**
     * Update sparepart
     */
    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'kode_sparepart' => 'required|string|max:50|unique:spareparts,kode_sparepart,' . $id . ',id_sparepart',
            'nama_sparepart' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request, $sparepart) {
                // Log perubahan harga
                if ($request->harga != $sparepart->harga) {
                    SparepartLog::create([
                        'id_sparepart' => $sparepart->id_sparepart,
                        'id_user' => auth()->id(),
                        'tipe_perubahan' => 'harga',
                        'nilai_lama' => $sparepart->harga,
                        'nilai_baru' => $request->harga,
                        'selisih' => $request->harga - $sparepart->harga,
                        'keterangan' => 'Perubahan harga'
                    ]);
                }

                $sparepart->update([
                    'kode_sparepart' => $request->kode_sparepart,
                    'nama_sparepart' => $request->nama_sparepart,
                    'merk' => $request->merk,
                    'spesifikasi' => $request->spesifikasi,
                    'harga' => $request->harga,
                    'stok_minimum' => $request->stok_minimum,
                    'satuan' => $request->satuan,
                    'is_active' => $request->is_active,
                    'keterangan' => $request->keterangan
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adjust stock
     */
    public function adjustStok(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'tipe' => 'required|in:tambah,kurang',
            'kategori' => 'required|in:produksi,service,pembelian',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
            'id_karyawan' => 'nullable|exists:recruitments,id'
        ]);

        try {
            DB::transaction(function () use ($request, $sparepart) {
                $stokLama = $sparepart->stok;
                $jumlah = $request->jumlah;

                if ($request->tipe === 'tambah') {
                    $sparepart->tambahStok($jumlah);
                    $selisih = $jumlah;
                } else {
                    if (!$sparepart->isStokMencukupi($jumlah)) {
                        throw new \Exception('Stok tidak mencukupi');
                    }
                    $sparepart->kurangiStok($jumlah);
                    $selisih = -$jumlah;
                }

                // Generate keterangan otomatis jika kosong
                $keterangan = $request->keterangan;
                if (empty($keterangan) && $request->tipe === 'tambah') {
                    if ($request->kategori === 'service') {
                        $keterangan = 'Pengembalian dari Service';
                    } elseif ($request->kategori === 'produksi') {
                        $keterangan = 'Pengembalian dari Produksi';
                    } else {
                        $keterangan = 'Penyesuaian stok ' . $request->kategori;
                    }
                } elseif (empty($keterangan)) {
                    $keterangan = 'Penyesuaian stok ' . $request->kategori;
                }

                // Log perubahan stok
                SparepartLog::create([
                    'id_sparepart' => $sparepart->id_sparepart,
                    'id_user' => auth()->id(),
                    'id_karyawan' => $request->id_karyawan,
                    'tipe_perubahan' => 'stok',
                    'kategori' => $request->kategori,
                    'nilai_lama' => $stokLama,
                    'nilai_baru' => $sparepart->stok,
                    'selisih' => $selisih,
                    'keterangan' => $keterangan
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil disesuaikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyesuaikan stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete sparepart
     */
    public function destroy($id)
    {
        try {
            $sparepart = Sparepart::findOrFail($id);
            $sparepart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sparepart berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sparepart logs
     */
    public function getLogs($id)
    {
        $logs = SparepartLog::with(['user', 'karyawan'])
            ->where('id_sparepart', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->kode_log = 'LOG-' . str_pad($log->id_log, 6, '0', STR_PAD_LEFT);
                return $log;
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Search sparepart (for autocomplete)
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');
        
        $spareparts = Sparepart::active()
            ->where(function($query) use ($search) {
                $query->where('kode_sparepart', 'like', '%' . $search . '%')
                    ->orWhere('nama_sparepart', 'like', '%' . $search . '%')
                    ->orWhere('merk', 'like', '%' . $search . '%');
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $spareparts
        ]);
    }

    /**
     * Generate next kode sparepart
     */
    public function generateKode()
    {
        $lastSparepart = Sparepart::orderBy('kode_sparepart', 'desc')->first();
        
        $nextNumber = 1;
        if ($lastSparepart) {
            $lastCode = $lastSparepart->kode_sparepart;
            if (preg_match('/SP(\d+)/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }
        
        $newCode = 'SP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return response()->json([
            'success' => true,
            'kode' => $newCode
        ]);
    }

    /**
     * Adjust price
     */
    public function adjustPrice(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'harga_baru' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $sparepart) {
                $hargaLama = $sparepart->harga;
                $hargaBaru = $request->harga_baru;

                // Log perubahan harga
                SparepartLog::create([
                    'id_sparepart' => $sparepart->id_sparepart,
                    'id_user' => auth()->id(),
                    'tipe_perubahan' => 'harga',
                    'nilai_lama' => $hargaLama,
                    'nilai_baru' => $hargaBaru,
                    'selisih' => $hargaBaru - $hargaLama,
                    'keterangan' => $request->keterangan
                ]);

                $sparepart->update(['harga' => $hargaBaru]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Harga berhasil disesuaikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyesuaikan harga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete spareparts
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id_sparepart'
        ]);

        try {
            $count = Sparepart::whereIn('id_sparepart', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$count} sparepart"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export sparepart data
     */
    public function export(Request $request)
    {
        // Handle both JSON and form data
        $data = $request->all();
        
        // Parse JSON strings if they exist (from form submission)
        if (isset($data['ids']) && is_string($data['ids'])) {
            $data['ids'] = json_decode($data['ids'], true);
        }
        if (isset($data['current_order']) && is_string($data['current_order'])) {
            $data['current_order'] = json_decode($data['current_order'], true);
        }
        if (isset($data['filters']) && is_string($data['filters'])) {
            $data['filters'] = json_decode($data['filters'], true);
        }
        
        // Merge parsed data back to request
        $request->merge($data);
        
        $request->validate([
            'format' => 'required|in:pdf,excel',
            'data_type' => 'required|in:all,selected',
            'include_history' => 'required|in:yes,no',
            'ids' => 'required_if:data_type,selected|array',
            'log_start_date' => 'nullable|date',
            'log_end_date' => 'nullable|date',
            'log_category' => 'nullable|in:produksi,service,pembelian',
            'log_sort' => 'nullable|in:asc,desc',
            'current_order' => 'nullable|array',
            'current_search' => 'nullable|string',
            'filters' => 'nullable|array'
        ]);

        try {
            $query = Sparepart::with(['outlet']);
            
            // Include logs only if requested
            if ($request->include_history === 'yes') {
                $query->with(['logs.user', 'logs.karyawan']);
            }
            
            // Filter by accessible outlets
            if (!$this->isSuperAdmin()) {
                $accessibleOutletIds = $this->getAccessibleOutletIds();
                $query->whereIn('outlet_id', $accessibleOutletIds);
            }

            // Apply current filters from the table
            if ($request->filters) {
                $filters = $request->filters;
                
                if (!empty($filters['outlet_id'])) {
                    $query->where('outlet_id', $filters['outlet_id']);
                }
                
                if (!empty($filters['start_date'])) {
                    $query->whereDate('created_at', '>=', $filters['start_date']);
                }
                
                if (!empty($filters['end_date'])) {
                    $query->whereDate('created_at', '<=', $filters['end_date']);
                }
            }

            // Apply current search
            if ($request->current_search) {
                $search = $request->current_search;
                $query->where(function($q) use ($search) {
                    $q->where('kode_sparepart', 'like', '%' . $search . '%')
                      ->orWhere('nama_sparepart', 'like', '%' . $search . '%')
                      ->orWhere('merk', 'like', '%' . $search . '%');
                });
            }

            // Apply current sorting
            if ($request->current_order && is_array($request->current_order) && count($request->current_order) > 0) {
                foreach ($request->current_order as $order) {
                    if (isset($order[0]) && isset($order[1])) {
                        $columnIndex = $order[0];
                        $direction = $order[1];
                        
                        // Map column index to actual column name
                        $columns = ['', 'id_sparepart', 'kode_sparepart', 'nama_sparepart', 'merk', 'harga', 'stok', 'stok_minimum'];
                        if (isset($columns[$columnIndex])) {
                            $columnName = $columns[$columnIndex];
                            if ($columnName) {
                                $query->orderBy($columnName, $direction);
                            }
                        }
                    }
                }
            } else {
                $query->orderBy('kode_sparepart', 'asc');
            }

            // Filter by selected IDs or all data
            if ($request->data_type === 'selected') {
                $query->whereIn('id_sparepart', $request->ids);
            }

            $spareparts = $query->get();

            // Filter logs based on criteria if history is included
            if ($request->include_history === 'yes') {
                foreach ($spareparts as $sparepart) {
                    $logsQuery = $sparepart->logs();
                    
                    if ($request->log_start_date) {
                        $logsQuery->whereDate('created_at', '>=', $request->log_start_date);
                    }
                    if ($request->log_end_date) {
                        $logsQuery->whereDate('created_at', '<=', $request->log_end_date);
                    }
                    if ($request->log_category) {
                        $logsQuery->where('kategori', $request->log_category);
                    }
                    
                    $sortOrder = $request->log_sort ?? 'desc';
                    $logsQuery->orderBy('created_at', $sortOrder);
                    
                    $sparepart->filtered_logs = $logsQuery->get();
                }
            } else {
                // Set empty logs if history not included
                foreach ($spareparts as $sparepart) {
                    $sparepart->filtered_logs = collect();
                }
            }

            if ($request->format === 'pdf') {
                return $this->exportPdf($spareparts, $request->all());
            } else {
                // Excel export only for superadmin
                if (!auth()->user()->hasRole('superadmin')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Export Excel hanya tersedia untuk Superadmin'
                    ], 403);
                }
                return $this->exportExcel($spareparts, $request->all());
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search karyawan for autocomplete
     */
    public function searchKaryawan(Request $request)
    {
        $search = $request->get('search', '');
        
        $karyawan = \App\Models\Recruitment::where('name', 'like', '%' . $search . '%')
            ->orWhere('position', 'like', '%' . $search . '%')
            ->limit(10)
            ->get(['id', 'name', 'position']);

        return response()->json([
            'success' => true,
            'data' => $karyawan
        ]);
    }

    /**
     * Export to PDF
     */
    private function exportPdf($spareparts, $filters)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.inventaris.sparepart.export-pdf', compact('spareparts', 'filters'));
        $pdf->setPaper('A4', 'landscape'); // Set landscape for better table display
        
        $filename = 'sparepart-export-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Always use stream for PDF
        return $pdf->stream($filename);
    }

    /**
     * Export to Excel
     */
    private function exportExcel($spareparts, $filters)
    {
        return \Excel::download(new \App\Exports\SparepartExport($spareparts, $filters), 
            'sparepart-export-' . date('Y-m-d-H-i-s') . '.xlsx');
    }
}
