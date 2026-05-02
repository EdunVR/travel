<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Tipe;
use App\Models\Outlet;

class MemberController extends Controller
{
    
    public function index()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $tipe = Tipe::when(!empty($userOutlets), function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->pluck('nama_tipe', 'id_tipe');
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('member.index', compact('tipe','outlets', 'userOutlets'));
    }

    public function data(Request $request)
{
    $userOutlets = auth()->user()->akses_outlet ?? [];
    $selectedOutlet = $request->id_outlet;

    $member = Member::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('member.id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('member.id_outlet', $selectedOutlet);
            }
            return $query;
        })
        ->leftJoin('tipe', 'tipe.id_tipe', 'member.id_tipe')
        ->select('member.*', 'tipe.nama_tipe')
        // Tambahkan subquery untuk total piutang
        ->selectSub(function ($query) {
            $query->selectRaw('COALESCE(SUM(piutang), 0)')
                  ->from('piutang')
                  ->whereColumn('id_member', 'member.id_member')
                  ->where('status', 'belum_lunas');
        }, 'total_piutang')
        ->latest('member.created_at')->get();

    return datatables()
        ->of($member)
        ->addIndexColumn()
        ->addColumn('nama_outlet', function ($member) {
            return $member->outlet ? $member->outlet->nama_outlet : '-';
        })
        ->addColumn('select_all', function ($member) {
            return '
                <input type="checkbox" name="id_member[]" value="'. $member->id_member .'">
            ';
        })
        ->addColumn('kode_member', function ($member) {
            return '<span class="label label-success">'. $member->kode_member .'<span>';
        })
        ->addColumn('piutang', function ($member) {
            return '<span class="label label-danger">'. format_uang($member->total_piutang) .'</span>';
        })
        ->addColumn('saldo', function ($member) {
            return '<span class="label label-success">'. format_uang($member->saldo) .'</span>';
        })
        ->addColumn('aksi', function ($member) {
            $buttons = '
            <div class="btn-group">
                <button type="button" onclick="editForm(`'. route('member.update', $member->id_member) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                <button type="button" onclick="deleteData(`'. route('member.destroy', $member->id_member) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
            ';
            
            if ($member->nama_tipe == 'Jemaah') {
                $buttons .= '
                <button type="button" onclick="showJemaahData(`'. route('jemaah.show', $member->id_member) .'`)" class="btn btn-xs btn-primary btn-flat"><i data-feather="user"></i> Data Jemaah</button>
                ';
            }
            
            $buttons .= '</div>';
            
            return $buttons;
        })
        ->rawColumns(['aksi', 'select_all', 'kode_member', 'piutang', 'saldo'])
        ->make(true);
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $member = Member::latest()->first() ?? new Member();
        $kode_member = (int) $member->kode_member +1;

        $member = new Member();
        $member->kode_member = tambah_nol_didepan($kode_member, 5);
        $member->nama = $request->nama;
        $member->telepon = $request->telepon;
        $member->alamat = $request->alamat;
        $member->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0]; 
        $member->id_tipe = $request->id_tipe;
        $member->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = Member::find($id);

        return response()->json($member);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        $member->nama = $request->nama;
        $member->telepon = $request->telepon;
        $member->alamat = $request->alamat;
        $member->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0]; 
        $member->id_tipe = $request->id_tipe;
        $member->update();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);
        $member->delete();

        return response(null, 204);
    }

    public function cetakMember(Request $request)
    {
        $datamember = collect(array());
        foreach ($request->id_member as $id) {
            $member = Member::find($id);
            $datamember[] = $member;
        }

        $datamember = $datamember->chunk(2);
        $setting    = Setting::first();

        $no  = 1;
        //return view('member.cetak', compact('datamember', 'no', 'setting'));
        $pdf = PDF::loadView('member.cetak', compact('datamember', 'no', 'setting'));
        $pdf->setPaper(array(0, 0, 566.93, 850.39), 'potrait');
        return $pdf->stream('member.pdf');
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_member as $id) {
            $member = Member::find($id);
            $member->delete();
        }

        return response(null, 204);
    }

    public function cari(Request $request)
    {
        $keyword = $request->get('keyword');
        
        $member = Member::where('nama', 'like', "%$keyword%")
                    ->orWhere('telepon', 'like', "%$keyword%")
                    ->limit(10)
                    ->get();
        
        return response()->json($member);
    }
}