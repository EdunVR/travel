<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use PDF;
use App\Models\Setting;

class TrainingController extends Controller
{
    // Menampilkan semua data pelatihan
    public function index()
    {
        $trainings = Training::with('recruitment')->get();
        return view('hrm.training.index', compact('trainings'));
    }

    // Menampilkan form tambah pelatihan
    public function create()
    {
        $recruitments = Recruitment::all(); // Ambil data recruitments untuk dropdown
        return view('hrm.training.create', compact('recruitments'));
    }

    // Menyimpan data pelatihan baru
    public function store(Request $request)
    {
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'training_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'trainer' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Training::create($request->all());

        return redirect()->route('hrm.training.index')->with('success', 'Data pelatihan berhasil ditambahkan.');
    }

    // Menampilkan form edit pelatihan
    public function edit(Training $training)
    {
        $recruitments = Recruitment::all(); // Ambil data recruitments untuk dropdown
        return view('hrm.training.edit', compact('training', 'recruitments'));
    }

    // Mengupdate data pelatihan
    public function update(Request $request, Training $training)
    {
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'training_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'trainer' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $training->update($request->all());

        return redirect()->route('hrm.training.index')->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    // Menghapus data pelatihan
    public function destroy(Training $training)
    {
        $training->delete();
        return redirect()->route('hrm.training.index')->with('success', 'Data pelatihan berhasil dihapus.');
    }

    public function print($id)
    {
        $setting = Setting::first();
        // Ambil data pelatihan berdasarkan ID
        $training = Training::with('recruitment')->findOrFail($id);

        // Generate PDF
        $pdf = Pdf::loadView('hrm.training.print', compact('training', 'setting'));

        // Download PDF
        return $pdf->download('flyer_pelatihan_' . $training->training_name . '.pdf');
    }
}