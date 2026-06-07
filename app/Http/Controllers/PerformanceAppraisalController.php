<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\JobTarget;
use App\Models\JobGradeSetting;
use App\Models\Recruitment;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $today = Carbon::today();

        $items = $targets->map(function ($t) use ($today) {
            $dueDate = $t->due_date; // Carbon instance or null (cast: 'date')

            $dueDateFormatted = $dueDate
                ? $dueDate->format('d M Y')
                : null;

            $isOverdue = $dueDate !== null
                && $dueDate->lt($today)
                && $t->realisasi_percent < $t->target_percent;

            return [
                'id'                => $t->id,
                'title'             => $t->title,
                'description'       => $t->description,
                'target_percent'    => $t->target_percent,
                'realisasi_percent' => $t->realisasi_percent,
                'progress'          => $t->getProgressPercent(),
                'period'            => $t->period,
                'due_date'          => $dueDate ? $dueDate->toDateString() : null,
                'due_date_formatted'=> $dueDateFormatted,
                'is_overdue'        => $isOverdue,
            ];
        });

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

            // Task-based metrics
            $tasks = Task::where('assigned_to', $user->id)->get();
            $taskCount = $tasks->count();
            $taskAvgRealisasi = $taskCount > 0
                ? round($tasks->avg(fn($t) => (float)($t->realisasi_pct ?? 0)), 1)
                : 0.0;
            $taskGrade = JobGradeSetting::resolveGrade($taskAvgRealisasi);

            return [
                'user_id'            => $user->id,
                'name'               => $user->name,
                'email'              => $user->email,
                'job_count'          => $jobCount,
                'task_count'         => $taskCount,
                'task_avg_realisasi' => $taskAvgRealisasi,
                'overall'            => $taskAvgRealisasi,
                'grade'              => $taskGrade?->grade ?? '-',
                'grade_label'        => $taskGrade?->label ?? '-',
                'grade_color'        => $taskGrade?->color ?? 'gray',
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
            'due_date'       => 'nullable|date',
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
                'due_date'          => 'nullable|date',
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

        try {
            DB::beginTransaction();

            // Gunakan DELETE bukan TRUNCATE — TRUNCATE tidak bisa di-rollback
            // dan menyebabkan implicit commit di dalam transaction di MySQL
            JobGradeSetting::query()->delete();

            $now = now();
            $userId = auth()->id();

            foreach ($validated['grades'] as $g) {
                JobGradeSetting::create([
                    'grade'       => $g['grade'],
                    'min_percent' => $g['min_percent'],
                    'max_percent' => $g['max_percent'],
                    'label'       => $g['label'],
                    'color'       => $g['color'],
                    'updated_by'  => $userId,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('saveGradeSettings error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user'  => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengaturan: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan nilai berhasil disimpan']);
    }

    // ─── API: ringkasan kehadiran per user per periode ────────────────────────
    public function getAttendanceSummary(Request $request)
    {
        $userId = $request->get('user_id');
        $period = $request->get('period'); // format: "YYYY-MM" atau "YYYY-MM-DD:YYYY-MM-DD"

        // Resolve date range from period param
        [$startDate, $endDate] = $this->resolvePeriodRange($period);

        // Fallback jika tidak ada user_id, gunakan auth user
        if (!$userId) {
            $userId = auth()->id();
        }

        // Cari user
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => true,
                'data'    => ['present' => 0, 'absent' => 0, 'late' => 0],
            ]);
        }

        $summary = $this->computeAttendanceSummary($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data'    => $summary,
        ]);
    }

    /**
     * Parse period string ke pasangan [startDate, endDate].
     * Mendukung:
     *   - "YYYY-MM"              → awal–akhir bulan tersebut
     *   - "YYYY-MM-DD:YYYY-MM-DD" → rentang eksplisit
     *   - null / kosong          → bulan berjalan
     */
    private function resolvePeriodRange(?string $period): array
    {
        if (!$period) {
            $now = Carbon::now();
            return [
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
            ];
        }

        // Rentang eksplisit: "2025-07-01:2025-07-31"
        if (str_contains($period, ':')) {
            [$start, $end] = explode(':', $period, 2);
            return [
                Carbon::parse(trim($start))->toDateString(),
                Carbon::parse(trim($end))->toDateString(),
            ];
        }

        // Format bulan-tahun: "2025-07"
        if (preg_match('/^\d{4}-\d{2}$/', trim($period))) {
            $date = Carbon::createFromFormat('Y-m', trim($period));
            return [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ];
        }

        // Fallback: parse sebagai tanggal single → hari itu saja
        try {
            $date = Carbon::parse($period);
            return [$date->toDateString(), $date->toDateString()];
        } catch (\Throwable $e) {
            $now = Carbon::now();
            return [
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
            ];
        }
    }

    // ─── Export PDF per karyawan ──────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $userId = $request->get('user_id');
        $period = $request->get('period');

        // Temukan user; 404 jika tidak ditemukan
        $user = User::find($userId);
        if (!$user) {
            abort(404, 'User tidak ditemukan.');
        }

        // Resolve date range dari period param
        [$startDate, $endDate] = $this->resolvePeriodRange($period);

        // Ambil job_targets untuk user ini
        $targets = JobTarget::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        // Hitung overall progress
        $overall = $targets->count() > 0
            ? round($targets->avg(fn($t) => $t->getProgressPercent()), 1)
            : 0.0;

        // Hitung grade overall
        $grade = JobGradeSetting::resolveGrade($overall);

        // Hitung attendance summary (menggunakan private helper)
        $attendanceSummary = $this->computeAttendanceSummary($user, $startDate, $endDate);

        // Ambil company setting untuk kop surat
        $company = \App\Models\CompanySetting::where('is_active', true)->first()
            ?? new \App\Models\CompanySetting([
                'company_name'    => config('app.name', 'Perusahaan'),
                'company_address' => '',
                'company_phone'   => '',
                'company_email'   => '',
            ]);

        // Siapkan logo sebagai base64 agar bisa dirender DomPDF
        $logoBase64 = null;
        if ($company->company_logo) {
            $logoPath = storage_path('app/public/' . ltrim($company->company_logo, '/'));
            if (file_exists($logoPath) && filesize($logoPath) > 100) {
                $mime = mime_content_type($logoPath);
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
        // Fallback: cari logo di public/img
        if (!$logoBase64) {
            $candidates = [
                public_path('img/logo-20250616163221.png'),
                public_path('img/logo_2.png'),
                public_path('img/logo.png'),
            ];
            foreach ($candidates as $c) {
                if (file_exists($c) && filesize($c) > 500) {
                    $mime = mime_content_type($c);
                    $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($c));
                    break;
                }
            }
        }

        $data = compact(
            'user', 'targets', 'attendanceSummary', 'grade', 'overall',
            'period', 'startDate', 'endDate', 'company', 'logoBase64'
        );

        $pdf = Pdf::loadView('admin.inventaris.tasks.pdf.performance', $data);

        $filename = 'kinerja-' . str_replace(' ', '-', strtolower($user->name)) . '-' . ($period ?? date('Y-m')) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Helper: hitung attendance summary untuk user + date range tertentu.
     * Dapat digunakan oleh getAttendanceSummary() dan exportPdf().
     *
     * @param  User   $user
     * @param  string $startDate  Format: Y-m-d
     * @param  string $endDate    Format: Y-m-d
     * @return array{present: int, absent: int, late: int}
     */
    private function computeAttendanceSummary(User $user, string $startDate, string $endDate): array
    {
        $recruitment = Recruitment::where('email', $user->email)->first();

        if (!$recruitment) {
            return ['present' => 0, 'absent' => 0, 'late' => 0];
        }

        $baseQuery = Attendance::where('recruitment_id', $recruitment->id)
            ->whereBetween('date', [$startDate, $endDate]);

        $present = (clone $baseQuery)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $late = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('status', 'late')
                  ->orWhere('late_minutes', '>', 0);
            })
            ->count();

        $absent = (clone $baseQuery)
            ->where('status', 'absent')
            ->count();

        return compact('present', 'absent', 'late');
    }

    // ─── Kept for route compatibility (unused) ────────────────────────────────
    public function show($id) { return response()->json(['success' => false], 404); }
    public function getData_legacy(Request $request) { return $this->getData($request); }
    public function getStatistics(Request $request) { return response()->json(['success' => true, 'data' => []]); }
    public function getEmployees(Request $request) { return response()->json(['success' => true, 'data' => []]); }
}
