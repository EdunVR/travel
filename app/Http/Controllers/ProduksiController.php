<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\ProduksiDetail;
use App\Models\BahanDetail;
use App\Models\HppProduk;
use App\Models\Outlet;
use Pdf;
use Carbon\Carbon;

class ProduksiController extends Controller
{
    public function index(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $id_outlet = $request->get('id_outlet');
        $produks = Produk::when(!empty($userOutlets), function ($query) use ($userOutlets, $id_outlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($id_outlet) {
                $query->where('id_outlet', $id_outlet);
            }
            return $query;
        })->latest()->get();
        
        $bahans = Bahan::when(!empty($userOutlets), function ($query) use ($userOutlets, $id_outlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($id_outlet) {
                $query->where('id_outlet', $id_outlet);
            }
            return $query;
        })
        ->with(['hargaBahan', 'satuan'])
        ->latest()->get();

        // Get dashboard data
        $dashboardData = $this->getDashboardData($id_outlet);

        return view('produksi.index', compact('outlets', 'userOutlets', 'produks', 'bahans', 'id_outlet', 'dashboardData'));
    }

    private function getDashboardData($outletId = null)
{
    $userOutlets = auth()->user()->akses_outlet ?? [];
    
    $produksiQuery = Produksi::when($userOutlets, function ($query) use ($userOutlets, $outletId) {
        $query->whereIn('id_outlet', $userOutlets);
        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }
        return $query;
    });

    // Total produksi hari ini
    $produksiHariIni = $produksiQuery->whereDate('created_at', today())->count();

    // Total produksi bulan ini
    $produksiBulanIni = $produksiQuery->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count();

    // Total unit diproduksi bulan ini
    $totalUnitBulanIni = $produksiQuery->whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->sum('jumlah');

    // Rata-rata HPP bulan ini
    $produksiBulanIniWithDetail = Produksi::with('detail')
        ->when($userOutlets, function ($query) use ($userOutlets, $outletId) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($outletId) {
                $query->where('id_outlet', $outletId);
            }
            return $query;
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->get();

    $totalHPPBulanIni = 0;
    $totalUnit = 0;
    
    foreach ($produksiBulanIniWithDetail as $produksi) {
        foreach ($produksi->detail as $detail) {
            $totalHPPBulanIni += $detail->harga_beli * $detail->jumlah;
        }
        $totalUnit += $produksi->jumlah;
    }

    $rataHPPBulanIni = $totalUnit > 0 ? round($totalHPPBulanIni / $totalUnit) : 0;

    // Produk paling sering diproduksi bulan ini
    $produkTerbanyak = Produksi::when($userOutlets, function ($query) use ($userOutlets, $outletId) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($outletId) {
                $query->where('id_outlet', $outletId);
            }
            return $query;
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->selectRaw('id_produk, COUNT(*) as total')
        ->groupBy('id_produk')
        ->orderByDesc('total')
        ->with('produk')
        ->first();

    // Trend produksi 7 hari terakhir - PERBAIKAN: Ambil data yang benar
    $trendHarian = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $count = Produksi::when($userOutlets, function ($query) use ($userOutlets, $outletId) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($outletId) {
                    $query->where('id_outlet', $outletId);
                }
                return $query;
            })
            ->whereDate('created_at', $date)
            ->count();
        $trendHarian[] = [
            'date' => $date->format('d M'),
            'count' => $count
        ];
    }

    // Bahan paling banyak digunakan bulan ini
    $bahanTerbanyak = ProduksiDetail::whereHas('produksi', function ($query) use ($userOutlets, $outletId) {
            $query->when($userOutlets, function ($q) use ($userOutlets, $outletId) {
                $q->whereIn('id_outlet', $userOutlets);
                if ($outletId) {
                    $q->where('id_outlet', $outletId);
                }
                return $q;
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
        })
        ->selectRaw('id_bahan, SUM(jumlah) as total_jumlah')
        ->groupBy('id_bahan')
        ->orderByDesc('total_jumlah')
        ->with('bahan')
        ->first();

    // Data untuk chart - PERBAIKAN: Ambil jumlah unit per hari
    $chartData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $totalUnit = Produksi::when($userOutlets, function ($query) use ($userOutlets, $outletId) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($outletId) {
                    $query->where('id_outlet', $outletId);
                }
                return $query;
            })
            ->whereDate('created_at', $date)
            ->sum('jumlah');
        $chartData[] = [
            'date' => $date->format('d M'),
            'unit' => $totalUnit
        ];
    }

