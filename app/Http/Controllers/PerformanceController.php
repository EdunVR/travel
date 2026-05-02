<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Performance;
use App\Models\Recruitment;
use PDF;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari request
        $month = $request->input('month', date('Y-m')); // Default: bulan dan tahun saat ini
        $startDate = date('Y-m-01', strtotime($month)); // Tanggal awal bulan
        $endDate = date('Y-m-t', strtotime($month)); // Tanggal akhir bulan

        // Ambil data kinerja berdasarkan periode yang dipilih
        $performances = Performance::with('recruitment')
            ->whereBetween('evaluation_date', [$startDate, $endDate])
            ->get();

        return view('hrm.performance.index', compact('performances', 'month'));
    }

    public function create()
    {
        $recruitments = Recruitment::all();
        return view('hrm.performance.create', compact('recruitments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'evaluation_date' => 'required|date',
            'criteria' => 'required|string|max:255',
            'score' => 'required|numeric|between:0,10', // Nilai antara 0 hingga 10
            'remarks' => 'nullable|string',
        ]);

        // Simpan data kinerja
        Performance::create([
            'recruitment_id' => $request->recruitment_id,
            'evaluation_date' => $request->evaluation_date,
            'criteria' => $request->criteria,
            'score' => $request->score,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('hrm.performance.index')->with('success', 'Data kinerja berhasil ditambahkan.');
    }

    public function edit(Performance $performance)
    {
        $recruitments = Recruitment::all();
        return view('hrm.performance.edit', compact('recruitments', 'performance'));
    }

    public function update(Request $request, Performance $performance)
    {
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'evaluation_date' => 'required|date',
            'criteria' => 'required|string|max:255',
            'score' => 'required|numeric|between:0,10', // Nilai antara 0 hingga 10
            'remarks' => 'nullable|string',
        ]);

        // Update data kinerja
        $performance->update([
            'recruitment_id' => $request->recruitment_id,
            'evaluation_date' => $request->evaluation_date,
            'criteria' => $request->criteria,
            'score' => $request->score,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('hrm.performance.index')->with('success', 'Data kinerja berhasil diperbarui.');
    }

    public function destroy(Performance $performance)
    {
        $performance->delete();
        return redirect()->route('hrm.performance.index')->with('success', 'Performance deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        // Ambil bulan dan tahun dari request
        $month = $request->input('month', date('Y-m')); // Default: bulan dan tahun saat ini
        $startDate = date('Y-m-01', strtotime($month)); // Tanggal awal bulan
        $endDate = date('Y-m-t', strtotime($month)); // Tanggal akhir bulan

        // Ambil data kinerja berdasarkan periode yang dipilih
        $performances = Performance::with('recruitment')
            ->whereBetween('evaluation_date', [$startDate, $endDate])
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('hrm.performance.export_pdf', compact('performances', 'month'));

        // Download PDF
        return $pdf->download('laporan_kinerja_karyawan_' . $month . '.pdf');
    }
}