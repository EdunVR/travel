<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Produk;
use App\Models\Gerobak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AgenLaporanController extends Controller
{
    public function index($id_agen)
    {
        // Cek jika user mencoba akses halaman agen lain
        $user = auth()->user();
        
        if ($user->is_agen && $user->id_agen != $id_agen) {
            // Redirect ke halaman agen mereka sendiri
            return redirect()->route('agen_laporan.index', $user->id_agen);
        }
        
        $agen = Member::findOrFail($id_agen);
        return view('agen_laporan.index', compact('agen'));
    }

    public function data($id_agen, Request $request)
    {
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-d');
        
        // 1. GET DATA PEMBELIAN (STOK MASUK)
        $pembelianData = DB::table('agen_stok_history as ash')
            ->join('produk as pr', 'ash.id_produk', '=', 'pr.id_produk')
            ->where('ash.id_agen', $id_agen)
            ->where('ash.tipe', 'masuk')
            ->whereBetween('ash.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->select(
                'ash.created_at as tanggal',
                'ash.id_produk',
                'pr.kode_produk',
                'pr.nama_produk',
                DB::raw('ash.jumlah as jumlah'),
                DB::raw('ash.jumlah * pr.harga_jual as total'),
                DB::raw("'pembelian' as tipe"),
                DB::raw('NULL as kode_gerobak'),
                DB::raw('NULL as nama_gerobak')
            )
            ->get();

        // 2. GET DATA PENJUALAN
        $penjualanData = DB::table('agen_penjualan as ap')
            ->join('agen_penjualan_detail as apd', 'ap.id_penjualan', '=', 'apd.id_penjualan')
            ->join('produk as pr', 'apd.id_produk', '=', 'pr.id_produk')
            ->leftJoin('gerobak as g', 'apd.id_gerobak', '=', 'g.id_gerobak')
            ->where('ap.id_agen', $id_agen)
            ->where('ap.total_item', '>', 0)
            ->whereBetween('ap.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->select(
                'ap.created_at as tanggal',
                'apd.id_produk',
                'pr.kode_produk',
                'pr.nama_produk',
                DB::raw('apd.jumlah as jumlah'),
                DB::raw('apd.jumlah * apd.harga_jual as total'),
                DB::raw("'penjualan' as tipe"),
                'g.kode_gerobak',
                'g.nama_gerobak'
            )
            ->get();

        // 3. GABUNGKAN DATA MANUALLY
        $transaksi = $pembelianData->merge($penjualanData)
            ->sortByDesc('tanggal')
            ->values();

        // 4. HITUNG STOK AWAL
    $stokAwalPerProduk = [];
    $produkIds = $transaksi->pluck('id_produk')->unique();
    
    foreach ($produkIds as $produkId) {
        $stokAwalPerProduk[$produkId] = DB::table('agen_stok_history')
            ->where('id_agen', $id_agen)
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
                $stokAkhir = $currentStok + $item->jumlah;
                $currentStok = $stokAkhir; // Update current stok
            } else {
                $stokAkhir = $currentStok - $item->jumlah;
                $currentStok = $stokAkhir; // Update current stok
            }
            
            $formattedData[] = [
                'tanggal' => $item->tanggal,
                'kode_produk' => $item->kode_produk,
                'nama_produk' => $item->nama_produk,
                'tipe' => $item->tipe,
                'stok_awal' => max(0, $stokAwal),
                'pembelian' => $item->tipe === 'pembelian' ? $item->jumlah : 0,
                'penjualan' => $item->tipe === 'penjualan' ? $item->jumlah : 0,
                'stok_akhir' => max(0, $stokAkhir),
                'total' => $item->total,
                'gerobak' => $item->tipe === 'penjualan' ? ($item->nama_gerobak ?: 'Tidak Ada Gerobak') : '-',
                'kode_gerobak' => $item->tipe === 'penjualan' ? $item->kode_gerobak : null
            ];
        }
    }

    // Urutkan seluruh data secara DESCENDING by tanggal untuk tampilan
    usort($formattedData, function($a, $b) {
        return strtotime($b['tanggal']) - strtotime($a['tanggal']);
    });

    // 6. HITUNG TOTAL - PERBAIKI INI
    $totalPembelian = collect($formattedData)->where('tipe', 'pembelian')->sum('total');
    $totalPenjualan = collect($formattedData)->where('tipe', 'penjualan')->sum('total');
    $totalTransaksi = collect($formattedData)->where('tipe', 'penjualan')->count();

    return DataTables::of($formattedData)
        ->addIndexColumn()
        ->addColumn('stok_awal', function($item) {
            return $item['stok_awal'];
        })
        ->addColumn('pembelian', function($item) {
            return $item['pembelian'] > 0 ? $item['pembelian'] : '-';
        })
        ->addColumn('penjualan', function($item) {
            return $item['penjualan'] > 0 ? $item['penjualan'] : '-';
        })
        ->addColumn('stok_akhir', function($item) {
            $stokAkhir = $item['stok_akhir'];
            if ($stokAkhir < 0) {
                return '<span class="text-danger">' . $stokAkhir . ' (Kurang)</span>';
            } else if ($stokAkhir == 0) {
                return '<span class="text-warning">' . $stokAkhir . ' (Habis)</span>';
            } else {
                return '<span class="text-success">' . $stokAkhir . '</span>';
            }
        })
        ->addColumn('total', function($item) {
            return 'Rp ' . number_format($item['total'], 0, ',', '.');
        })
        ->addColumn('tipe_badge', function($item) {
            if ($item['tipe'] === 'pembelian') {
                return '<span class="label label-success">Pembelian</span>';
            } else {
                return '<span class="label label-danger">Penjualan</span>';
            }
        })
        ->addColumn('detail_gerobak', function($item) {
            if ($item['tipe'] === 'penjualan') {
                return $item['gerobak'] . ($item['kode_gerobak'] ? ' (' . $item['kode_gerobak'] . ')' : '');
            }
            return '-';
        })
        ->rawColumns(['stok_akhir', 'total', 'tipe_badge'])
        ->with('omset', [
            'total_pembelian' => $totalPembelian,
            'total_penjualan' => $totalPenjualan,
            'total_transaksi' => $totalTransaksi,
            'start_date' => $start_date,
            'end_date' => $end_date
        ])
        ->make(true);
    }

    public function create($id_agen)
    {
        $agen = Member::findOrFail($id_agen);
        
        // Get gerobak milik agen
        $gerobaks = Gerobak::where('id_agen', $id_agen)->get();
        
        return view('agen_laporan.create', compact('agen', 'gerobaks'));
    }

    public function getProdukByGerobak($id_gerobak)
    {
        try {
            $produk = DB::table('gerobak_produk as gp')
                ->join('produk as p', 'gp.id_produk', '=', 'p.id_produk')
                ->where('gp.id_gerobak', $id_gerobak)
                ->where('gp.stok', '>', 0)
                ->select('p.id_produk', 'p.kode_produk', 'p.nama_produk', 'p.harga_jual as harga_default', 'gp.stok')
                ->get();
                
            return response()->json([
                'success' => true,
                'produk' => $produk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat produk: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request, $id_agen)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_gerobak' => 'required|exists:gerobak,id_gerobak',
            'produk' => 'required|array',
            'produk.*.id_produk' => 'required|exists:produk,id_produk',
            'produk.*.harga_jual' => 'required|integer|min:1',
            'produk.*.jumlah' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            // 1. Buat entri agen_penjualan
            $totalItem = 0;
            $totalHarga = 0;

            foreach ($request->produk as $produkId => $produkData) {
                if ($produkData['jumlah'] > 0) {
                    $totalItem += $produkData['jumlah'];
                    $totalHarga += $produkData['jumlah'] * $produkData['harga_jual'];
                }
            }

            $penjualan = DB::table('agen_penjualan')->insertGetId([
                'id_agen' => $id_agen,
                'tanggal' => $request->tanggal,
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Buat entri agen_penjualan_detail dan update stok gerobak
            foreach ($request->produk as $produkId => $produkData) {
                if ($produkData['jumlah'] > 0) {
                    // Insert ke agen_penjualan_detail
                    DB::table('agen_penjualan_detail')->insert([
                        'id_penjualan' => $penjualan,
                        'id_produk' => $produkId,
                        'id_gerobak' => $request->id_gerobak,
                        'jumlah' => $produkData['jumlah'],
                        'harga_jual' => $produkData['harga_jual'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Update stok gerobak (kurangi stok)
                    DB::table('gerobak_produk')
                        ->where('id_gerobak', $request->id_gerobak)
                        ->where('id_produk', $produkId)
                        ->decrement('stok', $produkData['jumlah']);

                    // Update stok agen (kurangi stok karena penjualan)
                    \App\Services\AgenStokService::updateStok(
                        $id_agen,
                        $produkId,
                        $produkData['jumlah'],
                        'penjualan',
                        'Penjualan manual - Gerobak: ' . $request->id_gerobak
                    );
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Laporan penjualan berhasil disimpan',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving agen laporan: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal menyimpan laporan: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function indexAgen()
    {
        // Cek jika user adalah agen
        $user = auth()->user();
        $agen = null;
        
        // Cari data agen berdasarkan nama user (karena tabel member tidak ada email)
        $agen = Member::where('id_tipe', 15) // Tipe agen
            ->where('nama', $user->name) // Cari berdasarkan nama saja
            ->first();

        if (!$agen) {
            // Coba cari dengan format lain jika tidak ditemukan
            $agen = Member::where('id_tipe', 15)
                ->where('kode_member', 'LIKE', '%' . substr($user->name, 0, 5) . '%')
                ->first();
                
            if (!$agen) {
                Log::error('User tidak terdaftar sebagai agen: ' . $user->name);
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak terdaftar sebagai agen. Hubungi administrator.');
            }
        }

        return redirect()->route('agen_laporan.index', $agen->id_member);
    }
}