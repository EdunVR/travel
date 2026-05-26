
<div class="space-y-4">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold">Keberangkatan</h3>
            <p class="text-sm text-slate-500">Kelola batch keberangkatan untuk paket ini</p>
        </div>
        <button x-on:click="openCreateKeberangkatan()"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 text-sm">
            <i class='bx bx-plus-circle'></i> Tambah Keberangkatan
        </button>
    </div>

    <!-- Loading -->
    <div x-show="loadingKeberangkatan" class="text-center py-8">
        <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
        <span class="ml-2 text-slate-500">Memuat keberangkatan...</span>
    </div>

    <!-- Empty -->
    <div x-show="!loadingKeberangkatan && keberangkatanList.length === 0" class="text-center py-12 text-slate-400">
        <i class='bx bx-plane-departure text-5xl mb-3'></i>
        <p>Belum ada keberangkatan untuk paket ini</p>
    </div>

    <!-- Keberangkatan List -->
    <div x-show="!loadingKeberangkatan" class="space-y-4">
        <template x-for="kb in keberangkatanList" :key="kb.id">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <!-- Keberangkatan Header -->
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div>
                            <div class="font-semibold" x-text="kb.keberangkatan_name"></div>
                            <div class="text-xs text-slate-500 font-mono" x-text="kb.keberangkatan_code"></div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="{
                                'bg-gray-100 text-gray-700': kb.status === 'planning',
                                'bg-green-100 text-green-700': kb.status === 'confirmed',
                                'bg-blue-100 text-blue-700': kb.status === 'departed',
                                'bg-purple-100 text-purple-700': kb.status === 'completed'
                              }"
                              x-text="kb.status?.toUpperCase()"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-sm text-slate-600">
                            <i class='bx bx-calendar'></i>
                            <span x-text="kb.departure_date_formatted"></span>
                        </div>
                        <div class="text-sm text-slate-600 ml-3">
                            <i class='bx bx-group'></i>
                            <span x-text="(kb.bookings ? kb.bookings.reduce((s,b) => s + 1 + (b.family_count||0), 0) : (kb.confirmed_jamaah||0)) + '/' + kb.total_jamaah + ' jiwa'"></span>
                        </div>
                        <!-- Action buttons -->
                        <div class="flex gap-1 ml-3">
                            <button x-on:click="openRabModal(kb)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs"
                                    title="RAB Keberangkatan">
                                <i class='bx bx-calculator text-blue-600'></i> RAB
                            </button>
                            <a :href="`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${kb.id}/stiker-koper`"
                               target="_blank"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs"
                               title="Cetak Stiker Koper">
                                <i class='bx bx-briefcase text-amber-600'></i> Koper
                            </a>
                            <a :href="`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${kb.id}/manage-manifest`"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs"
                                    title="Kelola & Download Manifest">
                                <i class='bx bx-file-pdf text-red-600'></i> Manifest
                            </a>
                            <button x-on:click="openRoomlistSetting(kb)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs"
                                    title="Roomlist Setting">
                                <i class='bx bx-hotel text-purple-600'></i> Roomlist
                            </button>
                            <button x-on:click="openFinancialReport(kb)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs"
                                    title="Laporan Keuangan">
                                <i class='bx bx-bar-chart-alt-2 text-green-600'></i> Keuangan
                            </button>
                            <button x-on:click="openEditKeberangkatan(kb)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs">
                                <i class='bx bx-edit-alt'></i>
                            </button>
                            <button x-on:click="confirmDeleteKeberangkatan(kb)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Jamaah Bookings Table -->
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-slate-700">Daftar Jamaah Booking</h4>
                        <button x-on:click="openAddJamaah(kb)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 border border-primary-200 hover:bg-primary-100 text-xs">
                            <i class='bx bx-user-plus'></i> Tambah Jamaah
                        </button>
                    </div>

                    <div x-show="!kb.bookings || kb.bookings.length === 0" class="text-center py-4 text-slate-400 text-sm">
                        Belum ada jamaah terdaftar
                    </div>

                    <div x-show="kb.bookings && kb.bookings.length > 0" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-xs text-slate-500">
                                <tr>
                                    <th class="text-left px-3 py-2">Jamaah</th>
                                    <th class="text-left px-3 py-2">Tipe Kamar</th>
                                    <th class="text-left px-3 py-2">Add-ons</th>
                                    <th class="text-right px-3 py-2">HPP Aktual</th>
                                    <th class="text-right px-3 py-2">Harga Jual</th>
                                    <th class="text-center px-3 py-2">Pembayaran</th>
                                    <th class="text-center px-3 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="booking in kb.bookings" :key="booking.id">
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2">
                                            <div class="font-medium text-sm" x-text="booking.jamaah_name"></div>
                                            <div class="text-xs text-slate-500" x-text="booking.booking_code"></div>
                                            <div x-show="booking.family_count > 0" class="mt-1">
                                                <button x-on:click="booking._expandFamily = !booking._expandFamily"
                                                        class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                                    <i class='bx' :class="booking._expandFamily ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                                                    + <span x-text="booking.family_count"></span> anggota keluarga
                                                </button>
                                                <div x-show="booking._expandFamily" class="mt-1 pl-2 border-l-2 border-blue-200 space-y-0.5">
                                                    <template x-for="(fm, fi) in (booking.family_members_list || [])" :key="fi">
                                                        <div class="text-xs text-slate-600 flex items-center gap-2">
                                                            <i class='bx bx-user text-slate-400'></i>
                                                            <span x-text="fm.nama || fm.name || 'Anggota ' + (fi+1)"></span>
                                                            <span x-show="fm.tanggal_lahir" class="text-slate-400"
                                                                  x-text="'(' + calcAge(fm.tanggal_lahir) + ' thn)'"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-0.5 rounded-full text-xs capitalize"
                                                  :class="{
                                                    'bg-blue-100 text-blue-700': booking.room_type === 'double',
                                                    'bg-green-100 text-green-700': booking.room_type === 'triple',
                                                    'bg-purple-100 text-purple-700': booking.room_type === 'quad'
                                                  }"
                                                  x-text="booking.room_type || '-'"></span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div x-show="booking.addons && booking.addons.length > 0" class="space-y-0.5">
                                                <template x-for="addon in booking.addons" :key="addon.id">
                                                    <div class="text-xs text-slate-600">
                                                        <span x-text="addon.nama"></span>
                                                        <span class="text-slate-400 ml-1" x-text="'(' + formatCurrency(addon.harga * addon.qty) + ')'"></span>
                                                        <span x-show="addon.masuk_hpp" class="ml-1 px-1 rounded bg-orange-100 text-orange-700 text-xs">HPP</span>
                                                    </div>
                                                </template>
                                            </div>
                                            <span x-show="!booking.addons || booking.addons.length === 0" class="text-xs text-slate-400">-</span>
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <div class="text-sm font-semibold" x-text="formatCurrency(booking.hpp_aktual)"></div>
                                            <div class="text-xs text-slate-400">
                                                <span x-text="'Dasar: ' + formatCurrency(booking.hpp_dasar||0)"></span>
                                                <span x-show="(booking.hpp_hotel||0) > 0" x-text="' + Hotel: ' + formatCurrency(booking.hpp_hotel||0)"></span>
                                                <span x-show="(booking.hpp_addons||0) > 0" x-text="' + Addons: ' + formatCurrency(booking.hpp_addons||0)"></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <div class="text-sm font-semibold text-green-700" x-text="formatCurrency(booking.harga_jual_aktual || booking.total_price)"></div>
                                            <div class="text-xs text-slate-400" x-show="(booking.harga_jual_aktual||0) !== (parseFloat(booking.total_price)||0)">
                                                <span x-text="'Paket: ' + formatCurrency(booking.total_price||0)"></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs"
                                                  :class="{
                                                    'bg-red-100 text-red-700': booking.payment_status === 'unpaid',
                                                    'bg-yellow-100 text-yellow-700': booking.payment_status === 'partial',
                                                    'bg-green-100 text-green-700': booking.payment_status === 'paid'
                                                  }"
                                                  x-text="booking.payment_status"></span>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <a :href="`<?php echo e(url('admin/inventaris/travel/booking')); ?>/${booking.id}`"
                                               class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs">
                                                <i class='bx bx-show'></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <!-- Summary row -->
                            <tfoot class="bg-slate-50 text-xs font-semibold">
                                <tr>
                                    <td class="px-3 py-2" colspan="3">
                                        Total (<span x-text="kb.bookings?.length || 0"></span> booking,
                                        <span x-text="kb.bookings?.reduce((s,b) => s + 1 + (b.family_count||0), 0) || 0"></span> jiwa)
                                    </td>
                                    <td class="px-3 py-2 text-right" x-text="formatCurrency(kb.bookings?.reduce((s,b) => s+(b.hpp_aktual||0), 0))"></td>
                                    <td class="px-3 py-2 text-right text-green-700" x-text="formatCurrency(kb.bookings?.reduce((s,b) => s+(b.harga_jual_aktual||parseFloat(b.total_price)||0), 0))"></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>


