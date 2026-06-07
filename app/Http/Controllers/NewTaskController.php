<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewTaskController extends Controller
{
    // ─── Halaman utama (superadmin) ───────────────────────────────────────────
    public function index()
    {
        return view('admin.inventaris.tasks.index');
    }

    // ─── API: daftar user aktif (untuk dropdown assignee) ────────────────────
    public function users()
    {
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }

    // ─── Halaman My Tasks (karyawan) ──────────────────────────────────────────
    public function myTasks()
    {
        return view('admin.inventaris.tasks.my-tasks');
    }

    // ─── API: JSON list task dengan filter ───────────────────────────────────
    public function getData(Request $request)
    {
        $query = Task::with('assignedUser');

        // ── Filter: status ────────────────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // ── Filter: priority ──────────────────────────────────────────────────
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // ── Filter: assigned_to ───────────────────────────────────────────────
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        // ── Filter: category ──────────────────────────────────────────────────
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // ── Filter: start_date (due_date >=) ──────────────────────────────────
        if ($request->filled('start_date')) {
            $query->where('due_date', '>=', $request->input('start_date'));
        }

        // ── Filter: end_date (due_date <=) ────────────────────────────────────
        if ($request->filled('end_date')) {
            $query->where('due_date', '<=', $request->input('end_date'));
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        $today = today();

        $data = $tasks->map(function (Task $task) use ($today) {
            // Format due_date: "31 Jul 2025" (or null if no due_date)
            $dueDateFormatted = $task->due_date
                ? $task->due_date->format('d M Y')
                : null;

            // is_overdue: due_date not null AND due_date < today AND status != 'done'
            $isOverdue = $task->due_date !== null
                && $task->due_date->lt($today)
                && $task->status !== 'done';

            return [
                'id'                 => $task->id,
                'title'              => $task->title,
                'description'        => $task->description,
                'due_date'           => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'due_date_formatted' => $dueDateFormatted,
                'priority'           => $task->priority,
                'status'             => $task->status,
                'assigned_to'        => $task->assigned_to,
                'assignee_name'      => $task->assignedUser ? $task->assignedUser->name : null,
                'category'           => $task->category,
                'attachment_notes'   => $task->attachment_notes,
                'realisasi_pct'      => (float) ($task->realisasi_pct ?? 0),
                'is_overdue'         => $isOverdue,
                'created_at'         => $task->created_at,
                'updated_at'         => $task->updated_at,
            ];
        });

        // ── Stats (derived from the already-fetched filtered collection) ─────────
        $stats = [
            'total'       => $tasks->count(),
            'todo'        => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'done'        => $tasks->where('status', 'done')->count(),
            'overdue'     => $tasks->filter(function ($t) use ($today) {
                return $t->due_date !== null
                    && $t->due_date->lt($today)
                    && $t->status !== 'done';
            })->count(),
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
            'stats'   => $stats,
        ]);
    }

    // ─── CRUD: buat task baru (implementasi task 3.1) ────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'due_date'         => ['nullable', 'date'],
            'priority'         => ['nullable', 'in:low,medium,high,urgent'],
            'status'           => ['nullable', 'in:todo,in_progress,review,done'],
            'assigned_to'      => ['nullable', 'exists:users,id'],
            'category'         => ['nullable', 'string', 'max:100'],
            'attachment_notes' => ['nullable', 'string'],
        ]);

        // Reject titles that are whitespace-only
        if (trim($validated['title']) === '') {
            return response()->json([
                'success' => false,
                'errors'  => ['title' => ['The title field must not be empty or whitespace only.']],
            ], 422);
        }

        $validated['title']      = trim($validated['title']);
        $validated['created_by'] = auth()->id();

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $task,
        ], 201);
    }

    // ─── CRUD: update task (implementasi task 3.2) ───────────────────────────
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'due_date'         => ['nullable', 'date'],
            'priority'         => ['nullable', 'in:low,medium,high,urgent'],
            'status'           => ['nullable', 'in:todo,in_progress,review,done'],
            'assigned_to'      => ['nullable', 'exists:users,id'],
            'category'         => ['nullable', 'string', 'max:100'],
            'attachment_notes' => ['nullable', 'string'],
        ]);

        // Reject titles that are whitespace-only
        if (trim($validated['title']) === '') {
            return response()->json([
                'success' => false,
                'errors'  => ['title' => ['The title field must not be empty or whitespace only.']],
            ], 422);
        }

        $validated['title'] = trim($validated['title']);

        $task = Task::findOrFail($id);
        $task->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $task->fresh(),
        ]);
    }

    // ─── CRUD: hapus task (implementasi task 3.3) ────────────────────────────
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // ─── Bulk action (implementasi task 5.1) ─────────────────────────────────
    public function bulkAction(Request $request)
    {
        // ── Step 1: Validate top-level fields ─────────────────────────────────
        $request->validate([
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer'],
            'action' => ['required', 'in:update_status,assign'],
        ]);

        $ids    = $request->input('ids');
        $action = $request->input('action');

        // ── Step 2: Guard against empty ids array ─────────────────────────────
        if (count($ids) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal satu task',
            ], 422);
        }

        // ── Step 3: Validate action-specific target value ─────────────────────
        if ($action === 'update_status') {
            $request->validate([
                'status' => ['required', 'in:todo,in_progress,review,done'],
            ]);

            $updated = Task::whereIn('id', $ids)
                ->update(['status' => $request->input('status')]);

        } else {
            // action === 'assign'
            $request->validate([
                'assigned_to' => ['nullable', 'exists:users,id'],
            ]);

            $updated = Task::whereIn('id', $ids)
                ->update(['assigned_to' => $request->input('assigned_to')]);
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }

    // ─── API: JSON task milik auth user (task 5.3) ───────────────────────────
    public function getMyTasksData(Request $request)
    {
        $userId = auth()->id();

        // Build base query: only tasks assigned to the authenticated user
        $query = Task::where('assigned_to', $userId)
            ->with('assignedUser:id,name');

        // ── Stats (computed on the full "my tasks" set, before any ad-hoc filter) ──
        $baseQuery = Task::where('assigned_to', $userId);

        $stats = [
            'todo'       => (clone $baseQuery)->where('status', 'todo')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'overdue'    => (clone $baseQuery)->overdue()->count(),
        ];

        // ── Fetch task data ───────────────────────────────────────────────────
        $tasks = $query->orderBy('due_date')->orderBy('id', 'desc')->get();

        $today = today();

        $data = $tasks->map(function (Task $task) use ($today) {
            $isOverdue = $task->due_date !== null
                && $task->due_date->lt($today)
                && $task->status !== 'done';

            return [
                'id'                => $task->id,
                'title'             => $task->title,
                'description'       => $task->description,
                'due_date'          => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'due_date_formatted' => $task->due_date ? $task->due_date->translatedFormat('d M Y') : null,
                'priority'          => $task->priority,
                'status'            => $task->status,
                'assigned_to'       => $task->assigned_to,
                'assignee_name'     => $task->assignedUser ? $task->assignedUser->name : null,
                'category'          => $task->category,
                'attachment_notes'  => $task->attachment_notes,
                'created_by'        => $task->created_by,
                'is_overdue'        => $isOverdue,
                'realisasi_pct'     => (float) ($task->realisasi_pct ?? 0),
                'created_at'        => $task->created_at,
                'updated_at'        => $task->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'stats'   => $stats,
        ]);
    }

    // ─── Update status task oleh karyawan (implementasi task 5.5) ───────────
    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // 9.4 / 9.5: karyawan hanya boleh update task miliknya sendiri
        if ((int) $task->assigned_to !== (int) auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: you are not the assignee of this task.',
            ], 403);
        }

        // 9.1 / 9.2 / 9.3: validasi status (restricted enum) dan attachment_notes
        $validated = $request->validate([
            'status'           => ['required', 'in:in_progress,review,done'],
            'attachment_notes' => ['nullable', 'string'],
        ]);

        $task->status           = $validated['status'];
        $task->attachment_notes = $validated['attachment_notes'] ?? $task->attachment_notes;
        $task->save();

        return response()->json([
            'success' => true,
            'data'    => $task->fresh(),
        ]);
    }

    // ─── API: ringkasan task progress untuk satu user (dipakai kinerja) ─────────
    public function getTasksSummaryForUser(Request $request)
    {
        $userId = $request->get('user_id', auth()->id());

        $tasks = Task::where('assigned_to', $userId)->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total'           => 0,
                    'done'            => 0,
                    'in_progress'     => 0,
                    'todo'            => 0,
                    'overdue'         => 0,
                    'avg_realisasi'   => 0,
                    'tasks'           => [],
                ],
            ]);
        }

        $today = today();

        $taskData = $tasks->map(function (Task $t) use ($today) {
            $isOverdue = $t->due_date && $t->due_date->lt($today) && $t->status !== 'done';
            return [
                'id'            => $t->id,
                'title'         => $t->title,
                'status'        => $t->status,
                'realisasi_pct' => (float) ($t->realisasi_pct ?? 0),
                'due_date'      => $t->due_date ? $t->due_date->format('Y-m-d') : null,
                'due_date_formatted' => $t->due_date ? $t->due_date->format('d M Y') : null,
                'is_overdue'    => $isOverdue,
                'priority'      => $t->priority,
                'category'      => $t->category,
            ];
        });

        $avgRealisasi = round($tasks->avg(fn($t) => (float) ($t->realisasi_pct ?? 0)), 1);

        return response()->json([
            'success' => true,
            'data' => [
                'total'         => $tasks->count(),
                'done'          => $tasks->where('status', 'done')->count(),
                'in_progress'   => $tasks->where('status', 'in_progress')->count(),
                'todo'          => $tasks->where('status', 'todo')->count(),
                'overdue'       => $tasks->filter(fn($t) =>
                    $t->due_date && $t->due_date->lt($today) && $t->status !== 'done'
                )->count(),
                'avg_realisasi' => $avgRealisasi,
                'tasks'         => $taskData,
            ],
        ]);
    }

    // ─── Update realisasi % task oleh karyawan ────────────────────────────────
    public function updateRealisasi(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Hanya assignee yang boleh update realisasi
        if ((int) $task->assigned_to !== (int) auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: you are not the assignee of this task.',
            ], 403);
        }

        $validated = $request->validate([
            'realisasi_pct' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $task->realisasi_pct = $validated['realisasi_pct'];

        // Auto-update status berdasarkan realisasi
        if ((float) $validated['realisasi_pct'] >= 100 && $task->status !== 'done') {
            $task->status = 'done';
        } elseif ((float) $validated['realisasi_pct'] > 0 && $task->status === 'todo') {
            $task->status = 'in_progress';
        }

        $task->save();

        return response()->json([
            'success' => true,
            'data'    => $task->fresh(),
        ]);
    }
}
