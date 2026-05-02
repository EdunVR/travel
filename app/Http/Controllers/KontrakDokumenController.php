<?php

namespace App\Http\Controllers;

use App\Models\KontrakKerja;
use App\Models\PerpanjanganKontrak;
use App\Models\SuratPeringatan;
use App\Models\DokumenHr;
use App\Models\Recruitment;
use App\Models\Outlet;
use App\Traits\HasOutletFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KontrakDokumenController extends Controller
{
    use HasOutletFilter;
    
    // Helper method to get current outlet ID
    private function getCurrentOutletId()
    {
        return session('selected_outlet_id') ?? session('outlet_id');
    }
    
    // Helper method to get employees (using Recruitment model)
    private function getEmployees($outletId = null)
    {
        // Priority: parameter > selected_outlet_id session > outlet_id session
        $outletId = $outletId ?? session('selected_outlet_id') ?? session('outlet_id');
        
        \Log::info('=== GET EMPLOYEES DEBUG ===');
        \Log::info('Outlet ID from parameter: ' . ($outletId ?? 'null'));
        \Log::info('selected_outlet_id from session: ' . (session('selected_outlet_id') ?? 'null'));
        \Log::info('outlet_id from session: ' . (session('outlet_id') ?? 'null'));
        \Log::info('Final Outlet ID used: ' . ($outletId ?? 'null'));
        
        // Use Recruitment model as employee data
        $query = Recruitment::where('outlet_id', $outletId)
            ->where('status', 'active')
            ->select('id', 'name', 'position', 'department');
        
        // Log the SQL query
        \Log::info('SQL Query: ' . $query->toSql());
        \Log::info('Query Bindings: ' . json_encode($query->getBindings()));
        
        $employees = $query->get();
        
        \Log::info('Total employees found: ' . $employees->count());
        \Log::info('Employees data: ' . $employees->toJson());
        
        // Also check total recruitments in database
        $totalRecruitments = Recruitment::count();
        $activeRecruitments = Recruitment::where('status', 'active')->count();
        \Log::info('Total recruitments in DB: ' . $totalRecruitments);
        \Log::info('Active recruitments in DB: ' . $activeRecruitments);
        
        // Check if outlet_id is null
        $nullOutletCount = Recruitment::whereNull('outlet_id')->where('status', 'active')->count();
        \Log::info('Active recruitments with NULL outlet_id: ' . $nullOutletCount);
        
        return $employees;
    }
    
    // ==================== DASHBOARD ====================
    public function index(Request $request)
    {
        // Get user's accessible outlets
        $outlets = $this->getUserOutlets();
        
        // Get selected outlet from request or session
        $selectedOutletId = $request->get('outlet_id', session('selected_outlet_id'));
        
        // If no outlet selected, use first accessible outlet
        if (!$selectedOutletId && $outlets->isNotEmpty()) {
            $selectedOutletId = $outlets->first()->id_outlet;
        }
        
        // Store in session
        if ($selectedOutletId) {
            session(['selected_outlet_id' => $selectedOutletId]);
        }
        
        \Log::info('=== DASHBOARD INDEX DEBUG ===');
        \Log::info('Selected Outlet ID: ' . ($selectedOutletId ?? 'null'));
        \Log::info('Total accessible outlets: ' . $outlets->count());
        
        $stats = [
            'total_kontrak_aktif' => KontrakKerja::where('outlet_id', $selectedOutletId)
                ->where('status', 'aktif')->count(),
            'kontrak_akan_habis' => KontrakKerja::where('outlet_id', $selectedOutletId)
                ->where('status', 'aktif')
                ->whereDate('tanggal_selesai', '<=', Carbon::now()->addDays(30))
                ->whereDate('tanggal_selesai', '>=', Carbon::now())
                ->count(),
            'total_sp_aktif' => SuratPeringatan::where('outlet_id', $selectedOutletId)
                ->where('status', 'aktif')->count(),
            'total_dokumen' => DokumenHr::where('outlet_id', $selectedOutletId)->count(),
        ];

        return view('admin.sdm.kontrak.index', compact('stats', 'outlets', 'selectedOutletId'));
    }

    // ==================== KONTRAK KERJA ====================
    public function kontrakIndex(Request $request)
    {
        $outletId = session('selected_outlet_id') ?? session('outlet_id');
        
        \Log::info('=== KONTRAK INDEX DEBUG ===');
        \Log::info('selected_outlet_id: ' . (session('selected_outlet_id') ?? 'null'));
        \Log::info('outlet_id: ' . (session('outlet_id') ?? 'null'));
        \Log::info('Final outlet_id used: ' . ($outletId ?? 'null'));
        
        $employees = $this->getEmployees($outletId);
        
        \Log::info('Employees for filter: ' . $employees->count());
        
        $query = KontrakKerja::with(['recruitment', 'outlet']);
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        // Filter
        if ($request->filled('recruitment_id')) {
            $query->where('recruitment_id', $request->recruitment_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_kontrak')) {
            $query->where('jenis_kontrak', $request->jenis_kontrak);
        }

        $kontrak = $query->latest()->paginate(20);

        return view('admin.sdm.kontrak.kontrak-index', compact('kontrak', 'employees'));
    }

    public function kontrakCreate()
    {
        $outletId = session('selected_outlet_id') ?? session('outlet_id');
        
        \Log::info('=== KONTRAK CREATE DEBUG ===');
        \Log::info('selected_outlet_id: ' . (session('selected_outlet_id') ?? 'null'));
        \Log::info('outlet_id: ' . (session('outlet_id') ?? 'null'));
        \Log::info('Final outlet_id used: ' . ($outletId ?? 'null'));
        \Log::info('All session data: ' . json_encode(session()->all()));
        
        $employees = $this->getEmployees($outletId);
        
        \Log::info('Employees passed to view: ' . $employees->count() . ' records');
        \Log::info('View data: ' . json_encode(['employees' => $employees->toArray()]));
        
        return view('admin.sdm.kontrak.kontrak-form', compact('employees'));
    }

    public function kontrakStore(Request $request)
    {
        $validated = $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'nomor_kontrak' => 'required|unique:kontrak_kerja,nomor_kontrak',
            'jenis_kontrak' => 'required|string',
            'jabatan' => 'required|string',
            'unit_kerja' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'gaji_pokok' => 'nullable|numeric',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['outlet_id'] = session('selected_outlet_id') ?? session('outlet_id');

        // Upload file
        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('kontrak', 'public');
        }

        // Hitung durasi
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $validated['durasi_bulan'] = Carbon::parse($request->tanggal_mulai)
                ->diffInMonths(Carbon::parse($request->tanggal_selesai));
        }

        KontrakKerja::create($validated);

        return redirect()->route('sdm.kontrak.kontrak.index')
            ->with('success', 'Kontrak kerja berhasil ditambahkan');
    }

    public function kontrakEdit($id)
    {
        $kontrak = KontrakKerja::findOrFail($id);
        $outletId = session('selected_outlet_id') ?? session('outlet_id');
        $employees = $this->getEmployees($outletId);
        
        return view('admin.sdm.kontrak.kontrak-form', compact('kontrak', 'employees'));
    }

    public function kontrakUpdate(Request $request, $id)
    {
        $kontrak = KontrakKerja::findOrFail($id);

        $validated = $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'nomor_kontrak' => 'required|unique:kontrak_kerja,nomor_kontrak,' . $id,
            'jenis_kontrak' => 'required|string',
            'jabatan' => 'required|string',
            'unit_kerja' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'gaji_pokok' => 'nullable|numeric',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,habis,diperpanjang,dibatalkan',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Upload file baru
        if ($request->hasFile('file')) {
            if ($kontrak->file_path) {
                Storage::disk('public')->delete($kontrak->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('kontrak', 'public');
        }

        // Hitung durasi
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $validated['durasi_bulan'] = Carbon::parse($request->tanggal_mulai)
                ->diffInMonths(Carbon::parse($request->tanggal_selesai));
        }

        $kontrak->update($validated);

        return redirect()->route('sdm.kontrak.kontrak.index')
            ->with('success', 'Kontrak kerja berhasil diperbarui');
    }

    public function kontrakDestroy($id)
    {
        $kontrak = KontrakKerja::findOrFail($id);
        
        if ($kontrak->file_path) {
            Storage::disk('public')->delete($kontrak->file_path);
        }
        
        $kontrak->delete();

        return redirect()->route('sdm.kontrak.kontrak.index')
            ->with('success', 'Kontrak kerja berhasil dihapus');
    }

    // ==================== PERPANJANGAN KONTRAK ====================
    public function perpanjanganIndex(Request $request)
    {
        $outletId = $this->getCurrentOutletId();
        $query = PerpanjanganKontrak::with(['kontrakLama.recruitment', 'kontrakBaru'])
            ->whereHas('kontrakLama', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            });

        $perpanjangan = $query->latest()->paginate(20);

        return view('admin.sdm.kontrak.perpanjangan-index', compact('perpanjangan'));
    }

    public function perpanjanganCreate()
    {
        $outletId = $this->getCurrentOutletId();
        $kontrakAktif = KontrakKerja::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('status', 'aktif')
            ->get();
        
        return view('admin.sdm.kontrak.perpanjangan-form', compact('kontrakAktif'));
    }
    
    public function printPerpanjanganPdf($id)
    {
        $perpanjangan = PerpanjanganKontrak::with(['kontrakLama.recruitment', 'kontrakBaru.recruitment', 'kontrakLama.outlet'])
            ->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.perpanjangan-single', compact('perpanjangan'));
        return $pdf->stream('perpanjangan-kontrak-' . $perpanjangan->kontrakBaru->nomor_kontrak . '.pdf');
    }

    public function perpanjanganStore(Request $request)
    {
        $validated = $request->validate([
            'kontrak_lama_id' => 'required|exists:kontrak_kerja,id',
            'tanggal_mulai_baru' => 'required|date',
            'tanggal_selesai_baru' => 'required|date|after:tanggal_mulai_baru',
            'alasan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $kontrakLama = KontrakKerja::findOrFail($request->kontrak_lama_id);

        // Buat kontrak baru
        $kontrakBaru = KontrakKerja::create([
            'recruitment_id' => $kontrakLama->recruitment_id,
            'outlet_id' => $kontrakLama->outlet_id,
            'nomor_kontrak' => $request->nomor_kontrak_baru,
            'jenis_kontrak' => $kontrakLama->jenis_kontrak,
            'jabatan' => $kontrakLama->jabatan,
            'unit_kerja' => $kontrakLama->unit_kerja,
            'tanggal_mulai' => $request->tanggal_mulai_baru,
            'tanggal_selesai' => $request->tanggal_selesai_baru,
            'gaji_pokok' => $kontrakLama->gaji_pokok,
            'deskripsi' => $kontrakLama->deskripsi,
            'status' => 'aktif',
            'perpanjangan_dari' => $kontrakLama->id,
        ]);

        // Upload file perpanjangan
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('perpanjangan', 'public');
        }

        // Simpan data perpanjangan
        PerpanjanganKontrak::create([
            'kontrak_lama_id' => $kontrakLama->id,
            'kontrak_baru_id' => $kontrakBaru->id,
            'tanggal_perpanjangan' => now(),
            'alasan' => $request->alasan,
            'file_path' => $filePath,
        ]);

        // Update status kontrak lama
        $kontrakLama->update(['status' => 'diperpanjang']);

        return redirect()->route('sdm.kontrak.perpanjangan.index')
            ->with('success', 'Kontrak berhasil diperpanjang');
    }

    // ==================== SURAT PERINGATAN ====================
    public function spIndex(Request $request)
    {
        $outletId = $this->getCurrentOutletId();
        $query = SuratPeringatan::with(['recruitment', 'outlet'])
            ->where('outlet_id', $outletId);

        // Filter
        if ($request->filled('recruitment_id')) {
            $query->where('recruitment_id', $request->recruitment_id);
        }
        if ($request->filled('jenis_sp')) {
            $query->where('jenis_sp', $request->jenis_sp);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sp = $query->latest()->paginate(20);
        $employees = $this->getEmployees($outletId);

        return view('admin.sdm.kontrak.sp-index', compact('sp', 'employees'));
    }

    public function spStore(Request $request)
    {
        $validated = $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'nomor_sp' => 'required|unique:surat_peringatan,nomor_sp',
            'jenis_sp' => 'required|in:SP1,SP2,SP3',
            'tanggal_sp' => 'required|date',
            'tanggal_berlaku' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku',
            'alasan' => 'required|string',
            'catatan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['outlet_id'] = $this->getCurrentOutletId();

        // Upload file
        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('sp', 'public');
        }

        SuratPeringatan::create($validated);

        return redirect()->route('sdm.kontrak.sp.index')
            ->with('success', 'Surat peringatan berhasil ditambahkan');
    }

    public function spCreate()
    {
        $outletId = $this->getCurrentOutletId();
        $employees = $this->getEmployees($outletId);
        
        return view('admin.sdm.kontrak.sp-form', compact('employees'));
    }

    public function spEdit($id)
    {
        $sp = SuratPeringatan::findOrFail($id);
        $outletId = $this->getCurrentOutletId();
        $employees = $this->getEmployees($outletId);
        
        return view('admin.sdm.kontrak.sp-form', compact('sp', 'employees'));
    }

    public function spUpdate(Request $request, $id)
    {
        $sp = SuratPeringatan::findOrFail($id);

        $validated = $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'nomor_sp' => 'required|unique:surat_peringatan,nomor_sp,' . $id,
            'jenis_sp' => 'required|in:SP1,SP2,SP3',
            'tanggal_sp' => 'required|date',
            'tanggal_berlaku' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku',
            'alasan' => 'required|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:aktif,selesai,dibatalkan',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Upload file baru
        if ($request->hasFile('file')) {
            if ($sp->file_path) {
                Storage::disk('public')->delete($sp->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('sp', 'public');
        }

        $sp->update($validated);

        return redirect()->route('sdm.kontrak.sp.index')
            ->with('success', 'Surat peringatan berhasil diperbarui');
    }

    public function spDestroy($id)
    {
        $sp = SuratPeringatan::findOrFail($id);
        
        if ($sp->file_path) {
            Storage::disk('public')->delete($sp->file_path);
        }
        
        $sp->delete();

        return redirect()->route('sdm.kontrak.sp.index')
            ->with('success', 'Surat peringatan berhasil dihapus');
    }

    public function printSpPdf($id)
    {
        $sp = SuratPeringatan::with(['recruitment', 'outlet'])->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.sp-single', compact('sp'));
        return $pdf->stream('surat-peringatan-' . $sp->nomor_sp . '.pdf');
    }

    // ==================== DOKUMEN HR ====================
    public function dokumenIndex(Request $request)
    {
        $outletId = $this->getCurrentOutletId();
        $query = DokumenHr::with(['recruitment', 'outlet'])
            ->where('outlet_id', $outletId);

        // Filter
        if ($request->filled('recruitment_id')) {
            $query->where('recruitment_id', $request->recruitment_id);
        }
        if ($request->filled('jenis_dokumen')) {
            $query->where('jenis_dokumen', $request->jenis_dokumen);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dokumen = $query->latest()->paginate(20);
        $employees = $this->getEmployees($outletId);

        return view('admin.sdm.kontrak.dokumen-index', compact('dokumen', 'employees'));
    }

    public function dokumenCreate()
    {
        $outletId = $this->getCurrentOutletId();
        $employees = $this->getEmployees($outletId);
        
        return view('admin.sdm.kontrak.dokumen-form', compact('employees'));
    }

    public function dokumenStore(Request $request)
    {
        $validated = $request->validate([
            'recruitment_id' => 'nullable|exists:recruitments,id',
            'nomor_dokumen' => 'required|unique:dokumen_hr,nomor_dokumen',
            'jenis_dokumen' => 'required|string',
            'judul_dokumen' => 'required|string',
            'deskripsi' => 'nullable|string',
            'tanggal_terbit' => 'required|date',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku',
            'memiliki_masa_berlaku' => 'boolean',
            'catatan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['outlet_id'] = $this->getCurrentOutletId();
        $validated['memiliki_masa_berlaku'] = $request->has('memiliki_masa_berlaku');

        // Upload file
        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('dokumen-hr', 'public');
        }

        DokumenHr::create($validated);

        return redirect()->route('sdm.kontrak.dokumen.index')
            ->with('success', 'Dokumen HR berhasil ditambahkan');
    }

    public function dokumenEdit($id)
    {
        $dokumen = DokumenHr::findOrFail($id);
        $outletId = session('outlet_id');
        $employees = $this->getEmployees($outletId);
        
        return view('admin.sdm.kontrak.dokumen-form', compact('dokumen', 'employees'));
    }

    public function dokumenUpdate(Request $request, $id)
    {
        $dokumen = DokumenHr::findOrFail($id);

        $validated = $request->validate([
            'recruitment_id' => 'nullable|exists:recruitments,id',
            'nomor_dokumen' => 'required|unique:dokumen_hr,nomor_dokumen,' . $id,
            'jenis_dokumen' => 'required|string',
            'judul_dokumen' => 'required|string',
            'deskripsi' => 'nullable|string',
            'tanggal_terbit' => 'required|date',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku',
            'memiliki_masa_berlaku' => 'boolean',
            'catatan' => 'nullable|string',
            'status' => 'required|in:aktif,habis,dibatalkan',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['memiliki_masa_berlaku'] = $request->has('memiliki_masa_berlaku');

        // Upload file baru
        if ($request->hasFile('file')) {
            if ($dokumen->file_path) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('dokumen-hr', 'public');
        }

        $dokumen->update($validated);

        return redirect()->route('sdm.kontrak.dokumen.index')
            ->with('success', 'Dokumen HR berhasil diperbarui');
    }

    public function dokumenDestroy($id)
    {
        $dokumen = DokumenHr::findOrFail($id);
        
        if ($dokumen->file_path) {
            Storage::disk('public')->delete($dokumen->file_path);
        }
        
        $dokumen->delete();

        return redirect()->route('sdm.kontrak.dokumen.index')
            ->with('success', 'Dokumen HR berhasil dihapus');
    }

    public function printDokumenPdf($id)
    {
        $dokumen = DokumenHr::with(['recruitment', 'outlet'])->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.dokumen-single', compact('dokumen'));
        return $pdf->stream('dokumen-hr-' . $dokumen->nomor_dokumen . '.pdf');
    }

    // ==================== MONITORING ====================
    public function monitoring(Request $request)
    {
        $outletId = $this->getCurrentOutletId();
        
        // Ambil semua dokumen dengan masa berlaku
        $kontrak = KontrakKerja::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_selesai')
            ->get();

        $sp = SuratPeringatan::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->get();

        $dokumen = DokumenHr::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('memiliki_masa_berlaku', true)
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->get();

        // Filter berdasarkan status
        if ($request->filled('filter_status')) {
            $filter = $request->filter_status;
            
            $kontrak = $kontrak->filter(function($item) use ($filter) {
                return $item->status_warna === $filter;
            });
            
            $sp = $sp->filter(function($item) use ($filter) {
                return $item->status_warna === $filter;
            });
            
            $dokumen = $dokumen->filter(function($item) use ($filter) {
                return $item->status_warna === $filter;
            });
        }

        return view('admin.sdm.kontrak.monitoring', compact('kontrak', 'sp', 'dokumen'));
    }

    // ==================== EXPORT PDF ====================
    public function exportKontrakPdf(Request $request)
    {
        $outletId = session('outlet_id');
        $query = KontrakKerja::with(['recruitment', 'outlet'])
            ->where('outlet_id', $outletId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kontrak = $query->latest()->get();
        $outlet = Outlet::find($outletId);

        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.kontrak', compact('kontrak', 'outlet'));
        return $pdf->stream('daftar-kontrak-' . date('Y-m-d') . '.pdf');
    }

    public function exportSpPdf(Request $request)
    {
        $outletId = session('outlet_id');
        $query = SuratPeringatan::with(['recruitment', 'outlet'])
            ->where('outlet_id', $outletId);

        if ($request->filled('jenis_sp')) {
            $query->where('jenis_sp', $request->jenis_sp);
        }

        $sp = $query->latest()->get();
        $outlet = Outlet::find($outletId);

        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.sp', compact('sp', 'outlet'));
        return $pdf->stream('daftar-sp-' . date('Y-m-d') . '.pdf');
    }

    public function printKontrakPdf($id)
    {
        $kontrak = KontrakKerja::with(['recruitment', 'outlet'])->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.kontrak-single', compact('kontrak'));
        return $pdf->stream('kontrak-' . $kontrak->nomor_kontrak . '.pdf');
    }

    public function exportMonitoringPdf(Request $request)
    {
        $outletId = $this->getCurrentOutletId();
        
        $kontrak = KontrakKerja::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_selesai')
            ->get();

        $sp = SuratPeringatan::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->get();

        $dokumen = DokumenHr::with('recruitment')
            ->where('outlet_id', $outletId)
            ->where('memiliki_masa_berlaku', true)
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->get();

        $outlet = Outlet::find($outletId);

        $pdf = Pdf::loadView('admin.sdm.kontrak.pdf.monitoring', compact('kontrak', 'sp', 'dokumen', 'outlet'));
        return $pdf->stream('monitoring-dokumen-' . date('Y-m-d') . '.pdf');
    }
}
