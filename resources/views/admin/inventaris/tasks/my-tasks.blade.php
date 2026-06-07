<x-layouts.admin title="My Tasks">
<div x-data="myTasksDashboard()" x-init="init()" class="space-y-6">

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
                <h1 class="text-xl font-bold text-slate-900">My Tasks</h1>
                <p class="text-sm text-slate-500 mt-0.5">Lihat dan perbarui task yang di-assign kepada Anda</p>
            </div>
            <button
                @click="loadTasks()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
            >
                <i class="bx bx-refresh text-base"></i>
                Refresh
            </button>
        </div>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
        {{-- Overdue --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Overdue</p>
                <div class="h-8 w-8 rounded-xl bg-red-50 flex items-center justify-center">
                    <i class="bx bx-error-circle text-red-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-red-600" x-text="stats.overdue ?? 0"></p>
        </div>
    </div>

    {{-- ── Loading ───────────────────────────────────────────────────────────────── --}}
    <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-card">
        <div class="flex items-center justify-center gap-2 text-slate-500">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
            <span class="text-sm">Memuat task Anda...</span>
        </div>
    </div>

    {{-- ── Task Cards ────────────────────────────────────────────────────────────── --}}
    <div x-show="!loading">

        {{-- Empty State --}}
        <div x-show="tasks.length === 0"
             class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <i class="bx bx-task text-5xl text-slate-300 block mb-3"></i>
            <p class="text-slate-500 text-sm font-medium">Belum ada task yang di-assign ke Anda</p>
            <p class="text-slate-400 text-xs mt-1">Hubungi admin jika Anda merasa seharusnya ada task untuk Anda</p>
        </div>

        {{-- Cards --}}
        <div x-show="tasks.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="task in tasks" :key="task.id">
                <div class="rounded-2xl border bg-white shadow-card overflow-hidden flex flex-col transition-shadow hover:shadow-lg"
                     :class="task.is_overdue ? 'border-red-300' : 'border-slate-200'">

                    {{-- Top colour bar --}}
                    <div class="h-1 w-full"
                         :class="{
                             'bg-slate-400':   task.status === 'todo',
                             'bg-blue-500':    task.status === 'in_progress',
                             'bg-yellow-500':  task.status === 'review',
                             'bg-emerald-500': task.status === 'done',
                         }"></div>

                    <div class="p-4 flex flex-col flex-1">

                        {{-- Title + overdue badge --}}
                        <div class="flex items-start gap-2 mb-3">
                            <p class="font-semibold text-slate-900 text-sm leading-tight flex-1" x-text="task.title"></p>
                            <span x-show="task.is_overdue"
                                  class="inline-flex items-center gap-1 rounded-md bg-red-100 text-red-700 border border-red-200 px-1.5 py-0.5 text-xs font-semibold shrink-0">
                                <i class="bx bx-error text-xs"></i>Overdue
                            </span>
                        </div>

                        {{-- Description --}}
                        <p x-show="task.description"
                           class="text-xs text-slate-500 mb-3 line-clamp-2"
                           x-text="task.description"></p>

                        {{-- Badges: status + priority --}}
                        <div class="flex flex-wrap gap-2 mb-3">
                            {{-- Status badge --}}
                            <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold"
                                  :class="{
                                      'bg-slate-100 text-slate-600':     task.status === 'todo',
                                      'bg-blue-100 text-blue-700':       task.status === 'in_progress',
                                      'bg-yellow-100 text-yellow-700':   task.status === 'review',
                                      'bg-emerald-100 text-emerald-700': task.status === 'done',
                                  }"
                                  x-text="statusLabel(task.status)"></span>
                            {{-- Priority badge --}}
                            <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold"
                                  :class="{
                                      'bg-slate-100 text-slate-600':   task.priority === 'low',
                                      'bg-blue-100 text-blue-700':     task.priority === 'medium',
                                      'bg-orange-100 text-orange-700': task.priority === 'high',
                                      'bg-red-100 text-red-700':       task.priority === 'urgent',
                                  }"
                                  x-text="priorityLabel(task.priority)"></span>
                        </div>

                        {{-- Due date --}}
                        <div x-show="task.due_date" class="flex items-center gap-2 text-xs mb-3"
                             :class="task.is_overdue ? 'text-red-600 font-semibold' : 'text-slate-500'">
                            <i class="bx bx-calendar-exclamation shrink-0"
                               :class="task.is_overdue ? 'text-red-500' : 'text-slate-400'"></i>
                            <span x-text="task.due_date_formatted"></span>
                        </div>

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- Update Status button --}}
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <button
                                x-show="task.status !== 'done'"
                                @click="openUpdateModal(task)"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-3 py-2 text-xs font-medium text-white hover:bg-primary-700 transition-colors"
                            >
                                <i class="bx bx-edit-alt text-sm"></i>
                                Update Status
                            </button>
                            <div x-show="task.status === 'done'"
                                 class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-50 text-emerald-700 px-3 py-2 text-xs font-semibold">
                                <i class="bx bx-check-circle text-sm"></i>
                                Selesai
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         MODAL: Update Status Task
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showUpdateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/50" @click="showUpdateModal = false"></div>
        <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Update Status Task</h3>
                <button @click="showUpdateModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>

            <div class="px-6 py-4 space-y-4">

                {{-- Task title preview --}}
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-xs text-slate-500 mb-1">Task</p>
                    <p class="font-semibold text-slate-900 text-sm" x-text="updateForm.title"></p>
                </div>

                {{-- Status dropdown --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Status Baru <span class="text-red-500">*</span>
                    </label>
                    <select x-model="updateForm.status"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>

                {{-- Attachment notes / catatan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Catatan <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea x-model="updateForm.attachment_notes"
                              rows="3"
                              placeholder="Tambahkan catatan perkembangan atau link attachment..."
                              class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                </div>

            </div>

            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="showUpdateModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button @click="submitStatusUpdate()" :disabled="saving"
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
function myTasksDashboard() {
    const BASE    = '/admin/inventaris/travel/tasks';
    const HEADERS = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
        'Accept':       'application/json',
    };

    return {
        // ── State ──────────────────────────────────────────────────────────────
        loading: false,
        saving:  false,

        tasks: [],
        stats: { todo: 0, in_progress: 0, overdue: 0 },

        showUpdateModal: false,
        updateForm: {
            id:               null,
            title:            '',
            status:           'in_progress',
            attachment_notes: '',
        },

        notification: { show: false, message: '', type: 'success' },

        // ── Lifecycle ───────────────────────────────────────────────────────────
        async init() {
            await this.loadTasks();
        },

        // ── Data loading ────────────────────────────────────────────────────────
        async loadTasks() {
            this.loading = true;
            try {
                const res  = await fetch(BASE + '/my-tasks/data', { headers: HEADERS });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.tasks = data.data;
                    this.stats = data.stats;
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch {
                this.handleApiError(null, 0);
            } finally {
                this.loading = false;
            }
        },

        // ── Modal ────────────────────────────────────────────────────────────────
        openUpdateModal(task) {
            this.updateForm = {
                id:               task.id,
                title:            task.title,
                status:           task.status === 'todo' ? 'in_progress' : task.status,
                attachment_notes: task.attachment_notes ?? '',
            };
            this.showUpdateModal = true;
        },

        // ── Submit status update ─────────────────────────────────────────────────
        async submitStatusUpdate() {
            this.saving = true;
            try {
                const res  = await fetch(BASE + '/' + this.updateForm.id + '/status', {
                    method:  'PUT',
                    headers: HEADERS,
                    body:    JSON.stringify({
                        status:           this.updateForm.status,
                        attachment_notes: this.updateForm.attachment_notes || null,
                    }),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification('Status task berhasil diperbarui', 'success');
                    this.showUpdateModal = false;
                    await this.loadTasks();
                } else if (res.status === 403) {
                    this.showNotification('Anda tidak memiliki akses untuk memperbarui task ini', 'error');
                    this.showUpdateModal = false;
                } else if (res.status === 422) {
                    this.handleApiError(data, 422);
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch {
                this.handleApiError(null, 0);
            } finally {
                this.saving = false;
            }
        },

        // ── Helpers ──────────────────────────────────────────────────────────────
        statusLabel(status) {
            return {
                todo:        'Todo',
                in_progress: 'In Progress',
                review:      'Review',
                done:        'Done',
            }[status] ?? status;
        },

        priorityLabel(priority) {
            return {
                low:    'Low',
                medium: 'Medium',
                high:   'High',
                urgent: 'Urgent',
            }[priority] ?? priority;
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
