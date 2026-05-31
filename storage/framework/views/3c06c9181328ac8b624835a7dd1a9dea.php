<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Pengingat Pembayaran']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Pengingat Pembayaran')]); ?>
<div x-data="paymentReminderPage()" x-init="init()" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Pengingat Pembayaran Otomatis</h1>
            <p class="text-gray-600">Monitoring cron job & pengaturan jadwal penagihan</p>
        </div>
        <div class="flex gap-2">
            <button @click="triggerManual(true)" :disabled="triggering" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 text-sm">
                <i class="bx bx-test-tube"></i> Simulasi (Dry Run)
            </button>
            <button @click="triggerManual(false)" :disabled="triggering" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 text-sm">
                <i class="bx bx-send"></i> Kirim Sekarang
            </button>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <div class="text-xs text-gray-500 uppercase">Last Cron Run</div>
            <div class="text-sm font-semibold mt-1" x-text="data.last_run || '-'"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <div class="text-xs text-gray-500 uppercase">Terkirim Hari Ini</div>
            <div class="text-2xl font-bold text-green-600" x-text="data.today_stats?.sent || 0"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <div class="text-xs text-gray-500 uppercase">Gagal Hari Ini</div>
            <div class="text-2xl font-bold text-red-600" x-text="data.today_stats?.failed || 0"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <div class="text-xs text-gray-500 uppercase">Pending</div>
            <div class="text-2xl font-bold text-yellow-600" x-text="data.today_stats?.pending || 0"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-gray-400">
            <div class="text-xs text-gray-500 uppercase">Dilewati</div>
            <div class="text-2xl font-bold text-gray-600" x-text="data.today_stats?.skipped || 0"></div>
        </div>
    </div>

    <!-- Settings Section -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="bx bx-cog text-blue-500"></i> Pengaturan Jadwal</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select x-model="settings.is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hari Pengingat (H-)</label>
                <input type="text" x-model="settings.reminder_days" placeholder="30,15,7" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma. Contoh: 30,15,7</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Kirim</label>
                <input type="text" x-model="settings.start_time" placeholder="09:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Interval (menit)</label>
                <input type="number" x-model="settings.interval_minutes" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <p class="text-xs text-gray-400 mt-1">Selisih waktu antar pengiriman</p>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Template Pesan WhatsApp</label>
            <textarea x-model="settings.message_template" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm"></textarea>
            <p class="text-xs text-gray-400 mt-1">Variabel: {nama}, {paket}, {kode_booking}, {sisa_bayar}, {tgl_berangkat}, {sisa_hari}</p>
        </div>
        <button @click="saveSettings()" :disabled="savingSettings" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            <i class="bx" :class="savingSettings ? 'bx-loader-alt bx-spin' : 'bx-save'"></i>
            <span x-text="savingSettings ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
        </button>
    </div>

    <!-- Cron Job History -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="bx bx-time text-green-500"></i> Riwayat Eksekusi Cron Job</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left">Waktu Mulai</th>
                        <th class="px-3 py-2 text-left">Selesai</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-center">Diproses</th>
                        <th class="px-3 py-2 text-center">Terkirim</th>
                        <th class="px-3 py-2 text-center">Gagal</th>
                        <th class="px-3 py-2 text-center">Durasi</th>
                        <th class="px-3 py-2 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="log in data.cron_logs" :key="log.id">
                        <tr class="border-t">
                            <td class="px-3 py-2" x-text="log.started_at || '-'"></td>
                            <td class="px-3 py-2" x-text="log.finished_at || '-'"></td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="log.status === 'success' ? 'bg-green-100 text-green-700' : (log.status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')"
                                      x-text="log.status"></span>
                            </td>
                            <td class="px-3 py-2 text-center" x-text="log.processed_count"></td>
                            <td class="px-3 py-2 text-center text-green-600 font-medium" x-text="log.sent_count"></td>
                            <td class="px-3 py-2 text-center text-red-600" x-text="log.failed_count"></td>
                            <td class="px-3 py-2 text-center" x-text="log.duration"></td>
                            <td class="px-3 py-2 text-xs text-gray-500 max-w-xs truncate" x-text="log.notes"></td>
                        </tr>
                    </template>
                    <tr x-show="!data.cron_logs || data.cron_logs.length === 0">
                        <td colspan="8" class="px-3 py-6 text-center text-gray-400">Belum ada riwayat eksekusi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reminder Logs -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="bx bx-message-detail text-purple-500"></i> Log Pengiriman Pengingat</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left">Tanggal</th>
                        <th class="px-3 py-2 text-left">Jamaah</th>
                        <th class="px-3 py-2 text-left">Paket</th>
                        <th class="px-3 py-2 text-left">Kode Booking</th>
                        <th class="px-3 py-2 text-center">Tipe</th>
                        <th class="px-3 py-2 text-left">No. HP</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-left">Error</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="log in data.reminder_logs" :key="log.id">
                        <tr class="border-t">
                            <td class="px-3 py-2" x-text="log.created_at"></td>
                            <td class="px-3 py-2 font-medium" x-text="log.jamaah_name"></td>
                            <td class="px-3 py-2 text-xs" x-text="log.package_name"></td>
                            <td class="px-3 py-2 font-mono text-xs" x-text="log.booking_code"></td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-medium" x-text="log.reminder_type"></span>
                            </td>
                            <td class="px-3 py-2 text-xs" x-text="log.phone || '-'"></td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-700': log.status === 'sent',
                                          'bg-red-100 text-red-700': log.status === 'failed',
                                          'bg-yellow-100 text-yellow-700': log.status === 'pending',
                                          'bg-gray-100 text-gray-600': log.status === 'skipped'
                                      }"
                                      x-text="log.status"></span>
                            </td>
                            <td class="px-3 py-2 text-xs text-red-500 max-w-xs truncate" x-text="log.error_message || ''"></td>
                        </tr>
                    </template>
                    <tr x-show="!data.reminder_logs || data.reminder_logs.length === 0">
                        <td colspan="8" class="px-3 py-6 text-center text-gray-400">Belum ada log pengiriman</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hostinger Cron Setup Guide -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-400">
        <h3 class="text-lg font-semibold mb-3"><i class="bx bx-server text-orange-500"></i> Setup Cron Job di Hostinger</h3>
        <p class="text-sm text-gray-600 mb-3">Tambahkan cron job berikut di Hostinger hPanel → Advanced → Cron Jobs:</p>
        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto">
            <p class="mb-2">// Jalankan setiap 15 menit (antara jam 09:00-18:00)</p>
            <p class="text-white">*/15 9-17 * * * cd /home/u123456789/domains/hmtourtravel.com/public_html && php artisan payment:send-reminders >> /dev/null 2>&1</p>
            <p class="mt-3 mb-2">// Atau jalankan scheduler (recommended)</p>
            <p class="text-white">* * * * * cd /home/u123456789/domains/hmtourtravel.com/public_html && php artisan schedule:run >> /dev/null 2>&1</p>
        </div
    </div>
