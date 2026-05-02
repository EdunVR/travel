<?php

namespace App\Http\Controllers;

use App\Models\PembelianDetail;
use Illuminate\Http\Request;
use App\Models\Bahan;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\Hutang;
use Illuminate\Support\Facades\DB;
use App\Models\BahanDetail;
use App\Models\Outlet;
use Illuminate\Support\Facades\Log;

class PembelianDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_pembelian = session('id_pembelian');
        $supplier = Supplier::with('outlet')->find(session('id_supplier'));
        $selectedOutlet = session('id_outlet');
        $diskon = Pembelian::find($id_pembelian)->diskon ?? 0;

        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $bahan = Bahan::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('bahan.id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('bahan.id_outlet', $selectedOutlet);
            }
            return $query;
        })
        ->select('bahan.id_bahan', 'bahan.nama_bahan', 'bahan.merk', DB::raw('SUM(harga_bahan.stok) as stok'))
        ->leftJoin('harga_bahan', 'bahan.id_bahan', '=', 'harga_bahan.id_bahan')
        ->groupBy('bahan.id_bahan', 'bahan.nama_bahan', 'bahan.merk')
        ->latest('bahan.created_at')->get();

        if(! $supplier){
            abort(404);
        }
        return view('pembelian_detail.index', compact('id_pembelian', 'bahan', 'supplier', 'diskon', 'outlets', 'userOutlets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function data($id)
    {
        $detail = PembelianDetail::with('bahan')
            ->where('id_pembelian', $id)
            ->get();
        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['nama_bahan'] = '<span class="label label-success">'. $item->bahan['nama_bahan'] .'</span';
            $row['harga_beli']  = format_uang($item->harga_beli);
            $row['jumlah']      = '
            <div class="input-group">
                <input type="number" class="form-control input-sm quantity" data-id="'. $item->id_pembelian_detail .'" value="'. $item->jumlah .'" readonly>
                <span class="input-group-btn">
                    <button onclick="editJumlah('. $item->id_pembelian_detail .')" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-pencil"></i></button>
                </span>
            </div>';
            $row['subtotal']    = format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('pembelian_detail.destroy', $item->id_pembelian_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->harga_beli * $item->jumlah;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'nama_bahan' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'harga_beli'  => '',
            'jumlah'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'nama_bahan', 'jumlah'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $bahan = Bahan::where('id_bahan', $request->id_bahan)->first();
        if (! $bahan) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new PembelianDetail();
        $detail->id_pembelian = $request->id_pembelian;
        $detail->id_bahan = $bahan->id_bahan;
        $detail->harga_beli = $request->harga_beli;
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $request->harga_beli * $request->jumlah;
        $detail->id_harga_bahan = $request->id_harga_bahan;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(PembelianDetail $pembelianDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembelianDetail $pembelianDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $detail = PembelianDetail::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_beli * $request->jumlah;
        $detail->update();
    }

    /**
     * Remove the specified resource from storage.    
     */
    public function destroy($id)
    {
        $detail = PembelianDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon, $total, $isChecked = "false", $isBayarHutang = "false", $hutang = 0)
    {

        if ($isChecked == "true") {
            $bayar = 0; // Transaksi menggunakan hutang
            session(['isHutang' => 'true']);
        } elseif ($isBayarHutang == "true") {
            $bayar = ($total - ($diskon / 100 * $total)) + $hutang; // Bayar hutang
            session(['isHutang' => 'false']);
        } else {
            $bayar = $total - ($diskon / 100 * $total); // Transaksi normal
            session(['isHutang' => 'false']);
        }

        session(['bayarPembelian' => $bayar]);

        $data = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar) . ' Rupiah')
        ];

        session(['isBayarHutang' => $isBayarHutang]);
        return response()->json($data);
    }

    public function updateHutang(Request $request)
    {
        $supplier = Supplier::find($request->id_supplier);
        if (!$supplier) {
            return response()->json('Supplier tidak ditemukan', 404);
        }

        $bayar = $request->bayar; // Pastikan ini diambil dari input yang sesuai
        $isChecked = $request->isChecked;
        $hutang = $request->hutang;
        $isBayarHutang = $request->isBayarHutang;
        $hutangLama = $request->hutangLama;

        session(['hutang' => $hutang]);

        if($isChecked == 'true') {
            $supplier->hutang += $hutang;
            session(['isHutang' => 'true']);
            $supplier->save();

            Hutang::create([
                'id_supplier' => $supplier->id_supplier,
                'id_outlet' => $supplier->id_outlet ?? auth()->user()->akses_outlet[0],
                'nama' => $supplier->nama, // Ambil nama dari supplier
                'hutang' => $hutang, // Jumlah hutang yang baru
                'status' => 'belum_lunas', // Status hutang
            ]);
            return response()->json('ANDA BERHUTANG KE SUPPLIER', 200);
        } elseif($isBayarHutang == 'true') {
            $supplier->hutang = 0;
            $supplier->save();

            Hutang::create([
                'id_supplier' => $supplier->id_supplier,
                'id_outlet' => $supplier->id_outlet ?? auth()->user()->akses_outlet[0],
                'nama' => $supplier->nama,
                'hutang' => '-' .$hutangLama, // Jumlah hutang menjadi 0
                'status' => 'lunas', // Status hutang
            ]);
            return response()->json('LUNAS KE SUPPLIER DENGAN HUTANG', 200);
        } else {
            $supplier->save();
            return response()->json('LUNAS KE SUPPLIER', 200);
        }
    }

    public function getHargaBeli($id)
    {
        $detail = BahanDetail::with('bahan')->where('id_bahan', $id)->orderBy('created_at', 'desc')->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($detail) {
                return tanggal_indonesia($detail->created_at, false);
            })
            ->addColumn('harga_beli', function ($detail) {
                return format_uang($detail->harga_beli);
            })
            ->addColumn('stok', function ($detail) {
                return $detail->stok;
            })
            ->addColumn('aksi', function ($detail) {
                $updateUrl = $detail->id ? route('pembelian_detail.edit_harga', $detail->id) : '#';
                $deleteUrl = $detail->id ? route('pembelian_detail.destroy_harga', $detail->id) : '#';
            
                return '
                <div class="btn-group">
                    <button onclick="editForm_harga(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData_harga(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    <button onclick="pilihBahan('.$detail->id_bahan.', \''.$detail->bahan->nama.'\', '.$detail->harga_beli.', '.$detail->stok.', '.$detail->id.')" class="btn btn-xs btn-success btn-flat"><i class="fa fa-check-circle"></i> Pilih Harga</button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function simpanHargaBahan(Request $request)
    {
        $request->validate([
            'id_bahan' => 'required|exists:bahan,id_bahan',
            'harga_beli' => 'required|numeric|min:1',
            'stok' => 'required|numeric|min:0'
        ]);

        $hargaBahan = new BahanDetail();
        $hargaBahan->id_bahan = $request->id_bahan;
        $hargaBahan->harga_beli = $request->harga_beli;
        $hargaBahan->stok = $request->stok;
        $hargaBahan->save();

        return response()->json(['message' => 'Harga bahan berhasil ditambahkan!'], 200);
    }

    public function updateJumlah(Request $request)
    {
        $detail = PembelianDetail::find($request->id_pembelian_detail);
        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Detail pembelian tidak ditemukan'], 404);
        }

        // Update jumlah dan subtotal
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_beli * $request->jumlah;
        $detail->save();

        return response()->json(['success' => true]);
    }

    public function editHarga($id)
    {
        $bahanDetail = BahanDetail::find($id);

        if (!$bahanDetail) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($bahanDetail);
    }

    public function updateHarga(Request $request, string $id)
    {
        $bahan_detail = BahanDetail::find($id);
        try {
            $bahan_detail->update($request->all());
            return response()->json(['message' => 'Data berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyHarga(string $id)
    {
        BahanDetail::find($id)->delete();
        return response(null, 204);
    }
}
