<?php

namespace App\Http\Controllers;

use App\Models\OngkosKirim;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SalesOngkirController extends Controller
{
    // Halaman Ongkos Kirim
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $ongkosKirim = OngkosKirim::query();
            return DataTables::of($ongkosKirim)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" onclick="editForm(`'.route('sales.ongkos-kirim.update', $row->id_ongkir).'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>';
                    $btn .= '<button type="button" onclick="deleteData(`'.route('sales.ongkos-kirim.destroy', $row->id_ongkir).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        
        return view('sales_management.ongkos_kirim.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'daerah' => 'required',
            'harga' => 'required|numeric'
        ]);

        OngkosKirim::create($request->all());
        return redirect()->back()->with('success', 'Data ongkos kirim berhasil disimpan');
    }

    public function edit($id)
    {
        $ongkosKirim = OngkosKirim::findOrFail($id);
        return response()->json($ongkosKirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'daerah' => 'required',
            'harga' => 'required|numeric'
        ]);

        $ongkosKirim = OngkosKirim::findOrFail($id);
        $ongkosKirim->update($request->all());
        return redirect()->back()->with('success', 'Data ongkos kirim berhasil diupdate');
    }

    public function destroy($id)
    {
        $ongkosKirim = OngkosKirim::findOrFail($id);
        $ongkosKirim->delete();
        return redirect()->back()->with('success', 'Data ongkos kirim berhasil dihapus');
    }
}