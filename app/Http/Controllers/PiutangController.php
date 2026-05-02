<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Member;
use App\Models\Outlet;
use Illuminate\Http\Request;

class PiutangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $members = Member::when(!empty($userOutlets), function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('piutang.index', compact('outlets', 'userOutlets', 'members'));
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        $piutang = Piutang::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }
            return $query;
        })->latest()->get();

        return datatables()
            ->of($piutang)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($piutang) {
                return tanggal_indonesia($piutang->created_at, false);
            })
            ->addColumn('nama_outlet', function ($piutang) {
                return $piutang->outlet ? $piutang->outlet->nama_outlet : '-';
            })
            ->addColumn('member', function ($piutang) {
                return $piutang->nama; 
            })
            ->addColumn('piutang', function ($piutang) {
                return format_uang($piutang->piutang);
            })
            ->addColumn('status', function ($piutang) {
                return $piutang->status == 'lunas' ? '<span class="label label-success">Lunas</span>' : '<span class="label label-danger">Belum Lunas</span>';
            })
            ->addColumn('aksi', function ($piutang) {
                return '
                <div class="btn-group">
                    <button onclick="deleteData(`'. route('piutang.destroy', $piutang->id_piutang) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'status'])
            ->make(true);
    }

    public function destroy($id)
    {
        $piutang = Piutang::find($id);
        $piutang->delete();

        return response(null, 204);
    }
}