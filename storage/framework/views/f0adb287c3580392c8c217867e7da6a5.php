<?php
    $config = [
        'isSuperAdmin' => $isSuperAdmin,
        'csrfToken'    => csrf_token(),
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
?>

<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Manajemen Kinerja']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Manajemen Kinerja']); ?>
    <div
        x-data="kinerjaDashboard(<?php echo e(json_encode($config)); ?>)"
        x-init="init()"
        class="space-y-6"
    >
        
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
            class="fixed top-4 right-4 z-[9999] flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg max-w-sm"
            style="display: none;"
        >
            <i :class="notification.type === 'success' ? 'bx bx-check-circle text-emerald-500' : 'bx bx-x-circle text-red-500'"
               class="text-xl flex-shrink-0"></i>
            <span class="text-sm font-medium" x-text="notification.message"></span>
        </div>

        
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Kinerja</h1>
                    <p class="text-sm text-slate-500 mt-0.5" x-text="isSuperAdmin
                        ? 'Kelola job target dan pantau progress kinerja seluruh tim'
                        : 'Pantau progress kinerja dan perbarui realisasi pekerjaan Anda'">
                    </p>
                </div>
                <button
                    x-show="isSuperAdmin"
                    @click="openGradeModal()"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors"
                >
                    <i class="bx bx-cog"></i>
                    Atur Nilai/Grade
                </button>
            </div>
        </section>

        
        <div x-show="isSuperAdmin" class="space-y-4">

            
            <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
                <div class="flex items-center justify-center gap-2 text-slate-500">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                    <span class="text-sm">Memuat data pengguna...</span>
                </div>
            </div>

            
            <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Job</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Progress</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        
                        <tr x-show="usersData.length === 0">
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">
                                <i class="bx bx-user text-3xl block mb-2"></i>
                                Tidak ada data pengguna aktif
                            </td>
                        </tr>
                        
                        <template x-for="(user, index) in usersData" :key="user.user_id">
                            <tbody>
                                
                                <tr
                                    @click="toggleExpandUser(user.user_id)"
                                    class="cursor-pointer hover:bg-slate-50 transition-colors border-t border-slate-100"
                                    :class="expandedUserId === user.user_id ? 'bg-slate-50' : ''"
                                >
                                    <td class="px-4 py-3 text-sm text-slate-500" x-text="index + 1"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i :class="expandedUserId === user.user_id ? 'bx bx-chevron-down' : 'bx bx-chevron-right'"
                                               class="text-slate-400 text-lg flex-shrink-0 transition-transform duration-200"></i>
                                            <span class="text-sm font-medium text-slate-900" x-text="user.name"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-500 hidden sm:table-cell" x-text="user.email"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                            <span x-text="user.job_count"></span>
                                            <span class="ml-1">job</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div
                                                    :class="progressBarColorClass(user.overall)"
                                                    class="h-2 rounded-full transition-all duration-500"
                                                    :style="'width: ' + user.overall + '%'"
                                                ></div>
                                            </div>
                                            <span class="text-xs text-slate-600 font-medium w-10 text-right" x-text="user.overall + '%'"></span>
                                            <span
                                                :class="gradeColorClass(user.grade_color)"
                                                class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-bold"
                                                x-text="user.grade"
                                            ></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center" @click.stop>
                                        <button
                                            @click="openAddJobModal(user.user_id, user.name)"
                                            class="inline-flex items-center gap-1 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-medium text-primary-700 hover:bg-primary-100 transition-colors"
                                        >
                                            <i class="bx bx-plus"></i>
                                            Tambah Job
                                        </button>
                                    </td>
                                </tr>
                                
                                <tr x-show="expandedUserId === user.user_id" style="display: none;">
                                    <td colspan="6" class="bg-slate-50 px-0 py-0 border-t border-slate-200">
                                        <div class="px-6 py-4">
                                            
                                            <div x-show="loadingExpanded[user.user_id]" class="py-6 text-center text-sm text-slate-400">
                                                <div class="flex items-center justify-center gap-2">
                                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600"></div>
                                                    <span>Memuat job list...</span>
                                                </div>
                                            </div>
                                            
                                            <div x-show="!loadingExpanded[user.user_id]">
                                                <p
                                                    x-show="!expandedJobs[user.user_id] || expandedJobs[user.user_id].items.length === 0"
                                                    class="py-4 text-sm text-slate-400 text-center"
                                                >Belum ada job target untuk user ini</p>
                                                <div
                                                    x-show="expandedJobs[user.user_id] && expandedJobs[user.user_id].items.length > 0"
                                                    class="space-y-3"
                                                >
                                                    <template x-for="job in (expandedJobs[user.user_id] ? expandedJobs[user.user_id].items : [])" :key="job.id">
                                                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                                                            <div class="flex items-start justify-between gap-4">
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="font-medium text-slate-900 text-sm" x-text="job.title"></p>
                                                                    <p x-show="job.description" class="text-xs text-slate-500 mt-0.5" x-text="job.description"></p>
                                                                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-slate-600">
                                                                        <span>Target: <b x-text="job.target_percent + '%'"></b></span>
                                                                        <span>Realisasi: <b x-text="job.realisasi_percent + '%'"></b></span>
                                                                        <span x-show="job.period">Periode: <b x-text="job.period"></b></span>
                                                                    </div>
                                                                    <div class="mt-2 flex items-center gap-2">
                                                                        <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                                            <div
                                                                                :class="progressBarColorClass(job.progress)"
                                                                                class="h-1.5 rounded-full transition-all duration-500"
                                                                                :style="'width: ' + job.progress + '%'"
                                                                            ></div>
                                                                        </div>
                                                                        <span class="text-xs text-slate-600 w-12 text-right" x-text="job.progress + '%'"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex gap-2 flex-shrink-0">
                                                                    <button
                                                                        @click.stop="openEditJobModal(job, user.user_id)"
                                                                        class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs text-slate-600 hover:bg-slate-100 transition-colors"
                                                                    ><i class="bx bx-pencil"></i> Edit</button>
                                                                    <button
                                                                        @click.stop="deleteJob(job.id, user.user_id, job.title)"
                                                                        class="rounded-lg border border-red-100 px-2.5 py-1.5 text-xs text-red-600 hover:bg-red-50 transition-colors"
                                                                    ><i class="bx bx-trash"></i> Hapus</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div x-show="!isSuperAdmin" class="space-y-4">

            
            <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
                <div class="flex items-center justify-center gap-2 text-slate-500">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                    <span class="text-sm">Memuat data kinerja Anda...</span>
                </div>
            </div>

            <div x-show="!loading" class="space-y-4">
                
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Progress Overall</h2>
                            <div class="mt-3 flex items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs text-slate-500">Progress</span>
                                        <span class="text-sm font-bold text-slate-900" x-text="(myOverall.progress ?? 0) + '%'"></span>
                                    </div>
                                    <div class="bg-slate-100 rounded-full h-3 overflow-hidden">
                                        <div
                                            :class="progressBarColorClass(myOverall.progress ?? 0)"
                                            class="h-3 rounded-full transition-all duration-700"
                                            :style="'width: ' + (myOverall.progress ?? 0) + '%'"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 text-center">
                            <span
                                :class="gradeColorClass(myOverall.grade_color ?? 'gray')"
                                class="inline-flex items-center justify-center rounded-xl border-2 text-3xl font-black h-16 w-16"
                                x-text="myOverall.grade ?? '-'"
                            ></span>
                            <p class="text-xs text-slate-500 mt-1" x-text="myOverall.grade_label ?? '-'"></p>
                        </div>
                    </div>
                </div>

                
                <div
                    x-show="myJobs.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"
                >
                    <i class="bx bx-task text-4xl text-slate-300 block mb-3"></i>
                    <p class="text-sm text-slate-500">Belum ada job target yang ditetapkan untuk Anda</p>
                </div>

                
                <div x-show="myJobs.length > 0" class="space-y-3">
                    <template x-for="job in myJobs" :key="job.id">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-900" x-text="job.title"></p>
                                    <p x-show="job.description" class="text-sm text-slate-500 mt-0.5" x-text="job.description"></p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-sm text-slate-600">
                                        <span>Target: <b x-text="job.target_percent + '%'"></b></span>
                                        <span>Realisasi: <b x-text="job.realisasi_percent + '%'"></b></span>
                                        <span x-show="job.period">Periode: <b x-text="job.period"></b></span>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div
                                                :class="progressBarColorClass(job.progress)"
                                                class="h-2 rounded-full transition-all duration-500"
                                                :style="'width: ' + job.progress + '%'"
                                            ></div>
                                        </div>
                                        <span class="text-xs text-slate-600 font-medium w-12 text-right" x-text="job.progress + '%'"></span>
                                    </div>
                                </div>
                                <button
                                    @click="openUpdateModal(job)"
                                    class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-3 py-2 text-xs font-medium text-white hover:bg-primary-700 transition-colors"
                                >
                                    <i class="bx bx-edit-alt"></i>
                                    Update Realisasi
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        
        <div
            x-show="showJobModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-black/50" @click="showJobModal = false"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h3 class="font-semibold text-slate-900">
                        <span x-text="modalJobForm.id ? 'Edit Job Target' : 'Tambah Job Target'"></span>
                        <span x-show="modalJobForm.user_name" class="font-normal text-slate-500">
                            — <span x-text="modalJobForm.user_name"></span>
                        </span>
                    </h3>
                    <button @click="showJobModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
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
                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button @click="saveJob()" :disabled="saving"
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

        
        <div
            x-show="showUpdateModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-black/50" @click="showUpdateModal = false"></div>
            <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h3 class="font-semibold text-slate-900">Update Realisasi</h3>
                    <button @click="showUpdateModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="rounded-lg bg-slate-50 px-4 py-3 space-y-1">
                        <p class="text-xs text-slate-500">Job Target</p>
                        <p class="font-semibold text-slate-900 text-sm" x-text="modalUpdateForm.title"></p>
                        <p class="text-xs text-slate-500">Target: <b class="text-slate-700" x-text="modalUpdateForm.target_percent + '%'"></b></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Realisasi % <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="modalUpdateForm.realisasi_percent"
                            min="0" max="100"
                            placeholder="0–100"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                        >
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                            <span>Preview Progress</span>
                            <span class="font-bold" :class="progressBarColorClass(previewProgress).replace('bg-','text-')" x-text="previewProgress + '%'"></span>
                        </div>
                        <div class="bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div
                                :class="progressBarColorClass(previewProgress)"
                                class="h-3 rounded-full transition-all duration-300"
                                :style="'width: ' + previewProgress + '%'"
                            ></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                    <button @click="showUpdateModal = false"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button @click="saveUpdate()" :disabled="saving"
                            class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span x-show="!saving">Update</span>
                        <span x-show="saving" class="flex items-center gap-2">
                            <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        
        <div
            x-show="showGradeModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-black/50" @click="showGradeModal = false"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h3 class="font-semibold text-slate-900">Pengaturan Nilai Grade</h3>
                    <button @click="showGradeModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                <div class="px-6 py-4">
                    <p class="text-xs text-slate-500 mb-3">Edit label dan rentang persentase untuk setiap grade. Perubahan akan berlaku setelah disimpan.</p>
                    <div x-show="gradeSettingsForm.length === 0" class="py-6 text-center text-sm text-slate-400">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-slate-400 mx-auto mb-2"></div>
                        Memuat pengaturan...
                    </div>
                    <table x-show="gradeSettingsForm.length > 0" class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-slate-500 uppercase">
                                <th class="pb-2 text-left w-16">Grade</th>
                                <th class="pb-2 text-left">Label</th>
                                <th class="pb-2 text-center w-20">Min %</th>
                                <th class="pb-2 text-center w-20">Max %</th>
                                <th class="pb-2 text-center w-12">Warna</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(grade, index) in gradeSettingsForm" :key="grade.id ?? grade.grade">
                                <tr>
                                    <td class="py-2 pr-3">
                                        <span
                                            :class="gradeColorClass(grade.color)"
                                            class="inline-flex items-center justify-center rounded-md border px-2.5 py-0.5 text-sm font-bold"
                                            x-text="grade.grade"
                                        ></span>
                                    </td>
                                    <td class="py-2 pr-3">
                                        <input type="text" x-model="gradeSettingsForm[index].label"
                                               class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input type="number" x-model="gradeSettingsForm[index].min_percent" min="0" max="100"
                                               class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input type="number" x-model="gradeSettingsForm[index].max_percent" min="0" max="100"
                                               class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    </td>
                                    <td class="py-2 text-center">
                                        <span
                                            :class="{
                                                'bg-emerald-500': grade.color === 'emerald',
                                                'bg-blue-500':    grade.color === 'blue',
                                                'bg-amber-500':   grade.color === 'amber',
                                                'bg-red-500':     grade.color === 'red',
                                                'bg-slate-400':   !['emerald','blue','amber','red'].includes(grade.color),
                                            }"
                                            class="inline-block h-4 w-4 rounded-full"
                                            :title="grade.color"
                                        ></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                    <button @click="showGradeModal = false"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button @click="saveGradeSettings()" :disabled="saving"
                            class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span x-show="!saving">Simpan Pengaturan</span>
                        <span x-show="saving" class="flex items-center gap-2">
                            <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></div>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    
    <script>
    function kinerjaDashboard(config) {
        return {
            // ── Config ──────────────────────────────────────────────────────────
            isSuperAdmin: config.isSuperAdmin,
            csrfToken:    config.csrfToken,
            routes:       config.routes,

            // ── State ────────────────────────────────────────────────────────────
            loading:  false,
            saving:   false,
            notification: { show: false, message: '', type: 'success' },

            // Super Admin
            usersData:       [],
            expandedUserId:  null,
            expandedJobs:    {},
            loadingExpanded: {},

            // User Biasa
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
            modalUpdateForm: {
                id: null, title: '',
                target_percent: 0, realisasi_percent: 0,
            },
            gradeSettingsForm: [],

            // ── Computed ─────────────────────────────────────────────────────────
            get previewProgress() {
                const target    = parseFloat(this.modalUpdateForm.target_percent) || 0;
                const realisasi = parseFloat(this.modalUpdateForm.realisasi_percent) || 0;
                if (target <= 0) return 0;
                return Math.min(100, Math.round((realisasi / target) * 100 * 10) / 10);
            },

            // ── Lifecycle ────────────────────────────────────────────────────────
            init() {
                if (this.isSuperAdmin) {
                    this.loadAllUsersProgress();
                } else {
                    this.loadMyJobs();
                }
            },

            // ── Super Admin: load all users ──────────────────────────────────────
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
                } catch (e) {
                    this.handleApiError(null, 0);
                } finally {
                    this.loading = false;
                }
            },

            // ── Super Admin: accordion ───────────────────────────────────────────
            toggleExpandUser(userId) {
                if (this.expandedUserId === userId) {
                    this.expandedUserId = null;
                    return;
                }
                this.expandedUserId = userId;
                if (!this.expandedJobs[userId]) {
                    this.loadJobsForUser(userId);
                }
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
                } catch (e) {
                    this.handleApiError(null, 0);
                    this.expandedUserId = null;
                } finally {
                    this.loadingExpanded = { ...this.loadingExpanded, [userId]: false };
                }
            },

            // ── Super Admin: add/edit job modal ──────────────────────────────────
            openAddJobModal(userId, userName) {
                this.modalJobForm = {
                    user_id: userId, user_name: userName,
                    id: null, title: '', description: '',
                    target_percent: '', realisasi_percent: '',
                    period: this.currentYearMonth(),
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
                this.saving = true;
                try {
                    const isCreate = !this.modalJobForm.id;
                    const url    = isCreate ? this.routes.store : this.routes.update + '/' + this.modalJobForm.id;
                    const method = isCreate ? 'POST' : 'PUT';

                    const res  = await fetch(url, {
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
                        const userId = this.modalJobForm.user_id;
                        const cache  = { ...this.expandedJobs };
                        delete cache[userId];
                        this.expandedJobs = cache;
                        await this.loadJobsForUser(userId);
                        await this.loadAllUsersProgress();
                    } else if (res.status === 422) {
                        this.handleApiError(data, 422);
                    } else {
                        this.handleApiError(data, res.status);
                    }
                } catch (e) {
                    this.handleApiError(null, 0);
                } finally {
                    this.saving = false;
                }
            },

            // ── Super Admin: delete job ──────────────────────────────────────────
            async deleteJob(jobId, userId, jobTitle) {
                if (!confirm('Hapus job "' + (jobTitle || 'ini') + '"?\nTindakan ini tidak dapat dibatalkan.')) return;
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
                } catch (e) {
                    this.handleApiError(null, 0);
                }
            },

            // ── User Biasa: my jobs ──────────────────────────────────────────────
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
                } catch (e) {
                    this.handleApiError(null, 0);
                } finally {
                    this.loading = false;
                }
            },

            // ── User Biasa: update realisasi modal ───────────────────────────────
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
                        // Reactive patch
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
                        // Refresh untuk update overall & grade
                        await this.loadMyJobs();
                    } else if (res.status === 403) {
                        this.handleApiError(data, 403);
                    } else if (res.status === 422) {
                        this.handleApiError(data, 422);
                    } else {
                        this.handleApiError(data, res.status);
                    }
                } catch (e) {
                    this.handleApiError(null, 0);
                } finally {
                    this.saving = false;
                }
            },

            // ── Grade Settings ───────────────────────────────────────────────────
            async openGradeModal() {
                this.showGradeModal  = true;
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
                } catch (e) {
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
                        this.expandedJobs = {};
                        await this.loadAllUsersProgress();
                    } else if (res.status === 422) {
                        this.handleApiError(data, 422);
                    } else if (res.status === 403) {
                        this.handleApiError(data, 403);
                    } else {
                        this.handleApiError(data, res.status);
                    }
                } catch (e) {
                    this.handleApiError(null, 0);
                } finally {
                    this.saving = false;
                }
            },

            // ── Utilities ────────────────────────────────────────────────────────
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
                        const key = Object.keys(response.errors)[0];
                        if (key) msg = Array.isArray(response.errors[key]) ? response.errors[key][0] : response.errors[key];
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
                const map = {
                    emerald: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    blue:    'bg-blue-100 text-blue-800 border-blue-200',
                    amber:   'bg-amber-100 text-amber-800 border-amber-200',
                    red:     'bg-red-100 text-red-800 border-red-200',
                    gray:    'bg-slate-100 text-slate-600 border-slate-200',
                };
                return map[color] ?? 'bg-slate-100 text-slate-600 border-slate-200';
            },

            progressBarColorClass(progress) {
                const p = parseFloat(progress) || 0;
                if (p >= 90) return 'bg-emerald-500';
                if (p >= 75) return 'bg-blue-500';
                if (p >= 60) return 'bg-amber-500';
                return 'bg-red-500';
            },

            currentYearMonth() {
                return new Date().toISOString().slice(0, 7);
            },
        };
    }
    </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kinerja\index.blade.php ENDPATH**/ ?>