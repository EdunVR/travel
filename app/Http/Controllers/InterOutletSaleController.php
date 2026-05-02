<?php

namespace App\Http\Controllers;

use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;
use App\Models\InterOutletProductPrice;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Member;
use App\Models\SettingCOAInterOutletSale;
use App\Services\JournalEntryService;
use App\Traits\HasOutletFilter;
use App\Traits\HasCompanySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class InterOutletSaleController extends Controller
{
    use HasOutletFilter, HasCompanySettings;

    protected $journalService;

    public function __construct(JournalEntryService $journalService)
    {
        $this->middleware('auth');
        $this->journalService = $journalService;
    }

    /**
     * Display inter outlet sale interface
     */
    public function index(Request $request)
    {
        // Get user's accessible outlets only
        $outlets = $this->getUserOutlets();
        
        // Get selected outlet from request
        $selectedOutlet = $request->get('outlet_id');
        
        // If no accessible outlets, redirect with error
        if ($outlets->isEmpty()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke outlet manapun.');
        }
        
        // Validate selected outlet access if provided
        if ($selectedOutlet && $selectedOutlet !== 'ALL' && is_numeric($selectedOutlet) && !$this->hasOutletAccess((int)$selectedOutlet)) {
            $selectedOutlet = null; // Reset invalid outlet
        }
        
        // Default to first accessible outlet if none selected
        if (!$selectedOutlet || $selectedOutlet === 'ALL') {
            $selectedOutlet = $outlets->first()->id_outlet;
        }
        
        return view('admin.penjualan.inter-outlet.index', compact('selectedOutlet', 'outlets'));
    }

    /**
     * Get products for inter outlet sale
     */
    public function getProducts(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login again.',
                    'data' => [],
                    'count' => 0
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication error. Please login again.',
                'data' => [],
                'count' => 0
            ], 401);
        }

        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        try {
            // Query produk dengan stok, gambar, dan harga inter outlet terpisah
            $rawProducts = DB::select("
                SELECT 
                    p.id_produk,
                    p.kode_produk as sku,
                    p.nama_produk as name,
                    p.harga_jual as regular_price,
                    COALESCE(iopp.inter_outlet_price, 0) as inter_outlet_price,
                    COALESCE(k.nama_kategori, 'Barang') as category,
                    COALESCE(s.nama_satuan, 'pcs') as satuan,
                    COALESCE(
                        (SELECT SUM(hpp.stok) FROM hpp_produk hpp WHERE hpp.id_produk = p.id_produk), 
                        0
                    ) as stock,
                    COALESCE(
                        (SELECT pi.path FROM product_images pi 
                         INNER JOIN produk p2 ON pi.id_produk = p2.id_produk 
                         WHERE p2.kode_produk = p.kode_produk AND pi.is_primary = 1 
                         LIMIT 1),
                        (SELECT pi.path FROM product_images pi WHERE pi.id_produk = p.id_produk LIMIT 1)
                    ) as image_path
                FROM produk p
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
                LEFT JOIN inter_outlet_product_prices iopp ON p.id_produk = iopp.id_produk AND iopp.outlet_id = ?
                WHERE p.id_outlet = ? 
                AND p.is_active = 1
                ORDER BY p.nama_produk
            ", [$outletId, $outletId]);
            
            // Convert to array format yang diharapkan frontend
            $products = array_map(function($product) {
                // Gunakan harga inter outlet jika ada, jika tidak gunakan harga regular
                $displayPrice = $product->inter_outlet_price > 0 ? $product->inter_outlet_price : $product->regular_price;
                
                return [
                    'id_produk' => (int) $product->id_produk,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category' => $product->category,
                    'regular_price' => (float) $product->regular_price,
                    'inter_outlet_price' => (float) $product->inter_outlet_price,
                    'price' => (float) $displayPrice, // Harga yang ditampilkan untuk transaksi
                    'stock' => (float) $product->stock,
                    'satuan' => $product->satuan,
                    'image' => $product->image_path ? config('app.url'). \Illuminate\Support\Facades\Storage::url($product->image_path) : null,
                ];
            }, $rawProducts);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'count' => count($products),
                'outlet_id' => $outletId,
                'authenticated' => true,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Inter outlet sale getProducts error', [
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
     * Get outlets for destination
     */
    public function getOutlets(Request $request)
    {
        $currentOutletId = $request->get('current_outlet_id');
        
        // Get ALL outlets for destination dropdown (tidak dibatasi akses user)
        $outlets = Outlet::where('is_active', true)
            ->when($currentOutletId, function($query) use ($currentOutletId) {
                return $query->where('id_outlet', '!=', $currentOutletId);
            })
            ->orderBy('nama_outlet')
            ->get()
            ->map(function($outlet) {
                return [
                    'id' => $outlet->id_outlet,
                    'name' => $outlet->nama_outlet,
                    'address' => $outlet->alamat
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $outlets
        ]);
    }

    /**
     * Store inter outlet sale transaction
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'outlet_asal' => 'required|exists:outlets,id_outlet',
            'outlet_tujuan' => 'required|exists:outlets,id_outlet|different:outlet_asal',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produk,id_produk',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $saleData = null;
            $maxRetries = 3;
            $attempt = 0;
            
            while ($attempt < $maxRetries) {
                try {
                    DB::transaction(function () use ($request, &$saleData) {
                        $outletAsal = $request->outlet_asal;
                        $outletTujuan = $request->outlet_tujuan;
                        
                        // Generate nomor transaksi with transaction date
                        $noTransaksi = InterOutletSale::generateTransactionNumber($outletAsal, $request->tanggal);
                        
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
                        
                        // Create Inter Outlet Sale
                        $interOutletSale = InterOutletSale::create([
                            'no_transaksi' => $noTransaksi,
                            'tanggal' => $request->tanggal,
                            'outlet_asal' => $outletAsal,
                            'outlet_tujuan' => $outletTujuan,
                            'id_user' => auth()->id(),
                            'subtotal' => $subtotal,
                            'diskon_persen' => $diskonPersen,
                            'diskon_nominal' => $diskonNominal,
                            'total_diskon' => $totalDiskon,
                            'ppn' => $ppn,
                            'total' => $total,
                            'status' => 'approved', // Langsung approved
                            'approved_by' => auth()->id(), // Set approved by
                            'approved_at' => now(), // Set approved at
                            'catatan' => $request->catatan,
                        ]);

                        // Create Inter Outlet Sale Items dengan data HPP dan kurangi stok outlet asal
                        foreach ($request->items as $item) {
                            // Hitung data HPP menggunakan FIFO sebelum mengurangi stok
                            $dataHpp = $this->calculateFifoHppData($item['id_produk'], $item['kuantitas'], $request->tanggal);
                            
                            InterOutletSaleItem::create([
                                'inter_outlet_sale_id' => $interOutletSale->id,
                                'id_produk' => $item['id_produk'],
                                'kuantitas' => $item['kuantitas'],
                                'harga' => $item['harga'],
                                'subtotal' => $item['subtotal'],
                                'data_hpp' => $dataHpp, // Simpan data HPP dalam format JSON
                            ]);

                            // Kurangi stok outlet asal dan tambah stok outlet tujuan
                            $produkAsal = Produk::where('id_produk', $item['id_produk'])
                                ->where('id_outlet', $outletAsal)
                                ->first();
                            
                            if ($produkAsal) {
                                // Kurangi stok outlet asal menggunakan FIFO
                                try {
                                    $produkAsal->reduceStock($item['kuantitas']);
                                } catch (\Exception $e) {
                                    throw new \Exception("Gagal mengurangi stok outlet asal: " . $e->getMessage());
                                }
                                
                                // Cari atau buat produk di outlet tujuan
                                $produkTujuan = Produk::where('kode_produk', $produkAsal->kode_produk)
                                    ->where('id_outlet', $outletTujuan)
                                    ->first();
                                
                                if (!$produkTujuan) {
                                    // Buat produk baru di outlet tujuan
                                    $produkTujuan = $produkAsal->replicate();
                                    $produkTujuan->id_outlet = $outletTujuan;
                                    $produkTujuan->save();
                                }
                                
                                // Tambah stok di outlet tujuan
                                // HPP di outlet tujuan = harga jual dari outlet asal
                                $hppTujuan = floatval($item['harga']); // Harga jual menjadi HPP di outlet tujuan
                                $produkTujuan->addStock($hppTujuan, $item['kuantitas']);
                            }
                        }

                        // Create journal entry
                        $this->createInterOutletSaleJournal($interOutletSale);

                        // Store data for return
                        $saleData = [
                            'id' => $interOutletSale->id,
                            'no_transaksi' => $noTransaksi,
                            'total' => $total
                        ];

                        Log::info('Inter outlet sale transaction created successfully', [
                            'inter_outlet_sale_id' => $interOutletSale->id,
                            'no_transaksi' => $noTransaksi,
                            'total' => $total
                        ]);
                    });
                    
                    // If we reach here, transaction was successful
                    break;
                    
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if it's a duplicate key error
                    if ($e->errorInfo[1] == 1062 && $attempt < $maxRetries - 1) { // MySQL duplicate entry error
                        $attempt++;
                        Log::warning('Duplicate transaction number detected, retrying...', [
                            'attempt' => $attempt,
                            'error' => $e->getMessage()
                        ]);
                        usleep(100000); // Wait 100ms before retry
                        continue;
                    }
                    throw $e; // Re-throw if not duplicate or max retries reached
                }
            }
            
            if ($attempt >= $maxRetries) {
                throw new \Exception('Failed to create transaction after ' . $maxRetries . ' attempts due to duplicate transaction numbers');
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi penjualan antar outlet berhasil disimpan',
                'data' => $saleData
            ]);

        } catch (\Exception $e) {
            Log::error('Inter outlet sale transaction error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate FIFO HPP data for inter-outlet transaction
     * Returns array of HPP data in JSON format for storage
     */
    private function calculateFifoHppData($idProduk, $kuantitas, $transactionDate)
    {
        try {
            // Ambil data HPP yang tersedia pada saat transaksi (berdasarkan tanggal)
            $hppData = \App\Models\HppProduk::where('id_produk', $idProduk)
                ->where('stok', '>', 0)
                ->where('created_at', '<=', $transactionDate)
                ->orderBy('created_at', 'asc') // FIFO
                ->get();
            
            if ($hppData->isEmpty()) {
                Log::warning("Tidak ada data HPP tersedia pada tanggal transaksi untuk produk {$idProduk}", [
                    'id_produk' => $idProduk,
                    'kuantitas' => $kuantitas,
                    'transaction_date' => $transactionDate
                ]);
                
                // Fallback: ambil HPP terdekat setelah tanggal transaksi
                $hppData = \App\Models\HppProduk::where('id_produk', $idProduk)
                    ->where('stok', '>', 0)
                    ->where('created_at', '>', $transactionDate)
                    ->orderBy('created_at', 'asc')
                    ->limit(5)
                    ->get();
                
                if ($hppData->isEmpty()) {
                    Log::error("Tidak ada data HPP sama sekali untuk produk {$idProduk}");
                    return []; // Return empty array jika tidak ada HPP
                }
            }
            
            // Hitung FIFO berdasarkan data HPP yang tersedia
            $dataHppJson = [];
            $remainingQty = floatval($kuantitas);
            
            foreach ($hppData as $hpp) {
                if ($remainingQty <= 0) break;
                
                $usedQty = min($hpp->stok, $remainingQty);
                $remainingQty -= $usedQty;
                
                $dataHppJson[] = [
                    'id_hpp' => $hpp->id,
                    'hpp' => floatval($hpp->hpp),
                    'qty_used' => $usedQty
                ];
            }
            
            if ($remainingQty > 0) {
                Log::warning("Stok HPP tidak mencukupi untuk inter-outlet transaction", [
                    'id_produk' => $idProduk,
                    'kuantitas_diminta' => $kuantitas,
                    'sisa_tidak_terpenuhi' => $remainingQty,
                    'transaction_date' => $transactionDate
                ]);
            }
            
            Log::info("HPP data calculated for inter-outlet transaction", [
                'id_produk' => $idProduk,
                'kuantitas' => $kuantitas,
                'hpp_batches' => count($dataHppJson),
                'transaction_date' => $transactionDate
            ]);
            
            return $dataHppJson;
            
        } catch (\Exception $e) {
            Log::error("Error calculating FIFO HPP data for inter-outlet transaction", [
                'id_produk' => $idProduk,
                'kuantitas' => $kuantitas,
                'transaction_date' => $transactionDate,
                'error' => $e->getMessage()
            ]);
            
            return []; // Return empty array on error
        }
    }

    /**
     * Create journal entry for inter outlet sale transaction
     */
    private function createInterOutletSaleJournal($interOutletSale)
    {
        try {
            $setting = SettingCOAInterOutletSale::getByOutlet($interOutletSale->outlet_asal);
            
            if (!$setting || !$setting->accounting_book_id) {
                Log::info('Setting COA Inter Outlet Sale belum diatur untuk outlet ' . $interOutletSale->outlet_asal . ', transaksi disimpan tanpa jurnal otomatis');
                return null;
            }

            // Validate required accounts
            if (empty($setting->akun_piutang_antar_outlet) || empty($setting->akun_pendapatan_antar_outlet)) {
                Log::warning('Setting COA Inter Outlet Sale tidak lengkap untuk outlet ' . $interOutletSale->outlet_asal . ', transaksi disimpan tanpa jurnal otomatis', [
                    'missing_accounts' => [
                        'piutang' => empty($setting->akun_piutang_antar_outlet),
                        'pendapatan' => empty($setting->akun_pendapatan_antar_outlet)
                    ]
                ]);
                return null;
            }

            $outletAsal = Outlet::find($interOutletSale->outlet_asal);
            $outletTujuan = Outlet::find($interOutletSale->outlet_tujuan);
            $description = "Transfer Penjualan {$interOutletSale->no_transaksi} - {$outletAsal->nama_outlet} ke {$outletTujuan->nama_outlet}";
            $entries = [];

            // Hitung total HPP
            $totalHpp = 0;
            foreach ($interOutletSale->items as $item) {
                $produk = Produk::find($item->id_produk);
                if ($produk) {
                    $hpp = $produk->calculateHppBarangDagang();
                    $totalHpp += $hpp * $item->kuantitas;
                }
            }

            // Hitung pendapatan bersih (tanpa PPN)
            $pendapatanBersih = $interOutletSale->subtotal - $interOutletSale->total_diskon;
            $ppnAmount = $interOutletSale->ppn;

            // Jurnal untuk outlet asal (penjual)
            // Piutang Antar Outlet (D) vs Pendapatan Penjualan Antar Outlet (K)
            $entries[] = [
                'account_id' => $this->getAccountIdByCode($setting->akun_piutang_antar_outlet, $interOutletSale->outlet_asal),
                'debit' => $interOutletSale->total,
                'credit' => 0,
                'memo' => 'Piutang dari ' . $outletTujuan->nama_outlet
            ];
            
            $entries[] = [
                'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_antar_outlet, $interOutletSale->outlet_asal),
                'debit' => 0,
                'credit' => $pendapatanBersih,
                'memo' => 'Pendapatan penjualan antar outlet'
            ];
            
            // PPN jika ada
            if ($ppnAmount > 0 && !empty($setting->akun_ppn)) {
                try {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_ppn, $interOutletSale->outlet_asal),
                        'debit' => 0,
                        'credit' => $ppnAmount,
                        'memo' => 'PPN 10% dari penjualan antar outlet'
                    ];
                } catch (\Exception $e) {
                    Log::warning('Akun PPN tidak ditemukan, skip jurnal PPN: ' . $e->getMessage());
                }
            }

            // HPP dan persediaan
            if ($totalHpp > 0 && !empty($setting->akun_hpp) && !empty($setting->akun_persediaan)) {
                try {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_hpp, $interOutletSale->outlet_asal),
                        'debit' => $totalHpp,
                        'credit' => 0,
                        'memo' => 'HPP penjualan antar outlet'
                    ];
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_persediaan, $interOutletSale->outlet_asal),
                        'debit' => 0,
                        'credit' => $totalHpp,
                        'memo' => 'Pengurangan persediaan'
                    ];
                } catch (\Exception $e) {
                    Log::warning('Akun HPP/Persediaan tidak ditemukan, skip jurnal HPP: ' . $e->getMessage());
                }
            }

            $journalEntry = $this->journalService->createAutomaticJournal(
                'inter_outlet_sale',
                $interOutletSale->id,
                $interOutletSale->tanggal,
                $description,
                $entries,
                $setting->accounting_book_id,
                $interOutletSale->outlet_asal
            );

            if ($journalEntry) {
                Log::info('Jurnal penjualan antar outlet berhasil dibuat', [
                    'inter_outlet_sale_id' => $interOutletSale->id,
                    'journal_entry_id' => $journalEntry->id,
                    'outlet_asal' => $interOutletSale->outlet_asal
                ]);
            }

            return $journalEntry;

        } catch (\Exception $e) {
            Log::error('Gagal membuat jurnal penjualan antar outlet: ' . $e->getMessage(), [
                'inter_outlet_sale_id' => $interOutletSale->id,
                'outlet_asal' => $interOutletSale->outlet_asal,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw exception - allow transaction to continue without journal
            return null;
        }
    }

    /**
     * Get account ID by code or ID
     */
    private function getAccountIdByCode(string $codeOrId, int $outletId): int
    {
        // Check if it's already an ID (numeric)
        if (is_numeric($codeOrId)) {
            $account = \App\Models\ChartOfAccount::where('id', $codeOrId)
                ->where('outlet_id', $outletId)
                ->first();
            
            if (!$account) {
                throw new \Exception("Akun dengan ID {$codeOrId} tidak ditemukan untuk outlet {$outletId}");
            }
            
            return $account->id;
        }
        
        // Otherwise, treat it as a code
        $account = \App\Models\ChartOfAccount::where('code', $codeOrId)
            ->where('outlet_id', $outletId)
            ->first();
        
        if (!$account) {
            throw new \Exception("Akun dengan kode {$codeOrId} tidak ditemukan untuk outlet {$outletId}");
        }
        
        return $account->id;
    }

    /**
     * Get transaction history
     */
    public function history(Request $request)
    {
        // If this is an AJAX request, return JSON data
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getHistoryJson($request);
        }
        
        // Otherwise return the view
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.penjualan.inter-outlet.history', compact('outletId', 'outlets'));
    }

    /**
     * Get history data as JSON
     */
    private function getHistoryJson(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', 'all');
            $status = $request->get('status', 'all');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search');

            $query = InterOutletSale::with([
                'outletAsal:id_outlet,nama_outlet',
                'outletTujuan:id_outlet,nama_outlet',
                'user:id,name'
            ]);

            // Apply outlet access filter
            if (!$this->isSuperAdmin()) {
                $accessibleOutletIds = $this->getAccessibleOutletIds();
                $query->where(function($q) use ($accessibleOutletIds) {
                    $q->whereIn('outlet_asal', $accessibleOutletIds)
                      ->orWhereIn('outlet_tujuan', $accessibleOutletIds);
                });
            }

            // Apply filters
            if ($outletId !== 'all') {
                $query->where(function($q) use ($outletId) {
                    $q->where('outlet_asal', $outletId)
                      ->orWhere('outlet_tujuan', $outletId);
                });
            }

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            if ($startDate) {
                $query->whereDate('tanggal', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('tanggal', '<=', $endDate);
            }

            if ($search) {
                $query->where('no_transaksi', 'like', "%{$search}%");
            }

            $transactions = $query->orderBy('tanggal', 'desc')->get();

            // Format data for frontend
            $formattedData = $transactions->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'no_transaksi' => $transaction->no_transaksi,
                    'tanggal_formatted' => $transaction->tanggal->format('d/m/Y H:i'),
                    'outlet_asal_name' => $transaction->outletAsal ? $transaction->outletAsal->nama_outlet : '-',
                    'outlet_tujuan_name' => $transaction->outletTujuan ? $transaction->outletTujuan->nama_outlet : '-',
                    'total' => $transaction->total,
                    'status' => $transaction->status,
                    'user_name' => $transaction->user ? $transaction->user->name : '-'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'count' => $formattedData->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading history data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data riwayat: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get data for DataTable
     */
    public function historyData(Request $request)
    {
        // Get parameters and ensure they are not arrays
        $outletId = $request->get('outlet_id', 'all');
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');

        // Sanitize parameters to prevent Array to string conversion
        if (is_array($outletId)) {
            $outletId = 'all';
        }
        if (is_array($status)) {
            $status = 'all';
        }
        if (is_array($startDate)) {
            $startDate = null;
        }
        if (is_array($endDate)) {
            $endDate = null;
        }
        if (is_array($search)) {
            $search = null;
        }

        $query = InterOutletSale::query()
            ->with([
                'outletAsal:id_outlet,nama_outlet',
                'outletTujuan:id_outlet,nama_outlet',
                'user:id,name',
                'items:id,inter_outlet_sale_id'
            ])
            ->when($outletId !== 'all', function($q) use ($outletId) {
                $q->where(function($query) use ($outletId) {
                    $query->where('outlet_asal', $outletId)
                          ->orWhere('outlet_tujuan', $outletId);
                });
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($startDate, function($q) use ($startDate) {
                $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                $q->whereDate('tanggal', '<=', $endDate);
            })
            ->when($search, function($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%");
            })
            ->orderBy('tanggal', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y H:i');
            })
            ->addColumn('outlet_asal_name', function ($row) {
                return $row->outletAsal ? $row->outletAsal->nama_outlet : '-';
            })
            ->addColumn('outlet_tujuan_name', function ($row) {
                return $row->outletTujuan ? $row->outletTujuan->nama_outlet : '-';
            })
            ->addColumn('total_formatted', function ($row) {
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $badges = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger'
                ];
                $badgeClass = $badges[$row->status] ?? 'secondary';
                $statusText = ucfirst($row->status);
                return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-' . $badgeClass . '-100 text-' . $badgeClass . '-800">' . $statusText . '</span>';
            })
            ->addColumn('items_count', function ($row) {
                return $row->items->count() . ' item';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                $actions .= '<a href="' . route('admin.penjualan.inter-outlet.print', $row->id) . '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                    <i class="bx bx-printer text-xs"></i> Print
                </a>';
                $actions .= '<button onclick="viewDetail(' . $row->id . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class="bx bx-show text-xs"></i> Detail
                </button>';
                if ($row->status === 'pending') {
                    $actions .= '<button onclick="approveTransaction(' . $row->id . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                        <i class="bx bx-check text-xs"></i> Approve
                    </button>';
                }
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
            $interOutletSale = InterOutletSale::with(['outletAsal', 'outletTujuan', 'user', 'items.produk'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $interOutletSale
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Approve transaction
     */
    public function approve($id)
    {
        try {
            $interOutletSale = InterOutletSale::findOrFail($id);
            
            // Validate outlet access
            if (!$this->hasOutletAccess($interOutletSale->outlet_asal) && !$this->hasOutletAccess($interOutletSale->outlet_tujuan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke transaksi ini.'
                ], 403);
            }
            
            if ($interOutletSale->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah diproses sebelumnya'
                ], 400);
            }
            
            $interOutletSale->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disetujui'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete transaction
     */
    public function destroy($id)
    {
        try {
            $interOutletSale = InterOutletSale::findOrFail($id);
            
            // Validate outlet access
            if (!$this->hasOutletAccess($interOutletSale->outlet_asal) && !$this->hasOutletAccess($interOutletSale->outlet_tujuan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke transaksi ini.'
                ], 403);
            }
            
            DB::transaction(function () use ($interOutletSale) {
                // If transaction is approved, restore stock
                if ($interOutletSale->status === 'approved') {
                    $this->restoreInterOutletStock($interOutletSale);
                }
                
                // Delete related journal entries
                \App\Models\JournalEntry::where('reference_type', 'inter_outlet_sale')
                    ->where('reference_number', $interOutletSale->no_transaksi)
                    ->delete();
                
                // Delete related items first
                $interOutletSale->items()->delete();
                
                // Delete the main transaction
                $interOutletSale->delete();
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus' . ($interOutletSale->status === 'approved' ? ' dan stok telah dikembalikan' : '')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting inter outlet sale: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore stock for inter outlet transaction using stored HPP data
     */
    private function restoreInterOutletStock($interOutletSale)
    {
        foreach ($interOutletSale->items as $item) {
            if ($item->id_produk) {
                try {
                    // 1. Remove stock from destination outlet
                    $produkTujuan = Produk::where('kode_produk', function($query) use ($item) {
                        $query->select('kode_produk')
                              ->from('produk')
                              ->where('id_produk', $item->id_produk)
                              ->limit(1);
                    })
                    ->where('id_outlet', $interOutletSale->outlet_tujuan)
                    ->first();

                    if ($produkTujuan) {
                        // Remove stock from destination outlet (FIFO)
                        $produkTujuan->reduceStock($item->kuantitas);
                        Log::info("Removed {$item->kuantitas} stock from destination outlet {$interOutletSale->outlet_tujuan} for product {$produkTujuan->id_produk}");
                    }

                    // 2. Restore stock to source outlet using stored HPP data
                    $produkAsal = Produk::find($item->id_produk);
                    if (!$produkAsal) {
                        throw new \Exception("Source product not found: {$item->id_produk}");
                    }

                    if (!empty($item->data_hpp) && is_array($item->data_hpp)) {
                        // Restore stock using stored HPP data (reverse FIFO)
                        foreach (array_reverse($item->data_hpp) as $hppData) {
                            if (!isset($hppData['id_hpp']) || !isset($hppData['hpp']) || !isset($hppData['qty_used'])) {
                                continue;
                            }

                            $hpp = floatval($hppData['hpp']);
                            $qtyUsed = floatval($hppData['qty_used']);

                            // Add stock back to source outlet with original HPP
                            $produkAsal->addStock($hpp, $qtyUsed);
                            
                            Log::info("Restored {$qtyUsed} stock to source outlet {$interOutletSale->outlet_asal} with HPP {$hpp} for product {$produkAsal->id_produk}");
                        }
                    } else {
                        // Fallback: restore with average HPP if no stored data
                        $hpp = $produkAsal->calculateHppBarangDagang();
                        $produkAsal->addStock($hpp, $item->kuantitas);
                        
                        Log::warning("No stored HPP data found for inter outlet item {$item->id}, using average HPP {$hpp}");
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Error restoring stock for inter outlet item {$item->id}: " . $e->getMessage());
                    // Continue with other items even if one fails
                }
            }
        }
    }

    /**
     * Print invoice
     */
    public function print($id, Request $request)
    {
        try {
            $interOutletSale = InterOutletSale::with(['outletAsal', 'outletTujuan', 'user', 'items.produk'])
                ->findOrFail($id);
            
            // Set the outlet context for company settings (use outlet asal)
            session(['selected_outlet_id' => $interOutletSale->outlet_asal]);
            
            $companySettings = $this->getCompanySettingsForPrint();
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.penjualan.inter-outlet.print', compact('interOutletSale', 'companySettings'))
                ->setPaper('a4', 'portrait');
            
            return $pdf->stream('Invoice-Inter-Outlet-' . $interOutletSale->no_transaksi . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating Inter Outlet PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get COA Settings Modal Data
     */
    public function getCoaModalData(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login again.'
                ], 401);
            }

            $outletId = $request->get('outlet_id', $user->outlet_id ?? 1);
            
            // Validate outlet access
            if (is_numeric($outletId) && !$this->hasOutletAccess((int)$outletId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke outlet ini.'
                ], 403);
            }
            
            // Get user's accessible outlets only
            $outlets = $this->getUserOutlets();
            
            // Get accounting books for the outlet
            $books = \App\Models\AccountingBook::where('outlet_id', $outletId)->get();
            
            // Get chart of accounts
            $allAccounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
                ->orderBy('code')
                ->get();
            
            // Filter leaf accounts only (accounts that don't have children)
            $leafAccounts = $allAccounts->filter(function($account) use ($allAccounts) {
                return !$allAccounts->contains(function($child) use ($account) {
                    return $child->parent_code === $account->code;
                });
            });
            
            // Group accounts by type
            $accountsByType = [
                'asset' => $leafAccounts->filter(fn($a) => $a->type === 'asset')->values(),
                'liability' => $leafAccounts->filter(fn($a) => $a->type === 'liability')->values(),
                'revenue' => $leafAccounts->filter(fn($a) => $a->type === 'revenue')->values(),
                'expense' => $leafAccounts->filter(fn($a) => $a->type === 'expense')->values(),
            ];
            
            // Get current settings
            $setting = SettingCOAInterOutletSale::with('accountingBook')->byOutlet($outletId)->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'outlets' => $outlets,
                    'books' => $books,
                    'accountsByType' => $accountsByType,
                    'setting' => $setting,
                    'currentOutletId' => $outletId
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting COA modal data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * COA Settings
     */
    public function coaSettings(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login again.'
                    ], 401);
                }
                return redirect()->route('login');
            }
        } catch (\Exception $e) {
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
            $setting = SettingCOAInterOutletSale::with('accountingBook')->byOutlet($outletId)->first();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $setting,
                    'authenticated' => true,
                    'user_id' => auth()->id()
                ]);
            }
            
            $books = \App\Models\AccountingBook::where('outlet_id', $outletId)->get();
            $outlets = $this->getUserOutlets(); // Use filtered outlets
            
            $allAccounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
                ->orderBy('code')
                ->get();
            
            $leafAccounts = $allAccounts->filter(function($account) use ($allAccounts) {
                return !$allAccounts->contains(function($child) use ($account) {
                    return $child->parent_code === $account->code;
                });
            });
            
            $accountsByType = [
                'asset' => $leafAccounts->filter(fn($a) => $a->type === 'asset')->values(),
                'liability' => $leafAccounts->filter(fn($a) => $a->type === 'liability')->values(),
                'equity' => $leafAccounts->filter(fn($a) => $a->type === 'equity')->values(),
                'revenue' => $leafAccounts->filter(fn($a) => $a->type === 'revenue')->values(),
                'expense' => $leafAccounts->filter(fn($a) => $a->type === 'expense')->values(),
            ];
            
            return view('admin.penjualan.inter-outlet.coa-settings', compact('setting', 'books', 'accountsByType', 'outletId', 'outlets'));
        }
        
        // POST - Update settings
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'akun_piutang_antar_outlet' => 'required|string',
            'akun_pendapatan_antar_outlet' => 'required|string',
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
            $outletId = $request->outlet_id;
            
            // Validate outlet access
            if (is_numeric($outletId) && !$this->hasOutletAccess((int)$outletId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke outlet ini.'
                ], 403);
            }
            
            SettingCOAInterOutletSale::updateOrCreateForOutlet($outletId, $request->only([
                'accounting_book_id',
                'akun_piutang_antar_outlet',
                'akun_pendapatan_antar_outlet',
                'akun_hpp',
                'akun_persediaan',
                'akun_ppn',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Setting COA Penjualan Antar Outlet berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving COA settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products with HPP for price settings
     */
    public function getPriceProducts(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login again.',
                    'data' => [],
                    'count' => 0
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication error. Please login again.',
                'data' => [],
                'count' => 0
            ], 401);
        }

        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        
        try {
            // Query produk dengan HPP dan harga inter outlet terpisah
            $rawProducts = DB::select("
                SELECT 
                    p.id_produk,
                    p.kode_produk as sku,
                    p.nama_produk as name,
                    p.harga_jual as regular_price,
                    COALESCE(iopp.inter_outlet_price, 0) as inter_outlet_price,
                    COALESCE(iopp.markup_percent, 0) as inter_outlet_markup,
                    COALESCE(k.nama_kategori, 'Barang') as category,
                    COALESCE(s.nama_satuan, 'pcs') as satuan,
                    COALESCE(
                        (SELECT AVG(hpp.hpp) FROM hpp_produk hpp WHERE hpp.id_produk = p.id_produk), 
                        0
                    ) as hpp,
                    COALESCE(
                        (SELECT pi.path FROM product_images pi 
                         INNER JOIN produk p2 ON pi.id_produk = p2.id_produk 
                         WHERE p2.kode_produk = p.kode_produk AND pi.is_primary = 1 
                         LIMIT 1),
                        (SELECT pi.path FROM product_images pi WHERE pi.id_produk = p.id_produk LIMIT 1)
                    ) as image_path
                FROM produk p
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
                LEFT JOIN inter_outlet_product_prices iopp ON p.id_produk = iopp.id_produk AND iopp.outlet_id = ?
                WHERE p.id_outlet = ? 
                AND p.is_active = 1
                ORDER BY p.nama_produk
            ", [$outletId, $outletId]);
            
            // Convert to array format yang diharapkan frontend
            $products = array_map(function($product) {
                // Gunakan harga inter outlet jika ada, jika tidak gunakan harga regular
                $displayPrice = $product->inter_outlet_price > 0 ? $product->inter_outlet_price : $product->regular_price;
                
                return [
                    'id_produk' => (int) $product->id_produk,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category' => $product->category,
                    'regular_price' => (float) $product->regular_price,
                    'inter_outlet_price' => (float) $product->inter_outlet_price,
                    'price' => (float) $displayPrice, // Harga yang ditampilkan
                    'hpp' => (float) $product->hpp,
                    'markup_percent' => (float) $product->inter_outlet_markup,
                    'satuan' => $product->satuan,
                    'image' => $product->image_path ? config('app.url'). \Illuminate\Support\Facades\Storage::url($product->image_path) : null,
                ];
            }, $rawProducts);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'count' => count($products),
                'outlet_id' => $outletId,
                'authenticated' => true,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Inter outlet sale getPriceProducts error', [
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
     * Update single product inter outlet price
     */
    public function updatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|exists:produk,id_produk',
            'inter_outlet_price' => 'required|numeric|min:0|max:999999999999.99',
            'markup_percent' => 'nullable|numeric|min:0|max:99999999.99',
            'outlet_id' => 'required|exists:outlets,id_outlet'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Sanitize markup_percent to prevent overflow
            $markupPercent = $request->markup_percent ?? 0;
            if ($markupPercent > 99999999.99) {
                $markupPercent = 99999999.99;
            }
            
            // Update atau create harga inter outlet terpisah
            \App\Models\InterOutletProductPrice::updateOrCreate(
                [
                    'id_produk' => $request->id_produk,
                    'outlet_id' => $request->outlet_id
                ],
                [
                    'inter_outlet_price' => $request->inter_outlet_price,
                    'markup_percent' => $markupPercent,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Harga inter outlet berhasil diupdate (tidak mempengaruhi harga produk umum)'
            ]);

        } catch (\Exception $e) {
            Log::error('Update inter outlet price error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate harga inter outlet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update inter outlet product prices
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.id_produk' => 'required|exists:produk,id_produk',
            'products.*.inter_outlet_price' => 'required|numeric|min:0|max:999999999999.99',
            'products.*.markup_percent' => 'nullable|numeric|min:0|max:99999999.99',
            'outlet_id' => 'required|exists:outlets,id_outlet'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->products as $productData) {
                    // Sanitize markup_percent to prevent overflow
                    $markupPercent = $productData['markup_percent'] ?? 0;
                    if ($markupPercent > 99999999.99) {
                        $markupPercent = 99999999.99;
                    }
                    
                    // Update atau create harga inter outlet terpisah
                    \App\Models\InterOutletProductPrice::updateOrCreate(
                        [
                            'id_produk' => $productData['id_produk'],
                            'outlet_id' => $request->outlet_id
                        ],
                        [
                            'inter_outlet_price' => $productData['inter_outlet_price'],
                            'markup_percent' => $markupPercent,
                        ]
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Semua harga inter outlet berhasil diupdate (tidak mempengaruhi harga produk umum)'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk update inter outlet prices error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate harga inter outlet: ' . $e->getMessage()
            ], 500);
        }
    }
}