<div x-show="showKbForm" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
    <div x-on:click.outside="showKbForm=false" class="w-full max-w-lg bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold" x-text="kbForm.id ? 'Edit Keberangkatan' : 'Tambah Keberangkatan'"></div>
            <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="showKbForm=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4 space-y-3">
            <div>
                <label class="text-sm text-slate-600">Nama Keberangkatan <span class="text-red-500">*</span></label>
                <input type="text" x-model.trim="kbForm.keberangkatan_name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm text-slate-600">Tanggal Berangkat <span class="text-red-500">*</span></label>
                    <input type="date" x-model="kbForm.departure_date" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="text-sm text-slate-600">Tanggal Kembali <span class="text-red-500">*</span></label>
                    <input type="date" x-model="kbForm.return_date" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                </div>
            </div>
            <div>
                <label class="text-sm text-slate-600">Total Kapasitas <span class="text-red-500">*</span></label>
                <input type="number" min="1" x-model.number="kbForm.total_jamaah" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
                <label class="text-sm text-slate-600">Status</label>
                <select x-model="kbForm.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <option value="planning">Planning</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="departed">Departed</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
        <div class="px-5 pb-4 pt-2 border-t border-slate-100 flex justify-end gap-2">
            <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="showKbForm=false">Batal</button>
            <button x-on:click="submitKbForm()" :disabled="savingKb"
                    class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!savingKb">Simpan</span>
                <span x-show="savingKb" class="flex items-center gap-2"><i class='bx bx-loader-alt bx-spin'></i></span>
            </button>
        </div>
    </div>
