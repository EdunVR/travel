<?php

namespace App\Services;

use App\Models\AgenProduk;
use App\Models\AgenStokHistory;
use App\Models\Member;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgenStokService
{
    public static function updateStok($id_agen, $id_produk, $jumlah, $tipe, $keterangan, $id_referensi = null)
    {
        return DB::transaction(function () use ($id_agen, $id_produk, $jumlah, $tipe, $keterangan, $id_referensi) {
            try {
                // Update atau create stok agen
                $stok = AgenProduk::firstOrCreate(
                    ['id_agen' => $id_agen, 'id_produk' => $id_produk],
                    ['stok' => 0]
                );

                // Update stok berdasarkan tipe
                if ($tipe === 'masuk') {
                    $stok->stok += $jumlah;
                } else {
                    $stok->stok -= $jumlah;
                    if ($stok->stok < 0) $stok->stok = 0;
                }

                $stok->save();

                // Simpan history
                AgenStokHistory::create([
                    'id_agen' => $id_agen,
                    'id_produk' => $id_produk,
                    'jumlah' => $jumlah,
                    'tipe' => $tipe,
                    'keterangan' => $keterangan,
                    'id_referensi' => $id_referensi
                ]);

                return true;
            } catch (\Exception $e) {
                Log::error('Error updating agen stok: ' . $e->getMessage());
                return false;
            }
        });
    }

    public static function getLaporanHarian($id_agen, $tanggal)
    {
        return DB::table('agen_stok_history as ash')
            ->join('produk as p', 'ash.id_produk', '=', 'p.id_produk')
            ->where('ash.id_agen', $id_agen)
            ->whereDate('ash.created_at', $tanggal)
            ->select(
                'p.kode_produk',
                'p.nama_produk',
                DB::raw('SUM(CASE WHEN ash.tipe = "masuk" THEN ash.jumlah ELSE 0 END) as stok_masuk'),
                DB::raw('SUM(CASE WHEN ash.tipe IN ("keluar", "penjualan", "distribusi") THEN ash.jumlah ELSE 0 END) as stok_keluar'),
                DB::raw('SUM(CASE WHEN ash.tipe = "penjualan" THEN ash.jumlah ELSE 0 END) as penjualan'),
                DB::raw('(SELECT ap.stok FROM agen_produk ap WHERE ap.id_agen = ash.id_agen AND ap.id_produk = ash.id_produk) as stok_sisa')
            )
            ->groupBy('ash.id_produk', 'p.kode_produk', 'p.nama_produk')
            ->get();
    }

    public static function getOmsetHarian($id_agen, $tanggal)
    {
        return DB::table('agen_stok_history as ash')
            ->join('produk as p', 'ash.id_produk', '=', 'p.id_produk')
            ->where('ash.id_agen', $id_agen)
            ->whereDate('ash.created_at', $tanggal)
            ->where('ash.tipe', 'penjualan')
            ->select(
                DB::raw('SUM(ash.jumlah * p.harga_jual) as omset'),
                DB::raw('COUNT(DISTINCT ash.id_referensi) as total_transaksi')
            )
            ->first();
    }

    public static function processPenjualanFromPOS($id_agen, $start_date, $end_date)
    {
        return DB::table('penjualan as p')
            ->join('penjualan_detail as pd', 'p.id_penjualan', '=', 'pd.id_penjualan')
            ->join('produk as pr', 'pd.id_produk', '=', 'pr.id_produk')
            ->where('p.id_member', $id_agen)
            ->where('p.total_item', '>', 0)
            ->whereBetween('p.created_at', [$start_date, $end_date])
            ->select(
                'p.id_penjualan',
                'p.created_at as tanggal',
                'pd.id_produk',
                'pr.kode_produk',
                'pr.nama_produk',
                DB::raw('SUM(pd.jumlah) as jumlah_terjual'),
                DB::raw('SUM(pd.jumlah * pd.harga_jual) as total_omset')
            )
            ->groupBy('p.id_penjualan', 'pd.id_produk', 'pr.kode_produk', 'pr.nama_produk', 'p.created_at')
            ->orderBy('p.created_at', 'desc')
            ->get();
    }

    public static function getStokAwal($id_agen, $id_produk, $tanggal)
    {
        return DB::table('agen_stok_history')
            ->where('id_agen', $id_agen)
            ->where('id_produk', $id_produk)
            ->whereDate('created_at', '<', $tanggal)
            ->selectRaw('
                SUM(CASE WHEN tipe = "masuk" THEN jumlah ELSE 0 END) -
                SUM(CASE WHEN tipe IN ("keluar", "penjualan", "distribusi") THEN jumlah ELSE 0 END) as stok_awal
            ')
            ->value('stok_awal') ?? 0;
    }

    public static function getStokAkhir($id_agen, $id_produk = null)
    {
        $query = DB::table('agen_stok_history')
            ->where('id_agen', $id_agen);
        
        if ($id_produk) {
            $query->where('id_produk', $id_produk);
        }
        
        return $query->selectRaw('
                id_produk,
                SUM(CASE WHEN tipe = "masuk" THEN jumlah ELSE 0 END) -
                SUM(CASE WHEN tipe IN ("keluar", "penjualan", "distribusi") THEN jumlah ELSE 0 END) as stok_akhir
            ')
            ->groupBy('id_produk')
            ->get();
    }

    public static function syncStokFromPenjualan($id_agen)
    {
        return DB::transaction(function () use ($id_agen) {
            try {
                // Get semua penjualan agen yang belum diproses
                $penjualan = DB::table('penjualan as p')
                    ->join('penjualan_detail as pd', 'p.id_penjualan', '=', 'pd.id_penjualan')
                    ->where('p.id_member', $id_agen)
                    ->where('p.total_item', '>', 0)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('agen_stok_history as ash')
                            ->whereRaw('ash.id_referensi = p.id_penjualan')
                            ->where('ash.tipe', 'penjualan');
                    })
                    ->select('p.id_penjualan', 'p.created_at', 'pd.id_produk', 'pd.jumlah')
                    ->get();

                foreach ($penjualan as $item) {
                    self::updateStok(
                        $id_agen,
                        $item->id_produk,
                        $item->jumlah,
                        'penjualan',
                        'Penjualan POS #' . $item->id_penjualan,
                        $item->id_penjualan
                    );
                }

                Log::info('Sync stok dari penjualan berhasil', [
                    'id_agen' => $id_agen,
                    'jumlah_penjualan' => count($penjualan)
                ]);

                return count($penjualan);
            } catch (\Exception $e) {
                Log::error('Error sync stok dari penjualan: ' . $e->getMessage());
                return false;
            }
        });
    }

    public static function syncStokFromPembelian($id_agen)
    {
        return DB::transaction(function () use ($id_agen) {
            try {
                // Get semua pembelian agen (penjualan dimana agen sebagai customer) yang belum diproses
                $pembelian = DB::table('penjualan as p')
                    ->join('penjualan_detail as pd', 'p.id_penjualan', '=', 'pd.id_penjualan')
                    ->where('p.id_member', $id_agen)
                    ->where('p.total_item', '>', 0)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('agen_stok_history as ash')
                            ->whereRaw('ash.id_referensi = p.id_penjualan')
                            ->where('ash.tipe', 'masuk');
                    })
                    ->select('p.id_penjualan', 'p.created_at', 'pd.id_produk', 'pd.jumlah', 'pd.harga_jual')
                    ->get();

                foreach ($pembelian as $item) {
                    self::updateStok(
                        $id_agen,
                        $item->id_produk,
                        $item->jumlah,
                        'masuk',
                        'Pembelian dari POS #' . $item->id_penjualan,
                        $item->id_penjualan
                    );
                }

                Log::info('Sync stok dari pembelian berhasil', [
                    'id_agen' => $id_agen,
                    'jumlah_pembelian' => count($pembelian)
                ]);

                return count($pembelian);
            } catch (\Exception $e) {
                Log::error('Error sync stok dari pembelian: ' . $e->getMessage());
                return false;
            }
        });
    }
}