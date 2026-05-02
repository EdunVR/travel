<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Member;

class UserController extends Controller
{
    public function index()
    {
        $outlets = Outlet::all();
        $agents = Member::where('id_tipe', 15)->get();
        return view('user.index', compact('outlets', 'agents'));
    }

    public function data()
    {
        $users = User::isNotAdmin()->orderBy('id', 'desc')->get();

        $users->transform(function ($user) {
            $outletNames = Outlet::whereIn('id_outlet', $user->akses_outlet ?? [])->pluck('nama_outlet')->toArray();
            $user->akses_outlet = $outletNames; // Ganti ID dengan nama outlet
            return $user;
        });

        return datatables()
            ->of($users)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('user.update', $user->id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('user.destroy', $user->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $aksesKhususOptions = ['Tampilkan Profit', 'Tampilkan Omset'];
        $outlets = Outlet::all();
        $agents = Member::where('id_tipe', 15)->get(); // Get semua agen
        
        return view('user.form', compact('aksesOptions', 'outlets', 'aksesKhususOptions', 'agents'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $aksesKhususOptions = ['Tampilkan Profit', 'Tampilkan Omset'];
        $outlets = Outlet::all();
        $agents = Member::where('id_tipe', 15)->get(); // Get semua agen
        
        return view('user.form', compact('user', 'aksesOptions', 'outlets', 'aksesKhususOptions', 'agents'));
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->level = 2;
        $user->foto = '/img/user.png';
        $user->akses = $request->akses;
        $user->akses_outlet = $request->akses_outlet;
        $user->akses_khusus = $request->akses_khusus;
        $user->id_agen = $request->id_agen;
        $user->is_agen = $request->has('is_agen') ? 1 : 0;

        $user->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('password') && $request->password != "") 
            $user->password = bcrypt($request->password);
        $user->akses = $request->akses;
        $user->akses_outlet = $request->akses_outlet;
        $user->akses_khusus = $request->akses_khusus;
        $user->id_agen = $request->id_agen;
        $user->is_agen = $request->has('is_agen') ? 1 : 0;

        $user->update();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return response()->json($user);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id)->delete();

        return response(null, 204);
    }

    public function profil()
    {
        $profil = auth()->user();
        return view('user.profil', compact('profil'));
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        
        $user->name = $request->name;
        if ($request->has('password') && $request->password != "") {
            if (Hash::check($request->old_password, $user->password)) {
                if ($request->password == $request->password_confirmation) {
                    $user->password = bcrypt($request->password);
                } else {
                    return response()->json('Konfirmasi password tidak sesuai', 422);
                }
            } else {
                return response()->json('Password lama tidak sesuai', 422);
            }
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama = 'logo-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $user->foto = "/img/$nama";
        }

        $user->update();

        return response()->json($user, 200);
    }

    
}