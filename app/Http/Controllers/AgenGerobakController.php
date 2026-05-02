<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Outlet;
use App\Models\HppProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AgenGerobakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('AgenGerobakController: Mengakses halaman index');
        
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('agen_gerobak.index', compact('outlets', 'userOutlets'));
    }

    public function data(Request $request)
    {
        Log::info('AgenGerobakController: Mengakses function data', [
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        // Get members with id_tipe 15 (agen)
        $agen = Member::with(['outlet', 'tipe'])
            ->where('id_tipe', 15)
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('member.id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('member.id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->select('member.*')
            ->latest('member.created_at');

        return DataTables::of($agen)
            ->addIndexColumn()
            ->addColumn('select_all', function ($agen) {
                return '<input type="checkbox" name="id_agen[]" value="'. $agen->id_member .'">';
            })
            ->addColumn('nama_outlet', function ($agen) {
                return $agen->outlet ? $agen->outlet->nama_outlet : '-';
            })
            ->addColumn('kode_member', function ($agen) {
                return '<span class="label label-success">'. $agen->kode_member .'</span>';
            })
            ->addColumn('total_gerobak', function ($agen) {
                // Count gerobak for this agen
                return $agen->gerobak()->count();
            })
            ->addColumn('total_produk', function ($agen) {
                // Count total produk in all gerobak for this agen
                return $agen->gerobak()->withCount('produk')->get()->sum('produk_count');
            })
            ->addColumn('jumlah_pembelian', function ($agen) {
                $totalQty = DB::table('agen_stok_history')
                    ->where('id_agen', $agen->id_member)
                    ->where('tipe', 'masuk')
                    ->sum('jumlah');
                
                return format_nomor($totalQty);
            })
            ->addColumn('total_pembelian', function ($agen) {
                // Get total nominal pembelian (dalam Rupiah)
                $totalNominal = DB::table('agen_stok_history as ash')
                    ->join('produk as p', 'ash.id_produk', '=', 'p.id_produk')
                    ->where('ash.id_agen', $agen->id_member)
                    ->where('ash.tipe', 'masuk')
                    ->sum(DB::raw('ash.jumlah * p.harga_jual'));
                
                return format_uang($totalNominal);
            })
            ->addColumn('total_penjualan', function ($agen) {
                // Get total omset penjualan untuk agen ini
                $totalOmset = DB::table('agen_penjualan as ap')
                    ->join('agen_penjualan_detail as apd', 'ap.id_penjualan', '=', 'apd.id_penjualan')
                    ->where('ap.id_agen', $agen->id_member)
                    ->where('ap.total_item', '>', 0)
                    ->sum(DB::raw('apd.jumlah * apd.harga_jual'));
                
                return format_uang($totalOmset);
            })
            ->addColumn('lokasi', function ($agen) {
                if ($agen->latitude && $agen->longitude) {
                    return '<span class="text-success"><i class="fa fa-map-marker"></i> Terdeteksi</span>';
                }
                return '<span class="text-muted">Belum di-set</span>';
            })
            ->addColumn('aksi', function ($agen) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="showDetail(`'. route('agen_gerobak.show', $agen->id_member) .'`)" 
                        class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i> Detail</button>
                    <button type="button" onclick="editForm(`'. route('agen_gerobak.edit', $agen->id_member) .'`)" 
                        class="btn btn-xs btn-warning btn-flat"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" onclick="manageGerobak('. $agen->id_member .')" 
                        class="btn btn-xs btn-primary btn-flat"><i class="fa fa-truck"></i> Kelola Gerobak</button>
                </div>';
            })
            ->rawColumns(['aksi', 'select_all', 'kode_member', 'lokasi', 'jumlah_pembelian', 'total_pembelian', 'total_penjualan'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Log::info('AgenGerobakController: Mengakses halaman create');
        
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('agen_gerobak.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('AgenGerobakController: Menyimpan data agen baru', $request->all());
        
        $request->validate([
            'nama' => 'required',
            'telepon' => 'required',
            'alamat' => 'required',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        try {
            // Generate kode member
            $lastMember = Member::latest()->first();
            $kode_member = (int) ($lastMember->kode_member ?? 0) + 1;

            $member = new Member();
            $member->kode_member = tambah_nol_didepan($kode_member, 5);
            $member->nama = $request->nama;
            $member->telepon = $request->telepon;
            $member->alamat = $request->alamat;
            $member->id_outlet = $request->id_outlet;
            $member->id_tipe = 15; // Tipe Agen
            $member->latitude = $request->latitude;
            $member->longitude = $request->longitude;
            $member->save();

            Log::info('AgenGerobakController: Data agen berhasil disimpan', [
                'id_member' => $member->id_member,
                'kode_member' => $member->kode_member
            ]);

            return response()->json('Data agen berhasil disimpan', 200);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal menyimpan data agen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Validasi bahwa parameter adalah numeric (ID)
        if (!is_numeric($id)) {
            Log::error('AgenGerobakController: Parameter ID tidak valid', ['id_agen' => $id]);
            return response()->json(['error' => 'Parameter ID tidak valid'], 400);
        }
        
        Log::info('AgenGerobakController: Menampilkan detail agen', ['id_agen' => $id]);
        
        try {
            $agen = Member::with(['outlet', 'gerobak', 'gerobak.produk'])->findOrFail($id);
            
            // Get sales data for this agen
            $penjualan = Penjualan::where('id_member', $id)
                ->with(['details.produk'])
                ->where('total_item', '>', 0)
                ->latest()
                ->get();

            // Get product inventory for this agen
            $inventory = [];
            foreach ($agen->gerobak as $gerobak) {
                foreach ($gerobak->produk as $produk) {
                    if (!isset($inventory[$produk->id_produk])) {
                        $inventory[$produk->id_produk] = [
                            'produk' => $produk,
                            'total_stok' => 0,
                            'gerobak' => []
                        ];
                    }
                    
                    $inventory[$produk->id_produk]['total_stok'] += $produk->pivot->stok;
                    $inventory[$produk->id_produk]['gerobak'][] = [
                        'nama_gerobak' => $gerobak->nama_gerobak,
                        'stok' => $produk->pivot->stok
                    ];
                }
            }

            Log::debug('AgenGerobakController: Data detail agen loaded', [
                'id_agen' => $id,
                'jumlah_gerobak' => $agen->gerobak->count(),
                'jumlah_penjualan' => $penjualan->count()
            ]);

            return response()->json([
                'agen' => $agen,
                'penjualan' => $penjualan,
                'inventory' => array_values($inventory)
            ]);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal memuat detail agen', [
                'id_agen' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Data agen tidak ditemukan'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Log::info('AgenGerobakController: Mengakses form edit agen', ['id_agen' => $id]);
        
        try {
            $agen = Member::findOrFail($id);
            $userOutlets = auth()->user()->akses_outlet ?? [];
            $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
                return $query->whereIn('id_outlet', $userOutlets);
            })->get();

            return response()->json([
                'agen' => $agen,
                'outlets' => $outlets
            ]);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal memuat form edit agen', [
                'id_agen' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Data agen tidak ditemukan'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info('AgenGerobakController: Memperbarui data agen', [
            'id_agen' => $id,
            'data' => $request->all()
        ]);
        
        $request->validate([
            'nama' => 'required',
            'telepon' => 'required',
            'alamat' => 'required',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        try {
            $member = Member::findOrFail($id);
            $member->nama = $request->nama;
            $member->telepon = $request->telepon;
            $member->alamat = $request->alamat;
            $member->id_outlet = $request->id_outlet;
            $member->latitude = $request->latitude;
            $member->longitude = $request->longitude;
            $member->update();

            Log::info('AgenGerobakController: Data agen berhasil diperbarui', ['id_agen' => $id]);

            return response()->json('Data agen berhasil diperbarui', 200);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal memperbarui data agen', [
                'id_agen' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui data'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Log::info('AgenGerobakController: Menghapus data agen', ['id_agen' => $id]);
        
        try {
            $agen = Member::findOrFail($id);
            
            // Check if agen has gerobak
            if ($agen->gerobak()->count() > 0) {
                Log::warning('AgenGerobakController: Gagal menghapus agen - masih memiliki gerobak', [
                    'id_agen' => $id,
                    'jumlah_gerobak' => $agen->gerobak()->count()
                ]);
                
                return response()->json(['error' => 'Tidak dapat menghapus agen yang masih memiliki gerobak'], 422);
            }

            $agen->delete();

            Log::info('AgenGerobakController: Data agen berhasil dihapus', ['id_agen' => $id]);

            return response(null, 204);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal menghapus data agen', [
                'id_agen' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }

    /**
     * Delete selected agen
     */
    public function deleteSelected(Request $request)
    {
        Log::info('AgenGerobakController: Menghapus agen terpilih', [
            'selected_ids' => $request->id_agen
        ]);
        
        try {
            $deletedCount = 0;
            $failedCount = 0;
            
            foreach ($request->id_agen as $id) {
                $agen = Member::find($id);
                if ($agen && $agen->gerobak()->count() === 0) {
                    $agen->delete();
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            }

            Log::info('AgenGerobakController: Hasil penghapusan agen terpilih', [
                'deleted_count' => $deletedCount,
                'failed_count' => $failedCount
            ]);

            return response(null, 204);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal menghapus agen terpilih', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }

    public function laporanPenjualan($id, Request $request)
    {
        Log::info('AgenGerobakController: Mengambil laporan penjualan agen', [
            'id_agen' => $id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
        
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-d');

        // 1. GET DATA PEMBELIAN (STOK MASUK) dari agen_stok_history
        $pembelian = DB::table('agen_stok_history as ash')
            ->join('produk as pr', 'ash.id_produk', '=', 'pr.id_produk')
            ->where('ash.id_agen', $id)
            ->where('ash.tipe', 'masuk')
            ->whereBetween('ash.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->select(
                'ash.created_at as tanggal',
                'ash.id_produk',
                'pr.kode_produk',
                'pr.nama_produk',
                DB::raw('ash.jumlah as jumlah_beli'),
                DB::raw('0 as jumlah_jual'),
                DB::raw('ash.jumlah * pr.harga_jual as total_pembelian'),
                DB::raw('0 as total_penjualan'),
                DB::raw("'pembelian' as tipe"),
                DB::raw('NULL as kode_gerobak'), // Tambahkan kolom kosong
                DB::raw('NULL as nama_gerobak')  // Tambahkan kolom kosong
            );

        // 2. GET DATA PENJUALAN dari agen_penjualan
        $penjualan = DB::table('agen_penjualan as ap')
            ->join('agen_penjualan_detail as apd', 'ap.id_penjualan', '=', 'apd.id_penjualan')
            ->join('produk as pr', 'apd.id_produk', '=', 'pr.id_produk')
            ->leftJoin('gerobak as g', 'apd.id_gerobak', '=', 'g.id_gerobak')
            ->where('ap.id_agen', $id)
            ->where('ap.total_item', '>', 0)
            ->whereBetween('ap.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->select(
                'ap.created_at as tanggal',
                'apd.id_produk',
                'pr.kode_produk',
                'pr.nama_produk',
                DB::raw('0 as jumlah_beli'),
                DB::raw('apd.jumlah as jumlah_jual'),
                DB::raw('0 as total_pembelian'),
                DB::raw('apd.jumlah * apd.harga_jual as total_penjualan'),
                DB::raw("'penjualan' as tipe"),
                'g.kode_gerobak',
                'g.nama_gerobak'
            );

        // 3. GABUNGKAN DATA PEMBELIAN DAN PENJUALAN
        $transaksi = $pembelian->unionAll($penjualan)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 4. HITUNG STOK AWAL (sebelum periode laporan)
        $stokAwalPerProduk = [];
        $produkIds = $transaksi->pluck('id_produk')->unique();
        
        foreach ($produkIds as $produkId) {
            $stokAwalPerProduk[$produkId] = DB::table('agen_stok_history')
                ->where('id_agen', $id)
                ->where('id_produk', $produkId)
                ->whereDate('created_at', '<', $start_date)
                ->selectRaw('
                    SUM(CASE WHEN tipe = "masuk" THEN jumlah ELSE 0 END) -
                    SUM(CASE WHEN tipe IN ("keluar", "penjualan", "distribusi") THEN jumlah ELSE 0 END) as stok_awal
                ')
                ->value('stok_awal') ?? 0;
        }

        // 5. FORMAT DATA DENGAN PERHITUNGAN STOK YANG BENAR (ASCENDING)
        $formattedData = [];
        
        // Group by produk terlebih dahulu
        $transaksiByProduk = $transaksi->groupBy('id_produk');
        
        foreach ($transaksiByProduk as $produkId => $produkTransaksi) {
            $currentStok = $stokAwalPerProduk[$produkId] ?? 0;
            
            // Urutkan transaksi secara ASCENDING (terlama ke terbaru)
            $sortedTransaksi = $produkTransaksi->sortBy('tanggal');
            
            foreach ($sortedTransaksi as $item) {
                $stokAwal = $currentStok;
                
                // Hitung stok akhir berdasarkan tipe transaksi
                if ($item->tipe === 'pembelian') {
                    $stokAkhir = $currentStok + $item->jumlah_beli;
                    $currentStok = $stokAkhir; // Update current stok
                } else {
                    $stokAkhir = $currentStok - $item->jumlah_jual;
                    $currentStok = $stokAkhir; // Update current stok
                }
                
                $formattedData[] = [
                    'tanggal' => $item->tanggal,
                    'kode_produk' => $item->kode_produk,
                    'nama_produk' => $item->nama_produk,
                    'tipe' => $item->tipe,
                    'stok_awal' => max(0, $stokAwal),
                    'pembelian' => $item->jumlah_beli,
                    'penjualan' => $item->jumlah_jual,
                    'stok_akhir' => max(0, $stokAkhir),
                    'omset' => $item->tipe === 'penjualan' ? $item->total_penjualan : $item->total_pembelian,
                    'gerobak' => $item->tipe === 'penjualan' ? ($item->nama_gerobak ?: 'Tidak Ada Gerobak') : '-',
                    'kode_gerobak' => $item->tipe === 'penjualan' ? $item->kode_gerobak : null
                ];
            }
        }

        // Urutkan seluruh data secara DESCENDING by tanggal untuk tampilan
        usort($formattedData, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        // 6. HITUNG TOTAL
        $totalOmset = collect($formattedData)->where('tipe', 'penjualan')->sum('omset');
        $totalPembelian = collect($formattedData)->where('tipe', 'pembelian')->sum('omset');
        $totalTransaksi = DB::table('agen_penjualan')
            ->where('id_agen', $id)
            ->where('total_item', '>', 0)
            ->whereBetween('tanggal', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->count();

        return response()->json([
            'draw' => $request->draw ?? 1,
            'recordsTotal' => count($formattedData),
            'recordsFiltered' => count($formattedData),
            'data' => $formattedData,
            'omset' => [
                'total_omset' => $totalOmset,
                'total_pembelian' => $totalPembelian,
                'total_transaksi' => $totalTransaksi,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        ]);
    }

    /**
         * Get inv/**
     * Get inventory for agen dengan stok aktual dari laporan penjualan
     */
    public function inventory($id)
    {
        Log::info('AgenGerobakController: Mengambil inventory agen', ['id_agen' => $id]);
        
        try {
            // 1. GET SEMUA PRODUK YANG PERNAH DIBELI AGEN (DARI LAPORAN PENJUALAN)
            $produkAgen = DB::table('penjualan_detail as pd')
                ->join('penjualan as p', 'pd.id_penjualan', '=', 'p.id_penjualan')
                ->join('produk as pr', 'pd.id_produk', '=', 'pr.id_produk')
                ->where('p.id_member', $id)
                ->select('pd.id_produk', 'pr.kode_produk', 'pr.nama_produk')
                ->distinct()
                ->get();

            $inventory = [];

            foreach ($produkAgen as $produk) {
                // 2. HITUNG STOK AKHIR DARI LAPORAN PENJUALAN (STOK TERAKHIR)
                $stokAkhir = DB::table('agen_stok_history as ash')
                    ->where('ash.id_agen', $id)
                    ->where('ash.id_produk', $produk->id_produk)
                    ->selectRaw('
                        SUM(CASE WHEN ash.tipe = "masuk" THEN ash.jumlah ELSE 0 END) -
                        SUM(CASE WHEN ash.tipe IN ("keluar", "penjualan", "distribusi") THEN ash.jumlah ELSE 0 END) as stok_akhir
                    ')
                    ->value('stok_akhir') ?? 0;

                // 3. HITUNG TOTAL STOK DI SEMUA GEROBAK
                $stokGerobak = DB::table('gerobak_produk as gp')
                    ->join('gerobak as g', 'gp.id_gerobak', '=', 'g.id_gerobak')
                    ->where('g.id_agen', $id)
                    ->where('gp.id_produk', $produk->id_produk)
                    ->sum('gp.stok');

                // 4. GET DETAIL STOK PER GEROBAK
                $detailGerobak = DB::table('gerobak_produk as gp')
                    ->join('gerobak as g', 'gp.id_gerobak', '=', 'g.id_gerobak')
                    ->where('g.id_agen', $id)
                    ->where('gp.id_produk', $produk->id_produk)
                    ->select('g.nama_gerobak', 'gp.stok')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'nama_gerobak' => $item->nama_gerobak,
                            'stok' => $item->stok
                        ];
                    })
                    ->toArray();

                $inventory[] = [
                    'produk' => [
                        'id_produk' => $produk->id_produk,
                        'kode_produk' => $produk->kode_produk,
                        'nama_produk' => $produk->nama_produk
                    ],
                    'stok_agen' => (int)$stokAkhir, // Stok akhir dari laporan penjualan
                    'total_stok_gerobak' => (int)$stokGerobak,
                    'stok_tersedia' => (int)$stokAkhir - (int)$stokGerobak,
                    'gerobak' => $detailGerobak,
                    'stok_tersedia_raw' => (int)$stokAkhir - (int)$stokGerobak
                ];
            }

            Log::debug('AgenGerobakController: Data inventory loaded', [
                'id_agen' => $id,
                'jumlah_produk' => count($inventory)
            ]);

            return DataTables::of($inventory)
                ->addIndexColumn()
                ->addColumn('kode_produk', function ($item) {
                    return $item['produk']['kode_produk'];
                })
                ->addColumn('nama_produk', function ($item) {
                    return $item['produk']['nama_produk'];
                })
                ->addColumn('stok_agen', function ($item) {
                    return $item['stok_agen'];
                })
                ->addColumn('total_stok_gerobak', function ($item) {
                    return $item['total_stok_gerobak'];
                })
                ->addColumn('stok_tersedia', function ($item) {
                    $stokTersedia = $item['stok_tersedia'];
                    if ($stokTersedia < 0) {
                        return '<span class="text-danger">' . $stokTersedia . ' (Kurang)</span>';
                    } else if ($stokTersedia == 0) {
                        return '<span class="text-warning">' . $stokTersedia . ' (Habis)</span>';
                    } else {
                        return '<span class="text-success">' . $stokTersedia . '</span>';
                    }
                })
                ->addColumn('detail_gerobak', function ($item) {
                    if (empty($item['gerobak'])) {
                        return '<span class="text-muted">Tidak ada di gerobak</span>';
                    }
                    
                    $html = '';
                    foreach ($item['gerobak'] as $gerobak) {
                        $html .= '<div class="small">• ' . $gerobak['nama_gerobak'] . ': ' . $gerobak['stok'] . '</div>';
                    }
                    return $html;
                })
                ->rawColumns(['stok_tersedia', 'detail_gerobak'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal memuat inventory agen', [
                'id_agen' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Gagal memuat data inventory'], 500);
        }
    }

    /**
     * Sync stok dari pembelian agen
     */
    public function syncStok($id)
    {
        Log::info('AgenGerobakController: Sync stok agen', ['id_agen' => $id]);
        
        try {
            // Panggil service untuk sync stok dari pembelian
            $jumlah = \App\Services\AgenStokService::syncStokFromPembelian($id);
            
            if ($jumlah === false) {
                return response()->json(['error' => 'Gagal sync stok'], 500);
            }

            Log::info('AgenGerobakController: Sync stok berhasil', [
                'id_agen' => $id,
                'jumlah_pembelian' => $jumlah
            ]);

            return response()->json([
                'message' => 'Berhasil sync ' . $jumlah . ' transaksi pembelian',
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('AgenGerobakController: Gagal sync stok', [
                'id_agen' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Gagal sync stok: ' . $e->getMessage()], 500);
        }
    }

    public function laporanPenjualanPerGerobak($id, Request $request)
    {
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-d');

        // Get penjualan per gerobak
        $penjualanPerGerobak = DB::table('penjualan as p')
            ->join('penjualan_detail as pd', 'p.id_penjualan', '=', 'pd.id_penjualan')
            ->join('produk as pr', 'pd.id_produk', '=', 'pr.id_produk')
            ->join('gerobak as g', 'p.id_gerobak', '=', 'g.id_gerobak') // Asumsi ada relasi gerobak di penjualan
            ->where('p.id_member', $id)
            ->where('p.total_item', '>', 0)
            ->whereBetween('p.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->select(
                'p.id_penjualan',
                'p.created_at as tanggal',
                'g.kode_gerobak',
                'g.nama_gerobak',
                'pd.id_produk',
                'pr.kode_produk',
                'pr.nama_produk',
                DB::raw('SUM(pd.jumlah) as jumlah_terjual'),
                DB::raw('SUM(pd.jumlah * pd.harga_jual) as total_omset')
            )
            ->groupBy('p.id_penjualan', 'g.id_gerobak', 'pd.id_produk')
            ->orderBy('p.created_at', 'desc')
            ->get();

        return response()->json($penjualanPerGerobak);
    }

    public function penjualanPerGerobak($id, $produkId, Request $request)
    {
        try {
            $start_date = $request->start_date ?? date('Y-m-01');
            $end_date = $request->end_date ?? date('Y-m-d');
            
            $agen = Member::findOrFail($id);
            
            // Cari produk, jika tidak ditemukan gunakan data dari penjualan
            try {
                $produk = Produk::findOrFail($produkId);
            } catch (\Exception $e) {
                // Jika produk tidak ditemukan, ambil info produk dari data penjualan
                $produkInfo = DB::table('penjualan_detail as pd')
                    ->join('produk as p', 'pd.id_produk', '=', 'p.id_produk')
                    ->where('pd.id_produk', $produkId)
                    ->select('p.kode_produk', 'p.nama_produk')
                    ->first();
                    
                if (!$produkInfo) {
                    return '<div class="alert alert-danger">Produk tidak ditemukan</div>';
                }
                
                // Buat objek produk dummy
                $produk = (object) [
                    'id_produk' => $produkId,
                    'kode_produk' => $produkInfo->kode_produk,
                    'nama_produk' => $produkInfo->nama_produk
                ];
            }
            
            // Debug: Log parameter yang diterima
            Log::info('penjualanPerGerobak parameters', [
                'agen_id' => $id,
                'produk_id' => $produkId,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            
            // Ambil data penjualan per gerobak
            $penjualanPerGerobak = DB::table('penjualan as p')
                ->join('penjualan_detail as pd', 'p.id_penjualan', '=', 'pd.id_penjualan')
                ->leftJoin('gerobak as g', 'p.id_gerobak', '=', 'g.id_gerobak') // Gunakan leftJoin jika mungkin null
                ->where('p.id_member', $id)
                ->where('pd.id_produk', $produkId)
                ->where('p.total_item', '>', 0)
                ->whereBetween('p.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->select(
                    'p.id_penjualan',
                    'p.created_at as tanggal',
                    'g.kode_gerobak',
                    'g.nama_gerobak',
                    DB::raw('SUM(pd.jumlah) as jumlah_terjual'),
                    DB::raw('SUM(pd.jumlah * pd.harga_jual) as total_omset'),
                    DB::raw('CASE WHEN g.id_gerobak IS NULL THEN "Tidak Ada Gerobak" ELSE "Ada Gerobak" END as status_gerobak')
                )
                ->groupBy('p.id_penjualan', 'g.id_gerobak', 'g.kode_gerobak', 'g.nama_gerobak')
                ->orderBy('p.created_at', 'desc')
                ->get();

            // Debug: Log hasil query
            Log::info('penjualanPerGerobak results', [
                'count' => $penjualanPerGerobak->count(),
                'data' => $penjualanPerGerobak->toArray()
            ]);
                
            $html = view('agen_gerobak.partials.penjualan_gerobak', [
                'produk' => $produk,
                'agen' => $agen,
                'penjualanPerGerobak' => $penjualanPerGerobak,
                'start_date' => $start_date,
                'end_date' => $end_date
            ])->render();
                
            return $html;
        } catch (\Exception $e) {
            Log::error('Error in penjualanPerGerobak: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return '<div class="alert alert-danger">Gagal memuat penjualan per gerobak: ' . $e->getMessage() . '</div>';
        }
    }
}