</div>


<div x-show="showFinancialModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
    <div x-on:click.outside="showFinancialModal=false" class="w-full max-w-3xl bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <div class="font-semibold">Laporan Keuangan Keberangkatan</div>
                <div class="text-sm text-slate-500" x-text="selectedKb?.keberangkatan_name"></div>
            </div>
            <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="showFinancialModal=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4 space-y-4" x-show="financialData">
            <!-- Summary Cards -->
            <div class="grid grid-cols-3 gap-3">
                <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                    <div class="text-xs text-blue-600">Total Revenue</div>
                    <div class="text-lg font-bold text-blue-800" x-text="formatCurrency(financialData?.total_revenue||0)"></div>
                </div>
                <div class="p-3 rounded-xl bg-orange-50 border border-orange-200">
                    <div class="text-xs text-orange-600">Total HPP Aktual</div>
                    <div class="text-lg font-bold text-orange-800" x-text="formatCurrency(financialData?.total_hpp||0)"></div>
                </div>
                <div class="p-3 rounded-xl bg-green-50 border border-green-200">
                    <div class="text-xs text-green-600">Profit</div>
                    <div class="text-lg font-bold"
                         :class="(financialData?.profit||0) >= 0 ? 'text-green-800' : 'text-red-800'"
                         x-text="formatCurrency(financialData?.profit||0)"></div>
                </div>
            </div>

            <!-- HPP Breakdown -->
            <div class="rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50 border-b text-sm font-semibold">Breakdown HPP per Jamaah</div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="text-left px-4 py-2">Jamaah</th>
                            <th class="text-center px-4 py-2">Tipe Kamar</th>
                            <th class="text-right px-4 py-2">HPP Dasar</th>
                            <th class="text-right px-4 py-2">HPP Hotel</th>
                            <th class="text-right px-4 py-2">Add-ons HPP</th>
                            <th class="text-right px-4 py-2">Total HPP</th>
                            <th class="text-right px-4 py-2">Harga Jual</th>
                            <th class="text-right px-4 py-2">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="row in (financialData?.rows||[])" :key="row.booking_id">
                            <tr>
                                <td class="px-4 py-2 text-sm" x-text="row.jamaah_name"></td>
                                <td class="px-4 py-2 text-center capitalize text-xs" x-text="row.room_type||'-'"></td>
                                <td class="px-4 py-2 text-right text-xs" x-text="formatCurrency(row.hpp_dasar||0)"></td>
                                <td class="px-4 py-2 text-right text-xs" x-text="formatCurrency(row.hpp_hotel||0)"></td>
                                <td class="px-4 py-2 text-right text-xs" x-text="formatCurrency(row.hpp_addons||0)"></td>
                                <td class="px-4 py-2 text-right text-xs font-semibold" x-text="formatCurrency(row.total_hpp||0)"></td>
                                <td class="px-4 py-2 text-right text-xs text-green-700" x-text="formatCurrency(row.harga_jual||0)"></td>
                                <td class="px-4 py-2 text-right text-xs font-semibold"
                                    :class="(row.profit||0) >= 0 ? 'text-green-700' : 'text-red-600'"
                                    x-text="formatCurrency(row.profit||0)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="px-5 pb-4 pt-2 border-t border-slate-100 flex justify-end gap-2">
            <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50 text-sm" x-on:click="showFinancialModal=false">Tutup</button>
        </div>
    </div>
