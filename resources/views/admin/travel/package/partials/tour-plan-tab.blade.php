<div x-data="tourPlanTab()" x-init="init()" class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold">Rencana Perjalanan</h3>
            <p class="text-sm text-gray-600">Atur jadwal kegiatan setiap hari perjalanan</p>
        </div>
        <div class="flex gap-2">
            <button @click="loadStandardTourPlan()" type="button" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="bx bx-list-plus"></i> + Tour Plan Standar
            </button>
            <button @click="addDay()" type="button" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="bx bx-plus"></i> Tambah Hari
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <i class="bx bx-loader-alt bx-spin text-4xl text-gray-400"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && tourPlans.length === 0" class="text-center py-12 bg-gray-50 rounded-xl">
        <i class="bx bx-calendar text-6xl text-gray-300"></i>
        <p class="mt-4 text-gray-500">Belum ada rencana perjalanan</p>
        <button @click="addDay()" type="button" 
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="bx bx-plus"></i> Tambah Hari Pertama
        </button>
    </div>

    <!-- Tour Plans List -->
    <div x-show="!loading && tourPlans.length > 0" class="space-y-4">
        <template x-for="(day, dayIndex) in tourPlans" :key="dayIndex">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <!-- Day Header -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold"
                                      x-text="day.day_number"></span>
                                <input type="text" 
                                       x-model="day.day_title"
                                       placeholder="Judul Hari (contoh: Tiba di Jeddah)"
                                       class="flex-1 px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2">
                                <div>
                                    <label class="block text-xs font-medium text-blue-700 mb-1">Tanggal</label>
                                    <input type="date" 
                                           x-model="day.day_date"
                                           class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-blue-700 mb-1">Deskripsi Hari (opsional)</label>
                                    <input type="text" 
                                           x-model="day.description"
                                           placeholder="Deskripsi singkat"
                                           class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </div>
                        <button @click="removeDay(dayIndex)" type="button"
                                class="text-red-600 hover:text-red-700 p-2">
                            <i class="bx bx-trash text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Activities -->
                <div class="p-6 space-y-3">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-700">Kegiatan</h4>
                        <button @click="addActivity(dayIndex)" type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="bx bx-plus"></i> Tambah Kegiatan
                        </button>
                    </div>

                    <!-- Empty Activities -->
                    <div x-show="!day.activities || day.activities.length === 0" 
                         class="text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <i class="bx bx-time text-3xl text-gray-400"></i>
                        <p class="mt-2 text-sm text-gray-500">Belum ada kegiatan</p>
                    </div>

                    <!-- Activities List -->
                    <template x-for="(activity, actIndex) in day.activities" :key="actIndex">
                        <div class="flex gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="bx bx-time text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jam</label>
                                        <input type="time" 
                                               x-model="activity.activity_time"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Judul Kegiatan</label>
                                        <input type="text" 
                                               x-model="activity.activity_title"
                                               placeholder="Judul Kegiatan"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    </div>
                                    <div class="md:col-span-5">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi (opsional)</label>
                                        <input type="text" 
                                               x-model="activity.activity_description"
                                               placeholder="Deskripsi kegiatan"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    </div>
                                    <div class="md:col-span-1 flex items-end justify-center">
                                        <button @click="removeActivity(dayIndex, actIndex)" type="button"
                                                class="text-red-600 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Transport Info Checkbox -->
                                <div class="mt-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" x-model="activity.is_transport_info"
                                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-xs font-medium text-indigo-700"><i class="bx bx-bus"></i> Keperluan Transport Info Paket</span>
                                    </label>
                                </div>
                                <!-- Transport Fields (shown when checked) -->
                                <div x-show="activity.is_transport_info" x-transition class="mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-indigo-700 mb-1">FROM</label>
                                            <input type="text" x-model="activity.transport_from"
                                                   placeholder="Contoh: JEDDAH AIRPORT"
                                                   class="w-full px-3 py-2 border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-indigo-700 mb-1">TO</label>
                                            <input type="text" x-model="activity.transport_to"
                                                   placeholder="Contoh: MADINAH HOTEL"
                                                   class="w-full px-3 py-2 border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-indigo-700 mb-1">REMARK</label>
                                            <input type="text" x-model="activity.transport_remark"
                                                   placeholder="Contoh: Landing 17.30 BAWA KOPER"
                                                   class="w-full px-3 py-2 border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Save Button -->
    <div x-show="!loading && tourPlans.length > 0" class="flex justify-end gap-3 pt-4 border-t">
        <button @click="saveTourPlans()" type="button"
                :disabled="saving"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="bx" :class="saving ? 'bx-loader-alt bx-spin' : 'bx-save'"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Rencana Perjalanan'"></span>
        </button>
    </div>
