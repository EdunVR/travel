<?php

namespace App\Http\Controllers;

use App\Models\JobTarget;
use App\Models\JobGradeSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceAppraisalController extends Controller
{
    // ─── Helper: cek super admin ─────────────────────────────────────────────
    private function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    // ─── Halaman utama ────────────────────────────────────────────────────────
    public function index()
    {
        $isSuperAdmin = $this->isSuperAdmin();

        // Super admin: lihat semua user + ringkasan progress mereka
        // User biasa: lihat job list sendiri saja
        if ($isSuperAdmin) {
            $users = User::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        } else {
            $users = collect();
        }

        $gradeSettings = JobGradeSetting::orderByDesc('min_percent')->get();

        // Seed defaults jika belum ada setting
        if ($gradeSettings->isEmpty()) {
            foreach (JobGradeSetting::defaults() as $d) {
                JobGradeSetting::create($d + ['updated_by' => auth()->id()]);
            }
            $gradeSettings = JobGradeSetting::orderByDesc('min_percent')->get();
        }

        return view('admin.sdm.kinerja.index', compact('isSuperAdmin', 'users', 'gradeSettings'));
    }

    // ─── API: ambil job list + progress per user ──────────────────────────────
    public function getData(Request $request)
    {
        $userId = $request->get('user_id');

        // User biasa hanya boleh lihat miliknya sendiri (abaikan user_id dari request)
        if (!$this->isSuperAdmin()) {
            $userId = auth()->id();
        }

        // Super admin tanpa user_id → fallback ke dirinya sendiri
        if (!$userId) {
            $userId = auth()->id();
        }

        $targets = JobTarget::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $items = $targets->map(fn($t) => [
            'id'                => $t->id,
            'title'             => $t->title,
            'description'       => $t->description,
            'target_percent'    => $t->target_percent,
            'realisasi_percent' => $t->realisasi_percent,
            'progress'          => $t->getProgressPercent(),
            'period'            => $t->period,
        ]);

        // Overall: rata-rata progress dari semua job
        $overall = $items->count() > 0
            ? round($items->avg('progress'), 1)
            : 0;

        $grade = JobGradeSetting::resolveGrade($overall);

        return response()->json([
            'success' => true,
            'data'    => $items,
            'overall' => [
                'progress'    => $overall,
                'grade'       => $grade?->grade ?? '-',
                'grade_label' => $grade?->label ?? '-',
                'grade_color' => $grade?->color ?? 'gray',
            ],
        ]);
    }

    // ─── API: ringkasan semua user (super admin) ──────────────────────────────
    public function getAllUsersProgress(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $result = $users->map(function ($user) {
            $targets = JobTarget::where('user_id', $user->id)->get();
            $jobCount = $targets->count();

            $overall = 0;
            if ($jobCount > 0) {
                $overall = round($targets->avg(fn($t) => $t->getProgressPercent()), 1);
            }

            $grade = JobGradeSetting::resolveGrade($overall);

            return [
                'user_id'      => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'job_count'    => $jobCount,
                'overall'      => $overall,
                'grade'        => $grade?->grade ?? '-',
                'grade_label'  => $grade?->label ?? '-',
                'grade_color'  => $grade?->color ?? 'gray',
            ];
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    // ─── CRUD: tambah job target (super admin) ────────────────────────────────
    public function store(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'target_percent' => 'required|numeric|min:0|max:100',
            'period'         => 'nullable|string|max:100',
        ]);

        $target = JobTarget::create(array_merge($validated, [
            'realisasi_percent' => 0,
            'created_by'        => auth()->id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Job target berhasil ditambahkan',
            'data'    => $target,
        ]);
    }

    // ─── UPDATE job target (super admin edit title/target, user edit realisasi) 
    public function update(Request $request, $id)
    {
        $target = JobTarget::findOrFail($id);
        $isSuperAdmin = $this->isSuperAdmin();

        // User biasa hanya boleh update realisasi miliknya
        if (!$isSuperAdmin && $target->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if ($isSuperAdmin) {
            $validated = $request->validate([
                'title'             => 'required|string|max:255',
                'description'       => 'nullable|string',
                'target_percent'    => 'required|numeric|min:0|max:100',
                'realisasi_percent' => 'sometimes|numeric|min:0|max:100',
                'period'            => 'nullable|string|max:100',
            ]);
        } else {
            $validated = $request->validate([
                'realisasi_percent' => 'required|numeric|min:0|max:100',
            ]);
        }

        $target->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job target berhasil diupdate',
            'data'    => [
                'id'                => $target->id,
                'title'             => $target->title,
                'target_percent'    => $target->target_percent,
                'realisasi_percent' => $target->realisasi_percent,
                'progress'          => $target->getProgressPercent(),
            ],
        ]);
    }

    // ─── DELETE job target (super admin) ─────────────────────────────────────
    public function destroy($id)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $target = JobTarget::findOrFail($id);
        $target->delete();

        return response()->json(['success' => true, 'message' => 'Job target dihapus']);
    }

    // ─── Grade Settings (super admin) ────────────────────────────────────────
    public function getGradeSettings()
    {
        $settings = JobGradeSetting::orderByDesc('min_percent')->get();
        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function saveGradeSettings(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'grades'                => 'required|array|min:1',
            'grades.*.grade'        => 'required|string|max:1',
            'grades.*.min_percent'  => 'required|numeric|min:0|max:100',
            'grades.*.max_percent'  => 'required|numeric|min:0|max:100',
            'grades.*.label'        => 'required|string|max:100',
            'grades.*.color'        => 'required|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            JobGradeSetting::truncate();
            foreach ($validated['grades'] as $g) {
                JobGradeSetting::create(array_merge($g, ['updated_by' => auth()->id()]));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan nilai berhasil disimpan']);
    }

    // ─── Kept for route compatibility (unused) ────────────────────────────────
    public function show($id) { return response()->json(['success' => false], 404); }
    public function exportPdf(Request $request) { abort(404); }
    public function getData_legacy(Request $request) { return $this->getData($request); }
    public function getStatistics(Request $request) { return response()->json(['success' => true, 'data' => []]); }
    public function getEmployees(Request $request) { return response()->json(['success' => true, 'data' => []]); }
}
