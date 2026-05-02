<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tipe;
use App\Models\Produk;
use App\Models\ProdukTipe;
use App\Models\Outlet;

class TipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipe = Tipe::with('produkTipe.produk')->get(); // Fetch all types with their products and discounts
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('tipe.index', [
            'tipe' => $tipe, 
            'produk' => Produk::all()
        ], compact('outlets', 'userOutlets'));
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        
        $selectedOutlet = $request->id_outlet;

        $tipe = Tipe::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            // Filter berdasarkan akses outlet user
            $query->whereIn('id_outlet', $userOutlets);

            // Jika ada outlet yang dipilih, filter berdasarkan outlet tersebut
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }

            return $query;
        })->latest()->get();


        $data = datatables()
            ->of($tipe)
            ->addIndexColumn()
            ->addColumn('nama_tipe', function ($tipe) {
                return $tipe->nama_tipe;
            })
            ->addColumn('nama_outlet', function ($kategori) {
                return $kategori->outlet ? $kategori->outlet->nama_outlet : '-';
            })
            ->addColumn('produk_diskon', function ($tipe) {
                $produkDiskon = '';
                foreach ($tipe->produkTipe as $produkTipe) {
                    $produkDiskon .= $produkTipe->produk->nama_produk . ' - ' . $produkTipe->diskon . '%' . ' - ' . format_uang($produkTipe->harga_jual) . '<br>';
                }
                return $produkDiskon;
            })
            ->addColumn('aksi', function ($tipe) {
                $updateUrl = route('tipe.update', $tipe->id_tipe);
                $deleteUrl = route('tipe.destroy', $tipe->id_tipe);
                return '
                    <div class="btn-group">
                        <button onclick="editForm(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                        <button onclick="deleteData(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['aksi', 'produk_diskon'])
            ->make(true);

        // Debugging: Check the raw data
        // dd($data); // Uncomment this line to see the raw JSON response
        return $data;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = Produk::all(); // Ambil semua produk
        return view('tipe.form', compact('produk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $tipe = Tipe::create([
            'nama_tipe' => $request->nama_tipe,
            'id_outlet' => $request->id_outlet ?? auth()->user()->akses_outlet[0]
        ]);

        foreach ($request->produk ?? [] as $index => $id_produk) {
            ProdukTipe::create([
                'id_produk' => $id_produk,
                'id_tipe' => $tipe->id_tipe,
                'diskon' => $request->diskon[$index] ?? 0,
                'harga_jual' => $request->harga_jual[$index] ?? 0,
            ]);
        }

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipe = Tipe::with('produkTipe.produk')->findOrFail($id);
        return response()->json($tipe);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tipe = Tipe::findOrFail($id);
        $produk = Produk::all(); 
        $produkTipe = $tipe->produkTipe;
        return view('tipe.form', compact('tipe', 'produk', 'produkTipe'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $tipe = Tipe::find($id);
        $tipe->nama_tipe = $request->nama_tipe;
        $tipe->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0]; 
        $tipe->update();

        ProdukTipe::where('id_tipe', $tipe->id_tipe)->delete();

        foreach ($request->produk ?? [] as $index => $id_produk) {
            ProdukTipe::create([
                'id_produk' => $id_produk,
                'id_tipe' => $tipe->id_tipe,
                'diskon' => $request->diskon[$index],
                'harga_jual' => $request->harga_jual[$index] ?? 0,
            ]);
        }

        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Tipe::find($id)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }
}
