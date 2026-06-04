@php
    $config = [
        'isSuperAdmin' => $isSuperAdmin,
        'csrfToken'    => csrf_token(),
        'users'        => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->values()->toArray(),
        'routes'       => [
            'allUsers'          => route('sdm.kinerja.all-users'),
            'data'              => route('sdm.kinerja.data'),
            'store'             => route('sdm.kinerja.store'),
            'update'            => url('sdm/kinerja'),
            'destroy'           => url('sdm/kinerja'),
            'gradeSettings'     => route('sdm.kinerja.grade-settings.get'),
            'saveGradeSettings' => route('sdm.kinerja.grade-settings.save'),
        ],
    ];
@endphp

<x-layouts.admin title="Manajemen Kinerja">
<div x-data="kinerjaDashboard({{ json_encode($config) }})" x-init="init()" class="space-y-6">

    {{-- ── Toast ─────────────────────────────────────────────────────────────── --}}
    <div
        x-show="notification.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        :class="notification.type==='success'
            ? 'bg-emerald-50 border-emerald-400 text-emerald-800'
            : 'bg-red-50 border-red-400 text-red-800'"
        class="fixed top-4 right-4 z-[9999] flex items-center gap-3 rounded-xl border px-4 py-3 shadow-xl max-w-sm"
        style="display:none"
    >
        <i :class="notification.type==='success' ? 'bx bx-check-circle text-emerald-500' : 'bx bx-x-circle text-red-500'" class="text-xl shrink-0"></i>
        <span class="text-sm font-medium" x-text="notification.message"></span>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         SUPER ADMIN VIEW
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="isSuperAdmin">

        {{-- Header --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card mb-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Manajemen Kinerja</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Kelola job target dan pantau progress kinerja seluruh tim</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Tambah Job untuk User --}}
                    <button
                        @click="openAddJobModal(null, '')"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors"
                    >
                        <i class="bx bx-plus-circle text-base"></i>
                        Tambah Job
                    </button>
                    {{-- Grade Settings --}}
                    <button
                        @click="openGradeModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition-colors"
                    >
                        <i class="bx bx-cog text-base"></i>
                        Atur Grade
                    </button>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-card">
            <div class="flex items-center justify-center gap-2 text-slate-500">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                <span class="text-sm">Memuat data pengguna...</span>
            </div>
        </div>

        {{-- User List --}}
        <div x-show="!loading" class="space-y-3">

            {{-- Empty --}}
            <div x-show="usersData.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <i class="bx bx-group text-4xl text-slate-300 block mb-2"></i>
                <p class="text-sm text-slate-500">Tidak ada data pengguna aktif</p>
            </div>

            {{-- User Cards --}}
            <template x-for="user in usersData" :key="user.user_id">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">

                    {{-- User Summary Row --}}
                    <div class="flex items-center gap-4 px-5 py-4">
                        {{-- Avatar --}}
                        <div class="shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                            <span class="text-sm font-bold text-primary-700" x-text="user.name.charAt(0).toUpperCase()"></span>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-slate-900 text-sm" x-text="user.name"></p>
                                <span
                                    :class="gradeColorClass(user.grade_color)"
                                    class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-bold"
                                    x-text="user.grade"
                                ></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="user.email"></p>
                            {{-- Progress bar --}}
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div
                                        :class="progressBarColorClass(user.overall)"
                                        class="h-1.5 rounded-full transition-all duration-500"
                                        :style="'width:'+user.overall+'%'"
                                    ></div>
                                </div>
                                <span class="text-xs text-slate-500 w-12 text-right shrink-0" x-text="user.overall+'%'"></span>
                            </div>
                        </div>
                        {{-- Stats --}}
                        <div class="shrink-0 text-center hidden sm:block">
                            <p class="text-xl font-black text-slate-800" x-text="user.job_count"></p>
                            <p class="text-xs text-slate-400">Job</p>
                        </div>
                        {{-- Actions --}}
                        <div class="shrink-0 flex items-center gap-2">
                            <button
                                @click.stop="openAddJobModal(user.user_id, user.name)"
                                class="inline-flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2.5 py-1.5 text-xs font-medium text-primary-700 hover:bg-primary-100 transition-colors"
                                title="Tambah job untuk user ini"
                            >
                                <i class="bx bx-plus text-sm"></i>
                                <span class="hidden sm:inline">Tambah Job</span>
                            </button>
                            <button
                                @click="toggleExpandUser(user.user_id)"
                                :class="expandedUserId === user.user_id
                                    ? 'bg-slate-200 text-slate-700'
                                    : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium transition-colors"
                                title="Lihat detail job"
                            >
                                <i :class="expandedUserId === user.user_id ? 'bx bx-chevron-up' : 'bx bx-chevron-down'" class="text-sm"></i>
                                <span class="hidden sm:inline" x-text="expandedUserId === user.user_id ? 'Sembunyikan' : 'Lihat Detail'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Detail Panel (accordion) --}}
                    <div x-show="expandedUserId === user.user_id" style="display:none"
                         class="border-t border-slate-100 bg-slate-50 px-5 py-4">

                        {{-- Loading --}}
                        <div x-show="loadingExpanded[user.user_id]" class="py-4 text-center">
                            <div class="flex items-center justify-center gap-2 text-slate-400 text-sm">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-500"></div>
                                <span>Memuat job list...</span>
                            </div>
                        </div>

                        <div x-show="!loadingExpanded[user.user_id]">
                            {{-- Empty --}}
                            <div
                                x-show="!expandedJobs[user.user_id] || expandedJobs[user.user_id].items.length === 0"
                                class="py-4 text-center text-sm text-slate-400"
                            >
                                <i class="bx bx-task text-2xl block mb-1 text-slate-300"></i>
                                Belum ada job target untuk user ini.
                                <button @click.stop="openAddJobModal(user.user_id, user.name)"
                                        class="ml-1 text-primary-600 hover:underline font-medium">Tambah sekarang</button>
                            </div>

                            {{-- Job Items Grid --}}
                            <div
                                x-show="expandedJobs[user.user_id] && expandedJobs[user.user_id].items.length > 0"
                                class="grid grid-cols-1 md:grid-cols-2 gap-3"
                            >
                                <template x-for="job in (expandedJobs[user.user_id] ? expandedJobs[user.user_id].items : [])" :key="job.id">
                                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-slate-800 text-sm leading-tight" x-text="job.title"></p>
                                                <p x-show="job.description" class="text-xs text-slate-400 mt-0.5 line-clamp-2" x-text="job.description"></p>
                                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                                    <span>Target <b class="text-slate-700" x-text="job.target_percent+'%'"></b></span>
                                                    <span>Realisasi <b class="text-slate-700" x-text="job.realisasi_percent+'%'"></b></span>
                                                    <span x-show="job.period" x-text="job.period"></span>
                                                </div>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                        <div :class="progressBarColorClass(job.progress)" class="h-1.5 rounded-full transition-all duration-500"
                                                             :style="'width:'+job.progress+'%'"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold w-10 text-right shrink-0"
                                                          :class="progressTextColorClass(job.progress)"
                                                          x-text="job.progress+'%'"></span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-1 shrink-0">
                                                <button @click.stop="openEditJobModal(job, user.user_id)"
                                                        class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 hover:bg-slate-100 transition-colors">
                                                    <i class="bx bx-pencil"></i>
                                                </button>
                                                <button @click.stop="deleteJob(job.id, user.user_id, job.title)"
                                                        class="rounded-lg border border-red-100 px-2 py-1 text-xs text-red-500 hover:bg-red-50 transition-colors">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         USER BIASA VIEW
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="!isSuperAdmin">

        {{-- Header --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card mb-4">
            <h1 class="text-xl font-bold text-slate-900">Kinerja Saya</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau progress dan perbarui realisasi pekerjaan Anda</p>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-card">
            <div class="flex items-center justify-center gap-2 text-slate-500">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                <span class="text-sm">Memuat data kinerja Anda...</span>
            </div>
        </div>

        <div x-show="!loading" class="space-y-4">

            {{-- Overall Stats Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                    {{-- Grade Badge --}}
                    <div class="shrink-0 flex flex-col items-center justify-center rounded-2xl border-2 h-20 w-20"
                         :class="gradeColorClass(myOverall.grade_color ?? 'gray').replace('bg-','border-').replace(/-100/,'-300')">
                        <span class="text-3xl font-black" :class="gradeColorClass(myOverall.grade_color ?? 'gray').split(' ').find(c=>c.startsWith('text-'))"
                              x-text="myOverall.grade ?? '-'"></span>
                        <span class="text-xs mt-0.5" :class="gradeColorClass(myOverall.grade_color ?? 'gray').split(' ').find(c=>c.startsWith('text-'))"
                              x-text="myOverall.grade_label ?? '-'"></span>
                    </div>

                    {{-- Progress --}}
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-semibold text-slate-700">Overall Progress</p>
                            <p class="text-xl font-black text-slate-900" x-text="(myOverall.progress ?? 0)+'%'"></p>
                        </div>
                        <div class="bg-slate-100 rounded-full h-4 overflow-hidden">
                            <div
                                :class="progressBarColorClass(myOverall.progress ?? 0)"
                                class="h-4 rounded-full transition-all duration-700 flex items-center justify-end pr-2"
                                :style="'width:'+Math.max(myOverall.progress ?? 0, 3)+'%'"
                            ></div>
                        </div>
                        <div class="mt-2 flex gap-4 text-xs text-slate-500">
                            <span><b class="text-slate-700" x-text="myJobs.length"></b> job target</span>
                            <span><b class="text-slate-700" x-text="myJobs.filter(j=>j.progress>=100).length"></b> selesai</span>
                            <span><b class="text-slate-700" x-text="myJobs.filter(j=>j.progress>0&&j.progress<100).length"></b> dalam proses</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Empty state --}}
            <div x-show="myJobs.length === 0"
                 class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <i class="bx bx-task text-4xl text-slate-300 block mb-3"></i>
                <p class="text-slate-500 text-sm">Belum ada job target yang ditetapkan untuk Anda</p>
                <p class="text-slate-400 text-xs mt-1">Hubungi admin untuk menambahkan job target</p>
            </div>

            {{-- Job List --}}
            <div x-show="myJobs.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="job in myJobs" :key="job.id">
                    <div class="rounded-2xl border bg-white shadow-card overflow-hidden"
                         :class="job.progress >= 100
                            ? 'border-emerald-200'
                            : job.progress > 0 ? 'border-blue-100' : 'border-slate-200'">

                        {{-- Color bar top --}}
                        <div class="h-1 w-full" :class="progressBarColorClass(job.progress)"></div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-900" x-text="job.title"></p>
                                    <p x-show="job.description" class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="job.description"></p>
                                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                        <span>Target <b class="text-slate-700" x-text="job.target_percent+'%'"></b></span>
                                        <span>Realisasi <b class="text-slate-700" x-text="job.realisasi_percent+'%'"></b></span>
                                        <span x-show="job.period">
                                            <i class="bx bx-calendar text-slate-400"></i>
                                            <b x-text="job.period"></b>
                                        </span>
                                    </div>
                                </div>
                                {{-- Status badge --}}
                                <div class="shrink-0">
                                    <span x-show="job.progress >= 100"
                                          class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 text-emerald-700 px-2 py-1 text-xs font-semibold">
                                        <i class="bx bx-check-circle"></i> Selesai
                                    </span>
                                    <span x-show="job.progress > 0 && job.progress < 100"
                                          class="inline-flex items-center gap-1 rounded-lg bg-blue-50 text-blue-600 px-2 py-1 text-xs font-semibold">
                                        <i class="bx bx-loader-alt"></i> Proses
                                    </span>
                                    <span x-show="job.progress === 0"
                                          class="inline-flex items-center gap-1 rounded-lg bg-slate-100 text-slate-500 px-2 py-1 text-xs font-semibold">
                                        <i class="bx bx-time-five"></i> Belum
                                    </span>
                                </div>
                            </div>

                            {{-- Progress bar --}}
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Progress</span>
                                    <span class="font-bold" :class="progressTextColorClass(job.progress)" x-text="job.progress+'%'"></span>
                                </div>
                                <div class="bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div :class="progressBarColorClass(job.progress)"
                                         class="h-2 rounded-full transition-all duration-500"
                                         :style="'width:'+job.progress+'%'"></div>
                                </div>
                            </div>

                            {{-- Update button --}}
                            <div class="mt-3 flex justify-end">
                                <button
                                    @click="openUpdateModal(job)"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-3 py-2 text-xs font-medium text-white hover:bg-primary-700 transition-colors"
                                >
                                    <i class="bx bx-edit-alt"></i>
                                    Update Realisasi
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         MODAL: Tambah / Edit Job  (Super Admin)
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showJobModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/50" @click="showJobModal = false"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="font-semibold text-slate-900" x-text="modalJobForm.id ? 'Edit Job Target' : 'Tambah Job Target'"></h3>
                <button @click="showJobModal = false" class="text-slate-400 hover:text-slate-600"><i class="bx bx-x text-2xl"></i></button>
            </div>
            <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- User selector (only shown when no user pre-selected or in add mode) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Untuk User <span class="text-red-500">*</span>
                    </label>
                    <select x-model="modalJobForm.user_id"
                            :disabled="modalJobForm.id !== null"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:bg-slate-50 disabled:text-slate-400">
                        <option value="">— Pilih User —</option>
                        <template x-for="u in users" :key="u.id">
                            <option :value="u.id" x-text="u.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Job <span class="text-red-500">*</span></label>
                    <input type="text" x-model="modalJobForm.title" placeholder="Masukkan judul pekerjaan"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea x-model="modalJobForm.description" rows="2" placeholder="Deskripsi opsional"
                              class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Target % <span class="text-red-500">*</span></label>
                        <input type="number" x-model="modalJobForm.target_percent" min="0" max="100" placeholder="0–100"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
                        <input type="text" x-model="modalJobForm.period" placeholder="YYYY-MM"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div x-show="modalJobForm.id">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Realisasi %</label>
                    <input type="number" x-model="modalJobForm.realisasi_percent" min="0" max="100" placeholder="0–100"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="showJobModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                <button @click="saveJob()" :disabled="saving"
                        class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!saving">Simpan</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         MODAL: Update Realisasi  (User Biasa)
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showUpdateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/50" @click="showUpdateModal = false"></div>
        <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Update Realisasi</h3>
                <button @click="showUpdateModal = false" class="text-slate-400 hover:text-slate-600"><i class="bx bx-x text-2xl"></i></button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-xs text-slate-500 mb-1">Job Target</p>
                    <p class="font-semibold text-slate-900 text-sm" x-text="modalUpdateForm.title"></p>
                    <p class="text-xs text-slate-500 mt-1">Target: <b class="text-slate-700" x-text="modalUpdateForm.target_percent+'%'"></b></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Realisasi Saat Ini (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" x-model="modalUpdateForm.realisasi_percent" min="0" max="100" placeholder="0–100"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                {{-- Live preview --}}
                <div>
                    <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                        <span>Preview Progress</span>
                        <span class="font-bold" :class="progressTextColorClass(previewProgress)" x-text="previewProgress+'%'"></span>
                    </div>
                    <div class="bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div :class="progressBarColorClass(previewProgress)"
                             class="h-3 rounded-full transition-all duration-300"
                             :style="'width:'+previewProgress+'%'"></div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="showUpdateModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                <button @click="saveUpdate()" :disabled="saving"
                        class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!saving">Simpan Realisasi</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         MODAL: Grade Settings  (Super Admin)
    ════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showGradeModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/50" @click="showGradeModal = false"></div>
        <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Pengaturan Nilai Grade</h3>
                <button @click="showGradeModal = false" class="text-slate-400 hover:text-slate-600"><i class="bx bx-x text-2xl"></i></button>
            </div>
            <div class="px-6 py-4">
                <div x-show="gradeSettingsForm.length === 0" class="py-6 text-center text-sm text-slate-400">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-slate-400 mx-auto mb-2"></div>
                    Memuat pengaturan...
                </div>
                <div x-show="gradeSettingsForm.length > 0" class="space-y-2">
                    <template x-for="(grade, index) in gradeSettingsForm" :key="grade.id ?? grade.grade">
                        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                            <span :class="gradeColorClass(grade.color)"
                                  class="shrink-0 inline-flex items-center justify-center rounded-lg border w-9 h-9 text-sm font-black"
                                  x-text="grade.grade"></span>
                            <input type="text" x-model="gradeSettingsForm[index].label" placeholder="Label"
                                   class="flex-1 rounded-lg border border-slate-300 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <div class="flex items-center gap-1 shrink-0">
                                <input type="number" x-model="gradeSettingsForm[index].min_percent" min="0" max="100"
                                       class="w-16 rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <span class="text-slate-400 text-xs">–</span>
                                <input type="number" x-model="gradeSettingsForm[index].max_percent" min="0" max="100"
                                       class="w-16 rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <span class="text-slate-500 text-xs font-medium">%</span>
                            </div>
                            <span :class="{
                                      'bg-emerald-500': grade.color==='emerald',
                                      'bg-blue-500':    grade.color==='blue',
                                      'bg-amber-500':   grade.color==='amber',
                                      'bg-red-500':     grade.color==='red',
                                      'bg-slate-400':   !['emerald','blue','amber','red'].includes(grade.color),
                                  }" class="shrink-0 h-4 w-4 rounded-full" :title="grade.color"></span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="showGradeModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                <button @click="saveGradeSettings()" :disabled="saving"
                        class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!saving">Simpan</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

<script>
function kinerjaDashboard(config) {
    return {
        isSuperAdmin: config.isSuperAdmin,
        csrfToken:    config.csrfToken,
        routes:       config.routes,
        users:        config.users ?? [],   // daftar semua user aktif (untuk dropdown)

        loading:  false,
        saving:   false,
        notification: { show: false, message: '', type: 'success' },

        // Super Admin state
        usersData:       [],
        expandedUserId:  null,
        expandedJobs:    {},
        loadingExpanded: {},

        // User biasa state
        myJobs:    [],
        myOverall: { progress: 0, grade: '-', grade_label: '-', grade_color: 'gray' },

        // Modals
        showJobModal:    false,
        showUpdateModal: false,
        showGradeModal:  false,

        modalJobForm: {
            user_id: null, user_name: '',
            id: null, title: '', description: '',
            target_percent: '', realisasi_percent: '', period: '',
        },
        modalUpdateForm: { id: null, title: '', target_percent: 0, realisasi_percent: 0 },
        gradeSettingsForm: [],

        // Computed: preview progress
        get previewProgress() {
            const t = parseFloat(this.modalUpdateForm.target_percent) || 0;
            const r = parseFloat(this.modalUpdateForm.realisasi_percent) || 0;
            if (t <= 0) return 0;
            return Math.min(100, Math.round((r / t) * 1000) / 10);
        },

        // ── Lifecycle ──────────────────────────────────────────────────────────
        init() {
            if (this.isSuperAdmin) {
                this.loadAllUsersProgress();
            } else {
                this.loadMyJobs();
            }
        },

        // ── Super Admin ────────────────────────────────────────────────────────
        async loadAllUsersProgress() {
            this.loading = true;
            try {
                const res  = await fetch(this.routes.allUsers, {
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.usersData = data.data;
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.loading = false; }
        },

        toggleExpandUser(userId) {
            if (this.expandedUserId === userId) { this.expandedUserId = null; return; }
            this.expandedUserId = userId;
            if (!this.expandedJobs[userId]) this.loadJobsForUser(userId);
        },

        async loadJobsForUser(userId) {
            this.loadingExpanded = { ...this.loadingExpanded, [userId]: true };
            try {
                const res  = await fetch(this.routes.data + '?user_id=' + userId, {
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.expandedJobs = { ...this.expandedJobs, [userId]: { items: data.data, overall: data.overall } };
                } else {
                    this.handleApiError(data, res.status);
                    this.expandedUserId = null;
                }
            } catch {
                this.handleApiError(null, 0);
                this.expandedUserId = null;
            } finally {
                this.loadingExpanded = { ...this.loadingExpanded, [userId]: false };
            }
        },

        // Buka modal tambah job — userId boleh null (user dipilih via dropdown)
        openAddJobModal(userId, userName) {
            this.modalJobForm = {
                user_id:           userId ?? '',
                user_name:         userName ?? '',
                id:                null,
                title:             '',
                description:       '',
                target_percent:    '',
                realisasi_percent: '',
                period:            this.currentYearMonth(),
            };
            this.showJobModal = true;
        },

        openEditJobModal(job, userId) {
            this.modalJobForm = {
                user_id:           userId ?? job.user_id,
                user_name:         '',
                id:                job.id,
                title:             job.title,
                description:       job.description ?? '',
                target_percent:    job.target_percent,
                realisasi_percent: job.realisasi_percent,
                period:            job.period ?? '',
            };
            this.showJobModal = true;
        },

        async saveJob() {
            if (!this.modalJobForm.user_id) {
                this.showNotification('Pilih user terlebih dahulu', 'error');
                return;
            }
            this.saving = true;
            try {
                const isCreate = !this.modalJobForm.id;
                const url    = isCreate ? this.routes.store : this.routes.update + '/' + this.modalJobForm.id;
                const method = isCreate ? 'POST' : 'PUT';
                const res    = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify(this.modalJobForm),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification(data.message || 'Job berhasil disimpan', 'success');
                    this.showJobModal = false;
                    const userId = parseInt(this.modalJobForm.user_id);
                    const cache  = { ...this.expandedJobs };
                    delete cache[userId];
                    this.expandedJobs = cache;
                    if (this.expandedUserId === userId) {
                        await this.loadJobsForUser(userId);
                    }
                    await this.loadAllUsersProgress();
                } else if (res.status === 422) {
                    this.handleApiError(data, 422);
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.saving = false; }
        },

        async deleteJob(jobId, userId, jobTitle) {
            if (!confirm('Hapus job "' + (jobTitle || 'ini') + '"?')) return;
            try {
                const res  = await fetch(this.routes.destroy + '/' + jobId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                    },
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification(data.message || 'Job berhasil dihapus', 'success');
                    const cache = { ...this.expandedJobs };
                    delete cache[userId];
                    this.expandedJobs = cache;
                    await this.loadJobsForUser(userId);
                    await this.loadAllUsersProgress();
                } else if (res.status === 403) {
                    this.handleApiError(data, 403);
                } else if (res.status === 404) {
                    this.showNotification('Data tidak ditemukan', 'error');
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
        },

        // ── User Biasa ─────────────────────────────────────────────────────────
        async loadMyJobs() {
            this.loading = true;
            try {
                const res  = await fetch(this.routes.data, {
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.myJobs    = data.data;
                    this.myOverall = data.overall;
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.loading = false; }
        },

        openUpdateModal(job) {
            this.modalUpdateForm = {
                id:                job.id,
                title:             job.title,
                target_percent:    job.target_percent,
                realisasi_percent: job.realisasi_percent,
            };
            this.showUpdateModal = true;
        },

        async saveUpdate() {
            this.saving = true;
            try {
                const res  = await fetch(this.routes.update + '/' + this.modalUpdateForm.id, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ realisasi_percent: this.modalUpdateForm.realisasi_percent }),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.myJobs.findIndex(j => j.id === this.modalUpdateForm.id);
                    if (idx >= 0) {
                        this.myJobs[idx] = {
                            ...this.myJobs[idx],
                            realisasi_percent: data.data.realisasi_percent,
                            progress:          data.data.progress,
                        };
                    }
                    this.showUpdateModal = false;
                    this.showNotification(data.message || 'Realisasi berhasil diperbarui', 'success');
                    await this.loadMyJobs(); // refresh overall & grade
                } else if (res.status === 403) {
                    this.handleApiError(data, 403);
                } else if (res.status === 422) {
                    this.handleApiError(data, 422);
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.saving = false; }
        },

        // ── Grade Settings ─────────────────────────────────────────────────────
        async openGradeModal() {
            this.showGradeModal    = true;
            this.gradeSettingsForm = [];
            try {
                const res  = await fetch(this.routes.gradeSettings, {
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.gradeSettingsForm = data.data;
                } else {
                    this.handleApiError(data, res.status);
                    this.showGradeModal = false;
                }
            } catch {
                this.handleApiError(null, 0);
                this.showGradeModal = false;
            }
        },

        async saveGradeSettings() {
            this.saving = true;
            try {
                const res  = await fetch(this.routes.saveGradeSettings, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ grades: this.gradeSettingsForm }),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showNotification(data.message || 'Pengaturan grade disimpan', 'success');
                    this.showGradeModal = false;
                    this.expandedJobs  = {};
                    await this.loadAllUsersProgress();
                } else if (res.status === 422) {
                    this.handleApiError(data, 422);
                } else if (res.status === 403) {
                    this.handleApiError(data, 403);
                } else {
                    this.handleApiError(data, res.status);
                }
            } catch { this.handleApiError(null, 0); }
            finally  { this.saving = false; }
        },

        // ── Utilities ──────────────────────────────────────────────────────────
        showNotification(message, type = 'success') {
            this.notification = { show: true, message, type };
            setTimeout(() => { this.notification.show = false; }, 3000);
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

        gradeColorClass(color) {
            return {
                emerald: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                blue:    'bg-blue-100 text-blue-800 border-blue-200',
                amber:   'bg-amber-100 text-amber-800 border-amber-200',
                red:     'bg-red-100 text-red-800 border-red-200',
            }[color] ?? 'bg-slate-100 text-slate-600 border-slate-200';
        },

        progressBarColorClass(p) {
            p = parseFloat(p) || 0;
            if (p >= 90) return 'bg-emerald-500';
            if (p >= 75) return 'bg-blue-500';
            if (p >= 60) return 'bg-amber-500';
            return 'bg-red-400';
        },

        progressTextColorClass(p) {
            p = parseFloat(p) || 0;
            if (p >= 90) return 'text-emerald-600';
            if (p >= 75) return 'text-blue-600';
            if (p >= 60) return 'text-amber-600';
            return 'text-red-500';
        },

        currentYearMonth() {
            return new Date().toISOString().slice(0, 7);
        },
    };
}
</script>

</x-layouts.admin>