    return [
        'produksi_hari_ini' => $produksiHariIni,
        'produksi_bulan_ini' => $produksiBulanIni,
        'total_unit_bulan_ini' => $totalUnitBulanIni,
        'rata_hpp_bulan_ini' => $rataHPPBulanIni,
        'produk_terbanyak' => $produkTerbanyak,
        'bahan_terbanyak' => $bahanTerbanyak,
        'trend_harian' => $trendHarian,
        'chart_data' => $chartData, // Data baru untuk chart
        'total_hpp_bulan_ini' => $totalHPPBulanIni,
    ];
}

    /**
     * Get Dashboard Data via AJAX
     */
    public function getDashboardDataAjax(Request $request)
    {
        $outletId = $request->id_outlet;
        $dashboardData = $this->getDashboardData($outletId);
        
        return response()->json($dashboardData);
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        $produksi = Produksi::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }
            return $query;
        })
        ->with(['produk', 'outlet'])
        ->latest()->get();

        return datatables()
            ->of($produksi)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($produksi) {
                return tanggal_indonesia($produksi->created_at, false);
            })
            ->addColumn('nama_outlet', function ($produksi) {
                return $produksi->outlet ? $produksi->outlet->nama_outlet : '-';
            })
            ->addColumn('produk', function ($produksi) {
                return $produksi->produk->nama_produk ?? 'Produk Telah Dihapus';
            })
            ->addColumn('hpp_unit', function ($produksi) {
                $totalHPP = 0;
                foreach ($produksi->detail as $detail) {
                    $totalHPP += $detail->harga_beli * $detail->jumlah;
                }
                $hppUnit = $produksi->jumlah > 0 ? round($totalHPP / $produksi->jumlah) : 0;
                return format_uang($hppUnit);
            })
            ->addColumn('aksi', function ($produksi) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="showDetail(`'. route('produksi.show', $produksi->id_produksi) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('produksi.destroy', $produksi->id_produksi) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::all();
        return view('produksi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        if (!$request->id_produk) {
            return redirect()->back()->withErrors(['produk' => 'Produk harus dipilih!']);
        }

        // Validasi bahan yang dipilih
        $selectedBahan = array_filter($request->bahan ?? [], function($bahan) {
            return isset($bahan['checked']) && $bahan['checked'] == 1 && $bahan['jumlah'] > 0;
        });

        if (empty($selectedBahan)) {
            return redirect()->back()->withErrors(['bahan' => 'Pilih minimal satu bahan dengan jumlah yang valid!']);
        }

        // Simpan data produksi
        $produksi = new Produksi();
        $produksi->id_produk = $request->id_produk;
        $produksi->jumlah = $request->jumlah;
        $produksi->id_outlet = $request->id_outlet_produksi ?? auth()->user()->akses_outlet[0];
        $produksi->save();

        $total_harga_beli = 0;

        // Simpan detail produksi
        foreach ($selectedBahan as $id_bahan => $data) {
            $jumlah = $data['jumlah'] ?? 0;

            // Ambil stok bahan berdasarkan FIFO (urutkan berdasarkan created_at)
            $stok_bahan = BahanDetail::where('id_bahan', $id_bahan)
                ->orderBy('created_at', 'asc')
                ->get();

            $sisa_jumlah = $jumlah;

            foreach ($stok_bahan as $stok) {
                if ($sisa_jumlah <= 0) break;

                if ($stok->stok >= $sisa_jumlah) {
                    // Kurangi stok
                    $stok->stok -= $sisa_jumlah;
                    $stok->save();

                    // Simpan detail produksi
                    $produksiDetail = new ProduksiDetail();
                    $produksiDetail->id_produksi = $produksi->id_produksi;
                    $produksiDetail->id_bahan = $id_bahan;
                    $produksiDetail->jumlah = $sisa_jumlah;
                    $produksiDetail->harga_beli = $stok->harga_beli;
                    $produksiDetail->tanggal_harga = $stok->created_at;
                    $produksiDetail->save();

                    $total_harga_beli += $stok->harga_beli * $sisa_jumlah;
                    $sisa_jumlah = 0;
                } else {
                    // Kurangi stok sampai habis
                    $sisa_jumlah -= $stok->stok;

                    // Simpan detail produksi
                    $produksiDetail = new ProduksiDetail();
                    $produksiDetail->id_produksi = $produksi->id_produksi;
                    $produksiDetail->id_bahan = $id_bahan;
                    $produksiDetail->jumlah = $stok->stok;
                    $produksiDetail->harga_beli = $stok->harga_beli;
                    $produksiDetail->tanggal_harga = $stok->created_at;
                    $produksiDetail->save();

                    $total_harga_beli += $stok->harga_beli * $stok->stok;
                    $stok->stok = 0;
                    $stok->save();
                }
            }

            if ($sisa_jumlah > 0) {
                $bahan = Bahan::find($id_bahan);
                // Hapus produksi yang sudah dibuat
                $produksi->detail()->delete();
                $produksi->delete();
                
                return redirect()->back()->withErrors(['bahan' => 'Stok bahan tidak mencukupi untuk bahan ' . $bahan->nama_bahan . '.']);
            }
        }

        $hppUnit = $total_harga_beli / $produksi->jumlah;

        // Simpan ke tabel hpp_produk
        $hppProduk = new HppProduk();
        $hppProduk->id_produk = $produksi->id_produk;
        $hppProduk->hpp = $hppUnit;
        $hppProduk->stok = $produksi->jumlah;
        $hppProduk->save();

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produksi = Produksi::with(['detail.bahan.satuan', 'produk', 'outlet'])->find($id);
        
        if (!$produksi) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Hitung total HPP dan HPP per unit
        $totalHPP = 0;
        foreach ($produksi->detail as $detail) {
            $totalHPP += $detail->harga_beli * $detail->jumlah;
        }
        $hppUnit = $produksi->jumlah > 0 ? round($totalHPP / $produksi->jumlah) : 0;
        
        $html = view('produksi.detail', compact('produksi', 'totalHPP', 'hppUnit'))->render();
        
        return response()->json(['html' => $html]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produksi $produksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produksi $produksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        \Log::info('Menghapus produksi dengan ID: ' . $id);
        $produksi = Produksi::find($id);
        if (!$produksi) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Hapus detail produksi terlebih dahulu
        $produksi->detail()->delete();

        // Hapus produksi
        $produksi->delete();

        \Log::info('Produksi berhasil dihapus: ' . $id);
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    public function getHargaFifo(Request $request)
    {
        $id_bahan = $request->id_bahan;
        $jumlah = $request->jumlah;

        $stok_bahan = BahanDetail::where('id_bahan', $id_bahan)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $total_harga = 0;
        $sisa_jumlah = $jumlah;

        foreach ($stok_bahan as $stok) {
            if ($sisa_jumlah <= 0) break;

            if ($stok->stok >= $sisa_jumlah) {
                $total_harga += $stok->harga_beli * $sisa_jumlah;
                $sisa_jumlah = 0;
            } else {
                $total_harga += $stok->harga_beli * $stok->stok;
                $sisa_jumlah -= $stok->stok;
            }
        }

        // Jika stok tidak mencukupi, return error
        if ($sisa_jumlah > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ]);
        }

        return response()->json([
            'success' => true,
            'harga_total' => $total_harga,
            'harga_rata' => $jumlah > 0 ? $total_harga / $jumlah : 0
        ]);
    }

    public function generateLaporan(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $produksi = Produksi::with(['detail.bahan.satuan', 'produk', 'outlet'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->latest()
            ->get();

        // Hitung total HPP untuk setiap produksi
        $produksi->each(function ($item) {
            $totalHPP = 0;
            foreach ($item->detail as $detail) {
                $totalHPP += $detail->harga_beli * $detail->jumlah;
            }
            $item->total_hpp = $totalHPP;
            $item->hpp_unit = $item->jumlah > 0 ? round($totalHPP / $item->jumlah) : 0;
        });

        $totalProduksi = $produksi->sum('jumlah');
        $totalBiaya = $produksi->sum('total_hpp');
        $rataHPP = $totalProduksi > 0 ? round($totalBiaya / $totalProduksi) : 0;

        $data = [
            'produksi' => $produksi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedOutlet' => $selectedOutlet,
            'outlet' => $selectedOutlet ? Outlet::find($selectedOutlet) : null,
            'totalProduksi' => $totalProduksi,
            'totalBiaya' => $totalBiaya,
            'rataHPP' => $rataHPP,
            'tanggalCetak' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = PDF::loadView('produksi.laporan-pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'laporan-produksi-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        // Return stream untuk preview
        return $pdf->stream($filename);
    }

    /**
     * Download PDF Laporan Produksi
     */
    public function downloadLaporan(Request $request)
    {
        // Reuse the same logic as generateLaporan but for download
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $produksi = Produksi::with(['detail.bahan.satuan', 'produk', 'outlet'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
            })
            ->latest()
            ->get();

        // Hitung total HPP untuk setiap produksi
        $produksi->each(function ($item) {
            $totalHPP = 0;
            foreach ($item->detail as $detail) {
                $totalHPP += $detail->harga_beli * $detail->jumlah;
            }
            $item->total_hpp = $totalHPP;
            $item->hpp_unit = $item->jumlah > 0 ? round($totalHPP / $item->jumlah) : 0;
        });

        $totalProduksi = $produksi->sum('jumlah');
        $totalBiaya = $produksi->sum('total_hpp');
        $rataHPP = $totalProduksi > 0 ? round($totalBiaya / $totalProduksi) : 0;

        $data = [
            'produksi' => $produksi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedOutlet' => $selectedOutlet,
            'outlet' => $selectedOutlet ? Outlet::find($selectedOutlet) : null,
            'totalProduksi' => $totalProduksi,
            'totalBiaya' => $totalBiaya,
            'rataHPP' => $rataHPP,
            'tanggalCetak' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = PDF::loadView('produksi.laporan-pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'laporan-produksi-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show Laporan Form
     */
    public function showLaporanForm()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('produksi.laporan-form', compact('outlets'));
    }
}