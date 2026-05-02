<?php

namespace App\Http\Controllers;

use App\Models\ProduksiDetail;
use Illuminate\Http\Request;

class ProduksiDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $produksiDetail = ProduksiDetail::where('id_produksi', $id)->get();
        return datatables()
            ->of($produksiDetail)
            ->addIndexColumn()
            ->addColumn('nama_bahan', function ($detail) {
                return $detail->bahan->nama_bahan;
            })
            ->addColumn('jumlah', function ($detail) {
                return $detail->jumlah;
            })
            ->addColumn('aksi', function ($detail) {
                return '
                <div class="btn-group">
                    <button onclick="deleteData(`'. route('produksi_detail.destroy', $detail->id_produksi_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $produksiDetail = new ProduksiDetail();
        $produksiDetail->id_produksi = $request->id_produksi;
        $produksiDetail->id_bahan = $request->id_bahan;
        $produksiDetail->jumlah = $request->jumlah;
        $produksiDetail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProduksiDetail $produksiDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProduksiDetail $produksiDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProduksiDetail $produksiDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produksiDetail = ProduksiDetail::find($id);
        if (!$produksiDetail) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $produksiDetail->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }
}
