<x-layouts.admin title="Manajemen Task">
<div x-data="taskDashboard()" x-init="init()" class="space-y-6">

    {{-- ── Toast ─────────────────────────────────────────────────────────────── --}}
    <div
        x-show="notification.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        :class="notification.type === 'success'
            ? 'bg-emerald-50 border-emerald-400 text-emerald-800'
            : 'bg-red-50 border-red-400 text-red-800'"
        class="fixed top-4 right-4 z-[9999] flex items-center gap-3 rounded-xl border px-4 py-3 shadow-xl max-w-sm"
        style="display:none"
    >
        <i :class="notification.type === 'success' ? 'bx bx-check-circle text-emerald-500' : 'bx bx-x-circle text-red-500'" class="text-xl shrink-0"></i>
        <span class="text-sm font-medium" x-text="notification.message"></span>
    </div>

    {{-- ── Header ──────────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Manajemen Task</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola dan pantau semua task tim secara terpusat</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors"
                >
                    <i class="bx bx-plus-circle text-base"></i>
                    Buat Task
                </button>
            </div>
        </div>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Total --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total</p>
                <div class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center">
                    <i class="bx bx-list-ul text-slate-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900" x-text="stats.total ?? 0"></p>
        </div>
        {{-- Todo --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Todo</p>
                <div class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center">
                    <i class="bx bx-time-five text-slate-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900" x-text="stats.todo ?? 0"></p>
        </div>
        {{-- In Progress --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">In Progress</p>
                <div class="h-8 w-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="bx bx-loader-alt text-blue-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-blue-700" x-text="stats.in_progress ?? 0"></p>
        </div>
        {{-- Done --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Done</p>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i class="bx bx-check-circle text-emerald-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-emerald-700" x-text="stats.done ?? 0"></p>
        </div>
        {{-- Overdue --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Overdue</p>
                <div class="h-8 w-8 rounded-xl bg-red-50 flex items-center justify-center">
                    <i class="bx bx-error-circle text-red-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-red-600" x-text="stats.overdue ?? 0"></p>
        </div>
    </div>

    {{-- ── Filters ──────────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            {{-- Status --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                <select x-model="filters.status" @change="loadTasks()"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="todo">Todo</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="done">Done</option>
                </select>
            </div>
            {{-- Priority --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                <select x-model="filters.priority" @change="loadTasks()"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    <option value="">Semua Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            {{-- Assignee --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Assignee</label>
                <select x-model="filters.assigned_to" @change="loadTasks()"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    <option value="">Semua Assignee</option>
                    <template x-for="u in users" :key="u.id">
                        <option :value="u.id" x-text="u.name"></option>
                    </template>
                </select>
            </div>
            {{-- Category --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kategori</label>
                <input type="text" x-model="filters.category" @input.debounce.400ms="loadTasks()"
                       placeholder="Cari kategori..."
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            {{-- Start Date --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" x-model="filters.start_date" @change="loadTasks()"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            {{-- End Date --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" x-model="filters.end_date" @change="loadTasks()"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        {{-- Reset filters --}}
        <div class="mt-3 flex justify-end">
            <button @click="resetFilters()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                <i class="bx bx-reset text-sm"></i>
                Reset Filter
            </button>
        </div>
    </div>

    {{-- ── Bulk Action Panel ────────────────────────────────────────────────────── --}}
    <div x-show="selectedIds.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="rounded-2xl border border-primary-200 bg-primary-50 p-4 shadow-card"
         style="display:none">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <p class="text-sm font-semibold text-primary-800">
                <span x-text="selectedIds.length"></span> task dipilih
            </p>
            <div class="flex flex-wrap items-center gap-2 flex-1">
                {{-- Ubah Status --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-primary-700 font-medium">Ubah Status:</span>
                    <select x-model="bulkAction.status"
                            class="rounded-xl border border-primary-300 bg-white px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">— Pilih —</option>
                        <option value="todo">Todo</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                    <button @click="applyBulkAction('update_status')"
                            :disabled="!bulkAction.status"
                            class="rounded-xl bg-primary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Terapkan
                    </button>
                </div>
                {{-- Assign --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-primary-700 font-medium">Assign ke:</span>
                    <select x-model="bulkAction.assigned_to"
                            class="rounded-xl border border-primary-300 bg-white px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">— Pilih User —</option>
                        <template x-for="u in users" :key="u.id">
                            <option :value="u.id" x-text="u.name"></option>
                        </template>
                    </select>
                    <button @click="applyBulkAction('assign')"
                            :disabled="!bulkAction.assigned_to"
                            class="rounded-xl bg-slate-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Terapkan
                    </button>
                </div>
            </div>
            <button @click="selectedIds = []"
                    class="text-primary-500 hover:text-primary-700 text-sm font-medium shrink-0">
                <i class="bx bx-x-circle mr-1"></i>Batal
            </button>
        </div>
    </div>

    {{-- ── Loading ───────────────────────────────────────────────────────────────── --}}
    <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-card">
        <div class="flex items-center justify-center gap-2 text-slate-500">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
            <span class="text-sm">Memuat data task...</span>
        </div>
    </div>

    {{-- ── Task Cards Grid ──────────────────────────────────────────────────────── --}}
    <div x-show="!loading">
        {{-- Empty State --}}
        <div x-show="tasks.length === 0"
             class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <i class="bx bx-task text-5xl text-slate-300 block mb-3"></i>
            <p class="text-slate-500 text-sm font-medium">Belum ada task</p>
            <p class="text-slate-400 text-xs mt-1">Klik "Buat Task" untuk menambahkan task baru</p>
        </div>

        {{-- Cards --}}
        <div x-show="tasks.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="task in tasks" :key="task.id">
                <div class="rounded-2xl border bg-white shadow-card overflow-hidden flex flex-col transition-shadow hover:shadow-lg"
                     :class="task.is_overdue ? 'border-red-200' : 'border-slate-200'">

                    {{-- Top color bar --}}
                    <div class="h-1 w-full"
                         :class="{
                             'bg-slate-400': task.status === 'todo',
                             'bg-blue-500':  task.status === 'in_progress',
                             'bg-yellow-500':task.status === 'review',
                             'bg-emerald-500':task.status === 'done',
                         }"></div>

                    <div class="p-4 flex flex-col flex-1">
                        {{-- Top row: checkbox + title + overdue badge --}}
                        <div class="flex items-start gap-3 mb-3">
                            <input type="checkbox"
                                   :value="task.id"
                                   x-model="selectedIds"
                                   class="mt-0.5 h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 shrink-0 cursor-pointer">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-900 text-sm leading-tight flex-1" x-text="task.title"></p>
                                    <span x-show="task.is_overdue"
                                          class="inline-flex items-center gap-1 rounded-md bg-red-100 text-red-700 border border-red-200 px-1.5 py-0.5 text-xs font-semibold shrink-0">
                                        <i class="bx bx-error text-xs"></i>Overdue
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Badges: status + priority --}}
                        <div class="flex flex-wrap gap-2 mb-3">
                            {{-- Status badge --}}
                            <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold"
                                  :class="{
                                      'bg-slate-100 text-slate-600':  task.status === 'todo',
                                      'bg-blue-100 text-blue-700':    task.status === 'in_progress',
                                      'bg-yellow-100 text-yellow-700':task.status === 'review',
                                      'bg-emerald-100 text-emerald-700':task.status === 'done',
                                  }"
                                  x-text="statusLabel(task.status)"></span>
                            {{-- Priority badge --}}
                            <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold"
                                  :class="{
                                      'bg-slate-100 text-slate-600':  task.priority === 'low',
                                      'bg-blue-100 text-blue-700':    task.priority === 'medium',
                                      'bg-orange-100 text-orange-700':task.priority === 'high',
                                      'bg-red-100 text-red-700':      task.priority === 'urgent',
                                  }"
                                  x-text="priorityLabel(task.priority)"></span>
                        </div>

                        {{-- Meta info --}}
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <i class="bx bx-user text-slate-400 shrink-0"></i>
                                <span x-text="task.assignee_name || 'Unassigned'"
                                      :class="task.assignee_name ? 'text-slate-700' : 'text-slate-400 italic'"></span>
                            </div>
                            <div x-show="task.due_date" class="flex items-center gap-2 text-xs"
                                 :class="task.is_overdue ? 'text-red-600 font-semibold' : 'text-slate-500'">
                                <i class="bx bx-calendar-exclamation shrink-0"
                                   :class="task.is_overdue ? 'text-red-500' : 'text-slate-400'"></i>
                                <span x-text="task.due_date_formatted"></span>
                            </div>
                            <div x-show="task.category" class="flex items-center gap-2 text-xs text-slate-500">
                                <i class="bx bx-tag text-slate-400 shrink-0"></i>
                                <span x-text="task.category" class="truncate"></span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button @click="openEditModal(task)"
                                    class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                                <i class="bx bx-pencil text-sm"></i>
                                Edit
                            </button>
                            <button @click="deleteTask(task)"
                                    class="inline-flex items-center gap-1 rounded-xl border border-red-100 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 transition-colors">
                                <i class="bx bx-trash text-sm"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         MODAL: Buat / Edit Task
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showTaskModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/50" @click="showTaskModal = false"></div>
        <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="font-semibold text-slate-900" x-text="taskForm.id ? 'Edit Task' : 'Buat Task Baru'"></h3>
                <button @click="showTaskModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4 max-h-[72vh] overflow-y-auto">
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Judul Task <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="taskForm.title" placeholder="Masukkan judul task"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea x-model="taskForm.description" rows="3" placeholder="Deskripsi task (opsional)"
                              class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                </div>
                {{-- Due Date --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tenggat Waktu</label>
                    <input type="date" x-model="taskForm.due_date"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                {{-- Priority + Status (side by side) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Priority</label>
                        <select x-model="taskForm.priority"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select x-model="taskForm.status"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="todo">Todo</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">Review</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                </div>
                {{-- Assigned To --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Assign ke</label>
                    <select x-model="taskForm.assigned_to"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        <option value="">— Tidak di-assign —</option>
                        <template x-for="u in users" :key="u.id">
                            <option :value="u.id" x-text="u.name"></option>
                        </template>
                    </select>
                </div>
                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <input type="text" x-model="taskForm.category" placeholder="Contoh: Laporan, Marketing, IT..."
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                {{-- Attachment Notes --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan / Attachment</label>
                    <textarea x-model="taskForm.attachment_notes" rows="2"
                              placeholder="Catatan tambahan atau link attachment (opsional)"
                              class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="showTaskModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button @click="saveTask()" :disabled="saving"
                        class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!saving">Simpan</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

<script>
function taskDashboard() {
    const BASE   = '/admin/inventaris/travel/tasks';
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const HEADERS = {
        'X-CSRF-TOKEN': CSRF,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };

    return {
        // ── State ────────────────────────────────────────────────────────────
        loading: false,
        saving:  false,

        tasks: [],
        users: [],
        stats: { total: 0, todo: 0, in_progress: 0, done: 0, overdue: 0 },

        selectedIds: [],

        filters: {
            status:      '',
            priority:    '',
            assigned_to: '',
            category:    '',
            start_date:  '',
            end_date:    '',
        },

        bulkAction: {
            status:      '',
            assigned_to: '',
        },

        showTaskModal: false,
        taskForm: {
            id:               null,
            title:            '',
            description:      '',
            due_date:         '',
            priority:         'medium',
            status:           'todo',
            assigned_to:      '',
            category:         '',
            attachment_notes: '',
        },

        notification: { show: false, message: '', type: 'success' },

        // ── Lifecycle ─────────────────────────────────────────────────────────
        async init() {
            await Promise.all([this.loadUsers(), this.loadTasks()]);
        },

        // ── Data loading ──────────────────────────────────────────────────────
        async loadUsers() {
            try {
                const res  = await fetch(BASE + '/users', { headers: HEADERS });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.users = data.data;
                }
            } catch { /* fail silently, users list just won't populate */ }
        },

        async loadTasks() {
            this.loading = true;
            this.selectedIds = [];
            try {
                const params = new URLSearchParams();
                if (this.filters.status)      params.append('status',      this.filters.status);
                if (this.filters.priority)    params.append('priority',    this.filters.priority);
                if (this.filters.assigned_to) params.append('assigned_to', this.filters.assigned_to);
                if (this.filters.category)    params.append('category',    this.filters.category);
                if (this.filters.start_date)  params.append('start_date',  this.filters.start_date);
                if (this.filters.end_date)    params.append('end_date',    this.filters.end_date);

                const url  = BASE + '/data' + (params.toString() ? '?' + params.toString() : '');
                const res  = await fetch(url, { headers: HEADERS });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.tasks = data.data;
                    this.stats = data.stats;
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.loading = false; }
        },

        // ── Filters ───────────────────────────────────────────────────────────
        resetFilters() {
            this.filters = {
                status: '', priority: '', assigned_to: '',
                category: '', start_date: '', end_date: '',
            };
            this.loadTasks();
        },

        // ── CRUD ──────────────────────────────────────────────────────────────
        openCreateModal() {
            this.taskForm = {
                id: null, title: '', description: '', due_date: '',
                priority: 'medium', status: 'todo', assigned_to: '',
                category: '', attachment_notes: '',
            };
            this.showTaskModal = true;
        },

        openEditModal(task) {
            this.taskForm = {
                id:               task.id,
                title:            task.title,
                description:      task.description   ?? '',
                due_date:         task.due_date       ?? '',
                priority:         task.priority,
                status:           task.status,
                assigned_to:      task.assigned_to    ?? '',
                category:         task.category       ?? '',
                attachment_notes: task.attachment_notes ?? '',
            };
            this.showTaskModal = true;
        },

        async saveTask() {
            if (!this.taskForm.title.trim()) {
                this.showNotification('Judul task wajib diisi', 'error');
                return;
            }
            this.saving = true;
            try {
                const isCreate = !this.taskForm.id;
                const url      = isCreate ? BASE + '/store' : BASE + '/' + this.taskForm.id;
                const method   = isCreate ? 'POST' : 'PUT';

                const payload = {
                    title:            this.taskForm.title,
                    description:      this.taskForm.description      || null,
                    due_date:         this.taskForm.due_date         || null,
                    priority:         this.taskForm.priority,
                    status:           this.taskForm.status,
                    assigned_to:      this.taskForm.assigned_to      || null,
                    category:         this.taskForm.category         || null,
                    attachment_notes: this.taskForm.attachment_notes || null,
                };

                const res  = await fetch(url, {
                    method,
                    headers: HEADERS,
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification(data.message || (isCreate ? 'Task berhasil dibuat' : 'Task berhasil diperbarui'), 'success');
                    this.showTaskModal = false;
                    await this.loadTasks();
                } else if (res.status === 422) {
                    this.handleApiError(data, 422);
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.saving = false; }
        },

        async deleteTask(task) {
            if (!confirm('Hapus task "' + task.title + '"? Tindakan ini tidak dapat dibatalkan.')) return;
            try {
                const res  = await fetch(BASE + '/' + task.id, {
                    method: 'DELETE',
                    headers: HEADERS,
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification(data.message || 'Task berhasil dihapus', 'success');
                    await this.loadTasks();
                } else if (res.status === 404) {
                    this.showNotification('Task tidak ditemukan', 'error');
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
        },

        // ── Bulk Action ───────────────────────────────────────────────────────
        async applyBulkAction(action) {
            if (this.selectedIds.length === 0) {
                this.showNotification('Pilih minimal satu task', 'error');
                return;
            }

            const payload = { ids: this.selectedIds.map(Number), action };
            if (action === 'update_status') {
                if (!this.bulkAction.status) {
                    this.showNotification('Pilih status terlebih dahulu', 'error');
                    return;
                }
                payload.status = this.bulkAction.status;
            } else if (action === 'assign') {
                if (!this.bulkAction.assigned_to) {
                    this.showNotification('Pilih assignee terlebih dahulu', 'error');
                    return;
                }
                payload.assigned_to = parseInt(this.bulkAction.assigned_to);
            }

            this.saving = true;
            try {
                const res  = await fetch(BASE + '/bulk-action', {
                    method: 'POST',
                    headers: HEADERS,
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification(data.message || 'Bulk action berhasil diterapkan', 'success');
                    this.selectedIds        = [];
                    this.bulkAction.status      = '';
                    this.bulkAction.assigned_to = '';
                    await this.loadTasks();
                } else if (res.status === 422) {
                    this.handleApiError(data, 422);
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.saving = false; }
        },

        // ── Helpers ───────────────────────────────────────────────────────────
        statusLabel(status) {
            return { todo: 'Todo', in_progress: 'In Progress', review: 'Review', done: 'Done' }[status] ?? status;
        },

        priorityLabel(priority) {
            return { low: 'Low', medium: 'Medium', high: 'High', urgent: 'Urgent' }[priority] ?? priority;
        },

        showNotification(message, type = 'success') {
            this.notification = { show: true, message, type };
            setTimeout(() => { this.notification.show = false; }, 3500);
        },

        handleApiError(response, status) {
            if (status === 403) {
                this.showNotification('Anda tidak memiliki akses', 'error');
            } else if (status === 422) {
                let msg = 'Data tidak valid';
                if (response?.errors) {
                    const k = Object.keys(response.errors)[0];
                    msg = Array.isArray(response.errors[k]) ? response.errors[k][0] : response.errors[k];
                } else if (response?.message) {
                    msg = response.message;
                }
                this.showNotification(msg, 'error');
            } else if (status === 500) {
                this.showNotification(response?.message || 'Terjadi kesalahan server', 'error');
            } else if (!status || status === 0) {
                this.showNotification('Koneksi bermasalah, coba lagi', 'error');
            } else {
                this.showNotification(response?.message || 'Terjadi kesalahan', 'error');
            }
        },
    };
}
</script>

</x-layouts.admin>