</div>

<script>
function paymentReminderPage() {
    return {
        data: { cron_logs: [], reminder_logs: [], today_stats: {}, last_run: '-', settings: {} },
        settings: { reminder_days: '30,15,7', start_time: '09:00', interval_minutes: '15', is_active: '1', message_template: '' },
        savingSettings: false,
        triggering: false,

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const res = await fetch('<?php echo e(route("admin.inventaris.travel.payment-reminder.data")); ?>');
                const json = await res.json();
                this.data = json;
                if (json.settings) {
                    this.settings = json.settings;
                }
            } catch (e) {
                console.error('Error loading data:', e);
            }
        },

        async saveSettings() {
            this.savingSettings = true;
            try {
                const res = await fetch('<?php echo e(route("admin.inventaris.travel.payment-reminder.settings")); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.settings)
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: e.message });
            }
            this.savingSettings = false;
        },

        async triggerManual(dryRun) {
            const confirm = await Swal.fire({
                icon: dryRun ? 'info' : 'warning',
                title: dryRun ? 'Simulasi Pengiriman' : 'Kirim Pengingat Sekarang',
                text: dryRun ? 'Ini hanya simulasi, tidak ada pesan yang dikirim.' : 'Pengingat akan dikirim ke semua jamaah yang memenuhi syarat.',
                showCancelButton: true,
                confirmButtonText: dryRun ? 'Jalankan Simulasi' : 'Ya, Kirim',
                cancelButtonText: 'Batal'
            });
            if (!confirm.isConfirmed) return;

            this.triggering = true;
            try {
                const res = await fetch('<?php echo e(route("admin.inventaris.travel.payment-reminder.trigger")); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ dry_run: dryRun })
                });
                const data = await res.json();
                Swal.fire({ icon: data.success ? 'success' : 'error', title: data.success ? 'Selesai' : 'Gagal', text: data.message });
                await this.loadData();
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: e.message });
            }
            this.triggering = false;
        }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/payment-reminder/index.blade.php ENDPATH**/ ?>