<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function getKabupaten($provinsi_id)
    {
        $kabupatens = DB::table('reg_regencies')
            ->where('province_id', $provinsi_id)
            ->orderBy('name')
            ->get();
        return response()->json($kabupatens);
    }

    public function getKecamatan($kabupaten_id)
    {
        $kecamatans = DB::table('reg_districts')
            ->where('regency_id', $kabupaten_id)
            ->orderBy('name')
            ->get();
        return response()->json($kecamatans);
    }

    public function getDesa($kecamatan_id)
    {
        $desas = DB::table('reg_villages')
            ->where('district_id', $kecamatan_id)
            ->orderBy('name')
            ->get();
        return response()->json($desas);
    }

    public function getKecamatanDetail($id)
    {
        $kecamatan = DB::table('reg_districts')
            ->where('id', $id)
            ->first();
        return response()->json($kecamatan);
    }

    public function getDesaDetail($id)
    {
        $desa = DB::table('reg_villages')
            ->where('id', $id)
            ->first();
        return response()->json($desa);
    }
}