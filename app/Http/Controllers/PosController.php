<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Produk;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Piutang;
use App\Models\SettingCOAPos;
use App\Services\JournalEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HasCompanySettings;

class PosController extends Controller
{
    use \App\Traits\HasOutletFilter;
    use HasCompanySettings;

    protected $journalService;

    public function __construct(JournalEntryService $journalService)
    {
        $this->middleware('auth');
        $this->journalService = $journalService;
    }

    /**
     * Display POS interface
     */
    public function index(Request $request)
    {
        // Get only outlets that user has access to
        $outlets = $this->getUserOutlets()->where('is_active', true);
        
        // Ambil outlet pertama yang tersedia jika tidak ada parameter outlet_id
        $defaultOutlet = $outlets->first()->id_outlet ?? null;
        
        if (!$defaultOutlet) {
            // User has no outlet access
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke outlet manapun. Silakan hubungi administrator.');
        }
        
        $selectedOutlet = $request->get('outlet_id', $defaultOutlet);
        
        // Pastikan outlet yang dipilih ada dalam daftar outlet yang dapat diakses user
        if (!$outlets->where('id_outlet', $selectedOutlet)->first()) {
            $selectedOutlet = $defaultOutlet;
        }
        
        // Debug logging
        Log::info('POS Index called', [
            'request_outlet_id' => $request->get('outlet_id'),
            'default_outlet' => $defaultOutlet,
            'selected_outlet' => $selectedOutlet,
            'available_outlets' => $outlets->pluck('nama_outlet', 'id_outlet')->toArray(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email
        ]);
        
        return view('admin.penjualan.pos.index', compact('selectedOutlet', 'outlets'));
    }

    /**
     * Get products for POS - FIXED VERSION WITH AUTH CHECK
     * Perbaikan untuk memastikan produk muncul saat halaman pertama kali dibuka
     */
    public function getProducts(Request $request)
    {
        // Enhanced authentication check
        try {
            $user = auth()->user();
            if (!$user) {
                Log::warning('POS getProducts: No authenticated user found', [
                    'session_id' => session()->getId(),
                    'request_ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login again.',
                    'data' => [],
                    'count' => 0
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error('POS getProducts: Authentication check failed', [
                'error' => $e->getMessage(),
                'session_id' => session()->getId(),
                'request_ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Authentication error. Please login again.',
                'data' => [],
                'count' => 0
            ], 401);
        }

        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        // Validate outlet access for non-superadmin users
        if (!$this->hasOutletAccess($outletId)) {
            Log::warning('POS getProducts: Outlet access denied', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'requested_outlet_id' => $outletId,
                'accessible_outlets' => $this->getUserOutletIds()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini.',
                'data' => [],
                'count' => 0
            ], 403);
        }
        
        // Log authentication info
        Log::info('POS getProducts called', [
            'outlet_id' => $outletId,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown'
        ]);
        
        try {
            // Cache key untuk products per outlet
            $cacheKey = "pos_products_fixed_outlet_{$outletId}";
            
            // Disable cache untuk debugging - akan di-enable kembali setelah fix
            // $products = Cache::remember($cacheKey, 300, function() use ($outletId) {
                
                // Debug: Cek apakah ada produk di outlet ini
                $totalProducts = DB::table('produk')->where('id_outlet', $outletId)->count();
                $activeProducts = DB::table('produk')->where('id_outlet', $outletId)->where('is_active', 1)->count();
                
                Log::info('POS products debug', [
                    'outlet_id' => $outletId,
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts
                ]);
                
                // Simplified query - hapus GROUP BY yang bermasalah
                $rawProducts = DB::select("
                    SELECT 
                        p.id_produk,
                        p.kode_produk as sku,
                        p.nama_produk as name,
                        p.harga_jual as price,
                        COALESCE(k.nama_kategori, 'Barang') as category,
                        COALESCE(s.nama_satuan, 'pcs') as satuan,
                        COALESCE(
                            (SELECT SUM(hpp.stok) FROM hpp_produk hpp WHERE hpp.id_produk = p.id_produk), 
                            0
                        ) as stock,
                        (SELECT pi.path FROM product_images pi WHERE pi.id_produk = p.id_produk AND pi.is_primary = 1 LIMIT 1) as image_path
                    FROM produk p
                    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                    LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
                    WHERE p.id_outlet = ? 
                    AND p.is_active = 1
                    ORDER BY p.nama_produk
                ", [$outletId]);
                
                Log::info('POS products query result', [
                    'count' => count($rawProducts), 
                    'outlet_id' => $outletId,
                    'sample_product' => count($rawProducts) > 0 ? $rawProducts[0] : null
                ]);
                
                // Convert to array format yang diharapkan frontend
                $products = array_map(function($product) {
                    return [
                        'id_produk' => (int) $product->id_produk,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'category' => $product->category,
                        'price' => (float) $product->price,
                        'stock' => (float) $product->stock,
                        'satuan' => $product->satuan,
                        'image' => $product->image_path ? config('app.url'). Storage::url($product->image_path) : null,
                    ];
                }, $rawProducts);
                
            // });
            
            Log::info('POS products returned', ['count' => count($products), 'outlet_id' => $outletId]);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'count' => count($products),
                'performance' => 'fixed',
                'outlet_id' => $outletId,
                'authenticated' => true,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('POS getProducts error', [
                'outlet_id' => $outletId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat produk: ' . $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get customers for POS - SMART OUTLET FILTERING
     * Returns customers with smart outlet filtering that actually filters by outlet
     */
    public function getCustomers(Request $request)
    {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        Log::info('POS getCustomers called', [
            'outlet_id' => $outletId,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown',
            'request_params' => $request->all()
        ]);
        
        // Validate outlet access
        if (!$this->hasOutletAccess($outletId)) {
            Log::warning('POS getCustomers: Outlet access denied', [
                'user_id' => auth()->id(),
                'requested_outlet_id' => $outletId,
                'accessible_outlets' => $this->getUserOutletIds()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini.',
                'data' => []
            ], 403);
        }

        try {
            // Cache per outlet dengan smart filtering
            $cacheKey = "pos_customers_smart_{$outletId}_v5";
            
            $customers = Cache::remember($cacheKey, 60, function() use ($outletId) {
                Log::info('POS getCustomers: Building smart customer list', ['outlet_id' => $outletId]);
                
                // Strategy: Always filter by outlet, but create global customers if none exist
                
                // First, get customers for this specific outlet
                $outletCustomers = Member::select('id_member', 'nama', 'telepon', 'id_tipe', 'id_outlet')
                    ->with('tipe:id_tipe,nama_tipe')
                    ->where('id_outlet', $outletId)
                    ->orderBy('nama')
                    ->get();
                
                // Then, get global customers (id_outlet = null)
                $globalCustomers = Member::select('id_member', 'nama', 'telepon', 'id_tipe', 'id_outlet')
                    ->with('tipe:id_tipe,nama_tipe')
                    ->whereNull('id_outlet')
                    ->orderBy('nama')
                    ->get();
                
                // If no customers for this outlet and no global customers, 
                // create some "virtual global" customers from other outlets (limited)
                if ($outletCustomers->isEmpty() && $globalCustomers->isEmpty()) {
                    Log::info('POS getCustomers: No outlet or global customers, using limited fallback');
                    
                    // Get a few customers from other outlets as fallback
                    $fallbackCustomers = Member::select('id_member', 'nama', 'telepon', 'id_tipe', 'id_outlet')
                        ->with('tipe:id_tipe,nama_tipe')
                        ->where('id_outlet', '!=', $outletId)
                        ->orderBy('nama')
                        ->limit(10) // Limited fallback
                        ->get();
                    
                    $allCustomers = $fallbackCustomers;
                    $strategy = 'limited_fallback';
                } else {
                    // Combine outlet customers + global customers
                    $allCustomers = $outletCustomers->concat($globalCustomers);
                    $strategy = 'outlet_plus_global';
                }
                
                $customerArray = $allCustomers->map(function($customer) use ($outletId) {
                    return [
                        'id' => $customer->id_member,
                        'name' => $customer->nama,
                        'telepon' => $customer->telepon,
                        'piutang' => 0,
                        'id_tipe' => $customer->id_tipe,
                        'tipe_name' => $customer->tipe ? $customer->tipe->nama_tipe : null,
                        'id_outlet' => $customer->id_outlet,
                        'is_global' => $customer->id_outlet === null,
                        'is_current_outlet' => $customer->id_outlet == $outletId,
                        'outlet_badge' => $customer->id_outlet === null ? 'Global' : 
                                        ($customer->id_outlet == $outletId ? 'Outlet ini' : "Outlet {$customer->id_outlet}")
                    ];
                })->toArray();
                
                // Debug: Count customers by type
                $outletSpecific = collect($customerArray)->where('id_outlet', $outletId)->count();
                $globalCustomers = collect($customerArray)->whereNull('id_outlet')->count();
                $fallbackCustomers = collect($customerArray)->where('id_outlet', '!=', $outletId)->whereNotNull('id_outlet')->count();
                
                Log::info('POS getCustomers: Smart customer query completed', [
                    'outlet_id' => $outletId,
                    'total_customers' => count($customerArray),
                    'outlet_specific' => $outletSpecific,
                    'global_customers' => $globalCustomers,
                    'fallback_customers' => $fallbackCustomers,
                    'strategy' => $strategy,
                    'sample' => count($customerArray) > 0 ? $customerArray[0] : null
                ]);
                
                return $customerArray;
            });

            Log::info('POS getCustomers: Smart response prepared', [
                'outlet_id' => $outletId,
                'customers_count' => count($customers),
                'user_id' => auth()->id(),
                'cache_key' => $cacheKey
            ]);

            return response()->json([
                'success' => true,
                'data' => $customers,
                'outlet_id' => $outletId,
                'count' => count($customers),
                'debug' => 'smart_outlet_filter_v5',
                'timestamp' => now()->toISOString(),
                'filter_info' => [
                    'outlet_specific' => collect($customers)->where('id_outlet', $outletId)->count(),
                    'global_customers' => collect($customers)->whereNull('id_outlet')->count(),
                    'fallback_customers' => collect($customers)->where('id_outlet', '!=', $outletId)->whereNotNull('id_outlet')->count(),
                    'strategy' => collect($customers)->where('id_outlet', '!=', $outletId)->whereNotNull('id_outlet')->count() > 0 ? 'limited_fallback' : 'outlet_plus_global'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('POS getCustomers error', [
                'outlet_id' => $outletId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat customer: ' . $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get product prices for customer type
     */
    public function getCustomerTypePrices(Request $request)
    {
        $idTipe = $request->get('id_tipe');
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        // Validate outlet access for non-superadmin users
        if (!$this->hasOutletAccess($outletId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini.',
                'data' => []
            ], 403);
        }
        
        if (!$idTipe) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            // Get produk_tipe with diskon and harga_jual
            $produkTipe = \App\Models\ProdukTipe::where('id_tipe', $idTipe)
                ->with('produk:id_produk,kode_produk,harga_jual')
                ->get()
                ->map(function($pt) {
                    $hargaFinal = $pt->harga_jual; // Harga jual khusus
                    
                    // Jika tidak ada harga jual khusus, hitung dari diskon
                    if (!$hargaFinal || $hargaFinal == 0) {
                        $hargaNormal = $pt->produk->harga_jual;
                        $diskon = $pt->diskon ?? 0;
                        $hargaFinal = $hargaNormal * (1 - $diskon / 100);
                    }
                    
                    return [
                        'id_produk' => $pt->id_produk,
                        'sku' => $pt->produk->kode_produk,
                        'harga_normal' => $pt->produk->harga_jual,
                        'diskon' => $pt->diskon ?? 0,
                        'harga_khusus' => $pt->harga_jual,
                        'harga_final' => $hargaFinal
                    ];
                })
                ->keyBy('id_produk')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $produkTipe
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting customer type prices: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil harga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store POS transaction
     */
    public function store(Request $request)
    {
        // Enhanced logging untuk debugging validasi
        Log::info('POS Store Request Started', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown',
            'request_data' => $request->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'nullable|exists:produk,id_produk',
            'items.*.nama_produk' => 'required|string',
            'items.*.sku' => 'nullable|string',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe' => 'required|in:produk,jasa',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'jenis_pembayaran' => 'required|in:cash,transfer,qris',
            'is_bon' => 'required|boolean',
            'due_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->tanggal) {
                        $transactionDate = \Carbon\Carbon::parse($request->tanggal)->startOfDay();
                        $dueDate = \Carbon\Carbon::parse($value)->startOfDay();
                        
                        if ($dueDate->lt($transactionDate)) {
                            $fail('Tanggal jatuh tempo harus sama atau setelah tanggal transaksi (' . $transactionDate->format('d/m/Y') . ').');
                        }
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            Log::warning('POS Store Validation Failed', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email ?? 'unknown',
                'validation_errors' => $validator->errors()->toArray(),
                'request_data' => $request->all(),
                'failed_rules' => $validator->failed()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
                'debug_info' => [
                    'failed_fields' => array_keys($validator->errors()->toArray()),
                    'request_summary' => [
                        'tanggal' => $request->tanggal,
                        'id_outlet' => $request->id_outlet,
                        'items_count' => is_array($request->items) ? count($request->items) : 0,
                        'subtotal' => $request->subtotal,
                        'total' => $request->total,
                        'jenis_pembayaran' => $request->jenis_pembayaran,
                        'is_bon' => $request->is_bon
                    ]
                ]
            ], 422);
        }

        Log::info('POS Store Validation Passed', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown'
        ]);

        // Validate outlet access for non-superadmin users
        $outletId = $request->id_outlet;
        if (!$this->hasOutletAccess($outletId)) {
            Log::warning('POS store: Outlet access denied', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'requested_outlet_id' => $outletId,
                'accessible_outlets' => $this->getUserOutletIds(),
                'is_superadmin' => auth()->user()->hasRole('super_admin')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini.',
                'errors' => [
                    'id_outlet' => ['Anda tidak memiliki akses ke outlet ini.']
                ],
                'debug_info' => [
                    'requested_outlet' => $outletId,
                    'accessible_outlets' => $this->getUserOutletIds()
                ]
            ], 422);
        }

        Log::info('POS Store Outlet Access Validated', [
            'user_id' => auth()->id(),
            'outlet_id' => $outletId
        ]);

        try {
            Log::info('POS Store Transaction Processing Started', [
                'user_id' => auth()->id(),
                'outlet_id' => $outletId,
                'total_amount' => $request->total,
                'is_bon' => $request->is_bon,
                'items_count' => count($request->items)
            ]);

            $saleData = null;
            
            DB::transaction(function () use ($request, &$saleData) {
                $outletId = $request->id_outlet;
                $isBon = $request->is_bon;
                
                Log::info('POS Store DB Transaction Started', [
                    'outlet_id' => $outletId,
                    'is_bon' => $isBon
                ]);
                
                // Generate nomor transaksi
                $noTransaksi = PosSale::generateTransactionNumber($outletId);
                
                Log::info('POS Store Transaction Number Generated', [
                    'transaction_number' => $noTransaksi,
                    'outlet_id' => $outletId
                ]);
                
                // Hitung total
                $subtotal = $request->subtotal;
                $diskonNominal = $request->diskon_nominal ?? 0;
                $diskonPersen = $request->diskon_persen ?? 0;
                $totalDiskon = $diskonNominal;
                
                if ($diskonPersen > 0) {
                    $totalDiskon += ($subtotal * $diskonPersen / 100);
                }
                
                $ppn = $request->ppn ?? 0;
                $total = $subtotal - $totalDiskon + $ppn;
                
                // Create POS Sale
                $posSale = PosSale::create([
                    'no_transaksi' => $noTransaksi,
                    'tanggal' => $request->tanggal,
                    'id_outlet' => $outletId,
                    'id_member' => $request->id_member ?? null,
                    'id_user' => auth()->id(),
                    'subtotal' => $subtotal,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'total_diskon' => $totalDiskon,
                    'ppn' => $ppn,
                    'total' => $total,
                    'jenis_pembayaran' => $request->jenis_pembayaran,
                    'jumlah_bayar' => $isBon ? 0 : $request->jumlah_bayar,
                    'kembalian' => $isBon ? 0 : ($request->jumlah_bayar - $total),
                    'status' => $isBon ? 'menunggu' : 'lunas',
                    'catatan' => $request->catatan,
                    'is_bon' => $isBon,
                ]);

                Log::info('POS Sale Created', [
                    'pos_sale_id' => $posSale->id,
                    'transaction_number' => $noTransaksi,
                    'total' => $total
                ]);

                // Create Penjualan record (untuk kompatibilitas dengan sistem lama)
                $totalItem = 0;
                foreach ($request->items as $item) {
                    if ($item['tipe'] === 'produk') {
                        $totalItem += $item['kuantitas'];
                    }
                }

                $penjualan = Penjualan::create([
                    'id_member' => $request->id_member ?? null,
                    'id_outlet' => $outletId,
                    'total_item' => $totalItem,
                    'total_harga' => $total,
                    'total_diskon' => $totalDiskon,
                    'diskon' => $diskonPersen,
                    'bayar' => $isBon ? 0 : $total,
                    'diterima' => $isBon ? 0 : $request->jumlah_bayar,
                    'id_user' => auth()->id(),
                    'created_at' => $request->tanggal,
                    'updated_at' => $request->tanggal,
                ]);

                // Update POS Sale dengan id_penjualan
                $posSale->update(['id_penjualan' => $penjualan->id_penjualan]);

                // Create POS Sale Items dan kurangi stok
                foreach ($request->items as $item) {
                    PosSaleItem::create([
                        'pos_sale_id' => $posSale->id,
                        'id_produk' => $item['id_produk'] ?? null,
                        'nama_produk' => $item['nama_produk'],
                        'sku' => $item['sku'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? 'pcs',
                        'harga' => $item['harga'],
                        'subtotal' => $item['subtotal'],
                        'tipe' => $item['tipe'],
                    ]);

                    // Kurangi stok dan buat penjualan detail jika produk
                    if ($item['tipe'] === 'produk' && !empty($item['id_produk'])) {
                        $produk = Produk::with('hppProduk')->find($item['id_produk']);
                        if ($produk) {
                            // Kurangi stok menggunakan FIFO
                            try {
                                $produk->reduceStock($item['kuantitas']);
                            } catch (\Exception $e) {
                                throw new \Exception("Gagal mengurangi stok: " . $e->getMessage());
                            }
                            
                            // Hitung HPP
                            $hpp = $produk->calculateHppBarangDagang();
                            
                            // Create penjualan detail
                            PenjualanDetail::create([
                                'id_penjualan' => $penjualan->id_penjualan,
                                'id_produk' => $item['id_produk'],
                                'harga_jual' => $item['harga'],
                                'jumlah' => $item['kuantitas'],
                                'diskon' => $diskonPersen,
                                'subtotal' => $item['subtotal'],
                                'hpp' => $hpp,
                            ]);
                        }
                    }
                }

                // Create Piutang jika bon
                if ($isBon) {
                    // Gunakan tanggal jatuh tempo yang dipilih user, atau default 30 hari jika tidak ada
                    $dueDate = $request->due_date ? $request->due_date : now()->addDays(30)->format('Y-m-d');
                    
                    // Check if piutang already exists for this penjualan to prevent duplicates
                    $existingPiutang = Piutang::where('id_penjualan', $penjualan->id_penjualan)->first();
                    
                    if (!$existingPiutang) {
                        Piutang::create([
                            'id_penjualan' => $penjualan->id_penjualan,
                            'id_member' => $request->id_member ?? null,
                            'id_outlet' => $outletId,
                            'tanggal_tempo' => $request->tanggal,
                            'tanggal_jatuh_tempo' => $dueDate,
                            'piutang' => $total,
                            'jumlah_piutang' => $total,
                            'jumlah_dibayar' => 0,
                            'sisa_piutang' => $total,
                            'status' => 'belum_lunas',
                            'nama' => $request->id_member ? Member::find($request->id_member)->nama : 'Pelanggan Umum',
                        ]);
                        
                        Log::info('Piutang created for BON transaction', [
                            'id_penjualan' => $penjualan->id_penjualan,
                            'total' => $total,
                            'due_date' => $dueDate
                        ]);
                    } else {
                        Log::warning('Piutang already exists for this penjualan', [
                            'id_penjualan' => $penjualan->id_penjualan,
                            'existing_piutang_id' => $existingPiutang->id_piutang
                        ]);
                    }
                }

                // Create journal entry
                $this->createPosJournal($posSale);

                // Store data for return
                $saleData = [
                    'id' => $posSale->id,
                    'no_transaksi' => $noTransaksi,
                    'total' => $total,
                    'kembalian' => $isBon ? 0 : ($request->jumlah_bayar - $total)
                ];

                Log::info('POS transaction created successfully', [
                    'pos_sale_id' => $posSale->id,
                    'no_transaksi' => $noTransaksi,
                    'total' => $total
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi POS berhasil disimpan',
                'data' => $saleData
            ]);

        } catch (\Exception $e) {
            Log::error('POS transaction error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email ?? 'unknown',
                'outlet_id' => $request->id_outlet ?? 'unknown',
                'request_data' => $request->all(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
                'debug_info' => [
                    'error_type' => get_class($e),
                    'error_file' => basename($e->getFile()),
                    'error_line' => $e->getLine(),
                    'transaction_data' => [
                        'outlet_id' => $request->id_outlet ?? 'unknown',
                        'total' => $request->total ?? 'unknown',
                        'items_count' => is_array($request->items) ? count($request->items) : 0
                    ]
                ]
            ], 500);
        }
    }

    /**
     * Create journal entry for POS transaction
     */
    private function createPosJournal($posSale)
    {
        try {
            $setting = SettingCOAPos::getByOutlet($posSale->id_outlet);
            
            if (!$setting || !$setting->accounting_book_id) {
                Log::info('Setting COA POS belum diatur untuk outlet ' . $posSale->id_outlet);
                return null;
            }

            $customerName = $posSale->member ? $posSale->member->nama : 'Pelanggan Umum';
            $description = "POS {$posSale->no_transaksi} - {$customerName}";
            $entries = [];

            // Hitung total HPP
            $totalHpp = 0;
            foreach ($posSale->items as $item) {
                if ($item->tipe === 'produk' && $item->id_produk) {
                    $produk = Produk::find($item->id_produk);
                    if ($produk) {
                        $hpp = $produk->calculateHppBarangDagang();
                        $totalHpp += $hpp * $item->kuantitas;
                    }
                }
            }

            // Hitung pendapatan bersih (tanpa PPN)
            $pendapatanBersih = $posSale->subtotal - $posSale->total_diskon;
            $ppnAmount = $posSale->ppn;

            if ($posSale->is_bon) {
                // Bon (Piutang): Piutang Usaha (D) vs Pendapatan Penjualan (K) + PPN (K)
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $posSale->id_outlet),
                    'debit' => $posSale->total,
                    'credit' => 0,
                    'memo' => 'Piutang usaha dari ' . $customerName
                ];
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $posSale->id_outlet),
                    'debit' => 0,
                    'credit' => $pendapatanBersih,
                    'memo' => 'Pendapatan penjualan POS'
                ];
                
                // Pisahkan PPN jika ada dan akun PPN sudah diset
                if ($ppnAmount > 0 && !empty($setting->akun_ppn)) {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_ppn, $posSale->id_outlet),
                        'debit' => 0,
                        'credit' => $ppnAmount,
                        'memo' => 'PPN 10% dari penjualan'
                    ];
                }
            } else {
                // Cash/Transfer: Kas/Bank (D) vs Pendapatan Penjualan (K) + PPN (K)
                $akunKasBank = $posSale->jenis_pembayaran === 'cash' 
                    ? $this->getAccountIdByCode($setting->akun_kas, $posSale->id_outlet)
                    : $this->getAccountIdByCode($setting->akun_bank, $posSale->id_outlet);

                $entries[] = [
                    'account_id' => $akunKasBank,
                    'debit' => $posSale->total,
                    'credit' => 0,
                    'memo' => 'Penerimaan kas dari penjualan POS'
                ];
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $posSale->id_outlet),
                    'debit' => 0,
                    'credit' => $pendapatanBersih,
                    'memo' => 'Pendapatan penjualan POS'
                ];
                
                // Pisahkan PPN jika ada dan akun PPN sudah diset
                if ($ppnAmount > 0 && !empty($setting->akun_ppn)) {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_ppn, $posSale->id_outlet),
                        'debit' => 0,
                        'credit' => $ppnAmount,
                        'memo' => 'PPN 10% dari penjualan'
                    ];
                }
            }

            // Tambahkan jurnal HPP dan persediaan jika ada produk
            if ($totalHpp > 0 && !empty($setting->akun_hpp) && !empty($setting->akun_persediaan)) {
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_hpp, $posSale->id_outlet),
                    'debit' => $totalHpp,
                    'credit' => 0,
                    'memo' => 'HPP penjualan POS'
                ];
                $entries[] = [
                    'account_id' => $this->getAccountIdByCode($setting->akun_persediaan, $posSale->id_outlet),
                    'debit' => 0,
                    'credit' => $totalHpp,
                    'memo' => 'Pengurangan persediaan'
                ];
            }

            return $this->journalService->createAutomaticJournal(
                'pos',
                $posSale->id,
                $posSale->tanggal,
                $description,
                $entries,
                $setting->accounting_book_id,
                $posSale->id_outlet
            );

        } catch (\Exception $e) {
            Log::error('Gagal membuat jurnal POS: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get account ID by code
     */
    private function getAccountIdByCode(string $code, int $outletId): int
    {
        $account = \App\Models\ChartOfAccount::where('code', $code)
            ->where('outlet_id', $outletId)
            ->first();
        
        if (!$account) {
            throw new \Exception("Akun dengan kode {$code} tidak ditemukan untuk outlet {$outletId}");
        }
        
        return $account->id;
    }

    /**
     * Get transaction history
     */
    public function history(Request $request)
    {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        // Get only outlets that user has access to
        $outlets = $this->getUserOutlets()->where('is_active', true);
        
        return view('admin.penjualan.pos.history', compact('outletId', 'outlets'));
    }

    /**
     * Get data for DataTable
     * Optimized with eager loading
     */
    public function historyData(Request $request)
    {
        $outletId = $request->get('outlet_id', 'all');
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'newest'); // newest atau oldest

        // Optimized eager loading - hanya load kolom yang diperlukan
        $query = PosSale::select([
                'id', 'no_transaksi', 'tanggal', 'id_outlet', 'id_member', 
                'id_user', 'total', 'status', 'jenis_pembayaran', 'jumlah_bayar', 'is_bon'
            ])
            ->with([
                'outlet:id_outlet,nama_outlet',
                'member:id_member,nama',
                'user:id,name',
                'items:id,pos_sale_id', // Hanya untuk count
                'piutang:id_piutang,id_penjualan,tanggal_jatuh_tempo,sisa_piutang' // Untuk tanggal jatuh tempo
            ])
            ->byOutlet($outletId)
            ->status($status)
            ->dateRange($startDate, $endDate)
            ->when($search, function($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%");
            });

        // Apply sorting
        if ($sortBy === 'oldest') {
            $query->orderBy('tanggal', 'asc');
        } else {
            $query->orderBy('tanggal', 'desc'); // default newest
        }

        // If AJAX request for modal, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            $data = $query->limit(100)->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        // Otherwise return DataTables
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y H:i');
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->outlet ? $row->outlet->nama_outlet : '-';
            })
            ->addColumn('customer_name', function ($row) {
                return $row->member ? $row->member->nama : 'Pelanggan Umum';
            })
            ->addColumn('total_formatted', function ($row) {
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = $row->status === 'lunas' ? 'success' : 'warning';
                $statusText = $row->status === 'lunas' ? 'Lunas' : 'Menunggu';
                return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-' . $badgeClass . '-100 text-' . $badgeClass . '-800">' . $statusText . '</span>';
            })
            ->addColumn('payment_type', function ($row) {
                $types = [
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS'
                ];
                return $types[$row->jenis_pembayaran] ?? $row->jenis_pembayaran;
            })
            ->addColumn('items_count', function ($row) {
                return $row->items->count() . ' item';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                // Removed pos.print route that doesn't exist
                $actions .= '<button onclick="viewDetail(' . $row->id . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class="bx bx-show text-xs"></i> Detail
                </button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show transaction detail
     */
    public function show($id)
    {
        try {
            $posSale = PosSale::with(['outlet', 'member', 'user', 'items.produk'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $posSale
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Print receipt
     */
    public function print($id, Request $request)
    {
        $companySettings = $this->getCompanySettingsForPrint();
        $posSale = PosSale::with(['outlet', 'member', 'user', 'items'])
            ->findOrFail($id);
        
        // Get piutang if bon
        $piutang = null;
        if ($posSale->is_bon && $posSale->id_penjualan) {
            $piutang = Piutang::where('id_penjualan', $posSale->id_penjualan)->first();
        }
        
        // Determine nota type (default: besar)
        $type = $request->get('type', 'besar');
        
        // Generate PDF
        $viewName = $type === 'kecil' ? 'admin.penjualan.pos.nota_kecil' : 'admin.penjualan.pos.nota_besar';
        
        $pdf = Pdf::loadView($viewName, compact('posSale', 'piutang', 'companySettings'))
            ->setPaper('a4', 'portrait');
        
        // Return PDF stream (untuk ditampilkan di modal)
        return $pdf->stream('Nota-POS-' . $posSale->invoice_number . '.pdf');
    }

    /**
     * Get/Update COA Settings
     */
    public function coaSettings(Request $request)
    {
        // Enhanced authentication check
        try {
            $user = auth()->user();
            if (!$user) {
                Log::warning('POS coaSettings: No authenticated user found', [
                    'session_id' => session()->getId(),
                    'request_ip' => $request->ip(),
                    'is_ajax' => $request->ajax()
                ]);
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login again.'
                    ], 401);
                }
                return redirect()->route('login');
            }
        } catch (\Exception $e) {
            Log::error('POS coaSettings: Authentication check failed', [
                'error' => $e->getMessage(),
                'session_id' => session()->getId(),
                'request_ip' => $request->ip()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication error. Please login again.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        if ($request->isMethod('get')) {
            $setting = SettingCOAPos::with('accountingBook')->byOutlet($outletId)->first();
            
            // If AJAX request, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $setting,
                    'authenticated' => true,
                    'user_id' => auth()->id()
                ]);
            }
            
            // Otherwise return view
            $books = \App\Models\AccountingBook::where('outlet_id', $outletId)->get();
            
            // Get all outlets for dropdown - only accessible ones
            $outlets = $this->getUserOutlets()->where('is_active', true);
            
            // Get leaf accounts only (accounts without children) grouped by type
            $allAccounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
                ->orderBy('code')
                ->get();
            
            // Filter only leaf accounts (no children)
            $leafAccounts = $allAccounts->filter(function($account) use ($allAccounts) {
                return !$allAccounts->contains(function($child) use ($account) {
                    return $child->parent_code === $account->code;
                });
            });
            
            // Group by account type
            $accountsByType = [
                'asset' => $leafAccounts->filter(fn($a) => $a->type === 'asset')->values(),
                'liability' => $leafAccounts->filter(fn($a) => $a->type === 'liability')->values(),
                'equity' => $leafAccounts->filter(fn($a) => $a->type === 'equity')->values(),
                'revenue' => $leafAccounts->filter(fn($a) => $a->type === 'revenue')->values(),
                'expense' => $leafAccounts->filter(fn($a) => $a->type === 'expense')->values(),
            ];
            
            return view('admin.penjualan.pos.coa-settings', compact('setting', 'books', 'accountsByType', 'outletId', 'outlets'));
        }
        
        // POST - Update settings
        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'akun_kas' => 'required|string',
            'akun_bank' => 'required|string',
            'akun_piutang_usaha' => 'required|string',
            'akun_pendapatan_penjualan' => 'required|string',
            'akun_hpp' => 'nullable|string',
            'akun_persediaan' => 'nullable|string',
            'akun_ppn' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            SettingCOAPos::updateOrCreateForOutlet($outletId, $request->only([
                'accounting_book_id',
                'akun_kas',
                'akun_bank',
                'akun_piutang_usaha',
                'akun_pendapatan_penjualan',
                'akun_hpp',
                'akun_persediaan',
                'akun_ppn',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Setting COA POS berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear POS customers cache for specific outlet
     * Call this when customers are updated or outlet changes
     */
    public function clearCustomerCache(Request $request)
    {
        $outletId = $request->get('outlet_id', 'all');
        
        if ($outletId === 'all') {
            // Clear cache for all outlets
            for ($i = 1; $i <= 20; $i++) {
                Cache::forget("pos_customers_outlet_{$i}");
            }
            $message = 'All POS customers cache cleared';
        } else {
            // Clear cache for specific outlet
            Cache::forget("pos_customers_outlet_{$outletId}");
            $message = "POS customers cache cleared for outlet {$outletId}";
        }
        
        Log::info('POS customer cache cleared', [
            'outlet_id' => $outletId,
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Warm up POS products cache for all outlets
     * Call this during off-peak hours or after major updates
     */
    public function warmProductsCache()
    {
        $outlets = \App\Models\Outlet::where('is_active', true)->pluck('id_outlet');
        $warmedCount = 0;
        
        foreach ($outlets as $outletId) {
            try {
                // Force cache refresh by calling getProducts
                $request = new Request(['outlet_id' => $outletId]);
                $this->getProducts($request);
                $warmedCount++;
            } catch (\Exception $e) {
                Log::warning("Failed to warm cache for outlet {$outletId}: " . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Cache warmed for {$warmedCount} outlets",
            'outlets_processed' => $warmedCount
        ]);
    }
}