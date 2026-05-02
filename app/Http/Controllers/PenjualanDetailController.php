<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Piutang;
use Illuminate\Http\Request;
use App\Models\ProdukTipe;
use App\Models\HppProduk;
use App\Models\Outlet;
use Illuminate\Support\Facades\Log;

class PenjualanDetailController extends Controller
{
    public function index(Request $request)
    {

        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $id_outlet = $request->get('id_outlet');
        $member = Member::when(!empty($userOutlets), function ($query) use ($userOutlets, $id_outlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($id_outlet) {
                $query->where('id_outlet', $id_outlet);
            }
            return $query;
        })
        ->with('tipeX')
        ->latest()->get();

        $produk = Produk::when(!empty($userOutlets), function ($query) use ($userOutlets, $id_outlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($id_outlet) {
                $query->where('id_outlet', $id_outlet);
            }
            return $query;
        })
        ->withSum('hppProduk', 'stok')
        ->latest()->get();

        $idProdukList = $produk->pluck('id_produk')->toArray();

        $produkTipeList = ProdukTipe::whereIn('id_produk', $idProdukList)
        ->where('id_tipe', $request->id_tipe)
        ->get()
        ->keyBy('id_produk');

        $produk->each(function ($item) use ($produkTipeList) {
            $produkTipe = $produkTipeList[$item->id_produk] ?? null;
        
            $item->hargaJual_FIX = $produkTipe->harga_jual ?? $item->harga_jual ?? 5;
            $item->diskon_FIX = $produkTipe->diskon ?? $item->diskon ?? 5;
        
            //Log::info('HargaJual_FIX:', ['hargaJual_FIX' => $item->hargaJual_FIX]);
        });

        $diskon = Setting::first()->diskon ?? 0;

        // Cek apakah ada transaksi yang sedang berjalan
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Penjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();

            return view('penjualan_detail.index', compact('produk', 'member', 'diskon', 'id_penjualan', 'penjualan', 'memberSelected', 'outlets', 'userOutlets'));
        } else {
            return redirect()->route('transaksi.baru');
        }
    }

    public function data($id)
    {
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">'. $item->produk['kode_produk'] .'</span';
            $row['nama_produk'] = $item->produk['nama_produk'];
            $row['harga_jual']  = format_uang($item->harga_jual);
            $row['jumlah']      = '
            <div class="input-group">
                <input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'" value="'. $item->jumlah .'" readonly>
                <span class="input-group-btn">
                    <button onclick="editJumlah('. $item->id_penjualan_detail .')" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-pencil"></i></button>
                </span>
            </div>';
            $row['diskon']      = '<span class="diskon">'. $item->diskon .'%</span>';
            $row['subtotal']    = format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_penjualan_detail) .'`, `'. $item->id_produk .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';

            $data[] = $row;

            $total += $item->harga_jual * $item->jumlah - (($item->diskon * $item->jumlah) / 100 * $item->harga_jual);;
            $total_item += $item->jumlah;
        }

        $data[] = [
            'id_produk'   => '',
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah', 'diskon'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::where('id_produk', $request->id_produk)->first();
        if (! $produk) {
            return response()->json('Data gagal disimpan', 400);
        }

        $id_member = $request->id_member;
        $member = Member::find($id_member);
        $tipeCustomer = $member ? $member->id_tipe : null;

        // Ambil diskon dari produk_tipe
        $produkTipe = ProdukTipe::where('id_produk', $produk->id_produk)
            ->where('id_tipe', $tipeCustomer)
            ->first();

        $hargaJual_FIX = $produkTipe->harga_jual ?? $produk->harga_jual;
        $diskon_FIX = $produkTipe->diskon ?? $produk->diskon;

        // Hitung HPP berdasarkan FIFO
        $totalHpp = $this->getHppFifo($produk->id_produk, $request->jumlah);

        $detail = new PenjualanDetail();
        $detail->id_penjualan = $request->id_penjualan;
        $detail->id_produk = $produk->id_produk;
        $detail->harga_jual = $hargaJual_FIX;
        $detail->jumlah = $request->jumlah;
        $detail->diskon = $diskon_FIX;
        $detail->subtotal = $hargaJual_FIX * $request->jumlah - ($diskon_FIX / 100 * $hargaJual_FIX * $request->jumlah);
        $detail->id_hpp = $request->id_hpp;
        $detail->hpp = $totalHpp;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = PenjualanDetail::find($id);
        if (!$detail) {
            return response()->json('Detail tidak ditemukan', 404);
        }

        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_jual * $request->jumlah - (($detail->diskon * $request->jumlah) / 100 * $detail->harga_jual);
        $detail->save();
    }

    public function destroy($id)
    {
        $detail = PenjualanDetail::find($id);

        if (!$detail) {
            return response()->json(['message' => 'Detail penjualan tidak ditemukan'], 404);
        }

        // Panggil fungsi kembalikanStok sebelum menghapus
        $this->kembalikanStok(new Request(['id_penjualan_detail' => $id]));

        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0, $piutang = 0, $isChecked = "false", $isCheckedIngatkan = "false")
    {
        $bayar   = $total - ($diskon / 100 * $total);
        if ($isChecked== "true" && $piutang > 0) {
            $bayar += $piutang; // Tambahkan piutang ke bayar
        }
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah'),
            'piutang' => format_uang($piutang),
        ];

        session(['isChecked' => $isChecked]);
        session(['isCheckedIngatkan' => $isCheckedIngatkan]);
        return response()->json($data);
    }

    public function getDiscount(Request $request)
    {
        $produk = Produk::find($request->id_produk);
        $member = Member::find($request->id_member);
        $tipeCustomer = $member ? $member->id_tipe : null;

        $produkTipe = ProdukTipe::where('id_produk', $produk->id_produk)
            ->where('id_tipe', $tipeCustomer)
            ->first();

        $diskonTipe = $produkTipe ? $produkTipe->diskon : 0;

        return response()->json(['diskon' => $diskonTipe]);
    }

    public function updatePiutang(Request $request)
    {
        $member = Member::find($request->id_member);
        if (!$member) {
            return response()->json('Member tidak ditemukan', 404);
        }

        $bayar = $request->bayar; // Pastikan ini diambil dari input yang sesuai
        $diterima = $request->diterima; // Pastikan ini diambil dari input yang sesuai
        $isChecked = $request->isChecked;
        $piutang = $request->piutang;

        $id_penjualan = session('id_penjualan');

        session(['isCheckedIngatkan' => $request->isCheckedIngatkan]);
        session(['piutang' => $piutang]);

        if ($diterima == 0) {
            // Jika diterima = 0, tambahkan piutang sebanyak bayar
            $member->piutang += $bayar;
            session(['isBon' => 'true']);
            $member->save();

            Piutang::create([
                'id_penjualan' => $id_penjualan,
                'id_member' => $member->id_member,
                'id_outlet' => $member->id_outlet ?? auth()->user()->akses_outlet[0],
                'nama' => $member->nama, // Ambil nama dari supplier
                'piutang' => $bayar, // Jumlah hutang yang baru
                'status' => 'belum_lunas', // Status hutang
            ]);
            return response()->json('Piutang ' . $bayar . ' berhasil ditambahkan ke' . $member->nama, 200);
        } elseif ($diterima < $bayar) {
            // Jika diterima < bayar, tambahkan piutang sebanyak kembali
            $kembali = $bayar - $diterima;
            $member->piutang += $kembali;
            session(['isBon' => 'true']);
            $member->save();

            Piutang::create([
                'id_penjualan' => $id_penjualan,
                'id_member' => $member->id_member,
                'id_outlet' => $member->id_outlet ?? auth()->user()->akses_outlet[0],
                'nama' => $member->nama, // Ambil nama dari supplier
                'piutang' => $kembali, // Jumlah hutang yang baru
                'status' => 'belum_lunas', // Status hutang
            ]);
            return response()->json('Piutang ' . $kembali . ' berhasil ditambahkan ke' . $member->nama, 200);
        } else {
            session(['isBon' => 'false']);
            if($isChecked == 'true') {
                $member->piutang = 0;
                $member->save();

                Piutang::create([
                    'id_penjualan' => $id_penjualan,
                    'id_member' => $member->id_member,
                    'id_outlet' => $member->id_outlet ?? auth()->user()->akses_outlet[0],
                    'nama' => $member->nama, // Ambil nama dari supplier
                    'piutang' => '-' .$piutang, // Jumlah hutang yang baru
                    'status' => 'lunas', // Status hutang
                ]);
                return response()->json('LUNAS DENGAN PIUTANG', 200);
            } else {
                $member->save();
                return response()->json('LUNAS', 200);
            }
            
        }
    }

    public function hapusProdukTerpilih(Request $request)
    {
        if ($request->ajax()) {
            $id_penjualan = $request->id_penjualan;

            // Hapus semua produk berdasarkan ID transaksi
            PenjualanDetail::where('id_penjualan', $id_penjualan)->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Request harus AJAX'], 400);
    }

    public function getHPP($id)
    {
        $detail = HppProduk::with('produk')->where('id_produk', $id)->orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($detail) {
                return tanggal_indonesia($detail->created_at, false);
            })
            ->addColumn('harga_jual', function ($detail) {
                return format_uang($detail->produk->harga_jual);
            })
            ->addColumn('hpp', function ($detail) {
                return format_uang($detail->hpp);
            })
            ->addColumn('stok', function ($detail) {
                return $detail->stok;
            })
            ->addColumn('aksi', function ($detail) {
                return '
                <div class="btn-group">
                    <button onclick="pilihProduk('.$detail->id_produk.', \''.$detail->produk->kode.'\', '.$detail->hpp.', '.$detail->stok.', '.$detail->id_hpp.')" class="btn btn-xs btn-success btn-flat"><i class="fa fa-check-circle"></i> Pilih Harga</button>
                </div>
            ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function getHppFifo($id_produk, $jumlah)
    {
        Log::info('Memulai metode getHppFifo', ['id_produk' => $id_produk, 'jumlah' => $jumlah]);

        // Ambil semua HPP produk yang masih memiliki stok lebih dari 0
        $hppDetails = HppProduk::where('id_produk', $id_produk)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        Log::info('Data HPP yang ditemukan:', ['hppDetails' => $hppDetails]);

        $totalHpp = 0;
        $remainingQty = $jumlah;
        $usedStocks = [];

        Log::info('Inisialisasi variabel', ['totalHpp' => $totalHpp, 'remainingQty' => $remainingQty]);

        foreach ($hppDetails as $hpp) {
            Log::info('Memproses HPP:', ['id_hpp' => $hpp->id_hpp, 'stok' => $hpp->stok, 'hpp' => $hpp->hpp]);

            if ($remainingQty <= 0) {
                Log::info('Sisa jumlah (remainingQty) sudah 0, menghentikan loop');
                break;
            }

            // Hitung jumlah stok yang akan digunakan dari HPP ini
            $usedQty = min($hpp->stok, $remainingQty);
            Log::info('Menghitung usedQty:', ['usedQty' => $usedQty]);

            $totalHpp += $hpp->hpp * $usedQty;
            Log::info('Menambahkan ke totalHpp:', ['totalHpp' => $totalHpp]);

            $remainingQty -= $usedQty;
            Log::info('Mengurangi remainingQty:', ['remainingQty' => $remainingQty]);

            // Catat stok yang digunakan
            $usedStocks[] = [
                'id_produk' => $hpp->id_produk,
                'id_hpp' => $hpp->id_hpp,
                'stok_terpakai' => $usedQty,
            ];
            Log::info('Stok yang digunakan dicatat:', ['usedStocks' => $usedStocks]);

            // Kurangi stok di database
            $hpp->stok -= $usedQty;
            $hpp->save();
            Log::info('Stok di database dikurangi:', ['id_hpp' => $hpp->id_hpp, 'stok_baru' => $hpp->stok]);
        }

        // Simpan stok yang digunakan ke dalam session
        $existingUsedStocks = session('used_stocks', []);
        $updatedUsedStocks = array_merge($existingUsedStocks, $usedStocks);
        session(['used_stocks' => $updatedUsedStocks]);
        session()->save(); // Simpan session secara eksplisit

        Log::info('Setelah menyimpan ke session:', ['updatedUsedStocks' => $updatedUsedStocks]);

        // Jika remainingQty masih lebih dari 0, berarti stok tidak mencukupi
        if ($remainingQty > 0) {
            Log::error('Stok tidak mencukupi', ['remainingQty' => $remainingQty]);
            throw new \Exception("Stok tidak mencukupi untuk produk dengan ID: $id_produk");
        }

        Log::info('Metode getHppFifo selesai', ['totalHpp' => $totalHpp]);
        return $totalHpp;
    }


    public function kembalikanStok(Request $request)
    {
        $detail = PenjualanDetail::find($request->id_penjualan_detail);
        if (!$detail) {
            return response()->json('Detail penjualan tidak ditemukan', 404);
        }

        // Ambil data stok yang digunakan dari session
        $usedStocks = session('used_stocks', []);
        $usedStocksForProduct = array_filter($usedStocks, function ($item) use ($detail) {
            return $item['id_produk'] == $detail->id_produk;
        });

        Log::info('Data stok yang digunakan dari session:', ['usedStocksForProduct' => $usedStocksForProduct]);

        // Jika session kosong, ambil data stok yang digunakan dari database
        if (empty($usedStocksForProduct)) {
            Log::info('Session used_stocks kosong, mengambil data dari database...');

            // Ambil semua HPP yang digunakan untuk produk ini, diurutkan berdasarkan created_at (FIFO)
            $hppDetails = HppProduk::where('id_produk', $detail->id_produk)
                ->orderBy('created_at', 'asc')
                ->get();

            $remainingQty = $detail->jumlah; // Jumlah stok yang perlu dikembalikan

            foreach ($hppDetails as $hpp) {
                if ($remainingQty <= 0) {
                    break;
                }

                // Hitung berapa stok yang bisa dikembalikan ke id_hpp ini
                $returnedQty = min($hpp->stok + $remainingQty, $detail->jumlah);

                if ($returnedQty > 0) {
                    // Tambahkan stok yang dikembalikan
                    $hpp->stok += $returnedQty;
                    $hpp->save();

                    // Kurangi sisa stok yang perlu dikembalikan
                    $remainingQty -= $returnedQty;

                    Log::info('Stok dikembalikan:', [
                        'id_hpp' => $hpp->id_hpp,
                        'stok_dikembalikan' => $returnedQty,
                        'stok_baru' => $hpp->stok,
                    ]);
                }
            }
        } else {
            // Jika session tidak kosong, kembalikan stok sesuai dengan data session
            foreach ($usedStocksForProduct as $usedStock) {
                $hpp = HppProduk::find($usedStock['id_hpp']);
                if ($hpp) {
                    // Tambahkan stok yang dikembalikan
                    $hpp->stok += $usedStock['stok_terpakai'];
                    $hpp->save();

                    Log::info('Stok dikembalikan:', [
                        'id_hpp' => $hpp->id_hpp,
                        'stok_dikembalikan' => $usedStock['stok_terpakai'],
                        'stok_baru' => $hpp->stok,
                    ]);
                }
            }
        }

        // Hapus data stok yang digunakan dari session untuk produk ini
        $usedStocks = array_filter($usedStocks, function ($item) use ($detail) {
            return $item['id_produk'] != $detail->id_produk;
        });
        session(['used_stocks' => $usedStocks]);
        session()->save(); // Simpan session secara eksplisit

        Log::info('Stok berhasil dikembalikan untuk produk:', ['id_produk' => $detail->id_produk]);

        return response()->json('Stok berhasil dikembalikan', 200);
    }

    public function updateJumlah(Request $request)
    {
        $request->validate([
            'id_penjualan_detail' => 'required|exists:penjualan_detail,id_penjualan_detail',
            'jumlah' => 'required|numeric|min:1'
        ]);

        $detail = PenjualanDetail::find($request->id_penjualan_detail);
        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Detail penjualan tidak ditemukan'], 404);
        }

        $selisihJumlah = $request->jumlah;
        $totalHpp = 0;
        if ($selisihJumlah > 0) {
            $totalHpp = $this->getHppFifo($detail->id_produk, $selisihJumlah);
        } else {
            // Jika selisih jumlah negatif, kembalikan stok
            $this->kembalikanStok(new Request([
                'id_penjualan_detail' => $detail->id_penjualan_detail,
                'jumlah' => abs($selisihJumlah)
            ]));
            $totalHpp = $detail->hpp; // Gunakan HPP yang sudah ada
        }

        // Update jumlah dan subtotal
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_jual * $request->jumlah - (($detail->diskon * $request->jumlah) / 100 * $detail->harga_jual);
        $detail->hpp = $totalHpp;
        $detail->save();

        return response()->json(['success' => true]);
    }
    
}