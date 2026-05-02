<x-layouts.admin :title="'Detail Buku Akuntansi'">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Detail Buku Akuntansi</h1>
                <p class="text-slate-600 text-sm">Informasi lengkap buku akuntansi</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.history.back()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
                    <i class='bx bx-arrow-back'></i> Kembali
                </button>
            </div>
        </div>

        {{-- Content --}}
        <div x-data="bookDetail()" x-init="init()">
            <!-- Loading -->
            <div x-show="loading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <!-- Error -->
            <div x-show="error" class="rounded-xl bg-red-50 border border-red-200 p-4">
                <div class="flex items-center gap-2 text-red-800">
                    <i class='bx bx-error-circle text-lg'></i>
                    <span x-text="error"></span>
                </div>
            </div>

            <!-- Book Detail -->
            <div x-show="!loading && !error && bookData" class="space-y-6">
                {{-- Book Info Card --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <div class="font-mono text-sm text-slate-600" x-text="bookData?.code || '-'"></div>
                                    <h2 class="text-xl font-bold text-slate-800" x-text="bookData?.name || '-'"></h2>
                                </div>
                                <div class="flex flex-col items-end gap-2" x-show="bookData">
                                    <span :class="getStatusBadgeClass(bookData?.status)" 
                                        class="px-3 py-1 rounded-full text-sm font-medium" 
                                        x-text="getStatusName(bookData?.status)"></span>
                                    <span x-show="bookData?.is_locked" 
                                        class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                        <i class='bx bx-lock-alt'></i> Terkunci
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-show="bookData">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Buku</label>
                                        <div class="flex items-center gap-2">
                                            <span :class="getTypeBadgeClass(bookData?.type)" 
                                                class="px-2 py-1 rounded-full text-xs"
                                                x-text="getTypeName(bookData?.type)"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
                                        <div class="text-slate-800" x-text="bookData?.outlet?.nama_outlet || '-'"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Mata Uang</label>
                                        <div class="text-slate-800" x-text="bookData?.currency || '-'"></div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
                                        <div class="text-slate-800" x-text="formatPeriod(bookData?.start_date, bookData?.end_date)"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Total Entri</label>
                                        <div class="text-2xl font-bold text-blue-600" x-text="bookData?.total_entries || 0"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Dibuat</label>
                                        <div class="text-slate-800" x-text="formatDate(bookData?.created_at)"></div>
                                    </div>
                                </div>
                            </div>

                            <div x-show="bookData?.description" class="mt-6 pt-6 border-t border-slate-200">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                                <p class="text-slate-600" x-text="bookData?.description"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Balance Card --}}
                    <div class="space-y-6" x-show="bookData">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Saldo</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Awal</label>
                                    <div class="text-xl font-semibold" x-text="formatCurrency(bookData?.opening_balance)"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Akhir</label>
                                    <div class="text-2xl font-bold" 
                                        :class="bookData?.closing_balance >= 0 ? 'text-green-600' : 'text-red-600'" 
                                        x-text="formatCurrency(bookData?.closing_balance)"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card" x-show="bookData">
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Aksi Cepat</h3>
                            <div class="space-y-2">
                                <button @click="openJournalModal()" 
                                        :disabled="bookData?.is_locked || bookData?.status !== 'active'"
                                        :class="bookData?.is_locked || bookData?.status !== 'active' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                                        class="w-full text-left px-4 py-3 rounded-lg transition-colors">
                                    <i class='bx bx-plus'></i> Tambah Entri Jurnal
                                </button>
                                <button @click="toggleBook(bookData.id, bookData?.status)" 
                                        :disabled="bookData?.is_locked"
                                        :class="bookData?.is_locked ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : bookData?.status === 'active' ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                                        class="w-full text-left px-4 py-3 rounded-lg transition-colors">
                                    <i :class="bookData?.status === 'active' ? 'bx bx-power-off' : 'bx bx-check-circle'"></i>
                                    <span x-text="bookData?.status === 'active' ? ' Nonaktifkan Buku' : ' Aktifkan Buku'"></span>
                                </button>
                                <button @click="closeBook(bookData)" 
                                        :disabled="bookData?.status !== 'active' || bookData?.is_locked"
                                        :class="bookData?.status !== 'active' || bookData?.is_locked ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'"
                                        class="w-full text-left px-4 py-3 rounded-lg transition-colors">
                                    <i class='bx bx-lock-alt'></i> Tutup Buku
                                </button>
                                <button @click="generateBookReport(bookData)" 
                                        class="w-full text-left px-4 py-3 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                                    <i class='bx bx-line-chart'></i> Laporan Buku
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Journal Entries Section --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card" x-show="bookData">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-slate-800">Entri Jurnal</h3>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-slate-500" x-text="'Total: ' + (bookData?.journal_entries?.length || 0) + ' entri'"></span>
                            <button @click="openJournalModal()" 
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">
                                <i class='bx bx-plus'></i> Tambah Entri
                            </button>
                        </div>
                    </div>

                    <div x-show="!bookData?.journal_entries || bookData?.journal_entries.length === 0" class="text-center py-8">
                        <i class='bx bx-book-open text-4xl text-slate-300 mb-3'></i>
                        <div class="text-slate-500 mb-4">Belum ada entri jurnal</div>
                        <button @click="openJournalModal()" 
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">
                            <i class='bx bx-plus'></i> Buat Entri Pertama
                        </button>
                    </div>

                    <div x-show="bookData?.journal_entries && bookData?.journal_entries.length > 0" class="space-y-4">
                        <template x-for="entry in bookData?.journal_entries" :key="entry.id">
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                {{-- Entry Header --}}
                                <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div class="flex items-center gap-4">
                                            <div>
                                                <div class="font-mono text-sm font-semibold text-slate-800" 
                                                    x-text="entry.transaction_number"></div>
                                                <div class="text-xs text-slate-500" 
                                                    x-text="formatDate(entry.transaction_date)"></div>
                                            </div>
                                            <span :class="getEntryStatusBadgeClass(entry.status)" 
                                                class="px-2 py-1 rounded-full text-xs"
                                                x-text="getEntryStatusName(entry.status)"></span>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm">
                                            <div class="text-right">
                                                <div class="font-mono text-slate-600" 
                                                    x-text="'Debit: ' + formatCurrency(entry.total_debit)"></div>
                                                <div class="font-mono text-slate-600" 
                                                    x-text="'Credit: ' + formatCurrency(entry.total_credit)"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-slate-600" x-text="entry.description"></div>
                                </div>

                                {{-- Entry Details --}}
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-slate-25">
                                            <tr>
                                                <th class="px-4 py-2 text-left w-8">#</th>
                                                <th class="px-4 py-2 text-left">Kode Akun</th>
                                                <th class="px-4 py-2 text-left">Nama Akun</th>
                                                <th class="px-4 py-2 text-right">Debit</th>
                                                <th class="px-4 py-2 text-right">Kredit</th>
                                                <th class="px-4 py-2 text-left">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(detail, detailIndex) in entry.journal_entry_details" :key="detail.id">
                                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                                    <td class="px-4 py-2 text-slate-500" x-text="detailIndex + 1"></td>
                                                    <td class="px-4 py-2">
                                                        <div class="font-mono text-sm text-slate-600" 
                                                            x-text="detail.account?.code"></div>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <div class="text-slate-800" x-text="detail.account?.name"></div>
                                                        <div class="text-xs text-slate-500" 
                                                            x-text="getTypeName(detail.account?.type)"></div>
                                                    </td>
                                                    <td class="px-4 py-2 text-right">
                                                        <template x-if="detail.debit > 0">
                                                            <div class="font-mono text-green-600 font-semibold" 
                                                                x-text="formatCurrency(detail.debit)"></div>
                                                        </template>
                                                        <template x-if="detail.debit === 0">
                                                            <div class="font-mono text-slate-400">-</div>
                                                        </template>
                                                    </td>
                                                    <td class="px-4 py-2 text-right">
                                                        <template x-if="detail.credit > 0">
                                                            <div class="font-mono text-red-600 font-semibold" 
                                                                x-text="formatCurrency(detail.credit)"></div>
                                                        </template>
                                                        <template x-if="detail.credit === 0">
                                                            <div class="font-mono text-slate-400">-</div>
                                                        </template>
                                                    </td>
                                                    <td class="px-4 py-2 text-slate-600" 
                                                        x-text="detail.description || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        {{-- Entry Summary --}}
                                        <tfoot class="bg-slate-50 border-t border-slate-200">
                                            <tr>
                                                <td colspan="3" class="px-4 py-2 text-right font-semibold">Total:</td>
                                                <td class="px-4 py-2 text-right">
                                                    <div class="font-mono font-semibold text-green-600" 
                                                        x-text="formatCurrency(entry.total_debit)"></div>
                                                </td>
                                                <td class="px-4 py-2 text-right">
                                                    <div class="font-mono font-semibold text-red-600" 
                                                        x-text="formatCurrency(entry.total_credit)"></div>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <div class="flex items-center gap-2" 
                                                        :class="entry.total_debit === entry.total_credit ? 'text-green-600' : 'text-red-600'">
                                                        <i :class="entry.total_debit === entry.total_credit ? 'bx bx-check-circle' : 'bx bx-error-circle'"></i>
                                                        <span class="text-xs font-medium" 
                                                            x-text="entry.total_debit === entry.total_credit ? 'Balance' : 'Tidak Balance'"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- Entry Footer --}}
                                <div class="bg-slate-25 px-4 py-2 border-t border-slate-200">
                                    <div class="flex justify-between items-center text-xs text-slate-500">
                                        <div>
                                            <span x-text="'Catatan: ' + (entry.notes || 'Tidak ada catatan')"></span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span x-text="'Dibuat: ' + formatDateTime(entry.created_at)"></span>
                                            <template x-if="entry.posted_at">
                                                <span x-text="'Diposting: ' + formatDateTime(entry.posted_at)"></span>
                                            </template>
                                            <div class="flex items-center gap-1">
                                                <button @click="viewJournalEntry(entry)" 
                                                        class="text-blue-600 hover:text-blue-800 p-1"
                                                        title="Lihat Detail">
                                                    <i class="bx bx-show text-sm"></i>
                                                </button>
                                                <button @click="editJournalEntry(entry)" 
                                                        class="text-green-600 hover:text-green-800 p-1"
                                                        title="Edit">
                                                    <i class="bx bx-edit text-sm"></i>
                                                </button>
                                                <button @click="deleteJournalEntry(entry)" 
                                                        class="text-red-600 hover:text-red-800 p-1"
                                                        title="Hapus">
                                                    <i class="bx bx-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Modal Tambah Entry Jurnal --}}
            <div x-show="showJournalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-10 z-50 overflow-y-auto">
                <div class="bg-white rounded-2xl w-full max-w-6xl my-4">
                    <div class="p-6 border-b border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-800" x-text="editingJournalEntry ? 'Edit Entri Jurnal' : 'Tambah Entri Jurnal'"></h3>
                    </div>
                    <form @submit.prevent="saveJournalEntry()">
                        <div class="p-6 space-y-4">
                            {{-- Error Validation --}}
                            <div x-show="journalFormErrors.length > 0" class="rounded-xl bg-red-50 border border-red-200 p-4">
                                <ul class="list-disc list-inside text-red-800 text-sm">
                                    <template x-for="error in journalFormErrors" :key="error">
                                        <li x-text="error"></li>
                                    </template>
                                </ul>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">No. Transaksi</label>
                                    <input type="text" x-model="journalForm.transaction_number" 
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Transaksi *</label>
                                    <input type="date" x-model="journalForm.transaction_date" 
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi *</label>
                                <input type="text" x-model="journalForm.description" 
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                    placeholder="Deskripsi transaksi..."
                                    required>
                            </div>

                            {{-- Journal Entries --}}
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-medium text-slate-700">Detail Entri *</label>
                                    <button type="button" @click="addJournalEntry()" 
                                            class="text-sm text-blue-600 hover:text-blue-800">
                                        + Tambah Baris
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <template x-for="(entry, index) in journalForm.entries" :key="index">
                                        <div class="grid grid-cols-12 gap-2 items-start p-3 border border-slate-200 rounded-lg">
                                            <div class="col-span-4">
                                                {{-- Pencarian Akun --}}
                                                <div class="relative">
                                                    <input type="text" 
                                                        x-model="entry.account_search"
                                                        @input.debounce.300ms="searchAccounts(entry, $event.target.value)"
                                                        @focus="showAccountDropdown(entry)"
                                                        @blur="setTimeout(() => hideAccountDropdown(entry), 200)"
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                                        placeholder="Cari akun..."
                                                        required>
                                                    
                                                    {{-- Dropdown Hasil Pencarian --}}
                                                    <div x-show="entry.showDropdown && entry.searchResults.length > 0" 
                                                        class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                                        <template x-for="account in entry.searchResults" :key="account.id">
                                                            <div @mousedown="selectAccount(entry, account)"
                                                                class="px-3 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-b-0">
                                                                <div class="font-mono text-sm text-slate-600" x-text="account.code"></div>
                                                                <div class="text-sm text-slate-800" x-text="account.name"></div>
                                                                <div class="text-xs text-slate-500" x-text="getTypeName(account.type)"></div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    {{-- Akun yang Terpilih --}}
                                                    <div x-show="entry.account_id && entry.selectedAccount" 
                                                        class="mt-1 p-2 bg-slate-50 rounded-lg border border-slate-200">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <div class="font-mono text-sm text-slate-600" x-text="entry.selectedAccount?.code || ''"></div>
                                                                <div class="text-sm text-slate-800" x-text="entry.selectedAccount?.name || ''"></div>
                                                            </div>
                                                            <button type="button" @click="clearAccountSelection(entry)"
                                                                    class="text-red-500 hover:text-red-700">
                                                                <i class='bx bx-x'></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-span-3">
                                                <input type="number" x-model="entry.debit" 
                                                    @input="calculateTotals()"
                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-right"
                                                    placeholder="0"
                                                    step="0.01"
                                                    min="0">
                                            </div>
                                            <div class="col-span-3">
                                                <input type="number" x-model="entry.credit" 
                                                    @input="calculateTotals()"
                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-right"
                                                    placeholder="0"
                                                    step="0.01"
                                                    min="0">
                                            </div>
                                            <div class="col-span-2">
                                                <button type="button" @click="removeJournalEntry(index)" 
                                                        class="w-full text-red-600 hover:text-red-800 p-2"
                                                        x-show="journalForm.entries.length > 2">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                            <div class="col-span-12 mt-2">
                                                <input type="text" x-model="entry.description" 
                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                                    placeholder="Deskripsi detail...">
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Total Summary --}}
                                <div class="mt-4 p-3 bg-slate-50 rounded-lg">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div class="text-right">
                                            <span class="font-medium">Total Debit:</span>
                                            <span class="ml-2 font-mono" x-text="formatCurrency(journalTotals.debit)"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-medium">Total Credit:</span>
                                            <span class="ml-2 font-mono" x-text="formatCurrency(journalTotals.credit)"></span>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <span x-show="journalTotals.debit !== journalTotals.credit" 
                                            class="text-red-600 text-sm">
                                            <i class='bx bx-error'></i> Debit dan Credit tidak balance!
                                        </span>
                                        <span x-show="journalTotals.debit === journalTotals.credit && journalTotals.debit > 0" 
                                            class="text-green-600 text-sm">
                                            <i class='bx bx-check'></i> Balance!
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                                <textarea x-model="journalForm.notes" rows="2" 
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                        placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>
                        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
                            <button type="button" @click="showJournalModal = false" 
                                    class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="savingJournal || journalTotals.debit !== journalTotals.credit || journalTotals.debit === 0"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span x-text="savingJournal ? 'Menyimpan...' : editingJournalEntry ? 'Update Entri' : 'Simpan Entri'"></span>
                                <i x-show="savingJournal" class='bx bx-loader-alt animate-spin'></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function bookDetail() {
            return {
                loading: true,
                error: null,
                bookData: null,
                bookId: {{ $id }},
                showJournalModal: false,
                savingJournal: false,
                editingJournalEntry: null,
                journalFormErrors: [],
                accounts: [],
                journalForm: {
                    book_id: null,
                    transaction_number: '',
                    transaction_date: '',
                    description: '',
                    entries: [
                        { 
                            account_id: '', 
                            account_search: '',
                            selectedAccount: null,
                            showDropdown: false,
                            searchResults: [],
                            debit: 0, 
                            credit: 0, 
                            description: '' 
                        },
                        { 
                            account_id: '', 
                            account_search: '',
                            selectedAccount: null,
                            showDropdown: false,
                            searchResults: [],
                            debit: 0, 
                            credit: 0, 
                            description: '' 
                        }
                    ],
                    notes: ''
                },
                journalTotals: {
                    debit: 0,
                    credit: 0
                },

                async init() {
                    await this.loadBook();
                },

                async loadBook() {
                    try {
                        const url = '{{ route("finance.accounting-books.show", ["id" => ":id"]) }}'.replace(':id', this.bookId);
                        const response = await fetch(url);
                        const result = await response.json();

                        if (result.success) {
                            this.bookData = result.data;
                        } else {
                            this.error = result.message;
                        }
                    } catch (error) {
                        this.error = 'Gagal memuat data buku';
                        console.error('Error:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                editBook(book) {
                    // Redirect to edit page
                    const url = '{{ route("finance.buku.index") }}';
                    window.location.href = url + `?edit=${book.id}`;
                },

                async toggleBook(id, currentStatus) {
                    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                    const action = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';

                    if (!confirm(`Apakah Anda yakin ingin ${action} buku ini?`)) {
                        return;
                    }

                    try {
                        const url = '{{ route("finance.accounting-books.toggle", ["id" => ":id"]) }}'.replace(':id', id);
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            await this.loadBook();
                            this.showNotification(result.message, 'success');
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('Gagal mengubah status buku', 'error');
                    }
                },

                getTypeName(type) {
                    if (!type) return '-';
                    const names = {
                        'general': 'Umum', 'cash': 'Kas', 'bank': 'Bank', 
                        'sales': 'Penjualan', 'purchase': 'Pembelian',
                        'inventory': 'Persediaan', 'payroll': 'Penggajian'
                    };
                    return names[type] || type;
                },

                getTypeBadgeClass(type) {
                    if (!type) return 'bg-gray-100 text-gray-800';
                    const classes = {
                        'general': 'bg-blue-100 text-blue-800',
                        'cash': 'bg-green-100 text-green-800',
                        'bank': 'bg-purple-100 text-purple-800',
                        'sales': 'bg-emerald-100 text-emerald-800',
                        'purchase': 'bg-orange-100 text-orange-800',
                        'inventory': 'bg-cyan-100 text-cyan-800',
                        'payroll': 'bg-pink-100 text-pink-800'
                    };
                    return classes[type] || 'bg-gray-100 text-gray-800';
                },

                getStatusName(status) {
                    if (!status) return '-';
                    const names = {
                        'active': 'Aktif', 'inactive': 'Nonaktif', 
                        'draft': 'Draft', 'closed': 'Ditutup'
                    };
                    return names[status] || status;
                },

                getStatusBadgeClass(status) {
                    if (!status) return 'bg-gray-100 text-gray-800';
                    const classes = {
                        'active': 'bg-green-100 text-green-800',
                        'inactive': 'bg-red-100 text-red-800',
                        'draft': 'bg-slate-100 text-slate-800',
                        'closed': 'bg-purple-100 text-purple-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },

                getEntryStatusName(status) {
                    const names = {
                        'draft': 'Draft', 'posted': 'Diposting', 'void': 'Dibatalkan'
                    };
                    return names[status] || status;
                },

                getEntryStatusBadgeClass(status) {
                    const classes = {
                        'draft': 'bg-slate-100 text-slate-800',
                        'posted': 'bg-green-100 text-green-800',
                        'void': 'bg-red-100 text-red-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                formatPeriod(startDate, endDate) {
                    if (!startDate || !endDate) return '-';
                    const start = new Date(startDate);
                    const end = new Date(endDate);
                    return start.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' }) + ' - ' + 
                        end.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
                },

                showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    const bgColor = type === 'success' ? 'bg-green-500' : 
                                type === 'error' ? 'bg-red-500' : 'bg-blue-500';
                    
                    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
                    notification.textContent = message;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.classList.add('opacity-100');
                    }, 10);

                    setTimeout(() => {
                        notification.classList.remove('opacity-100');
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                },

                async openJournalModal() {
                    if (!this.bookData) return;
                    
                    this.showJournalModal = true;
                    this.journalFormErrors = [];
                    this.resetJournalForm();
                    this.journalForm.book_id = this.bookData.id;
                    this.journalForm.transaction_date = new Date().toISOString().split('T')[0];
                    
                    // Generate transaction number
                    await this.generateTransactionNumber();
                },

                async generateTransactionNumber() {
                    try {
                        // Ini akan digenerate otomatis di backend, untuk frontend hanya placeholder
                        this.journalForm.transaction_number = 'JNL-' + this.bookData.code + '-XXXXXX';
                    } catch (error) {
                        console.error('Error generating transaction number:', error);
                    }
                },

                async loadAccounts() {
                    try {
                        const params = new URLSearchParams({
                            outlet_id: this.bookData.outlet_id,
                            status: 'active'
                        });
                        
                        const url = '{{ route("finance.chart-of-accounts.data") }}';
                        const response = await fetch(url + '?' + params);
                        const result = await response.json();
                        
                        if (result.success) {
                            this.accounts = result.data;
                        }
                    } catch (error) {
                        console.error('Error loading accounts:', error);
                    }
                },

                addJournalEntry() {
                    this.journalForm.entries.push({ 
                        account_id: '', 
                        debit: 0, 
                        credit: 0, 
                        description: '' 
                    });
                },

                removeJournalEntry(index) {
                    if (this.journalForm.entries.length > 2) {
                        this.journalForm.entries.splice(index, 1);
                        this.calculateTotals();
                    }
                },

                calculateTotals() {
                    this.journalTotals.debit = this.journalForm.entries.reduce((sum, entry) => 
                        sum + (parseFloat(entry.debit) || 0), 0);
                    this.journalTotals.credit = this.journalForm.entries.reduce((sum, entry) => 
                        sum + (parseFloat(entry.credit) || 0), 0);
                },

                async searchAccounts(entry, searchTerm) {
                    if (!searchTerm || searchTerm.length < 2) {
                        entry.searchResults = [];
                        return;
                    }

                    try {
                        const params = new URLSearchParams({
                            outlet_id: this.bookData.outlet_id,
                            status: 'active',
                            search: searchTerm
                        });
                        
                        const url = '{{ route("finance.chart-of-accounts.data") }}';
                        const response = await fetch(url + '?' + params);
                        const result = await response.json();
                        
                        if (result.success) {
                            // Filter hasil maksimal 10 item
                            entry.searchResults = result.data.slice(0, 10);
                            entry.showDropdown = true;
                        }
                    } catch (error) {
                        console.error('Error searching accounts:', error);
                        entry.searchResults = [];
                    }
                },

                // Method untuk menampilkan dropdown
                showAccountDropdown(entry) {
                    if (entry.account_search && entry.searchResults.length > 0) {
                        entry.showDropdown = true;
                    }
                },

                // Method untuk menyembunyikan dropdown
                hideAccountDropdown(entry) {
                    entry.showDropdown = false;
                },

                // Method untuk memilih akun
                selectAccount(entry, account) {
                    entry.account_id = account.id;
                    entry.selectedAccount = account;
                    entry.account_search = account.code + ' - ' + account.name;
                    entry.showDropdown = false;
                    entry.searchResults = [];
                },

                // Method untuk menghapus pilihan akun
                clearAccountSelection(entry) {
                    entry.account_id = '';
                    entry.selectedAccount = null;
                    entry.account_search = '';
                    entry.searchResults = [];
                },

                // Update method addJournalEntry
                addJournalEntry() {
                    this.journalForm.entries.push({ 
                        account_id: '', 
                        account_search: '',
                        selectedAccount: null,
                        showDropdown: false,
                        searchResults: [],
                        debit: 0, 
                        credit: 0, 
                        description: '' 
                    });
                },

                async saveJournalEntry() {
                    this.savingJournal = true;
                    this.journalFormErrors = [];

                    // Validasi: semua entri harus memiliki akun
                    const hasEmptyAccounts = this.journalForm.entries.some(entry => !entry.account_id);
                    if (hasEmptyAccounts) {
                        this.journalFormErrors = ['Semua entri harus memiliki akun yang dipilih'];
                        this.savingJournal = false;
                        return;
                    }

                    // Validasi: debit dan credit harus balance
                    if (this.journalTotals.debit !== this.journalTotals.credit) {
                        this.journalFormErrors = ['Total debit dan credit harus balance'];
                        this.savingJournal = false;
                        return;
                    }

                    // Validasi: minimal 2 entri
                    if (this.journalForm.entries.length < 2) {
                        this.journalFormErrors = ['Minimal harus ada 2 entri'];
                        this.savingJournal = false;
                        return;
                    }

                    try {
                        // Format data untuk dikirim ke backend
                        const formData = {
                            book_id: this.journalForm.book_id,
                            transaction_date: this.journalForm.transaction_date,
                            description: this.journalForm.description,
                            notes: this.journalForm.notes,
                            entries: this.journalForm.entries.map(entry => ({
                                account_id: entry.account_id,
                                debit: parseFloat(entry.debit) || 0,
                                credit: parseFloat(entry.credit) || 0,
                                description: entry.description
                            }))
                        };

                        // Tentukan URL dan method berdasarkan mode (create/edit)
                        let url, method;
                        if (this.editingJournalEntry) {
                            url = '{{ route("finance.journal-entries.update", ["id" => ":id"]) }}'.replace(':id', this.editingJournalEntry);
                            method = 'PUT';
                        } else {
                            url = '{{ route("finance.journal-entries.store") }}';
                            method = 'POST';
                        }

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showJournalModal = false;
                            const action = this.editingJournalEntry ? 'diperbarui' : 'dibuat';
                            this.showNotification(`Entri jurnal berhasil ${action}`, 'success');
                            // Reset form
                            this.resetJournalForm();
                            // Reload book data untuk update total entries dan saldo
                            await this.loadBook();
                        } else {
                            if (result.errors) {
                                this.journalFormErrors = Object.values(result.errors).flat();
                            } else {
                                this.journalFormErrors = [result.message];
                            }
                        }
                    } catch (error) {
                        console.error('Save journal error:', error);
                        this.journalFormErrors = ['Terjadi kesalahan saat menyimpan entri jurnal'];
                    } finally {
                        this.savingJournal = false;
                    }
                },

                // Method untuk reset form jurnal
                resetJournalForm() {
                    this.editingJournalEntry = null;
                    this.journalForm = {
                        book_id: this.bookData?.id || null,
                        transaction_number: '',
                        transaction_date: new Date().toISOString().split('T')[0],
                        description: '',
                        entries: [
                            { 
                                account_id: '', 
                                account_search: '',
                                selectedAccount: null,
                                showDropdown: false,
                                searchResults: [],
                                debit: 0, 
                                credit: 0, 
                                description: '' 
                            },
                            { 
                                account_id: '', 
                                account_search: '',
                                selectedAccount: null,
                                showDropdown: false,
                                searchResults: [],
                                debit: 0, 
                                credit: 0, 
                                description: '' 
                            }
                        ],
                        notes: ''
                    };
                    this.journalTotals = { debit: 0, credit: 0 };
                },

                formatDateTime(dateTimeString) {
                    if (!dateTimeString) return '-';
                    const date = new Date(dateTimeString);
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                // Method untuk view journal entry
                viewJournalEntry(entry) {
                    try {
                        const url = '{{ route("finance.journal-entries.show", ["id" => ":id"]) }}'.replace(':id', entry.id);
                        
                        fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Tampilkan modal detail atau expand view
                                this.showJournalEntryDetail(result.data);
                            } else {
                                this.showNotification(result.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('View journal entry error:', error);
                            this.showNotification('Gagal memuat detail entri jurnal', 'error');
                        });
                    } catch (error) {
                        console.error('View journal entry error:', error);
                        this.showNotification('Gagal memuat detail entri jurnal', 'error');
                    }
                },

                // Method untuk edit journal entry
                editJournalEntry(entry) {
                    try {
                        const url = '{{ route("finance.journal-entries.edit", ["id" => ":id"]) }}'.replace(':id', entry.id);
                        
                        fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Populate form dengan data untuk edit
                                this.populateEditForm(result.data);
                                this.showJournalModal = true;
                            } else {
                                this.showNotification(result.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Edit journal entry error:', error);
                            this.showNotification('Gagal memuat data untuk edit', 'error');
                        });
                    } catch (error) {
                        console.error('Edit journal entry error:', error);
                        this.showNotification('Gagal memuat data untuk edit', 'error');
                    }
                },

                // Method untuk delete journal entry
                async deleteJournalEntry(entry) {
                    if (!confirm(`Apakah Anda yakin ingin menghapus entri jurnal "${entry.transaction_number}"?`)) {
                        return;
                    }

                    try {
                        const url = '{{ route("finance.journal-entries.delete", ["id" => ":id"]) }}'.replace(':id', entry.id);
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showNotification(result.message, 'success');
                            // Reload book data
                            await this.loadBook();
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        console.error('Delete journal error:', error);
                        this.showNotification('Gagal menghapus entri jurnal', 'error');
                    }
                },

                // Method untuk menampilkan detail entri jurnal
                showJournalEntryDetail(entry) {
                    // Untuk sementara, tampilkan alert dengan detail
                    // Nanti bisa dibuat modal khusus untuk detail
                    let details = `Detail Entri Jurnal:\n\n`;
                    details += `No. Transaksi: ${entry.transaction_number}\n`;
                    details += `Tanggal: ${this.formatDate(entry.transaction_date)}\n`;
                    details += `Deskripsi: ${entry.description}\n`;
                    details += `Status: ${this.getEntryStatusName(entry.status)}\n\n`;
                    details += `Detail Akun:\n`;
                    
                    entry.journal_entry_details.forEach((detail, index) => {
                        details += `${index + 1}. ${detail.account.code} - ${detail.account.name}\n`;
                        details += `   Debit: ${this.formatCurrency(detail.debit)}\n`;
                        details += `   Kredit: ${this.formatCurrency(detail.credit)}\n`;
                        if (detail.description) {
                            details += `   Keterangan: ${detail.description}\n`;
                        }
                        details += `\n`;
                    });
                    
                    alert(details);
                },

                // Method untuk populate form edit
                populateEditForm(entry) {
                    this.editingJournalEntry = entry.id;
                    this.journalForm.transaction_date = entry.transaction_date;
                    this.journalForm.description = entry.description;
                    this.journalForm.notes = entry.notes || '';
                    
                    // Clear existing entries
                    this.journalForm.entries = [];
                    
                    // Populate entries from journal entry details
                    entry.journal_entry_details.forEach(detail => {
                        this.journalForm.entries.push({
                            account_id: detail.account_id,
                            account_search: `${detail.account.code} - ${detail.account.name}`,
                            selectedAccount: detail.account,
                            showDropdown: false,
                            searchResults: [],
                            debit: parseFloat(detail.debit) || 0,
                            credit: parseFloat(detail.credit) || 0,
                            description: detail.description || ''
                        });
                    });
                    
                    this.calculateTotals();
                },

                async closeBook(book) {
                    if (book.status !== 'active' || book.is_locked) {
                        this.showNotification('Buku harus aktif dan tidak terkunci untuk ditutup', 'error');
                        return;
                    }

                    const confirmation = confirm(`Apakah Anda yakin ingin menutup buku "${book.name}" (${book.code})?\n\nSetelah ditutup, buku tidak dapat diubah lagi dan akan terkunci secara permanen.`);
                    
                    if (!confirmation) return;

                    try {
                        const url = '{{ route("finance.accounting-books.close", ["id" => ":id"]) }}'.replace(':id', book.id);
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showNotification(result.message, 'success');
                            // Reload book data untuk update status
                            await this.loadBook();
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        console.error('Close book error:', error);
                        this.showNotification('Gagal menutup buku', 'error');
                    }
                },

                async generateBookReport(book) {
                    try {
                        // Generate laporan buku dalam format PDF
                        const url = '{{ route("finance.accounting-books.report", ["id" => ":id"]) }}'.replace(':id', book.id);
                        
                        // Buka laporan di tab baru
                        window.open(url, '_blank');
                        
                        this.showNotification('Laporan buku sedang digenerate...', 'info');
                    } catch (error) {
                        console.error('Generate report error:', error);
                        this.showNotification('Gagal generate laporan buku', 'error');
                    }
                },
            };
        }
    </script>

    <style>
        /* Custom scrollbar untuk dropdown */
        .max-h-60::-webkit-scrollbar {
            width: 6px;
        }
        
        .max-h-60::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .max-h-60::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .max-h-60::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Animation untuk dropdown */
        .absolute.z-10 {
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover effect untuk item dropdown */
        .px-3.py-2:hover\:bg-slate-50:hover {
            background-color: #f8fafc;
            transition: background-color 0.15s ease-in-out;
        }
    </style>
</x-layouts.admin>