</div>

<script>
function tourPlanTab() {
    return {
        packageId: {{ $package->id }},
        tourPlans: [],
        loading: false,
        saving: false,

        async init() {
            await this.loadTourPlans();
        },

        async loadTourPlans() {
            this.loading = true;
            try {
                const response = await fetch(`{{ url('admin/inventaris/travel/package') }}/${this.packageId}/tour-plans`);
                if (response.ok) {
                    const data = await response.json();
                    console.log('Loaded tour plans:', data); // Debug log
                    this.tourPlans = data.map(plan => {
                        // Ensure day_date is in YYYY-MM-DD format for input[type="date"]
                        let dayDate = plan.day_date || '';
                        if (dayDate && dayDate.includes(' ')) {
                            // If date has time component, extract just the date part
                            dayDate = dayDate.split(' ')[0];
                        }
                        
                        return {
                            ...plan,
                            day_date: dayDate,
                            activities: (plan.activities || []).map(act => ({
                                ...act,
                                // Ensure activity_time is in HH:MM format
                                activity_time: act.activity_time ? act.activity_time.substring(0, 5) : '09:00'
                            }))
                        };
                    });
                    console.log('Mapped tour plans:', this.tourPlans); // Debug log
                }
            } catch (error) {
                console.error('Error loading tour plans:', error);
            } finally {
                this.loading = false;
            }
        },

        addDay() {
            const nextDayNumber = this.tourPlans.length + 1;
            const departureDate = new Date('{{ $package->departure_date }}');
            const newDate = new Date(departureDate);
            newDate.setDate(newDate.getDate() + (nextDayNumber - 1));
            
            this.tourPlans.push({
                day_number: nextDayNumber,
                day_title: `Hari ${nextDayNumber}`,
                day_date: newDate.toISOString().split('T')[0],
                description: '',
                order: nextDayNumber,
                activities: []
            });
        },

        removeDay(dayIndex) {
            if (confirm('Hapus hari ini dan semua kegiatannya?')) {
                this.tourPlans.splice(dayIndex, 1);
                // Reorder day numbers
                this.tourPlans.forEach((day, index) => {
                    day.day_number = index + 1;
                    day.order = index + 1;
                });
            }
        },

        addActivity(dayIndex) {
            const day = this.tourPlans[dayIndex];
            
            if (!day.activities) {
                day.activities = [];
            }
            
            day.activities.push({
                activity_time: '09:00',
                activity_title: '',
                activity_description: '',
                order: day.activities.length + 1,
                is_transport_info: false,
                transport_from: '',
                transport_to: '',
                transport_remark: ''
            });
        },

        removeActivity(dayIndex, actIndex) {
            if (confirm('Hapus kegiatan ini?')) {
                this.tourPlans[dayIndex].activities.splice(actIndex, 1);
                // Reorder activities
                this.tourPlans[dayIndex].activities.forEach((activity, index) => {
                    activity.order = index + 1;
                });
            }
        },

        async saveTourPlans() {
            // Validate
            for (let day of this.tourPlans) {
                if (!day.day_title.trim()) {
                    alert('Judul hari tidak boleh kosong');
                    return;
                }
                if (!day.day_date) {
                    alert('Tanggal hari tidak boleh kosong');
                    return;
                }
                for (let activity of day.activities || []) {
                    if (!activity.activity_time) {
                        alert('Jam kegiatan tidak boleh kosong');
                        return;
                    }
                    if (!activity.activity_title.trim()) {
                        alert('Judul kegiatan tidak boleh kosong');
                        return;
                    }
                }
            }

            this.saving = true;
            try {
                const response = await fetch(`{{ url('admin/inventaris/travel/package') }}/${this.packageId}/tour-plans`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tour_plans: this.tourPlans
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil!', data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                    await this.loadTourPlans();
                } else {
                    throw new Error(data.message || 'Gagal menyimpan tour plan');
                }
            } catch (error) {
                console.error('Error saving tour plans:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', error.message, 'error');
                } else {
                    alert('Error: ' + error.message);
                }
            } finally {
                this.saving = false;
            }
        },

        loadStandardTourPlan() {
            if (this.tourPlans.length > 0) {
                if (!confirm('Ini akan menghapus tour plan yang ada dan menggantinya dengan template standar. Lanjutkan?')) {
                    return;
                }
            }

            const departureDate = new Date('{{ $package->departure_date }}');
            const duration = {{ $package->duration_days ?? 9 }};
            
            // Template Tour Plan Standar Umroh 9 Hari
            const standardTemplate = [
                {
                    day_number: 1,
                    day_title: 'Keberangkatan dari Indonesia',
                    description: 'Perjalanan menuju Tanah Suci',
                    activities: [
                        { activity_time: '07:00', activity_title: 'Berkumpul di Meeting Point', activity_description: 'Jamaah berkumpul di tempat yang telah ditentukan' },
                        { activity_time: '09:00', activity_title: 'Berangkat ke Bandara', activity_description: 'Perjalanan menuju bandara keberangkatan' },
                        { activity_time: '12:00', activity_title: 'Check-in dan Boarding', activity_description: 'Proses check-in dan boarding pesawat' },
                        { activity_time: '15:00', activity_title: 'Penerbangan ke Jeddah', activity_description: 'Penerbangan menuju Jeddah, Arab Saudi' }
                    ]
                },
                {
                    day_number: 2,
                    day_title: 'Tiba di Jeddah - Menuju Madinah',
                    description: 'Kedatangan dan perjalanan ke Madinah',
                    activities: [
                        { activity_time: '06:00', activity_title: 'Tiba di Bandara Jeddah', activity_description: 'Proses imigrasi dan pengambilan bagasi' },
                        { activity_time: '08:00', activity_title: 'Perjalanan ke Madinah', activity_description: 'Perjalanan darat menuju Madinah (±4-5 jam)' },
                        { activity_time: '13:00', activity_title: 'Check-in Hotel Madinah', activity_description: 'Check-in hotel dan istirahat' },
                        { activity_time: '16:00', activity_title: 'Sholat di Masjid Nabawi', activity_description: 'Sholat Ashar berjamaah di Masjid Nabawi' },
                        { activity_time: '19:00', activity_title: 'Orientasi Masjid Nabawi', activity_description: 'Pengenalan area Masjid Nabawi' }
                    ]
                },
                {
                    day_number: 3,
                    day_title: 'Ziarah Madinah',
                    description: 'Ziarah ke tempat-tempat bersejarah di Madinah',
                    activities: [
                        { activity_time: '08:00', activity_title: 'Ziarah Masjid Quba', activity_description: 'Sholat dan ziarah di Masjid Quba' },
                        { activity_time: '10:00', activity_title: 'Ziarah Jabal Uhud', activity_description: 'Ziarah ke Jabal Uhud dan makam para syuhada' },
                        { activity_time: '12:00', activity_title: 'Ziarah Masjid Qiblatain', activity_description: 'Ziarah ke Masjid Qiblatain' },
                        { activity_time: '14:00', activity_title: 'Istirahat di Hotel', activity_description: 'Kembali ke hotel untuk istirahat' },
                        { activity_time: '16:00', activity_title: 'Sholat di Raudhah', activity_description: 'Beribadah di Raudhah Masjid Nabawi' }
                    ]
                },
                {
                    day_number: 4,
                    day_title: 'Madinah - Makkah',
                    description: 'Perjalanan dari Madinah ke Makkah',
                    activities: [
                        { activity_time: '06:00', activity_title: 'Sholat Subuh di Masjid Nabawi', activity_description: 'Sholat Subuh berjamaah terakhir di Masjid Nabawi' },
                        { activity_time: '09:00', activity_title: 'Check-out Hotel', activity_description: 'Persiapan perjalanan ke Makkah' },
                        { activity_time: '10:00', activity_title: 'Perjalanan ke Makkah', activity_description: 'Perjalanan darat menuju Makkah (±5-6 jam)' },
                        { activity_time: '16:00', activity_title: 'Check-in Hotel Makkah', activity_description: 'Check-in hotel di Makkah' },
                        { activity_time: '18:00', activity_title: 'Umroh Pertama', activity_description: 'Pelaksanaan ibadah Umroh (Ihram, Thawaf, Sai, Tahallul)' }
                    ]
                },
                {
                    day_number: 5,
                    day_title: 'Ibadah di Masjidil Haram',
                    description: 'Beribadah di Masjidil Haram',
                    activities: [
                        { activity_time: '05:00', activity_title: 'Sholat Subuh di Masjidil Haram', activity_description: 'Sholat Subuh berjamaah' },
                        { activity_time: '07:00', activity_title: 'Thawaf Sunnah', activity_description: 'Thawaf sunnah mengelilingi Kabah' },
                        { activity_time: '10:00', activity_title: 'Istirahat di Hotel', activity_description: 'Kembali ke hotel untuk istirahat' },
                        { activity_time: '16:00', activity_title: 'Sholat Ashar di Masjidil Haram', activity_description: 'Sholat Ashar berjamaah' },
                        { activity_time: '20:00', activity_title: 'Thawaf Malam', activity_description: 'Thawaf dan beribadah di malam hari' }
                    ]
                },
                {
                    day_number: 6,
                    day_title: 'Ziarah Makkah',
                    description: 'Ziarah ke tempat-tempat bersejarah di Makkah',
                    activities: [
                        { activity_time: '08:00', activity_title: 'Ziarah Jabal Rahmah', activity_description: 'Ziarah ke Jabal Rahmah di Arafah' },
                        { activity_time: '10:00', activity_title: 'Ziarah Jabal Tsur', activity_description: 'Ziarah ke Gua Tsur' },
                        { activity_time: '12:00', activity_title: 'Ziarah Jabal Nur', activity_description: 'Ziarah ke Gua Hira di Jabal Nur' },
                        { activity_time: '15:00', activity_title: 'Istirahat di Hotel', activity_description: 'Kembali ke hotel untuk istirahat' },
                        { activity_time: '17:00', activity_title: 'Beribadah di Masjidil Haram', activity_description: 'Sholat dan beribadah' }
                    ]
                },
                {
                    day_number: 7,
                    day_title: 'Ibadah Bebas di Makkah',
                    description: 'Waktu bebas untuk ibadah',
                    activities: [
                        { activity_time: '05:00', activity_title: 'Sholat Subuh di Masjidil Haram', activity_description: 'Sholat Subuh berjamaah' },
                        { activity_time: '08:00', activity_title: 'Ibadah Bebas', activity_description: 'Waktu bebas untuk thawaf, sholat, dan ibadah lainnya' },
                        { activity_time: '12:00', activity_title: 'Istirahat Siang', activity_description: 'Istirahat di hotel' },
                        { activity_time: '16:00', activity_title: 'Ibadah Sore', activity_description: 'Melanjutkan ibadah di Masjidil Haram' },
                        { activity_time: '20:00', activity_title: 'Thawaf Wada', activity_description: 'Thawaf perpisahan (bagi yang ingin melaksanakan)' }
                    ]
                },
                {
                    day_number: 8,
                    day_title: 'Persiapan Kepulangan',
                    description: 'Hari terakhir di Makkah',
                    activities: [
                        { activity_time: '05:00', activity_title: 'Sholat Subuh Terakhir', activity_description: 'Sholat Subuh terakhir di Masjidil Haram' },
                        { activity_time: '08:00', activity_title: 'Thawaf Wada', activity_description: 'Thawaf perpisahan (wajib)' },
                        { activity_time: '10:00', activity_title: 'Check-out Hotel', activity_description: 'Check-out dan persiapan ke bandara' },
                        { activity_time: '12:00', activity_title: 'Perjalanan ke Jeddah', activity_description: 'Perjalanan menuju Bandara Jeddah' },
                        { activity_time: '15:00', activity_title: 'Check-in Bandara', activity_description: 'Proses check-in dan boarding' },
                        { activity_time: '18:00', activity_title: 'Penerbangan ke Indonesia', activity_description: 'Penerbangan kembali ke Indonesia' }
                    ]
                },
                {
                    day_number: 9,
                    day_title: 'Tiba di Indonesia',
                    description: 'Kepulangan ke Tanah Air',
                    activities: [
                        { activity_time: '08:00', activity_title: 'Tiba di Indonesia', activity_description: 'Tiba di bandara Indonesia' },
                        { activity_time: '09:00', activity_title: 'Proses Imigrasi', activity_description: 'Proses imigrasi dan pengambilan bagasi' },
                        { activity_time: '10:00', activity_title: 'Perjalanan Pulang', activity_description: 'Perjalanan kembali ke rumah masing-masing' }
                    ]
                }
            ];

            // Adjust template based on package duration
            let template = standardTemplate.slice(0, Math.min(duration, standardTemplate.length));
            
            // Set dates for each day
            template = template.map((day, index) => {
                const dayDate = new Date(departureDate);
                dayDate.setDate(dayDate.getDate() + index);
                
                return {
                    ...day,
                    day_date: dayDate.toISOString().split('T')[0],
                    order: day.day_number,
                    activities: day.activities.map((act, actIndex) => ({
                        ...act,
                        order: actIndex + 1
                    }))
                };
            });

            this.tourPlans = template;
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Tour Plan Standar Dimuat!',
                    text: 'Template tour plan standar telah dimuat. Anda dapat mengedit sesuai kebutuhan.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Tour plan standar telah dimuat. Silakan edit sesuai kebutuhan.');
            }
        }
    };
}
</script>
