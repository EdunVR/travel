<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProspekFieldSetting;

class ProspekSettingController extends Controller
{
    public function index()
    {
        $fields = [
            'nama', 'telepon', 'email', 'alamat', 'kota_kab', 'kecamatan', 'desa_kelurahan',
            'nama_perusahaan', 'jenis', 'pemilik_manager', 'kapasitas_produksi', 
            'sistem_produksi', 'bahan_bakar', 'informasi_perusahaan', 'latitude', 'longitude'
        ];
        
        $settings = ProspekFieldSetting::whereIn('field_name', $fields)->get();
        
        // Ensure all fields exist in settings
        foreach ($fields as $field) {
            if (!$settings->contains('field_name', $field)) {
                ProspekFieldSetting::create([
                    'field_name' => $field,
                    'is_required' => false
                ]);
            }
        }
        
        $settings = ProspekFieldSetting::whereIn('field_name', $fields)->get();
        
        return view('crm.prospek.setting', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'nama', 'telepon', 'email', 'alamat', 'kota_kab', 'kecamatan', 'desa_kelurahan',
            'nama_perusahaan', 'jenis', 'pemilik_manager', 'kapasitas_produksi', 
            'sistem_produksi', 'bahan_bakar', 'informasi_perusahaan', 'latitude', 'longitude'
        ];
        
        foreach ($fields as $field) {
            ProspekFieldSetting::updateOrCreate(
                ['field_name' => $field],
                ['is_required' => $request->$field === 'required']
            );
        }

        return redirect()->route('prospek.settings.index')->with('success', 'Pengaturan kolom berhasil diperbarui');
    }
}