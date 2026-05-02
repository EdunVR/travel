<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanPengiriman;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\Inventori;
use Illuminate\Support\Facades\Auth;

class PermintaanPengirimanController extends Controller
{
    /**
     * Menampilkan halaman manajemen gudang.
     */
    public function index()
    {
        // Ambil daftar outlet yang dapat diakses oleh user
        $userOutlets = Auth::user()->akses_outlet ?? [];
        $outlets = Outlet::whereIn('id_outlet', $userOutlets)->get();
        $outlet_all = Outlet::all();

        return view('gudang.index', compact('outlets', 'outlet_all'));
    }

    /**
     * Mengambil daftar item (produk, bahan, inventori) berdasarkan outlet.
     */
    public function getItems(Request $request)
    {
        $id_outlet = $request->id_outlet;
        $target = $request->target;

        // Ambil data produk, bahan, dan inventori dari outlet yang dipilih
        $produk = Produk::where('id_outlet', $id_outlet)
            ->withSum('hppProduk', 'stok')
            ->get();
        $bahan = Bahan::where('id_outlet', $id_outlet)
            ->withSum('hargaBahan', 'stok')
            ->get();
        $inventori = Inventori::where('id_outlet', $id_outlet)->get();

        // Kembalikan view partial yang berisi daftar item
        return view('gudang.partials.item-list', compact('produk', 'bahan', 'inventori', 'target'));
    }

    /**
     * Membuat permintaan pengiriman.
     */
    public function buatPermintaan(Request $request)
    {

        // Buat permintaan pengiriman
        $permintaan = PermintaanPengiriman::create([
            'id_outlet_asal' => $request->id_outlet_asal,
            'id_outlet_tujuan' => $request->id_outlet_tujuan,
            'id_produk' => $request->id_produk,
            'id_bahan' => $request->id_bahan,
            'id_inventori' => $request->id_inventori,
            'nama_produk' => $request->nama_produk,
            'nama_bahan' => $request->nama_bahan,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'status' => 'menunggu', // Status awal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pengiriman berhasil dibuat.',
            'data' => $permintaan,
        ]);
    }

    public function daftarPermintaan()
    {
        // Ambil daftar permintaan yang dibuat oleh user
        $userOutlets = Auth::user()->akses_outlet ?? [];
        $permintaan = PermintaanPengiriman::where('id_outlet_asal', $userOutlets)
            ->orWhere('id_outlet_tujuan', $userOutlets)
            ->with(['outletAsal', 'outletTujuan', 'produk', 'bahan', 'inventori'])
            ->get();

        return view('gudang.daftar-permintaan', compact('permintaan'));
    }

    /**
     * Menyetujui permintaan pengiriman.
     */
    public function setujuiPermintaan($id)
    {
        try {
            // Cari permintaan pengiriman
            $permintaan = PermintaanPengiriman::findOrFail($id);

            // Pastikan user memiliki akses ke outlet tujuan
            $userOutlets = Auth::user()->akses_outlet ?? [];
            if (!in_array($permintaan->id_outlet_tujuan, $userOutlets)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui permintaan ini.',
                ], 403);
            }

            // Proses pemindahan stok
            if ($permintaan->id_produk) {
                $this->pindahkanStokProduk($permintaan);
            } elseif ($permintaan->id_bahan) {
                $this->pindahkanStokBahan($permintaan);
            } elseif ($permintaan->id_inventori) {
                $this->pindahkanStokInventori($permintaan);
            }

