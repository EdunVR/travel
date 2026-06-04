<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Dashboard SDM']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard SDM']); ?>
    <div x-data="sdmDashboard()" x-init="init()" class="space-y-6">
        
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Dashboard SDM</h1>
                    <p class="text-slate-600 text-sm">Manajemen Sumber Daya Manusia Real-time</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full lg:w-auto">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
                        <div class="relative">
                            <button @click="showOutletDropdown = !showOutletDropdown" 
                                    class="h-10 w-full rounded-xl border border-slate-200 px-3 text-left flex items-center justify-between bg-white hover:border-slate-300">
                                <span class="text-sm" x-text="getSelectedOutletText()"></span>
                                <i class='bx bx-chevron-down text-slate-400'></i>
                            </button>
                            
                            <div x-show="showOutletDropdown" 
                                 @click.away="closeOutletDropdown()"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                              
                              <div class="p-2 border-b border-slate-100">
                                <div class="flex gap-2">
                                  <button @click="selectAllOutlets()" 
                                          class="flex-1 px-3 py-1.5 text-xs bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100">
                                    Pilih Semua
                                  </button>
                                  <button @click="clearAllOutlets()" 
                                          class="flex-1 px-3 py-1.5 text-xs bg-slate-50 text-slate-700 rounded-lg hover:bg-slate-100">
                                    Hapus Semua
                                  </button>
                                </div>
                              </div>
                              
                              <div class="p-1">
                                <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                                  <input type="checkbox" 
                                         x-model="filter.selectedOutlets" 
                                         value="<?php echo e($outlet->id_outlet); ?>"
                                         @change="loadData()"
                                         class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                                  <span class="text-sm text-slate-700"><?php echo e($outlet->nama_outlet); ?></span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Dari Tanggal</label>
                        <input type="date" x-model="filter.from" @change="loadData()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Sampai Tanggal</label>
                        <input type="date" x-model="filter.to" @change="loadData()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
                    </div>
                    <div class="flex items-end">
                        <button @click="resetFilter()" :disabled="isLoading"
                                class="h-10 w-full rounded-xl border border-slate-200 px-3 hover:bg-slate-50 disabled:opacity-50">
                            <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class='bx bx-user-check text-2xl text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Karyawan Aktif</p>
                        <p class="text-2xl font-bold text-slate-900" x-text="kpi.total_employees || 0"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-briefcase text-2xl text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Departemen</p>
                        <p class="text-2xl font-bold text-slate-900" x-text="kpi.total_departments || 0"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <i class='bx bx-time text-2xl text-yellow-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Absensi Hari Ini</p>
                        <p class="text-2xl font-bold text-slate-900" x-text="kpi.today_attendance || 0"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-money text-2xl text-purple-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Total Gaji</p>
                        <p class="text-xl font-bold text-slate-900" x-text="idr(kpi.total_payroll || 0)"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class='bx bx-file text-2xl text-indigo-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Kontrak Aktif</p>
                        <p class="text-2xl font-bold text-slate-900" x-text="kpi.active_contracts || 0"></p>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-sm font-semibold">Karyawan per Outlet</div>
                        <div class="text-xs text-slate-500" x-text="periodeLabel"></div>
                    </div>
                </div>
                <div class="space-y-3">
                    <template x-if="employeeSummary.length === 0">
                        <div class="text-center py-8 text-slate-400">
                            <i class='bx bx-user text-4xl'></i>
                            <p class="text-sm mt-2">Tidak ada data karyawan</p>
                        </div>
                    </template>
                    <template x-for="outlet in employeeSummary" :key="outlet.name">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div>
                                <div class="font-medium text-slate-900" x-text="outlet.name"></div>
                                <div class="text-xs text-slate-500" x-text="`${outlet.total} karyawan`"></div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-primary-600" x-text="outlet.total"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
                <div class="text-sm font-semibold mb-4">Ringkasan Absensi</div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 p-3 text-center">
                        <div class="text-xs text-emerald-600 font-medium">Hadir</div>
                        <div class="text-2xl font-bold text-emerald-700" x-text="attendanceSummary.present || 0"></div>
                    </div>
                    <div class="rounded-xl border-2 border-amber-200 bg-amber-50 p-3 text-center">
                        <div class="text-xs text-amber-600 font-medium">Terlambat</div>
                        <div class="text-2xl font-bold text-amber-700" x-text="attendanceSummary.late || 0"></div>
                    </div>
                    <div class="rounded-xl border-2 border-rose-200 bg-rose-50 p-3 text-center">
                        <div class="text-xs text-rose-600 font-medium">Tidak Hadir</div>
                        <div class="text-2xl font-bold text-rose-700" x-text="attendanceSummary.absent || 0"></div>
                    </div>
                    <div class="rounded-xl border-2 border-blue-200 bg-blue-50 p-3 text-center">
                        <div class="text-xs text-blue-600 font-medium">Lembur (Jam)</div>
                        <div class="text-2xl font-bold text-blue-700" x-text="attendanceSummary.overtime_hours || 0"></div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="rounded-2xl border border-slate-200 bg-white p-0 shadow-card">
            <div class="p-4 flex items-center justify-between border-b border-slate-200">
                <div>
                    <div class="text-sm font-semibold">Aktivitas Terbaru</div>
                    <div class="text-xs text-slate-500">Aktivitas SDM 30 hari terakhir</div>
                </div>
            </div>

            <div class="p-4">
                <template x-if="isLoading">
                    <div class="text-center py-8">
                        <div class="flex items-center justify-center gap-2">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                            <span class="text-slate-500">Memuat data...</span>
                        </div>
                    </div>
                </template>

                <template x-if="!isLoading && recentActivities.length === 0">
                    <div class="text-center py-8 text-slate-400">
                        <i class='bx bx-history text-4xl'></i>
                        <p class="text-sm mt-2">Tidak ada aktivitas terbaru</p>
                    </div>
                </template>

                <div class="space-y-3">
                    <template x-for="activity in recentActivities" :key="activity.title">
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                 :class="`bg-${activity.color}-100`">
                                <i :class="`bx ${activity.icon} text-${activity.color}-600`"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-slate-900" x-text="activity.title"></div>
                                <div class="text-sm text-slate-600" x-text="activity.description"></div>
                                <div class="text-xs text-slate-500 mt-1" x-text="fmtd(activity.date)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        
        <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Menu Cepat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="<?php echo e(route('sdm.kepegawaian.index')); ?>" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-primary-500 hover:bg-primary-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-primary-100 flex items-center justify-center">
                        <i class='bx bx-id-card text-2xl text-primary-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Kepegawaian & Rekrutmen</span>
                </a>

                <button onclick="showDemoModal('Penggajian / Payroll')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-wallet text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Penggajian / Payroll</span>
                </button>

                <button onclick="showDemoModal('Manajemen Kinerja')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-line-chart text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Manajemen Kinerja</span>
                </button>

                <button onclick="showDemoModal('Pelatihan & Pengembangan')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-book-reader text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Pelatihan & Pengembangan</span>
                </button>

                <button onclick="showDemoModal('Manajemen Absensi & Waktu Kerja')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-time-five text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Absensi & Waktu Kerja</span>
                </button>
            </div>
        </div>

        
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class='bx bx-info-circle text-2xl text-blue-600'></i>
                <div>
                    <h3 class="font-semibold text-blue-900">Modul SDM</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Modul Kepegawaian & Rekrutmen sudah tersedia. Modul lainnya masih dalam tahap pengembangan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        function sdmDashboard(){
            return {
                isLoading: false,
                showOutletDropdown: false,
                outlets: <?php echo json_encode($outlets ?? [], 15, 512) ?>,
                kpi: {},
                employeeSummary: [],
                attendanceSummary: {},
                payrollSummary: {},
                recentActivities: [],
                filter: {
                    selectedOutlets: [],
                    from: '',
                    to: ''
                },
                periodeLabel: '',

                async init(){
                    // Set default date range (30 days)
                    const now = new Date();
                    const from = new Date(now);
                    from.setDate(now.getDate() - 30);
                    this.filter.from = from.toISOString().slice(0, 10);
                    this.filter.to = now.toISOString().slice(0, 10);
                    
                    // Initialize with first outlet selected
                    if (this.outlets.length > 0) {
                        this.filter.selectedOutlets = [this.outlets[0].id_outlet];
                    }
                    
                    await this.loadData();
                },

                async loadData(){
                    this.isLoading = true;
                    try {
                        const params = new URLSearchParams({
                            start_date: this.filter.from,
                            end_date: this.filter.to
                        });

                        // Add multiple outlet IDs
                        if (this.filter.selectedOutlets.length > 0) {
                            this.filter.selectedOutlets.forEach(outletId => {
                                params.append('outlet_ids[]', outletId);
                            });
                        }

                        const response = await fetch(`<?php echo e(route('admin.sdm.dashboard.data')); ?>?${params}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            this.kpi = result.data.kpi || {};
                            this.employeeSummary = result.data.employee_summary || [];
                            this.attendanceSummary = result.data.attendance_summary || {};
                            this.payrollSummary = result.data.payroll_summary || {};
                            this.recentActivities = result.data.recent_activities || [];
                            this.updatePeriodeLabel();
                        } else {
                            this.showNotification('error', result.message || 'Gagal memuat data');
                        }
                    } catch (error) {
                        console.error('Error loading dashboard:', error);
                        this.showNotification('error', 'Terjadi kesalahan saat memuat data');
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetFilter(){
                    this.filter.selectedOutlets = this.outlets.length > 0 ? [this.outlets[0].id_outlet] : [];
                    this.filter.from = new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().slice(0,10);
                    this.filter.to = new Date().toISOString().slice(0,10);
                    this.loadData();
                },

                updatePeriodeLabel(){
                    const from = this.fmtd(this.filter.from);
                    const to = this.fmtd(this.filter.to);
                    this.periodeLabel = `${from} — ${to}`;
                },

                getSelectedOutletText() {
                    if (this.filter.selectedOutlets.length === 0) {
                        return 'Pilih Outlet';
                    } else if (this.filter.selectedOutlets.length === 1) {
                        const outlet = this.outlets.find(o => o.id_outlet == this.filter.selectedOutlets[0]);
                        return outlet ? outlet.nama_outlet : 'Unknown';
                    } else if (this.filter.selectedOutlets.length === this.outlets.length) {
                        return 'Semua Outlet';
                    } else {
                        return `${this.filter.selectedOutlets.length} Outlet Dipilih`;
                    }
                },

                selectAllOutlets() {
                    this.filter.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
                    this.loadData();
                },

                clearAllOutlets() {
                    this.filter.selectedOutlets = [];
                    this.loadData();
                },

                idr(n){ 
                    const num = parseFloat(n) || 0;
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(num);
                },

                fmtd(s){ 
                    if (!s) return '-';
                    const d = new Date(s); 
                    return d.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                showNotification(type, message) {
                    const event = new CustomEvent('notify', {
                        detail: { type, message }
                    });
                    window.dispatchEvent(event);
                }
            }
        }

        function showDemoModal(title) {
            Alpine.store('demoModal').show(title);
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\index.blade.php ENDPATH**/ ?>