</div>


<div x-show="showRabModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto" style="display:none">
    <div x-on:click.outside="showRabModal=false" class="w-full max-w-4xl bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <div class="font-semibold">RAB Keberangkatan</div>
                <div class="text-xs text-slate-500" x-text="rabKb?.keberangkatan_name + ' — ' + (rabData?.jamaah_count||0) + ' jamaah'"></div>
            </div>
            <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="showRabModal=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4 max-h-[75vh] overflow-y-auto">
            <!-- Error -->
            <div x-show="rabData?.error" class="p-4 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <i class='bx bx-info-circle'></i> <span x-text="rabData?.error"></span>
            </div>

            <!-- Data -->
            <div x-show="rabData && !rabData.error">
                <!-- Summary cards -->
                <div class="grid grid-cols-4 gap-3 mb-4">
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-3 text-center">
                        <div class="text-xs text-blue-600">Total Budget</div>
                        <div class="font-bold text-blue-800 text-sm" x-text="formatCurrency(rabData?.total_budget||0)"></div>
                    </div>
                    <div class="rounded-xl bg-green-50 border border-green-200 p-3 text-center">
                        <div class="text-xs text-green-600">Total Realisasi</div>
                        <div class="font-bold text-green-800 text-sm" x-text="formatCurrency(rabData?.total_realisasi||0)"></div>
                    </div>
                    <div class="rounded-xl bg-red-50 border border-red-200 p-3 text-center">
                        <div class="text-xs text-red-600">Total Hutang</div>
                        <div class="font-bold text-red-800 text-sm" x-text="formatCurrency(rabData?.total_hutang||0)"></div>
                    </div>
                    <div class="rounded-xl p-3 text-center"
                         :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'bg-emerald-50 border border-emerald-200' : 'bg-orange-50 border border-orange-200'">
                        <div class="text-xs" :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'text-emerald-600' : 'text-orange-600'"
                             x-text="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'Surplus' : 'Defisit'"></div>
                        <div class="font-bold text-sm"
                             :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'text-emerald-800' : 'text-orange-800'"
                             x-text="formatCurrency(Math.abs((rabData?.total_budget||0)-(rabData?.total_realisasi||0)))"></div>
                    </div>
                </div>

                <!-- Progress bar keseluruhan -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>Progress Realisasi Keseluruhan</span>
                        <span x-text="((rabData?.total_budget||0) > 0 ? Math.min(100,((rabData?.total_realisasi||0)/(rabData?.total_budget||1)*100)) : 0).toFixed(1) + '%'"></span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all"
                             :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'bg-green-500' : 'bg-orange-500'"
                             :style="'width:' + Math.min(100, (rabData?.total_budget||0) > 0 ? ((rabData?.total_realisasi||0)/(rabData?.total_budget||1)*100) : 0) + '%'"></div>
                    </div>
                </div>

                <!-- Tabel items -->
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="text-left px-3 py-2">Komponen Biaya</th>
                                <th class="text-center px-3 py-2">Tipe</th>
                                <th class="text-right px-3 py-2">Budget</th>
                                <th class="text-right px-3 py-2 w-40">Progress</th>
                                <th class="text-right px-3 py-2">Realisasi</th>
                                <th class="text-center px-3 py-2">Status</th>
                                <th class="text-center px-3 py-2 w-48">Aksi Realisasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="item in (rabData?.items||[])" :key="item.id">
                                <tr :class="item.type === 'hpp_dasar' ? '' : (item.type === 'hotel_aktual' ? 'bg-purple-50' : 'bg-orange-50')">
                                    <td class="px-3 py-2 font-medium" x-text="item.label"></td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-1.5 py-0.5 rounded text-xs"
                                              :class="item.type==='hpp_dasar' ? 'bg-blue-100 text-blue-700' : (item.type==='hotel_aktual' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700')"
                                              x-text="item.type==='hpp_dasar' ? 'HPP Dasar' : (item.type==='hotel_aktual' ? 'Hotel' : 'Add-on')"></span>
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold" x-text="formatCurrency(item.total||0)"></td>
                                    <!-- Progress bar per item -->
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-1">
                                            <div class="flex-1 bg-slate-200 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all"
                                                     :class="(item.realisasi||0) <= (item.total||0) ? 'bg-green-500' : 'bg-orange-500'"
                                                     :style="'width:' + Math.min(100, (item.total||0) > 0 ? ((item.realisasi||0)/(item.total||1)*100) : 0) + '%'"></div>
                                            </div>
                                            <span class="text-xs text-slate-500 whitespace-nowrap"
                                                  x-text="((item.total||0) > 0 ? Math.min(100,((item.realisasi||0)/(item.total||1)*100)) : 0).toFixed(0) + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-right"
                                        :class="(item.realisasi||0) > (item.total||0) ? 'text-orange-600 font-semibold' : 'text-green-700'"
                                        x-text="formatCurrency(item.realisasi||0)"></td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              :class="item.payment_status==='lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                              x-text="item.payment_status==='lunas' ? '✓ LUNAS' : '⚠ HUTANG'"></span>
                                    </td>
                                    <!-- Kolom aksi: hanya HPP dasar yang bisa diinput realisasi -->
                                    <td class="px-3 py-2">
                                        <template x-if="item.hpp_key">
                                            <div class="flex items-center gap-1">
                                                <input type="number" min="0"
                                                       :value="item.realisasi||0"
                                                       @change="item.realisasi = parseFloat($event.target.value)||0"
                                                       class="w-24 rounded-lg border border-slate-200 px-2 py-1 text-xs text-right focus:ring-1 focus:ring-primary-300"
                                                       placeholder="0">
                                                <button x-on:click="updateRabItemStatus(item.hpp_key, item.payment_status, item.hutang_amount||0, item.realisasi||0)"
                                                        :disabled="updatingRab"
                                                        class="px-2 py-1 rounded-lg bg-primary-600 text-white text-xs hover:bg-primary-700 disabled:opacity-50 whitespace-nowrap">
                                                    <i class='bx bx-save'></i> Simpan
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="!item.hpp_key">
                                            <span class="text-slate-300 text-xs">Auto</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-slate-50 font-semibold text-xs">
                            <tr>
                                <td class="px-3 py-2" colspan="2">Total</td>
                                <td class="px-3 py-2 text-right" x-text="formatCurrency(rabData?.total_budget||0)"></td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-1">
                                        <div class="flex-1 bg-slate-200 rounded-full h-2">
                                            <div class="h-2 rounded-full"
                                                 :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'bg-green-500' : 'bg-orange-500'"
                                                 :style="'width:' + Math.min(100, (rabData?.total_budget||0) > 0 ? ((rabData?.total_realisasi||0)/(rabData?.total_budget||1)*100) : 0) + '%'"></div>
                                        </div>
                                        <span x-text="((rabData?.total_budget||0) > 0 ? Math.min(100,((rabData?.total_realisasi||0)/(rabData?.total_budget||1)*100)) : 0).toFixed(0) + '%'"></span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-right" x-text="formatCurrency(rabData?.total_realisasi||0)"></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Keterangan surplus/defisit -->
                <div class="mt-3 p-3 rounded-xl border text-sm"
                     :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0) ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-orange-50 border-orange-200 text-orange-800'">
                    <template x-if="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0)">
                        <div class="flex items-center gap-2">
                            <i class='bx bx-check-circle text-lg'></i>
                            <span>
                                <strong>Surplus</strong>:
                                Realisasi lebih rendah dari budget sebesar
                                <strong x-text="formatCurrency((rabData?.total_budget||0)-(rabData?.total_realisasi||0))"></strong>.
                                Nilai ini dilaporkan sebagai efisiensi anggaran.
                            </span>
                        </div>
                    </template>
                    <template x-if="(rabData?.total_realisasi||0) > (rabData?.total_budget||0)">
                        <div class="flex items-center gap-2">
                            <i class='bx bx-error-circle text-lg'></i>
                            <span>
                                <strong>Defisit</strong>:
                                Realisasi melebihi budget sebesar
                                <strong x-text="formatCurrency((rabData?.total_realisasi||0)-(rabData?.total_budget||0))"></strong>.
                                Nilai ini dilaporkan sebagai kelebihan pengeluaran.
                            </span>
                        </div>
                    </template>
                </div>

                <div class="mt-2 text-xs text-slate-400">
                    <i class='bx bx-info-circle'></i>
                    Input nilai realisasi pada baris HPP Dasar, lalu klik Simpan. Hotel & Add-on otomatis dari data booking.
                    Nilai realisasi akan masuk ke laporan keuangan keberangkatan.
                </div>

                <!-- Status penyesuaian laporan -->
                <div x-show="rabData?.laporan_disesuaikan" class="mt-2 p-2 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-700 flex items-center gap-2">
                    <i class='bx bx-check-shield'></i>
                    <span>
                        Laporan keuangan sudah disesuaikan pada
                        <strong x-text="rabData?.laporan_disesuaikan_at"></strong>.
                        Penyesuaian:
                        <strong x-text="(rabData?.laporan_adjustment||0) >= 0 ? 'Surplus ' + formatCurrency(rabData?.laporan_adjustment||0) : 'Defisit ' + formatCurrency(Math.abs(rabData?.laporan_adjustment||0))"></strong>.
                    </span>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="!rabData" class="text-center py-8 text-slate-400">
                <i class='bx bx-loader-alt bx-spin text-xl'></i>
                <span class="ml-2">Memuat data RAB...</span>
            </div>
        </div>
        <div class="px-5 pb-4 pt-2 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <!-- Tombol Sesuaikan Laporan -->
                <template x-if="rabData && !rabData.error && Math.abs((rabData?.total_budget||0)-(rabData?.total_realisasi||0)) > 0">
                    <button x-on:click="sesuaikanLaporan()"
                            :disabled="updatingRab"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium disabled:opacity-50"
                            :class="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0)
                                ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                : 'bg-orange-600 text-white hover:bg-orange-700'">
                        <i class='bx bx-sync'></i>
                        <span x-text="(rabData?.total_realisasi||0) <= (rabData?.total_budget||0)
                            ? 'Sesuaikan Laporan (Surplus: ' + formatCurrency((rabData?.total_budget||0)-(rabData?.total_realisasi||0)) + ')'
                            : 'Sesuaikan Laporan (Defisit: ' + formatCurrency((rabData?.total_realisasi||0)-(rabData?.total_budget||0)) + ')'"></span>
                    </button>
                </template>
                <!-- Sudah disesuaikan & tombol reset -->
                <template x-if="rabData?.laporan_disesuaikan">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5">
                            <i class='bx bx-check-shield'></i> Laporan sudah disesuaikan
                        </span>
                        <button x-on:click="resetPenyesuaianLaporan()"
                                :disabled="updatingRab"
                                class="inline-flex items-center gap-1 text-xs text-slate-600 border border-slate-200 rounded-lg px-3 py-1.5 hover:bg-slate-50 disabled:opacity-50">
                            <i class='bx bx-reset'></i> Reset
                        </button>
                    </div>
                </template>
                <template x-if="!rabData || rabData.error || (Math.abs((rabData?.total_budget||0)-(rabData?.total_realisasi||0)) === 0 && !rabData?.laporan_disesuaikan)">
                    <span class="text-xs text-slate-400">Tidak ada penyesuaian diperlukan</span>
                </template>
            </div>
            <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50 text-sm" x-on:click="showRabModal=false">Tutup</button>
        </div>
    </div>
</div>


<div x-show="toDeleteKb" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
    <div x-on:click.outside="toDeleteKb=null" class="w-full max-w-md rounded-2xl bg-white shadow-float my-4">
        <div class="px-5 py-4">
            <div class="font-semibold">Hapus Keberangkatan?</div>
            <p class="text-slate-600 mt-1 text-sm" x-text="toDeleteKb?.keberangkatan_name"></p>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex justify-end gap-2">
            <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50 text-sm" x-on:click="toDeleteKb=null">Batal</button>
            <button x-on:click="deleteKbNow()" :disabled="deletingKb"
                    class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50 text-sm">
                <span x-show="!deletingKb">Hapus</span>
                <span x-show="deletingKb"><i class='bx bx-loader-alt bx-spin'></i></span>
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/partials/keberangkatan-tab.blade.php ENDPATH**/ ?>