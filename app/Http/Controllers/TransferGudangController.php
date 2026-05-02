<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use Illuminate\Http\Request;
use App\Models\PermintaanPengiriman;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\Inventori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransferGudangExport;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferGudangController extends Controller
{
    use \App\Traits\HasOutletFilter;

    /**
     * Menampilkan halaman transfer gudang
     */
    public function index()
    {
        return view('admin.inventaris.transfer-gudang.index');
    }

    /**
     * Mengambil daftar outlet
     */
    public function getOutlets()
    {
        try {
            $outlets = Outlet::where('is_active', true)
                ->select('id_outlet', 'nama_outlet', 'kode_outlet')
                ->get()
                ->map(function($outlet) {
                    return [
                        'id' => $outlet->id_outlet,
                        'name' => $outlet->nama_outlet,
                        'code' => $outlet->kode_outlet
                    ];
                });

            return response()->json($outlets);
        } catch (\Exception $e) {
            Log::error('Error getting outlets: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data outlet'], 500);
        }
    }

    /**
     * Mengambil daftar item berdasarkan outlet dan jenis
     */
    public function getItems(Request $request)
    {
        try {
            $outletId = $request->outlet_id;
            $type = $request->type;

            if (!$outletId) {
                return response()->json(['error' => 'Outlet ID diperlukan'], 400);
            }

            $items = [];

            switch ($type) {
                case 'produk':
                    $items = Produk::where('id_outlet', $outletId)
                        ->with(['satuan', 'kategori'])
                        ->get()
                        ->map(function($produk) {
                            return [
                                'id' => 'p-' . $produk->id_produk,
                                'type' => 'produk',
                                'outlet_id' => $produk->id_outlet,
                                'name' => $produk->nama_produk,
                                'stock' => $produk->hppProduk->sum('stok') ?? 0,
                                'code' => $produk->kode_produk,
                                'unit' => $produk->satuan->nama_satuan ?? '',
                                'original_id' => $produk->id_produk
                            ];
                        });
                    break;

                case 'bahan':
                    $items = Bahan::where('id_outlet', $outletId)
                        ->with(['satuan'])
                        ->get()
                        ->map(function($bahan) {
                            return [
                                'id' => 'b-' . $bahan->id_bahan,
                                'type' => 'bahan',
                                'outlet_id' => $bahan->id_outlet,
                                'name' => $bahan->nama_bahan,
                                'stock' => $bahan->hargaBahan->sum('stok') ?? 0,
                                'code' => $bahan->kode_bahan ?? '',
                                'unit' => $bahan->satuan->nama_satuan ?? '',
                                'original_id' => $bahan->id_bahan
                            ];
                        });
                    break;

                case 'inventori':
                    $items = Inventori::where('id_outlet', $outletId)
                        ->get()
                        ->map(function($inventori) {
                            return [
                                'id' => 'i-' . $inventori->id_inventori,
                                'type' => 'inventori',
                                'outlet_id' => $inventori->id_outlet,
                                'name' => $inventori->nama_barang,
                                'stock' => $inventori->stok ?? 0,
                                'code' => $inventori->kode_inventori ?? '',
                                'unit' => $inventori->satuan ?? 'pcs',
                                'original_id' => $inventori->id_inventori
                            ];
                        });
                    break;
            }

            return response()->json($items);

        } catch (\Exception $e) {
            Log::error('Error getting items: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data items'], 500);
        }
    }

    /**
     * Menyimpan permintaan transfer
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'outlet_asal_id' => 'required|exists:outlets,id_outlet',
                'outlet_tujuan_id' => 'required|exists:outlets,id_outlet',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
                'items.*.type' => 'required|in:produk,bahan,inventori',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.original_id' => 'required'
            ]);

            // Cek apakah outlet asal dan tujuan berbeda
            if ($validated['outlet_asal_id'] === $validated['outlet_tujuan_id']) {
                return response()->json([
                    'error' => 'Outlet pengirim dan penerima tidak boleh sama'
                ], 422);
            }

            $createdRequests = [];

            foreach ($validated['items'] as $item) {
                // Cek stok tersedia
                if (!$this->checkStockAvailability($item['type'], $item['original_id'], $item['quantity'])) {
                    return response()->json([
                        'error' => "Stok tidak mencukupi untuk item: {$item['name']}"
                    ], 422);
                }

                // Buat permintaan pengiriman
                $permintaanData = [
                    'id_outlet_asal' => $validated['outlet_asal_id'],
                    'id_outlet_tujuan' => $validated['outlet_tujuan_id'],
                    'jumlah' => $item['quantity'],
                    'status' => 'menunggu'
                ];

                // Set ID berdasarkan jenis item
                switch ($item['type']) {
                    case 'produk':
                        $permintaanData['id_produk'] = $item['original_id'];
                        $produk = Produk::find($item['original_id']);
                        $permintaanData['nama_produk'] = $produk->nama_produk ?? '';
                        break;
                    case 'bahan':
                        $permintaanData['id_bahan'] = $item['original_id'];
                        $bahan = Bahan::find($item['original_id']);
                        $permintaanData['nama_bahan'] = $bahan->nama_bahan ?? '';
                        break;
                    case 'inventori':
                        $permintaanData['id_inventori'] = $item['original_id'];
                        $inventori = Inventori::find($item['original_id']);
                        $permintaanData['nama_barang'] = $inventori->nama_barang ?? '';
                        break;
                }

                $permintaan = PermintaanPengiriman::create($permintaanData);
                $createdRequests[] = $permintaan;
            }

            return response()->json([
                'message' => 'Permintaan transfer berhasil dibuat',
                'data' => $createdRequests
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing transfer request: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat permintaan transfer'], 500);
        }
    }

    /**
     * Cek ketersediaan stok
     */
    private function checkStockAvailability($type, $itemId, $quantity)
    {
        switch ($type) {
            case 'produk':
                $produk = Produk::find($itemId);
                return $produk && ($produk->hppProduk->sum('stok') >= $quantity);
            
            case 'bahan':
                $bahan = Bahan::find($itemId);
                return $bahan && ($bahan->hargaBahan->sum('stok') >= $quantity);
            
            case 'inventori':
                $inventori = Inventori::find($itemId);
                return $inventori && ($inventori->stok >= $quantity);
            
            default:
                return false;
        }
    }

    /**
     * Mengambil data permintaan untuk Alpine.js (bukan DataTables)
     */
    public function data(Request $request)
    {
        try {
            $query = PermintaanPengiriman::with(['outletAsal', 'outletTujuan', 'produk', 'bahan', 'inventori']);

            // Filter berdasarkan status jika ada
            if ($request->has('status') && $request->status !== 'ALL') {
                $query->where('status', $request->status);
            }

            $permintaan = $query->latest()->get();

            // Format data untuk Alpine.js
            $data = $permintaan->map(function ($item) {
                $badgeClass = [
                    'menunggu' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'disetujui' => 'bg-green-100 text-green-800 border-green-200',
                    'ditolak' => 'bg-red-100 text-red-800 border-red-200'
                ][$item->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';

                $itemName = '-';
                $itemType = '-';
                
                if ($item->id_produk) {
                    $itemName = $item->produk->nama_produk ?? $item->nama_produk;
                    $itemType = 'Produk';
                } elseif ($item->id_bahan) {
                    $itemName = $item->bahan->nama_bahan ?? $item->nama_bahan;
                    $itemType = 'Bahan';
                } elseif ($item->id_inventori) {
                    $itemName = $item->inventori->nama_barang ?? $item->nama_barang;
                    $itemType = 'Inventori';
                }

                return [
                    'id' => $item->id,
                    'created_at' => $item->created_at,
                    'outlet_asal' => $item->outletAsal->nama_outlet ?? '-',
                    'outlet_tujuan' => $item->outletTujuan->nama_outlet ?? '-',
                    'item_name' => $itemName,
                    'item_type' => $itemType,
                    'quantity' => $item->jumlah,
                    'status' => '<span class="px-2 py-1 rounded-full text-xs border ' . $badgeClass . '">' . 
                               ucfirst($item->status) . '</span>',
                    'status_raw' => $item->status, // Raw status for Alpine.js conditionals
                ];
            });

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            Log::error('Error getting transfer data: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data transfer'], 500);
        }
    }

    /**
     * Menyetujui permintaan transfer
     */
    public function approve($id)
    {
        try {
            $permintaan = PermintaanPengiriman::findOrFail($id);

            // Pindahkan stok berdasarkan jenis item
            if ($permintaan->id_produk) {
                $this->pindahkanStokProduk($permintaan);
            } elseif ($permintaan->id_bahan) {
                $this->pindahkanStokBahan($permintaan);
            } elseif ($permintaan->id_inventori) {
                $this->pindahkanStokInventori($permintaan);
            }

            $permintaan->update(['status' => 'disetujui']);

            return response()->json([
                'message' => 'Permintaan transfer disetujui dan stok berhasil dipindahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving transfer: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyetujui permintaan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menolak permintaan transfer
     */
    public function reject($id)
    {
        try {
            $permintaan = PermintaanPengiriman::findOrFail($id);
            $permintaan->update(['status' => 'ditolak']);

            return response()->json([
                'message' => 'Permintaan transfer ditolak'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting transfer: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menolak permintaan'], 500);
        }
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $query = PermintaanPengiriman::with(['outletAsal', 'outletTujuan', 'produk', 'bahan', 'inventori']);

            if ($request->has('status') && $request->status !== 'ALL') {
                $query->where('status', $request->status);
            }

            $transfers = $query->latest()->get();

            $pdf = PDF::loadView('admin.inventaris.transfer-gudang.export_pdf', [
                'transfers' => $transfers,
                'filterStatus' => $request->status
            ]);

            return $pdf->download('transfer-gudang-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export PDF'], 500);
        }
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            return Excel::download(new TransferGudangExport($request), 'transfer-gudang-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Error exporting Excel: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export Excel'], 500);
        }
    }

    // Method untuk memindahkan stok (sama seperti di controller lama)
    private function pindahkanStokProduk($permintaan)
    {
        // Implementasi sama seperti di PermintaanPengirimanController lama
        $produkAsal = Produk::where('id_outlet', $permintaan->id_outlet_asal)
            ->where('id_produk', $permintaan->id_produk)
            ->first();

        if (!$produkAsal) {
            throw new \Exception('Produk tidak ditemukan di outlet asal.');
        }

        $hppAsal = $produkAsal->hppProduk()->orderBy('created_at')->get();
        $totalStokAsal = $hppAsal->sum('stok');

        if ($totalStokAsal < $permintaan->jumlah) {
            throw new \Exception('Stok produk di outlet asal tidak mencukupi.');
        }

        $jumlahYangDibutuhkan = $permintaan->jumlah;
        $hppTerpakai = [];

        foreach ($hppAsal as $hpp) {
            if ($jumlahYangDibutuhkan <= 0) break;

            $stokTerpakai = min($hpp->stok, $jumlahYangDibutuhkan);
            $hpp->decrement('stok', $stokTerpakai);

            $hppTerpakai[] = [
                'hpp' => $hpp->hpp,
                'stok' => $stokTerpakai,
                'created_at' => $hpp->created_at,
            ];

            $jumlahYangDibutuhkan -= $stokTerpakai;
        }

        $produkTujuan = Produk::firstOrCreate(
            [
                'id_outlet' => $permintaan->id_outlet_tujuan,
                'nama_produk' => $permintaan->produk->nama_produk,
            ],
            [
                'id_kategori' => $permintaan->produk->id_kategori ?? null,
                'id_satuan' => $permintaan->produk->id_satuan ?? null,
                'kode_produk' => $permintaan->produk->kode_produk ?? 'KODE_DEFAULT',
                'merk' => $permintaan->produk->merk ?? null,
                'diskon' => $permintaan->produk->diskon ?? 0,
                'harga_jual' => $permintaan->produk->harga_jual ?? 0,
                'spesifikasi' => $permintaan->produk->spesifikasi ?? null,
                'tipe_produk' => $permintaan->produk->tipe_produk ?? 'barang_dagang',
                'track_inventory' => $permintaan->produk->track_inventory ?? true,
                'metode_hpp' => $permintaan->produk->metode_hpp ?? 'rata_rata',
                'stok_minimum' => $permintaan->produk->stok_minimum ?? 0,
                'is_active' => $permintaan->produk->is_active ?? true,
            ]
        );

        // Salin gambar produk jika produk baru dibuat dan belum punya gambar
        if ($produkTujuan->wasRecentlyCreated || !$produkTujuan->images()->exists()) {
            $this->copyProductImages($produkAsal, $produkTujuan);
        }

        foreach ($hppTerpakai as $data) {
            $produkTujuan->hppProduk()->create([
                'stok' => $data['stok'],
                'hpp' => $data['hpp'],
                'created_at' => $data['created_at'],
            ]);
        }
    }

    /**
     * Salin gambar produk dari produk asal ke produk tujuan
     */
    private function copyProductImages($produkAsal, $produkTujuan)
    {
        try {
            // Load images dengan eager loading
            $images = $produkAsal->images()->get();
            
            if ($images->isEmpty()) {
                Log::info("Produk {$produkAsal->nama_produk} tidak memiliki gambar untuk disalin");
                return; // Tidak ada gambar untuk disalin
            }

            $copiedCount = 0;
            foreach ($images as $image) {
                // Salin record gambar dengan path yang sama (hanya field yang ada di tabel)
                $newImage = $produkTujuan->images()->create([
                    'id_produk' => $produkTujuan->id_produk,
                    'path' => $image->path,
                    'is_primary' => $image->is_primary ?? false,
                ]);
                
                if ($newImage) {
                    $copiedCount++;
                    Log::debug("Gambar tersalin: {$image->path} -> Produk ID {$produkTujuan->id_produk}");
                }
            }

            Log::info("✅ Berhasil menyalin {$copiedCount} gambar dari produk '{$produkAsal->nama_produk}' (ID: {$produkAsal->id_produk}) ke produk di outlet tujuan (ID: {$produkTujuan->id_produk})");
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan transfer
            Log::error("❌ Gagal menyalin gambar produk: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function pindahkanStokBahan($permintaan)
    {
        // Implementasi sama seperti di controller lama
        $bahanAsal = Bahan::where('id_outlet', $permintaan->id_outlet_asal)
            ->where('id_bahan', $permintaan->id_bahan)
            ->first();

        if (!$bahanAsal) {
            throw new \Exception('Bahan tidak ditemukan di outlet asal.');
        }

        $hargaBahanAsal = $bahanAsal->hargaBahan()->orderBy('created_at')->get();
        $totalStokAsal = $hargaBahanAsal->sum('stok');

        if ($totalStokAsal < $permintaan->jumlah) {
            throw new \Exception('Stok bahan di outlet asal tidak mencukupi.');
        }

        $jumlahYangDibutuhkan = $permintaan->jumlah;
        $hargaBahanTerpakai = [];

        foreach ($hargaBahanAsal as $hargaBahan) {
            if ($jumlahYangDibutuhkan <= 0) break;

            $stokTerpakai = min($hargaBahan->stok, $jumlahYangDibutuhkan);
            $hargaBahan->decrement('stok', $stokTerpakai);

            $hargaBahanTerpakai[] = [
                'harga_beli' => $hargaBahan->harga_beli,
                'stok' => $stokTerpakai,
                'created_at' => $hargaBahan->created_at,
            ];

            $jumlahYangDibutuhkan -= $stokTerpakai;
        }

        $bahanTujuan = Bahan::firstOrCreate(
            [
                'id_outlet' => $permintaan->id_outlet_tujuan,
                'nama_bahan' => $permintaan->bahan->nama_bahan,
            ],
            [
                'id_satuan' => $permintaan->bahan->id_satuan ?? null,
                'merk' => $permintaan->bahan->merk ?? null,
            ]
        );

        foreach ($hargaBahanTerpakai as $data) {
            $bahanTujuan->hargaBahan()->create([
                'stok' => $data['stok'],
                'harga_beli' => $data['harga_beli'],
                'created_at' => $data['created_at'],
            ]);
        }
    }

    private function pindahkanStokInventori($permintaan)
    {
        // Implementasi sama seperti di controller lama
        $inventoriAsal = Inventori::where('id_outlet', $permintaan->id_outlet_asal)
            ->where('id_inventori', $permintaan->id_inventori)
            ->first();
        
        if (!$inventoriAsal) {
            throw new \Exception('Inventori tidak ditemukan di outlet asal.');
        }

        if ($inventoriAsal->stok < $permintaan->jumlah) {
            throw new \Exception('Stok inventori di outlet asal tidak mencukupi.');
        }

        $inventoriAsal->decrement('stok', $permintaan->jumlah);

        $inventoriTujuan = Inventori::firstOrCreate(
            [
                'id_outlet' => $permintaan->id_outlet_tujuan,
                'nama_barang' => $permintaan->inventori->nama_barang,
            ],
            [
                'jumlah' => $permintaan->jumlah ?? $permintaan->inventori->jumlah ?? null,
                'id_kategori' => $permintaan->inventori->id_kategori ?? null,
                'keterangan' => $permintaan->inventori->keterangan ?? null,
                'penanggung_jawab' => $permintaan->inventori->penanggung_jawab ?? null,
                'stok' => 0,
                'lokasi' => $permintaan->inventori->lokasi ?? null,
                'status' => $permintaan->inventori->status ?? 'tersedia',
            ]
        );

        $inventoriTujuan->increment('stok', $permintaan->jumlah);
    }
}