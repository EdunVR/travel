<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\Inventori;
use App\Models\PermintaanPengiriman;
use App\Models\HppProduk;
use App\Models\BahanDetail; // Model untuk harga_bahan
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardInventarisController extends Controller
{
    public function index()
    {
        return view('admin.inventaris.index');
    }

    public function getStats(Request $request)
    {
        try {
            $outletIds = $request->input('outlet_ids', []);
            if (empty($outletIds)) {
                $outletIds = [$request->input('outlet_id', session('outlet_id'))];
            }
            $outletIds = array_filter($outletIds); // Remove null values

            // Total SKU (Produk + Inventori)
            $totalProduk = Produk::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))->count();
            $totalInventori = Inventori::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))->count();
            $totalSku = $totalProduk + $totalInventori;

            // Outlet Aktif
            $outletsAktif = Outlet::where('is_active', true)
                ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->count();

            // Total Stok - Perbaiki referensi tabel
            $totalStockProduk = HppProduk::whereHas('produk', function($q) use ($outletIds) {
                if (!empty($outletIds)) {
                    $q->whereIn('id_outlet', $outletIds);
                }
            })->sum('stok');

            $totalStockBahan = BahanDetail::whereHas('bahan', function($q) use ($outletIds) {
                if (!empty($outletIds)) {
                    $q->whereIn('id_outlet', $outletIds);
                }
            })->sum('stok');

            $totalStockInventori = Inventori::when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->sum('stok');

            $totalStock = $totalStockProduk + $totalStockBahan + $totalStockInventori;

            // Stok Rendah - approach sederhana
            $produks = Produk::with('hppProduk')
                ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->get();

            $lowStockCount = $produks->filter(function($produk) {
                $totalStok = $produk->hppProduk->sum('stok');
                return $totalStok <= $produk->stok_minimum || $totalStok <= 10;
            })->count();

            return response()->json([
                'totalSku' => $totalSku,
                'outlets' => $outletsAktif,
                'totalStock' => $totalStock,
                'lowStock' => $lowStockCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting inventory stats: ' . $e->getMessage());
            return response()->json([
                'totalSku' => 0,
                'outlets' => 0,
                'totalStock' => 0,
                'lowStock' => 0
            ], 500);
        }
    }

    public function getOutletsSummary(Request $request)
    {
        try {
            $outletIds = $request->input('outlet_ids', []);
            if (empty($outletIds)) {
                $outletIds = [$request->input('outlet_id', session('outlet_id'))];
            }
            $outletIds = array_filter($outletIds); // Remove null values

            $outlets = Outlet::where('is_active', true)
                ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->withCount(['produks', 'bahans', 'inventoris'])
                ->get()
                ->map(function($outlet) {
                    // Hitung total stok per outlet - Perbaiki query
                    $stockProduk = HppProduk::whereHas('produk', function($query) use ($outlet) {
                        $query->where('id_outlet', $outlet->id_outlet);
                    })->sum('stok');
                    
                    $stockBahan = BahanDetail::whereHas('bahan', function($query) use ($outlet) {
                        $query->where('id_outlet', $outlet->id_outlet);
                    })->sum('stok');
                    
                    $stockInventori = Inventori::where('id_outlet', $outlet->id_outlet)->sum('stok');
                    
                    $totalStock = $stockProduk + $stockBahan + $stockInventori;

                    return [
                        'name' => $outlet->nama_outlet,
                        'city' => $outlet->kota,
                        'products' => $outlet->produks_count,
                        'materials' => $outlet->bahans_count,
                        'inventory' => $outlet->inventoris_count,
                        'stock' => $totalStock
                    ];
                });

            return response()->json($outlets);

        } catch (\Exception $e) {
            Log::error('Error getting outlets summary: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getLowStockItems(Request $request)
    {
        try {
            $outletIds = $request->input('outlet_ids', []);
            if (empty($outletIds)) {
                $outletIds = [$request->input('outlet_id', session('outlet_id'))];
            }
            $outletIds = array_filter($outletIds); // Remove null values

            // Ambil semua produk dengan stok
            $produks = Produk::with(['kategori', 'outlet', 'hppProduk'])
                ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                ->get();
            
            $lowStockItems = $produks->filter(function($produk) {
                $totalStok = $produk->hppProduk->sum('stok');
                return $totalStok <= $produk->stok_minimum || $totalStok <= 10;
            })->map(function($produk) {
                $totalStok = $produk->hppProduk->sum('stok');
                
                return [
                    'id' => $produk->id_produk,
                    'name' => $produk->nama_produk,
                    'category' => $produk->kategori ? $produk->kategori->nama_kategori : 'Tidak Berkategori',
                    'outlet' => $produk->outlet ? $produk->outlet->nama_outlet : 'Tidak Ada Outlet',
                    'stock' => $totalStok,
                    'min_stock' => $produk->stok_minimum,
                    'manage_url' => route('admin.inventaris.produk.index')
                ];
            });

            return response()->json($lowStockItems->values());

        } catch (\Exception $e) {
            Log::error('Error getting low stock items: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getRecentActivities(Request $request)
    {
        try {
            $outletIds = $request->input('outlet_ids', []);
            if (empty($outletIds)) {
                $outletIds = [$request->input('outlet_id', session('outlet_id'))];
            }
            $outletIds = array_filter($outletIds); // Remove null values

            $activities = PermintaanPengiriman::with(['outletAsal', 'outletTujuan', 'produk', 'bahan', 'inventori'])
                ->when(!empty($outletIds), function($q) use ($outletIds) {
                    $q->where(function($query) use ($outletIds) {
                        $query->whereIn('id_outlet_asal', $outletIds)
                              ->orWhereIn('id_outlet_tujuan', $outletIds);
                    });
                })
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($transfer) {
                    $itemName = '';
                    if ($transfer->id_produk) {
                        $itemName = $transfer->produk->nama_produk ?? $transfer->nama_produk;
                    } elseif ($transfer->id_bahan) {
                        $itemName = $transfer->bahan->nama_bahan ?? $transfer->nama_bahan;
                    } elseif ($transfer->id_inventori) {
                        $itemName = $transfer->inventori->nama_barang ?? $transfer->nama_barang;
                    }

                    return [
                        'id' => $transfer->id_permintaan,
                        'icon' => $this->getActivityIcon($transfer->status),
                        'title' => $this->getActivityTitle($transfer->status),
                        'desc' => $this->getActivityDescription($transfer, $itemName),
                        'time' => $transfer->created_at->diffForHumans()
                    ];
                });

            return response()->json($activities);

        } catch (\Exception $e) {
            Log::error('Error getting recent activities: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    private function getActivityIcon($status)
    {
        $icons = [
            'menunggu' => 'bx bx-time',
            'disetujui' => 'bx bx-check',
            'ditolak' => 'bx bx-x'
        ];
        return $icons[$status] ?? 'bx bx-transfer';
    }

    private function getActivityTitle($status)
    {
        $titles = [
            'menunggu' => 'Permintaan Transfer',
            'disetujui' => 'Transfer Disetujui',
            'ditolak' => 'Transfer Ditolak'
        ];
        return $titles[$status] ?? 'Aktivitas';
    }

    private function getActivityDescription($transfer, $itemName)
    {
        $outletAsal = $transfer->outletAsal->nama_outlet ?? 'Unknown';
        $outletTujuan = $transfer->outletTujuan->nama_outlet ?? 'Unknown';
        
        return " {$transfer->jumlah} unit {$itemName} dari {$outletAsal} ke {$outletTujuan}";
    }

    public function search(Request $request)
    {
        try {
            $searchTerm = $request->q;
            $outletIds = $request->input('outlet_ids', []);
            if (empty($outletIds)) {
                $outletIds = [$request->input('outlet_id', session('outlet_id'))];
            }
            $outletIds = array_filter($outletIds); // Remove null values
            $results = [];

            if ($searchTerm) {
                // Search produk
                $produkResults = Produk::where('nama_produk', 'like', "%{$searchTerm}%")
                    ->orWhere('kode_produk', 'like', "%{$searchTerm}%")
                    ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                    ->with(['outlet', 'kategori'])
                    ->limit(5)
                    ->get()
                    ->map(function($produk) {
                        return [
                            'type' => 'produk',
                            'name' => $produk->nama_produk,
                            'code' => $produk->kode_produk,
                            'outlet' => $produk->outlet->nama_outlet ?? '',
                            'category' => $produk->kategori->nama_kategori ?? '',
                            'url' => route('admin.inventaris.produk.index')
                        ];
                    });

                // Search bahan
                $bahanResults = Bahan::where('nama_bahan', 'like', "%{$searchTerm}%")
                    ->orWhere('kode_bahan', 'like', "%{$searchTerm}%")
                    ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                    ->with(['outlet'])
                    ->limit(5)
                    ->get()
                    ->map(function($bahan) {
                        return [
                            'type' => 'bahan',
                            'name' => $bahan->nama_bahan,
                            'code' => $bahan->kode_bahan,
                            'outlet' => $bahan->outlet->nama_outlet ?? '',
                            'category' => 'Bahan',
                            'url' => route('admin.inventaris.bahan.index')
                        ];
                    });

                // Search inventori
                $inventoriResults = Inventori::where('nama_barang', 'like', "%{$searchTerm}%")
                    ->orWhere('kode_inventori', 'like', "%{$searchTerm}%")
                    ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                    ->with(['outlet', 'kategori'])
                    ->limit(5)
                    ->get()
                    ->map(function($inventori) {
                        return [
                            'type' => 'inventori',
                            'name' => $inventori->nama_barang,
                            'code' => $inventori->kode_inventori,
                            'outlet' => $inventori->outlet->nama_outlet ?? '',
                            'category' => $inventori->kategori->nama_kategori ?? '',
                            'url' => route('admin.inventaris.inventori.index')
                        ];
                    });

                // Search outlet
                $outletResults = Outlet::where('nama_outlet', 'like', "%{$searchTerm}%")
                    ->orWhere('kode_outlet', 'like', "%{$searchTerm}%")
                    ->orWhere('kota', 'like', "%{$searchTerm}%")
                    ->when(!empty($outletIds), fn($q) => $q->whereIn('id_outlet', $outletIds))
                    ->limit(5)
                    ->get()
                    ->map(function($outlet) {
                        return [
                            'type' => 'outlet',
                            'name' => $outlet->nama_outlet,
                            'code' => $outlet->kode_outlet,
                            'outlet' => $outlet->kota,
                            'category' => 'Outlet',
                            'url' => route('admin.inventaris.outlet.index')
                        ];
                    });

                $results = $produkResults->merge($bahanResults)
                            ->merge($inventoriResults)
                            ->merge($outletResults)
                            ->take(10); // Limit total results
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Error searching inventory: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}