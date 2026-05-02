<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Prospek;
use App\Models\ProspekTimeline;
use App\Models\Recruitment;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProspekTemplateExport;
use App\Imports\ProspekImport;
use App\Models\MapUmum;

class ProspekController extends Controller
{
    public function index()
    {
        $prospeks = Prospek::with([
                'recruitment', 
                'outlet',
                'timeline',
                'province:id,name',
                'regency:id,name',
                'district:id,name',
                'village:id,name'
            ])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('crm.prospek.index', compact('prospeks'));
    }

    public function create()
    {
        $recruitments = Recruitment::all();
        $outlets = Outlet::all();
        $provinsis = DB::table('reg_provinces')->orderBy('name')->get();
        
        return view('crm.prospek.create', compact('recruitments', 'outlets', 'provinsis'));
    }

    public function store(Request $request)
    {

        DB::transaction(function () use ($request) {
            $prospek = Prospek::create($request->all());

            $prospek->timeline()->create([
                'status' => 'prospek',
                'tanggal' => now(),
                'deskripsi' => $request->deskripsi_prospek ?? 'Prospek awal dibuat'
            ]);

            if ($request->latitude && $request->longitude) {
                $prospek->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                ]);
            }
        });

        return redirect()->route('prospek.index')->with('success', 'Prospek berhasil ditambahkan');
    }

    public function edit($id)
    {
        $prospek = Prospek::with('timeline')->findOrFail($id);
        $recruitments = Recruitment::all();
        $outlets = Outlet::all();
        $provinsis = DB::table('reg_provinces')->orderBy('name')->get();
        $kabupatens = DB::table('reg_regencies')
            ->where('province_id', $prospek->provinsi_id)
            ->orderBy('name')
            ->get();
        $kecamatans = DB::table('reg_districts')
            ->where('regency_id', $prospek->kabupaten_id)
            ->orderBy('name')
            ->get();
        $desas = DB::table('reg_villages')
            ->where('district_id', $prospek->kecamatan_id)
            ->orderBy('name')
            ->get();
        
        return view('crm.prospek.edit', compact('prospek', 'recruitments', 'outlets', 'provinsis', 'kabupatens', 'kecamatans', 'desas'));
    }

    public function update(Request $request, $id)
    {
        $prospek = Prospek::findOrFail($id);

        try {
            DB::transaction(function () use ($request, $prospek) {
                $oldStatus = $prospek->current_status;
                
                // Update data prospek
                $prospek->update($request->except(['_token', '_method']));

                // Update koordinat jika ada
                if ($request->filled('latitude') && $request->filled('longitude')) {
                    $prospek->update([
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude
                    ]);
                }

                // Buat timeline jika status berubah
                if ($request->filled('current_status') && $request->current_status != $oldStatus) {
                    $prospek->timeline()->create([
                        'status' => $request->current_status,
                        'tanggal' => now(),
                        'deskripsi' => $request->{'deskripsi_'.$request->current_status} ?? 'Status diubah ke '.$request->current_status
                    ]);
                }

                // Konversi ke member jika status berubah ke closing/deposit
                if (in_array($request->current_status, ['closing', 'deposit']) && 
                    !in_array($oldStatus, ['closing', 'deposit'])) {
                    $this->convertToMember($prospek);
                }
            });

            return redirect()->route('prospek.index')
                ->with('success', 'Prospek berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan perubahan: '.$e->getMessage())
                ->withInput();
        }
    }

    public function addTimeline(Request $request, $id)
    {
        $prospek = Prospek::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:prospek,followup,negosiasi,closing,deposit,gagal',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $timeline = $prospek->timeline()->create([
            'status' => $request->status,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi
        ]);

        $latestStatus = $prospek->timeline()->latest('tanggal')->first();
        if ($latestStatus && $latestStatus->status != $prospek->current_status) {
            $prospek->update(['current_status' => $latestStatus->status]);
            
            if (in_array($latestStatus->status, ['closing', 'deposit'])) {
                $this->convertToMember($prospek);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline berhasil ditambahkan'
            ]);
        }

        return redirect()->back()->with('success', 'Timeline berhasil ditambahkan');
    }

    public function removeTimeline($id)
    {
        $timeline = ProspekTimeline::findOrFail($id);
        $prospek = $timeline->prospek;
        
        DB::transaction(function () use ($timeline, $prospek) {
            $timeline->delete();
            
            $latestStatus = $prospek->timeline()->latest('tanggal')->first();
            $prospek->update([
                'current_status' => $latestStatus ? $latestStatus->status : 'prospek'
            ]);
        });

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline berhasil dihapus'
            ]);
        }

        return redirect()->back()->with('success', 'Timeline berhasil dihapus');
    }

    protected function convertToMember(Prospek $prospek)
    {
        $existingMember = Member::where('telepon', $prospek->telepon)
            ->orWhere('email', $prospek->email)
            ->first();

        if (!$existingMember) {
            Member::create([
                'nama' => $prospek->nama,
                'telepon' => $prospek->telepon,
                'email' => $prospek->email,
                'alamat' => $prospek->alamat,
                'kota_kab' => $prospek->kota_kab,
                'kecamatan' => $prospek->kecamatan,
                'desa_kelurahan' => $prospek->desa_kelurahan,
                'nama_perusahaan' => $prospek->nama_perusahaan,
                'id_tipe' => 1,
                'id_outlet' => $prospek->id_outlet,
                'latitude' => $prospek->latitude,
                'longitude' => $prospek->longitude
            ]);
        }
    }

    public function destroy($id)
    {
        $prospek = Prospek::findOrFail($id);
        $prospek->delete();
        
        return redirect()->route('prospek.index')->with('success', 'Prospek berhasil dihapus');
    }

    protected function validateProspek(Request $request, $prospek = null)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|email',
            'recruitment_id' => 'nullable|exists:recruitments,id',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'current_status' => 'required|in:prospek,followup,negosiasi,closing,deposit,gagal',
            'nama_perusahaan' => 'nullable|string|max:255',
            'jenis' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'kota_kab' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'pemilik_manager' => 'nullable|string|max:100',
            'kapasitas_produksi' => 'nullable|string|max:100',
            'sistem_produksi' => 'nullable|string|max:100',
            'bahan_bakar' => 'nullable|string|max:100',
            'informasi_perusahaan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        return Validator::make($request->all(), $rules);
    }

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

    public function showMap()
    {
        $prospeks = Prospek::with(['outlet', 'recruitment'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
            
        return view('crm.prospek.map', compact('prospeks'));
    }

    public function exportTemplate()
    {
        return Excel::download(new ProspekTemplateExport(), 'template_import_prospek.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $import = new ProspekImport();
            Excel::import($import, $request->file('file'));
            
            $importedCount = $import->getRowCount();
            $skippedRows = $import->getSkippedRows();
            
            $message = "Berhasil mengimport $importedCount data prospek";
            
            if ($skippedRows > 0) {
                $message .= " ($skippedRows baris kosong diabaikan)";
            }
            
            return redirect()->back()
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error("Error saat import: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function searchAndSaveLocation(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nama_lokasi' => 'required|string'
        ]);

        // Simpan ke tabel map_umum
        $mapUmum = MapUmum::create([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tipe' => 'pencarian'
        ]);

        return response()->json([
            'success' => true,
            'data' => $mapUmum
        ]);
    }

    // Update getLocations method
    public function getLocations()
    {
        $prospeks = Prospek::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id_prospek', 'nama_perusahaan', 'alamat', 'latitude', 'longitude', 'current_status', 'photo')
            ->get();
        
        $mapUmum = MapUmum::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'nama_lokasi as nama_perusahaan', 'latitude', 'longitude', 'tipe')
            ->get();
        
        return response()->json([
            'prospeks' => $prospeks,
            'map_umum' => $mapUmum
        ]);
    }

    public function deleteMapUmumLocation($id)
    {
        $mapUmum = MapUmum::findOrFail($id);
        $mapUmum->delete();
        
        return response()->json(['success' => true]);
    }

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $prospek = Prospek::findOrFail($id);
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($prospek->photo) {
                // Update path to public/img/prospek
                $oldPhotoPath = public_path('img/prospek/' . basename($prospek->photo));
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            
            // Store in public/img/prospek
            $file = $request->file('photo');
            $fileName = 'prospek_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/prospek'), $fileName);
            
            // Save relative path
            $prospek->photo = 'img/prospek/' . $fileName;
            $prospek->save();
        }

        return back()->with('success', 'Foto berhasil diupload');
    }

    public function deletePhoto($id)
    {
        $prospek = Prospek::findOrFail($id);
        
        if ($prospek->photo) {
            $photoPath = public_path($prospek->photo);
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
            $prospek->photo = null;
            $prospek->save();
        }

        return back()->with('success', 'Foto berhasil dihapus');
    }

}