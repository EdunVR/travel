<x-layouts.admin :title="'Penyesuaian Info Paket'">
    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div x-data="infoPaketForm()" x-init="init()" class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.inventaris.travel.package.detail', $package->id) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-2">
                    <i class="bx bx-arrow-back"></i> Kembali ke Detail Paket
                </a>
                <h1 class="text-2xl font-bold">Penyesuaian Info Paket</h1>
                <p class="text-gray-600">{{ $package->package_name }} — {{ $keberangkatan->keberangkatan_name }}</p>
            </div>
            <div class="flex gap-2">
                <button @click="saveData()" type="button" :disabled="saving"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                    <i class="bx" :class="saving ? 'bx-loader-alt bx-spin' : 'bx-save'"></i>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
                <button @click="saveAndDownload()" type="button" :disabled="saving"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                    <i class="bx bx-download"></i> Simpan & Download PDF
                </button>
            </div>
        </div>

        <!-- Date Adjustment Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-400">
            <div class="flex items-center gap-2 mb-4">
                <i class="bx bx-calendar text-orange-500 text-xl"></i>
                <h3 class="text-lg font-semibold">Penyesuaian Tanggal & Jam</h3>
            </div>
            <p class="text-xs text-gray-500 mb-4">
                <i class="bx bx-info-circle"></i> Mengubah tanggal keberangkatan akan otomatis menyesuaikan seluruh data terkait: tanggal paket, hotel check-in/out, keberangkatan, booking hotel jamaah, dan tour plan.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Jam Keberangkatan</label>
                    <input type="text" id="fp_departure" x-ref="fpDeparture" x-model="dateForm.departure_datetime"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="DD/MM/YYYY HH:MM">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Jam Kepulangan</label>
                    <input type="text" id="fp_return" x-ref="fpReturn" x-model="dateForm.return_datetime"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="DD/MM/YYYY HH:MM">
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <button @click="saveDates()" type="button" :disabled="savingDates"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50">
                    <i class="bx" :class="savingDates ? 'bx-loader-alt bx-spin' : 'bx-calendar-check'"></i>
                    <span x-text="savingDates ? 'Memproses...' : 'Simpan Perubahan Tanggal'"></span>
                </button>
                <span class="text-xs text-gray-400">Perubahan ini bersifat permanen dan akan mengubah data master paket.</span>
            </div>
        </div>

        <!-- Header Info Section -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Informasi Header</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                    <input type="text" x-model="form.group_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tour Leader Name</label>
                    <input type="text" x-model="form.tour_leader_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adult</label>
                        <input type="number" x-model.number="form.adult_count" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Child</label>
                        <input type="number" x-model.number="form.child_count" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Infant</label>
                        <input type="number" x-model.number="form.infant_count" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Rawdah Schedule -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Rawdah & Umrah Schedule</h3>
                <button @click="addRawdahRow()" type="button" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="bx bx-plus"></i> Tambah Baris
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-3 py-2 w-12">NO</th>
                            <th class="border border-gray-300 px-3 py-2">ACTIVITY</th>
                            <th class="border border-gray-300 px-3 py-2 w-44">DATE</th>
                            <th class="border border-gray-300 px-3 py-2 w-32">TIME</th>
                            <th class="border border-gray-300 px-3 py-2 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in form.rawdah_rows" :key="idx">
                            <tr>
                                <td class="border border-gray-300 px-2 py-1 text-center" x-text="idx + 1"></td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.activity" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.date" placeholder="30 MARET 2026" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.time" placeholder="07.00 WAS" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1 text-center">
                                    <button @click="form.rawdah_rows.splice(idx, 1)" type="button" class="text-red-500 hover:text-red-700">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Itinerary Table -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Itinerary (Rencana Perjalanan)</h3>
                <button @click="addItineraryRow()" type="button" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="bx bx-plus"></i> Tambah Baris
                </button>
            </div>
            <p class="text-xs text-gray-500 mb-3">Data ini untuk keperluan apply visa, titik jemput bus, dll. Bisa diedit sesuai kebutuhan.</p>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-3 py-2 w-12">NO.</th>
                            <th class="border border-gray-300 px-3 py-2">FROM</th>
                            <th class="border border-gray-300 px-3 py-2">TO</th>
                            <th class="border border-gray-300 px-3 py-2 w-44">DATE</th>
                            <th class="border border-gray-300 px-3 py-2 w-28">TIME</th>
                            <th class="border border-gray-300 px-3 py-2">REMARK</th>
                            <th class="border border-gray-300 px-3 py-2 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in form.itinerary_rows" :key="idx">
                            <tr>
                                <td class="border border-gray-300 px-2 py-1 text-center" x-text="idx + 1"></td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.from" placeholder="JEDDAH AIRPORT" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.to" placeholder="MADINAH HOTEL" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.date" placeholder="29 MARET 2026" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.time" placeholder="17.30 WAS" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1">
                                    <input type="text" x-model="row.remark" placeholder="Landing 17.30 BAWA KOPER" class="w-full px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 rounded text-sm">
                                </td>
                                <td class="border border-gray-300 px-1 py-1 text-center">
                                    <button @click="form.itinerary_rows.splice(idx, 1)" type="button" class="text-red-500 hover:text-red-700">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function infoPaketForm() {
        return {
            saving: false,
            savingDates: false,
            form: {
                group_name: '',
                tour_leader_name: '',
                adult_count: 0,
                child_count: 0,
                infant_count: 0,
                rawdah_rows: [],
                itinerary_rows: []
            },
            dateForm: {
                departure_datetime: '{{ $package->departure_datetime ? $package->departure_datetime->format("d/m/Y H:i") : ($package->departure_date ? \Carbon\Carbon::parse($package->departure_date)->format("d/m/Y") . " 00:00" : "") }}',
                return_datetime: '{{ $package->return_datetime ? $package->return_datetime->format("d/m/Y H:i") : ($package->return_date ? \Carbon\Carbon::parse($package->return_date)->format("d/m/Y") . " 00:00" : "") }}'
            },

            async init() {
                // Initialize Flatpickr
                this.$nextTick(() => {
                    flatpickr(this.$refs.fpDeparture, {
                        enableTime: true,
                        time_24hr: true,
                        dateFormat: 'd/m/Y H:i',
                        defaultDate: this.dateForm.departure_datetime || null,
                        onChange: (selectedDates, dateStr) => {
                            this.dateForm.departure_datetime = dateStr;
                        }
                    });
                    flatpickr(this.$refs.fpReturn, {
                        enableTime: true,
                        time_24hr: true,
                        dateFormat: 'd/m/Y H:i',
                        defaultDate: this.dateForm.return_datetime || null,
                        onChange: (selectedDates, dateStr) => {
                            this.dateForm.return_datetime = dateStr;
                        }
                    });
                });

                // Load existing data or auto-fill
                try {
                    const res = await fetch(`{{ route('admin.inventaris.travel.package.info-paket.data', ['id' => $package->id, 'keberangkatanId' => $keberangkatan->id]) }}`);
                    const data = await res.json();
                    if (data.form) {
                        this.form = data.form;
                    }
                } catch (e) {
                    console.error('Error loading info paket data:', e);
                }
            },

            addRawdahRow() {
                this.form.rawdah_rows.push({ activity: '', date: '', time: '' });
            },

            addItineraryRow() {
                this.form.itinerary_rows.push({ from: '', to: '', date: '', time: '', remark: '' });
            },

            async saveDates() {
                if (!this.dateForm.departure_datetime || !this.dateForm.return_datetime) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal keberangkatan dan kepulangan harus diisi.' });
                    return;
                }

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi Perubahan Tanggal',
                    html: `<div class="text-left text-sm">
                        <p class="mb-2"><strong>Perubahan ini akan mempengaruhi:</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Tanggal keberangkatan & kepulangan paket</li>
                            <li>Tanggal check-in/out hotel (Madinah, Makkah, tambahan)</li>
                            <li>Tanggal keberangkatan</li>
                            <li>Tanggal booking hotel jamaah</li>
                            <li>Tanggal tour plan</li>
                        </ul>
                        <p class="mt-3 text-red-600 font-medium">Perubahan ini bersifat permanen!</p>
                    </div>`,
                    showCancelButton: true,
                    confirmButtonColor: '#ea580c',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Ubah Tanggal',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                this.savingDates = true;
                try {
                    const res = await fetch(`{{ route('admin.inventaris.travel.package.update-dates', ['id' => $package->id, 'keberangkatanId' => $keberangkatan->id]) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.dateForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 2500,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload page to reflect updated data
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal memperbarui tanggal' });
                    }
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
                }
                this.savingDates = false;
            },

            async saveData() {
                this.saving = true;
                try {
                    const res = await fetch(`{{ route('admin.inventaris.travel.package.info-paket.save', ['id' => $package->id, 'keberangkatanId' => $keberangkatan->id]) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data info paket berhasil disimpan', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menyimpan' });
                    }
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
                }
                this.saving = false;
            },

            async saveAndDownload() {
                await this.saveData();
                // Open PDF in new tab
                window.open(`{{ route('admin.inventaris.travel.package.info-paket.pdf', ['id' => $package->id, 'keberangkatanId' => $keberangkatan->id]) }}`, '_blank');
            }
        };
    }
    </script>
</x-layouts.admin>