            // Update status permintaan menjadi "disetujui"
            $permintaan->update(['status' => 'disetujui']);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan pengiriman disetujui dan stok berhasil dipindahkan.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Tangani jika permintaan tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Permintaan pengiriman tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            // Tangani kesalahan lainnya
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function tolakPermintaan($id)
    {
        // Cari permintaan pengiriman
        $permintaan = PermintaanPengiriman::findOrFail($id);

        // Pastikan user memiliki akses ke outlet tujuan
        $userOutlets = Auth::user()->akses_outlet ?? [];
        if (!in_array($permintaan->id_outlet_tujuan, $userOutlets)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menolak permintaan ini.',
            ], 403);
        }

        // Update status permintaan menjadi "ditolak"
        $permintaan->update(['status' => 'ditolak']);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pengiriman ditolak.',
        ]);
    }

    /**
     * Memindahkan stok produk dari outlet asal ke tujuan.
     */
    private function pindahkanStokProduk($permintaan)
    {
        // Cari bahan di outlet asal
        $produkAsal = Produk::where('id_outlet', $permintaan->id_outlet_asal)
            ->where('id_produk', $permintaan->id_produk)
            ->first();

        if (!$produkAsal) {
            throw new \Exception('Produk tidak ditemukan di outlet asal.');
        }

        // Ambil semua hargaBahan di bahanasal, diurutkan berdasarkan created_at (FIFO)
        $hppAsal = $produkAsal->hppProduk()
            ->orderBy('created_at')
            ->get();

        // Hitung total stok bahan di outlet asal
        $totalStokAsal = $hppAsal->sum('stok');

        // Jika stok tidak mencukupi
        if ($totalStokAsal < $permintaan->jumlah) {
            throw new \Exception('Stok produk di outlet asal tidak mencukupi.');
        }

        // Kurangi stok di outlet asal berdasarkan FIFO
        $jumlahYangDibutuhkan = $permintaan->jumlah;
        $hppTerpakai = []; // Simpan data hargaBahan yang terpakai

        foreach ($hppAsal as $hpp) {
            if ($jumlahYangDibutuhkan <= 0) {
                break;
            }

            $stokTerpakai = min($hpp->stok, $jumlahYangDibutuhkan);
            $hpp->decrement('stok', $stokTerpakai);

            // Simpan data hargaBahan yang terpakai
            $hppTerpakai[] = [
                'hpp' => $hpp->hpp,
                'stok' => $stokTerpakai,
                'created_at' => $hpp->created_at,
            ];

            $jumlahYangDibutuhkan -= $stokTerpakai;
        }

        // Cari atau buat bahan di outlet tujuan
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
            ]
        );

        // Tambahkan stok yang terpakai ke bahanTujuan
        foreach ($hppTerpakai as $data) {
            $produkTujuan->hppProduk()->create([
                'stok' => $data['stok'],
                'hpp' => $data['hpp'],
                'created_at' => $data['created_at'],
            ]);
        }
    }

    /**
     * Memindahkan stok bahan dari outlet asal ke tujuan.
     */
    private function pindahkanStokBahan($permintaan)
    {
        // Cari bahan di outlet asal
        $bahanAsal = Bahan::where('id_outlet', $permintaan->id_outlet_asal)
            ->where('id_bahan', $permintaan->id_bahan)
            ->first();

        if (!$bahanAsal) {
            throw new \Exception('Bahan tidak ditemukan di outlet asal.');
        }

        // Ambil semua hargaBahan di bahanAsal, diurutkan berdasarkan created_at (FIFO)
        $hargaBahanAsal = $bahanAsal->hargaBahan()
            ->orderBy('created_at')
            ->get();

        // Hitung total stok bahan di outlet asal
        $totalStokAsal = $hargaBahanAsal->sum('stok');

        // Jika stok tidak mencukupi
        if ($totalStokAsal < $permintaan->jumlah) {
            throw new \Exception('Stok bahan di outlet asal tidak mencukupi.');
        }

        // Kurangi stok di outlet asal berdasarkan FIFO
        $jumlahYangDibutuhkan = $permintaan->jumlah;
        $hargaBahanTerpakai = []; // Simpan data hargaBahan yang terpakai

        foreach ($hargaBahanAsal as $hargaBahan) {
            if ($jumlahYangDibutuhkan <= 0) {
                break;
            }

            $stokTerpakai = min($hargaBahan->stok, $jumlahYangDibutuhkan);
            $hargaBahan->decrement('stok', $stokTerpakai);

            // Simpan data hargaBahan yang terpakai
            $hargaBahanTerpakai[] = [
                'harga_beli' => $hargaBahan->harga_beli,
                'stok' => $stokTerpakai,
                'created_at' => $hargaBahan->created_at,
            ];

            $jumlahYangDibutuhkan -= $stokTerpakai;
        }

        // Cari atau buat bahan di outlet Tujuan
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

        // Tambahkan stok yang terpakai ke bahanTujuan
        foreach ($hargaBahanTerpakai as $data) {
            $bahanTujuan->hargaBahan()->create([
                'stok' => $data['stok'],
                'harga_beli' => $data['harga_beli'],
                'created_at' => $data['created_at'],
            ]);
        }
    }

    /**
     * Memindahkan stok inventori dari outlet asal ke tujuan.
     */
    private function pindahkanStokInventori($permintaan)
    {
        $inventoriAsal = Inventori::where('id_outlet', $permintaan->id_outlet_asal)
            ->where('id_inventori', $permintaan->id_inventori)
            ->first();
        
        if (!$inventoriAsal) {
            throw new \Exception('Inventori tidak ditemukan di outlet asal.');
        }

        // Jika stok tidak mencukupi
        if ($inventoriAsal->stok < $permintaan->jumlah) {
            throw new \Exception('Stok inventori di outlet asal tidak mencukupi.');
        }

        // Kurangi stok di outlet asal
        $inventoriAsal->decrement('stok', $permintaan->jumlah);

        $inventoriTujuan = Inventori::firstOrCreate(
            [
                'id_outlet' => $permintaan->id_outlet_tujuan,
                'nama_barang' => $permintaan->inventori->nama_barang,
            ],
            [
                // Data default jika inventori dibuat baru
                'jumlah' => $permintaan->jumlah ?? $permintaan->inventori->jumlah ?? null,
                'id_kategori' => $permintaan->inventori->id_kategori ?? null,
                'keterangan' => $permintaan->inventori->keterangan ?? null,
                'penanggung_jawab' => $permintaan->inventori->penanggung_jawab ?? null,
                'stok' => 0,
                'lokasi' => $permintaan->inventori->lokasi ?? null,
                'status' => $permintaan->inventori->status ?? 'tersedia',
            ]
        );

        // Tambahkan stok di outlet tujuan
        $inventoriTujuan->increment('stok', $permintaan->jumlah);
    }
}