<?php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use App\Models\PermintaanBarangItem;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:supply-chain.permintaan-barang.view')->only(['index', 'show', 'getData']);
        $this->middleware('permission:supply-chain.permintaan-barang.create')->only(['create', 'store']);
        $this->middleware('permission:supply-chain.permintaan-barang.update')->only(['edit', 'update']);
        $this->middleware('permission:supply-chain.permintaan-barang.delete')->only(['destroy']);
        $this->middleware('permission:supply-chain.permintaan-barang.approve')->only(['approve', 'reject']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.supply-chain.permintaan-barang.index');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = PermintaanBarang::with(['outlet', 'user', 'items'])
            ->select('permintaan_barang.*');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_permintaan', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        if ($request->filled('sort_by')) {
            $sortBy = $request->sort_by;
            $sortDir = $request->get('sort_dir', 'asc');
            
            if ($sortBy === 'outlet') {
                // Use leftJoin to preserve relationships and avoid duplicates
                $query->leftJoin('outlets', 'permintaan_barang.outlet_id', '=', 'outlets.id_outlet')
                      ->orderBy('outlets.nama_outlet', $sortDir)
                      ->select('permintaan_barang.*'); // Ensure we only select from main table
            } elseif ($sortBy === 'user') {
                // Use leftJoin to preserve relationships and avoid duplicates
                $query->leftJoin('users', 'permintaan_barang.user_id', '=', 'users.id')
                      ->orderBy('users.name', $sortDir)
                      ->select('permintaan_barang.*'); // Ensure we only select from main table
            } else {
                $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $permintaan = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $permintaan->items(),
            'pagination' => [
                'current_page' => $permintaan->currentPage(),
                'last_page' => $permintaan->lastPage(),
                'per_page' => $permintaan->perPage(),
                'total' => $permintaan->total()
            ]
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => PermintaanBarang::count(),
            'draft' => PermintaanBarang::where('status', 'draft')->count(),
            'aktif' => PermintaanBarang::where('status', 'aktif')->count(),
            'disetujui' => PermintaanBarang::where('status', 'disetujui')->count(),
            'ditolak' => PermintaanBarang::where('status', 'ditolak')->count(),
            'urgent' => PermintaanBarang::where('prioritas', 'urgent')->count(),
            'total_budget' => PermintaanBarang::where('status', 'disetujui')->sum('estimasi_budget')
        ];

        return response()->json($stats);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::all();
        return view('admin.supply-chain.permintaan-barang.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'prioritas' => 'required|in:rendah,normal,tinggi,urgent',
            'tanggal_dibutuhkan' => 'nullable|date|after_or_equal:today',
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.tipe_item' => 'required|in:produk,bahan,custom',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.estimasi_harga' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $permintaan = new PermintaanBarang();
            $permintaan->nomor_permintaan = $permintaan->generateNomorPermintaan();
            $permintaan->judul = $request->judul;
            $permintaan->deskripsi = $request->deskripsi;
            $permintaan->prioritas = $request->prioritas;
            $permintaan->tanggal_dibutuhkan = $request->tanggal_dibutuhkan;
            $permintaan->outlet_id = $request->outlet_id;
            $permintaan->user_id = Auth::id();
            $permintaan->status = $request->get('status', 'draft');
            $permintaan->save();

            $totalBudget = 0;
            foreach ($request->items as $itemData) {
                $item = new PermintaanBarangItem();
                $item->permintaan_barang_id = $permintaan->id;
                $item->tipe_item = $itemData['tipe_item'];
                $item->produk_id = $itemData['produk_id'] ?? null;
                $item->bahan_id = $itemData['bahan_id'] ?? null;
                $item->nama_item = $itemData['nama_item'];
                $item->spesifikasi = $itemData['spesifikasi'] ?? null;
                $item->qty = $itemData['qty'];
                $item->satuan = $itemData['satuan'];
                $item->estimasi_harga = $itemData['estimasi_harga'] ?? 0;
                $item->total_estimasi = $item->qty * $item->estimasi_harga;
                $item->catatan = $itemData['catatan'] ?? null;
                $item->save();

                $totalBudget += $item->total_estimasi;
            }

            $permintaan->estimasi_budget = $totalBudget;
            $permintaan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan barang berhasil dibuat',
                'data' => $permintaan->load(['outlet', 'user', 'items'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permintaan barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permintaan = PermintaanBarang::with(['outlet', 'user', 'approver', 'items.produk', 'items.bahan'])
            ->findOrFail($id);

        return response()->json($permintaan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permintaan = PermintaanBarang::with(['items'])->findOrFail($id);
        
        if (!$permintaan->canBeEdited()) {
            return redirect()->route('admin.supply-chain.permintaan-barang.index')
                ->with('error', 'Permintaan barang tidak dapat diedit');
        }

        $outlets = Outlet::all();
        return view('admin.supply-chain.permintaan-barang.edit', compact('permintaan', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $permintaan = PermintaanBarang::findOrFail($id);
            
            if (!$permintaan->canBeEdited()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan barang tidak dapat diedit'
                ], 403);
            }

            // Handle items if sent as JSON string
            if ($request->has('items') && is_string($request->items)) {
                $items = json_decode($request->items, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format items tidak valid: ' . json_last_error_msg()
                    ], 400);
                }
                $request->merge(['items' => $items]);
            }

            // Validate request
            $validator = \Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'prioritas' => 'required|in:rendah,normal,tinggi,urgent',
                'tanggal_dibutuhkan' => 'nullable|date|after_or_equal:today',
                'outlet_id' => 'required|exists:outlets,id_outlet',
                'items' => 'required|array|min:1',
                'items.*.tipe_item' => 'required|in:produk,bahan,custom',
                'items.*.nama_item' => 'required|string|max:255',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.satuan' => 'required|string|max:50',
                'items.*.estimasi_harga' => 'nullable|numeric|min:0',
                'items.*.spesifikasi' => 'nullable|string|max:500',
                'items.*.catatan' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

        DB::beginTransaction();
        try {
            $permintaan->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'prioritas' => $request->prioritas,
                'tanggal_dibutuhkan' => $request->tanggal_dibutuhkan,
                'outlet_id' => $request->outlet_id,
                'status' => $request->get('status', $permintaan->status)
            ]);

            // Delete existing items
            $permintaan->items()->delete();

            // Add new items
            $totalBudget = 0;
            foreach ($request->items as $itemData) {
                $item = new PermintaanBarangItem();
                $item->permintaan_barang_id = $permintaan->id;
                $item->tipe_item = $itemData['tipe_item'];
                $item->produk_id = $itemData['produk_id'] ?? null;
                $item->bahan_id = $itemData['bahan_id'] ?? null;
                $item->nama_item = $itemData['nama_item'];
                $item->spesifikasi = $itemData['spesifikasi'] ?? null;
                $item->qty = $itemData['qty'];
                $item->satuan = $itemData['satuan'];
                $item->estimasi_harga = $itemData['estimasi_harga'] ?? 0;
                $item->total_estimasi = $item->qty * $item->estimasi_harga;
                $item->catatan = $itemData['catatan'] ?? null;
                $item->save();

                $totalBudget += $item->total_estimasi;
            }

            $permintaan->estimasi_budget = $totalBudget;
            $permintaan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan barang berhasil diupdate',
                'data' => $permintaan->load(['outlet', 'user', 'items'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate permintaan barang: ' . $e->getMessage()
            ], 500);
        }
        
        } catch (\Exception $e) {
            \Log::error('Update permintaan barang error: ' . $e->getMessage(), [
                'id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $permintaan = PermintaanBarang::findOrFail($id);
            
            if (!in_array($permintaan->status, ['draft', 'ditolak'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya permintaan dengan status draft atau ditolak yang dapat dihapus'
                ], 403);
            }

            $permintaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan barang berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permintaan barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve permintaan barang
     */
    public function approve(Request $request, $id)
    {
        // Validasi dasar
        $request->validate([
            'action_type' => 'required|in:approve_only,to_purchase_order,to_fixed_asset,to_journal',
            'catatan_approval' => 'nullable|string'
        ]);

        // Validasi kondisional berdasarkan action_type
        if ($request->action_type === 'to_purchase_order') {
            $request->validate([
                'supplier_id' => 'required|exists:supplier,id_supplier'
            ]);
        }

        if ($request->action_type === 'to_fixed_asset') {
            $request->validate([
                'book_id' => 'required|exists:accounting_books,id',
                'asset_account_id' => 'required|exists:chart_of_accounts,id',
                'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
                'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
                'payment_account_id' => 'required|exists:chart_of_accounts,id',
                'asset_name' => 'required|string|max:255',
                'asset_category' => 'nullable|string|max:100',
                'asset_location' => 'nullable|string|max:255',
                'acquisition_date' => 'required|date',
                'acquisition_cost' => 'required|numeric|min:0.01',
                'salvage_value' => 'nullable|numeric|min:0',
                'useful_life' => 'required|integer|min:1',
                'depreciation_method' => 'required|in:straight_line,declining_balance,double_declining',
                'asset_description' => 'nullable|string|max:1000'
            ]);
        }

        if ($request->action_type === 'to_journal') {
            $request->validate([
                'book_id' => 'required|exists:accounting_books,id',
                'journal_date' => 'required|date',
                'journal_description' => 'required|string|max:500',
                'journal_entries' => 'required|array|min:1',
                'journal_entries.*.account_id' => 'required|exists:chart_of_accounts,id',
                'journal_entries.*.debit' => 'nullable|numeric|min:0',
                'journal_entries.*.credit' => 'nullable|numeric|min:0',
                'journal_entries.*.description' => 'nullable|string|max:255'
            ]);
            
            // Validate that each entry has either debit or credit, but not both
            foreach ($request->journal_entries as $entry) {
                if ((!$entry['debit'] && !$entry['credit']) || ($entry['debit'] && $entry['credit'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Setiap entri jurnal harus memiliki debit atau kredit, tapi tidak keduanya'
                    ], 422);
                }
            }
            
            // Validate that journal is balanced
            $totalDebit = collect($request->journal_entries)->sum('debit');
            $totalCredit = collect($request->journal_entries)->sum('credit');
            
            if ($totalDebit != $totalCredit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak balance! Total debit harus sama dengan total kredit'
                ], 422);
            }
            
            if ($totalDebit == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total jurnal tidak boleh nol'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $permintaan = PermintaanBarang::findOrFail($id);
            
            if (!$permintaan->canBeApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan barang tidak dapat disetujui'
                ], 403);
            }

            $permintaan->update([
                'status' => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'catatan_approval' => $request->catatan_approval
            ]);

            $result = ['permintaan' => $permintaan];

            // Handle different approval actions
            switch ($request->action_type) {
                case 'to_purchase_order':
                    $result['purchase_order'] = $this->createPurchaseOrder($permintaan, $request->supplier_id);
                    break;
                case 'to_fixed_asset':
                    $result['fixed_asset'] = $this->createFixedAsset($permintaan, $request);
                    break;
                case 'to_journal':
                    $result['journal_entry'] = $this->createJournalEntry($permintaan, $request);
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan barang berhasil disetujui',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui permintaan barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject permintaan barang
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        try {
            $permintaan = PermintaanBarang::findOrFail($id);
            
            if (!$permintaan->canBeApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan barang tidak dapat ditolak'
                ], 403);
            }

            $permintaan->update([
                'status' => 'ditolak',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_penolakan' => $request->alasan_penolakan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan barang berhasil ditolak',
                'data' => $permintaan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for permintaan barang
     */
    public function generatePdf($id)
    {
        $permintaan = PermintaanBarang::with(['outlet', 'user', 'approver', 'items.produk', 'items.bahan'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.supply-chain.permintaan-barang.pdf', compact('permintaan'));
        
        return $pdf->stream('Permintaan_Barang_' . $permintaan->nomor_permintaan . '.pdf');
    }

    /**
     * Search products for autocomplete
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        $outlet_id = $request->get('outlet_id');
        
        $products = Produk::where('nama_produk', 'like', "%{$search}%")
            ->when($outlet_id, function($query) use ($outlet_id) {
                $query->where('id_outlet', $outlet_id);
            })
            ->with('satuan')
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id_produk,
                    'nama' => $product->nama_produk,
                    'sku' => $product->kode_produk,
                    'satuan_id' => $product->id_satuan,
                    'satuan' => $product->satuan ? [
                        'id' => $product->satuan->id_satuan,
                        'nama' => $product->satuan->nama_satuan
                    ] : null
                ];
            });

        return response()->json($products);
    }

    /**
     * Search materials for autocomplete
     */
    public function searchMaterials(Request $request)
    {
        $search = $request->get('q', '');
        $outlet_id = $request->get('outlet_id');
        
        $materials = Bahan::where('nama_bahan', 'like', "%{$search}%")
            ->when($outlet_id, function($query) use ($outlet_id) {
                $query->where('id_outlet', $outlet_id);
            })
            ->with('satuan')
            ->limit(10)
            ->get()
            ->map(function($material) {
                return [
                    'id' => $material->id_bahan,
                    'nama' => $material->nama_bahan,
                    'kode' => $material->kode_bahan,
                    'satuan_id' => $material->id_satuan,
                    'satuan' => $material->satuan ? [
                        'id' => $material->satuan->id_satuan,
                        'nama' => $material->satuan->nama_satuan
                    ] : null
                ];
            });

        return response()->json($materials);
    }

    /**
     * Get outlets for dropdown
     */
    public function getOutlets()
    {
        $outlets = Outlet::select('id_outlet as id', 'nama_outlet as nama', 'kode_outlet as kode')->get();
        return response()->json($outlets);
    }

    /**
     * Get suppliers for dropdown
     */
    public function getSuppliers(Request $request)
    {
        try {
            // Check if Supplier model exists and table exists
            if (class_exists('\App\Models\Supplier') && \Schema::hasTable('supplier')) {
                $query = \App\Models\Supplier::select('id_supplier as id', 'nama')
                    ->where('is_active', true);
                
                // Filter by outlet if provided
                if ($request->filled('outlet_id')) {
                    $query->where('id_outlet', $request->outlet_id);
                }
                
                $suppliers = $query->orderBy('nama')->get();
                return response()->json($suppliers);
            }
            
            // Fallback to empty array if Supplier model or table doesn't exist
            return response()->json([]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::warning('Error loading suppliers: ' . $e->getMessage());
            
            // Return empty array to prevent frontend errors
            return response()->json([]);
        }
    }

    /**
     * Get accounting books for dropdown
     */
    public function getBooks(Request $request)
    {
        try {
            // Check if AccountingBook model exists and table exists
            if (class_exists('\App\Models\AccountingBook') && \Schema::hasTable('accounting_books')) {
                $query = \App\Models\AccountingBook::select('id', 'name as nama')
                    ->where('status', 'active');
                
                // Filter by outlet if provided
                if ($request->filled('outlet_id')) {
                    $query->where('outlet_id', $request->outlet_id);
                }
                
                $books = $query->orderBy('name')->get();
                return response()->json($books);
            }
            
            // Fallback to empty array if AccountingBook model or table doesn't exist
            return response()->json([]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::warning('Error loading accounting books: ' . $e->getMessage());
            
            // Return empty array to prevent frontend errors
            return response()->json([]);
        }
    }

    /**
     * Get asset accounts for dropdown (hierarchical structure)
     */
    public function getAssetAccounts(Request $request)
    {
        try {
            // Check if ChartOfAccount model exists and table exists
            if (class_exists('\App\Models\ChartOfAccount') && \Schema::hasTable('chart_of_accounts')) {
                $query = \App\Models\ChartOfAccount::select('id', 'code', 'name', 'parent_id', 'level')
                    ->where('type', 'asset')
                    ->where('status', 'active');
                
                // Filter by outlet if provided
                if ($request->filled('outlet_id')) {
                    $query->where('outlet_id', $request->outlet_id);
                }
                
                $accounts = $query->orderBy('code')->get();
                
                // Build hierarchical structure
                $hierarchicalAccounts = [];
                
                // First, get all parent accounts (accounts without parent_id)
                $parentAccounts = $accounts->whereNull('parent_id');
                
                foreach ($parentAccounts as $parent) {
                    // Check if this parent has children
                    $children = $accounts->where('parent_id', $parent->id);
                    
                    if ($children->count() > 0) {
                        // Add parent as disabled option
                        $hierarchicalAccounts[] = [
                            'id' => $parent->id,
                            'code' => $parent->code,
                            'name' => $parent->name,
                            'level' => 0,
                            'disabled' => true,
                            'is_parent' => true
                        ];
                        
                        // Add children as selectable options with indentation
                        foreach ($children as $child) {
                            $hierarchicalAccounts[] = [
                                'id' => $child->id,
                                'code' => $child->code,
                                'name' => $child->name,
                                'level' => 1,
                                'disabled' => false,
                                'is_parent' => false
                            ];
                        }
                    } else {
                        // Parent without children, add as selectable
                        $hierarchicalAccounts[] = [
                            'id' => $parent->id,
                            'code' => $parent->code,
                            'name' => $parent->name,
                            'level' => 0,
                            'disabled' => false,
                            'is_parent' => false
                        ];
                    }
                }
                
                return response()->json($hierarchicalAccounts);
            }
            
            // Fallback to empty array if ChartOfAccount model or table doesn't exist
            return response()->json([]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::warning('Error loading asset accounts: ' . $e->getMessage());
            
            // Return empty array to prevent frontend errors
            return response()->json([]);
        }
    }

    /**
     * Get expense accounts for dropdown (hierarchical structure)
     */
    public function getExpenseAccounts(Request $request)
    {
        try {
            if (class_exists('\App\Models\ChartOfAccount') && \Schema::hasTable('chart_of_accounts')) {
                $query = \App\Models\ChartOfAccount::select('id', 'code', 'name', 'parent_id', 'level')
                    ->where('type', 'expense')
                    ->where('status', 'active');
                
                if ($request->filled('outlet_id')) {
                    $query->where('outlet_id', $request->outlet_id);
                }
                
                $accounts = $query->orderBy('code')->get();
                return response()->json($this->buildHierarchicalStructure($accounts));
            }
            
            return response()->json([]);
        } catch (\Exception $e) {
            \Log::warning('Error loading expense accounts: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get accumulated depreciation accounts for dropdown (hierarchical structure)
     */
    public function getAccumulatedDepreciationAccounts(Request $request)
    {
        try {
            if (class_exists('\App\Models\ChartOfAccount') && \Schema::hasTable('chart_of_accounts')) {
                $query = \App\Models\ChartOfAccount::select('id', 'code', 'name', 'parent_id', 'level')
                    ->where('type', 'asset')
                    ->where('status', 'active')
                    ->where(function($q) {
                        $q->where('name', 'like', '%akumulasi%')
                          ->orWhere('name', 'like', '%penyusutan%')
                          ->orWhere('code', 'like', '18%'); // Common code pattern for accumulated depreciation
                    });
                
                if ($request->filled('outlet_id')) {
                    $query->where('outlet_id', $request->outlet_id);
                }
                
                $accounts = $query->orderBy('code')->get();
                return response()->json($this->buildHierarchicalStructure($accounts));
            }
            
            return response()->json([]);
        } catch (\Exception $e) {
            \Log::warning('Error loading accumulated depreciation accounts: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get payment accounts for dropdown (hierarchical structure)
     */
    public function getPaymentAccounts(Request $request)
    {
        try {
            if (class_exists('\App\Models\ChartOfAccount') && \Schema::hasTable('chart_of_accounts')) {
                $query = \App\Models\ChartOfAccount::select('id', 'code', 'name', 'parent_id', 'level')
                    ->where('type', 'asset')
                    ->where('status', 'active')
                    ->where(function($q) {
                        $q->where('name', 'like', '%kas%')
                          ->orWhere('name', 'like', '%bank%')
                          ->orWhere('code', 'like', '10%'); // Common code pattern for cash/bank accounts
                    });
                
                if ($request->filled('outlet_id')) {
                    $query->where('outlet_id', $request->outlet_id);
                }
                
                $accounts = $query->orderBy('code')->get();
                return response()->json($this->buildHierarchicalStructure($accounts));
            }
            
            return response()->json([]);
        } catch (\Exception $e) {
            \Log::warning('Error loading payment accounts: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get all accounts for journal entries (hierarchical structure)
     */
    public function getJournalAccounts(Request $request)
    {
        try {
            if (class_exists('\App\Models\ChartOfAccount') && \Schema::hasTable('chart_of_accounts')) {
                $query = \App\Models\ChartOfAccount::select('id', 'code', 'name', 'parent_id', 'level', 'type')
                    ->where('status', 'active');
                
                if ($request->filled('outlet_id')) {
                    $query->where('outlet_id', $request->outlet_id);
                }
                
                $accounts = $query->orderBy('type')->orderBy('code')->get();
                return response()->json($this->buildHierarchicalStructure($accounts));
            }
            
            return response()->json([]);
        } catch (\Exception $e) {
            \Log::warning('Error loading journal accounts: ' . $e->getMessage());
            return response()->json([]);
        }
    }
    private function buildHierarchicalStructure($accounts)
    {
        $hierarchicalAccounts = [];
        
        // Get all parent accounts (accounts without parent_id)
        $parentAccounts = $accounts->whereNull('parent_id');
        
        foreach ($parentAccounts as $parent) {
            // Check if this parent has children
            $children = $accounts->where('parent_id', $parent->id);
            
            if ($children->count() > 0) {
                // Add parent as disabled option
                $hierarchicalAccounts[] = [
                    'id' => $parent->id,
                    'code' => $parent->code,
                    'name' => $parent->name,
                    'level' => 0,
                    'disabled' => true,
                    'is_parent' => true
                ];
                
                // Add children as selectable options with indentation
                foreach ($children as $child) {
                    $hierarchicalAccounts[] = [
                        'id' => $child->id,
                        'code' => $child->code,
                        'name' => $child->name,
                        'level' => 1,
                        'disabled' => false,
                        'is_parent' => false
                    ];
                }
            } else {
                // Parent without children, add as selectable
                $hierarchicalAccounts[] = [
                    'id' => $parent->id,
                    'code' => $parent->code,
                    'name' => $parent->name,
                    'level' => 0,
                    'disabled' => false,
                    'is_parent' => false
                ];
            }
        }
        
        return $hierarchicalAccounts;
    }
    
    private function createPurchaseOrder($permintaan, $supplierId)
    {
        try {
            // Create Purchase Order
            $po = new \App\Models\PurchaseOrder();
            $po->no_po = \App\Models\PurchaseOrder::generateDraftNumber();
            $po->tanggal = now();
            $po->id_supplier = $supplierId;
            $po->id_outlet = $permintaan->outlet_id;
            $po->id_user = Auth::id();
            $po->status = 'permintaan_pembelian'; // Changed from 'draft' to 'permintaan_pembelian'
            $po->keterangan = "Dibuat dari Permintaan Barang: {$permintaan->nomor_permintaan} - {$permintaan->judul}";
            
            // Calculate totals from items
            $subtotal = 0;
            $poItems = [];
            
            foreach ($permintaan->items as $item) {
                $itemSubtotal = $item->qty * ($item->estimasi_harga ?? 0);
                $subtotal += $itemSubtotal;
                
                $poItems[] = [
                    'tipe_item' => $item->tipe_item,
                    'id_produk' => $item->produk_id,
                    'id_bahan' => $item->bahan_id,
                    'deskripsi' => $item->nama_item,
                    'keterangan' => $item->spesifikasi ?? $item->catatan,
                    'kuantitas' => $item->qty,
                    'satuan' => $item->satuan,
                    'harga' => $item->estimasi_harga ?? 0,
                    'diskon' => 0,
                    'subtotal' => $itemSubtotal
                ];
            }
            
            $po->subtotal = $subtotal;
            $po->total_diskon = 0;
            $po->total = $subtotal;
            $po->total_dibayar = 0;
            $po->sisa_pembayaran = $subtotal;
            $po->save();
            
            // Create PO Items
            foreach ($poItems as $itemData) {
                $poItem = new \App\Models\PurchaseOrderItem();
                $poItem->id_purchase_order = $po->id_purchase_order;
                $poItem->tipe_item = $itemData['tipe_item'];
                $poItem->id_produk = $itemData['id_produk'];
                $poItem->id_bahan = $itemData['id_bahan'];
                $poItem->deskripsi = $itemData['deskripsi'];
                $poItem->keterangan = $itemData['keterangan'];
                $poItem->kuantitas = $itemData['kuantitas'];
                $poItem->satuan = $itemData['satuan'];
                $poItem->harga = $itemData['harga'];
                $poItem->diskon = $itemData['diskon'];
                $poItem->subtotal = $itemData['subtotal'];
                $poItem->save();
            }
            
            return [
                'success' => true,
                'message' => 'Purchase Order berhasil dibuat dengan status Permintaan Pembelian',
                'po_number' => $po->no_po,
                'po_id' => $po->id_purchase_order,
                'status' => $po->status,
                'total_items' => count($poItems),
                'total_amount' => $po->total
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error creating Purchase Order: ' . $e->getMessage(), [
                'permintaan_id' => $permintaan->id,
                'supplier_id' => $supplierId,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Gagal membuat Purchase Order: ' . $e->getMessage());
        }
    }

    private function createFixedAsset($permintaan, $request)
    {
        try {
            // Create Fixed Asset
            $fixedAsset = new \App\Models\FixedAsset();
            $fixedAsset->code = \App\Models\FixedAsset::generateCode($permintaan->outlet_id);
            $fixedAsset->name = $request->asset_name;
            $fixedAsset->category = $request->asset_category;
            $fixedAsset->location = $request->asset_location;
            $fixedAsset->acquisition_date = $request->acquisition_date;
            $fixedAsset->acquisition_cost = $request->acquisition_cost;
            $fixedAsset->salvage_value = $request->salvage_value ?? 0;
            $fixedAsset->useful_life = $request->useful_life;
            $fixedAsset->depreciation_method = $request->depreciation_method;
            $fixedAsset->description = $request->asset_description;
            $fixedAsset->outlet_id = $permintaan->outlet_id;
            $fixedAsset->book_id = $request->book_id;
            $fixedAsset->asset_account_id = $request->asset_account_id;
            $fixedAsset->depreciation_expense_account_id = $request->depreciation_expense_account_id;
            $fixedAsset->accumulated_depreciation_account_id = $request->accumulated_depreciation_account_id;
            $fixedAsset->payment_account_id = $request->payment_account_id;
            $fixedAsset->status = 'draft'; // Set as draft status
            $fixedAsset->book_value = $request->acquisition_cost; // Initial book value equals acquisition cost
            $fixedAsset->accumulated_depreciation = 0; // Start with zero depreciation
            $fixedAsset->created_by = Auth::id();
            $fixedAsset->save();
            
            return [
                'success' => true,
                'message' => 'Draft Aktiva Tetap berhasil dibuat',
                'asset_code' => $fixedAsset->code,
                'asset_id' => $fixedAsset->id,
                'asset_name' => $fixedAsset->name,
                'status' => $fixedAsset->status,
                'acquisition_cost' => $fixedAsset->acquisition_cost
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error creating Fixed Asset: ' . $e->getMessage(), [
                'permintaan_id' => $permintaan->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Gagal membuat Aktiva Tetap: ' . $e->getMessage());
        }
    }

    private function createJournalEntry($permintaan, $request)
    {
        try {
            // Generate transaction number
            $transactionNumber = \App\Models\JournalEntry::generateTransactionNumber($request->book_id);
            
            // Calculate totals
            $totalDebit = collect($request->journal_entries)->sum('debit');
            $totalCredit = collect($request->journal_entries)->sum('credit');
            
            // Create Journal Entry with actual database fields
            $journalEntry = \App\Models\JournalEntry::create([
                'book_id' => $request->book_id,
                'outlet_id' => $permintaan->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $request->journal_date,
                'description' => $request->journal_description,
                'status' => 'draft',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'notes' => "Dibuat dari Permintaan Barang: {$permintaan->nomor_permintaan} - {$permintaan->judul}",
                'reference_type' => 'permintaan_barang',
                'reference_number' => $permintaan->nomor_permintaan
            ]);
            
            // Create Journal Entry Details
            foreach ($request->journal_entries as $entryData) {
                \App\Models\JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $entryData['account_id'],
                    'debit' => floatval($entryData['debit'] ?? 0),
                    'credit' => floatval($entryData['credit'] ?? 0),
                    'description' => $entryData['description'] ?? $request->journal_description,
                    'reference_type' => 'permintaan_barang',
                    'reference_number' => $permintaan->nomor_permintaan
                ]);
            }
            
            return [
                'success' => true,
                'message' => 'Jurnal umum berhasil dibuat',
                'journal_id' => $journalEntry->id,
                'transaction_number' => $journalEntry->transaction_number,
                'reference_number' => $journalEntry->reference_number,
                'journal_date' => $journalEntry->transaction_date,
                'total_debit' => $journalEntry->total_debit,
                'total_credit' => $journalEntry->total_credit,
                'entries_count' => count($request->journal_entries),
                'status' => $journalEntry->status
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error creating Journal Entry: ' . $e->getMessage(), [
                'permintaan_id' => $permintaan->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Gagal membuat Jurnal Umum: ' . $e->getMessage());
        }
    }
}
