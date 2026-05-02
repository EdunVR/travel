<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recruitment;
use Illuminate\Support\Facades\Log;

class RecruitmentController extends Controller
{
    public function index()
    {
        // Hanya tampilkan rekrutmen dengan status "diterima"
        $recruitments = Recruitment::all();
        $managers = Recruitment::where('status', 'diterima')
                           ->get();
        return view('hrm.recruitment.index', compact('recruitments', 'managers'));
    }

    public function create()
    {
        $title = "Tambah Rekrutmen"; // Judul halaman
        $action = route('hrm.recruitment.store'); // URL untuk menyimpan data
        return view('hrm.recruitment.create', compact('title', 'action'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
            'department' => 'required',
            'jobdesk' => 'nullable|array',
            'salary' => 'required|numeric',
            'fingerprint_id' => 'nullable|integer', // Validasi fingerprint_id
            'is_registered_fingerprint' => 'nullable|boolean', // Validasi is_registered_fingerprint
        ]);

        // Simpan jobdesk sebagai JSON
        $data = $request->all();
        $data['jobdesk'] = json_encode($request->jobdesk);

        Recruitment::create($data);
        return redirect()->route('hrm.recruitment.index')->with('success', 'Rekrutmen berhasil ditambahkan.');
    }

    public function edit(Recruitment $recruitment)
    {
        $title = "Tambah Rekrutmen"; // Judul halaman
        $action = route('hrm.recruitment.update', $recruitment->id);
        return view('hrm.recruitment.edit', compact('title', 'action', 'recruitment'));
    }

    public function update(Request $request, Recruitment $recruitment)
    {
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
            'department' => 'required',
            'jobdesk' => 'nullable|array',
            'salary' => 'required|numeric',
            'fingerprint_id' => 'nullable|integer', // Validasi fingerprint_id
            'is_registered_fingerprint' => 'nullable|boolean', // Validasi is_registered_fingerprint
        ]);

        // Simpan jobdesk sebagai JSON
        $data = $request->all();
        $data['jobdesk'] = json_encode($request->jobdesk);

        $recruitment->update($data);
        return redirect()->route('hrm.recruitment.index')->with('success', 'Rekrutmen berhasil diperbarui.');
    }

    public function destroy(Recruitment $recruitment)
    {
        $recruitment->delete();
        return redirect()->route('hrm.recruitment.index')->with('success', 'Rekrutmen berhasil dihapus.');
    }

    public function printContract($id, Request $request)
    {
        $recruitment = Recruitment::findOrFail($id);
        $manager = Recruitment::findOrFail($request->manager_id); // Ambil data manager yang dipilih
        return view('hrm.recruitment.contract', compact('recruitment', 'manager'));
    }
}