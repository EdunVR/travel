
<div x-show="showApprovalModal" 
     x-transition.opacity.duration.300ms
     class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
     @click.self="showApprovalModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl my-4 flex flex-col"
         x-data="approvalPermintaanApp()"
         x-init="init()"
         @modal-opened="handleModalOpened($event.detail)">
        
        
        <div class="flex items-center justify-between p-6 border-b border-slate-200 flex-shrink-0">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Setujui Permintaan Barang</h2>
                <p class="text-sm text-slate-600 mt-1" x-text="selectedItem?.nomor_permintaan || ''"></p>
            </div>
            <button @click="closeModal()" 
                    class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class='bx bx-x text-xl text-slate-500'></i>
            </button>
        </div>

        
        <div class="flex-1 overflow-y-auto">
            <form @submit.prevent="submitApproval()" class="p-6 space-y-6">
                
                
                <div class="bg-slate-50 rounded-lg p-4">
                    <h3 class="font-medium text-slate-900 mb-3">Ringkasan Permintaan</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-600">Judul:</span>
                            <span class="font-medium ml-2" x-text="selectedItem?.judul"></span>
                        </div>
                        <div>
                            <span class="text-slate-600">Outlet:</span>
                            <span class="font-medium ml-2" x-text="selectedItem?.outlet?.nama"></span>
                        </div>
                        <div>
                            <span class="text-slate-600">Pemohon:</span>
                            <span class="font-medium ml-2" x-text="selectedItem?.user?.name"></span>
                        </div>
                        <div>
                            <span class="text-slate-600">Total Budget:</span>
                            <span class="font-medium ml-2 text-primary-600" x-text="formatCurrency(selectedItem?.estimasi_budget || 0)"></span>
                        </div>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">Pilih Tindakan Setelah Persetujuan *</label>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" 
                                   x-model="form.action_type" 
                                   value="approve_only" 
                                   class="mt-1 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium text-slate-900">Setujui Saja</div>
                                <div class="text-sm text-slate-600">Hanya menyetujui permintaan tanpa tindakan lanjutan</div>
                            </div>
                        </label>
                        
                        <label x-show="hasPurchasableItems" class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" 
                                   x-model="form.action_type" 
                                   value="to_purchase_order" 
                                   class="mt-1 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium text-slate-900">Lanjutkan ke Purchase Order</div>
                                <div class="text-sm text-slate-600">Otomatis membuat PO untuk pembelian barang (khusus produk/bahan)</div>
                            </div>
                        </label>
                        
                        <div x-show="!hasPurchasableItems" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start gap-2">
                                <i class='bx bx-info-circle text-yellow-600 text-lg mt-0.5'></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium">Purchase Order tidak tersedia</p>
                                    <p>Opsi ini hanya muncul jika ada item bertipe "produk" atau "bahan"</p>
                                </div>
                            </div>
                        </div>
                        
                        <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" 
                                   x-model="form.action_type" 
                                   value="to_fixed_asset" 
                                   class="mt-1 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium text-slate-900">Lanjutkan ke Aktiva Tetap</div>
                                <div class="text-sm text-slate-600">Membuat draft aktiva tetap untuk barang yang diminta</div>
                            </div>
                        </label>
                        
                        <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" 
                                   x-model="form.action_type" 
                                   value="to_journal" 
                                   class="mt-1 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium text-slate-900">Buat Jurnal Umum</div>
                                <div class="text-sm text-slate-600">Otomatis membuat jurnal umum untuk permintaan ini</div>
                            </div>
                        </label>
                    </div>
                </div>

                
                <div x-show="form.action_type === 'to_purchase_order'">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Supplier *</label>
                    <select x-model="form.supplier_id" 
                            :required="form.action_type === 'to_purchase_order'"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Supplier</option>
                        <template x-for="supplier in suppliers" :key="supplier.id">
                            <option :value="supplier.id" x-text="supplier.nama"></option>
                        </template>
                        <option x-show="suppliers.length === 0" value="" disabled>Tidak ada supplier tersedia</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Supplier yang akan digunakan untuk pembelian barang</p>
                    <div x-show="suppliers.length === 0" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                        <i class='bx bx-info-circle mr-1'></i>
                        Tabel supplier belum tersedia. Silakan buat supplier terlebih dahulu atau pilih opsi lain.
                    </div>
                </div>

                
                <div x-show="form.action_type === 'to_fixed_asset'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Buku Akuntansi *</label>
                            <select x-model="form.book_id" 
                                    :required="form.action_type === 'to_fixed_asset'"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Buku</option>
                                <template x-for="book in books" :key="book.id">
                                    <option :value="book.id" x-text="book.nama"></option>
                                </template>
                                <option x-show="books.length === 0" value="" disabled>Tidak ada buku akuntansi tersedia</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Buku akuntansi untuk mencatat aktiva tetap (difilter berdasarkan outlet)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Aktiva Tetap *</label>
                            <select x-model="form.asset_account_id" 
                                    :required="form.action_type === 'to_fixed_asset'"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun Aktiva</option>
                                <template x-for="account in assetAccounts" :key="account.id">
                                    <option :value="account.disabled ? '' : account.id" 
                                            :disabled="account.disabled"
                                            :class="account.disabled ? 'font-semibold text-slate-600 bg-slate-100' : 'text-slate-900'"
                                            x-text="getAccountDisplayText(account)"></option>
                                </template>
                                <option x-show="assetAccounts.length === 0" value="" disabled>Tidak ada akun aktiva tersedia</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat nilai aktiva tetap (akun induk tidak dapat dipilih)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Beban Penyusutan *</label>
                            <select x-model="form.depreciation_expense_account_id" 
                                    :required="form.action_type === 'to_fixed_asset'"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun Beban Penyusutan</option>
                                <template x-for="account in expenseAccounts" :key="account.id">
                                    <option :value="account.disabled ? '' : account.id" 
                                            :disabled="account.disabled"
                                            :class="account.disabled ? 'font-semibold text-slate-600 bg-slate-100' : 'text-slate-900'"
                                            x-text="getAccountDisplayText(account)"></option>
                                </template>
                                <option x-show="expenseAccounts.length === 0" value="" disabled>Tidak ada akun beban tersedia</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat beban penyusutan aktiva tetap</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Akumulasi Penyusutan *</label>
                            <select x-model="form.accumulated_depreciation_account_id" 
                                    :required="form.action_type === 'to_fixed_asset'"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun Akumulasi Penyusutan</option>
                                <template x-for="account in accumulatedDepreciationAccounts" :key="account.id">
                                    <option :value="account.disabled ? '' : account.id" 
                                            :disabled="account.disabled"
                                            :class="account.disabled ? 'font-semibold text-slate-600 bg-slate-100' : 'text-slate-900'"
                                            x-text="getAccountDisplayText(account)"></option>
                                </template>
                                <option x-show="accumulatedDepreciationAccounts.length === 0" value="" disabled>Tidak ada akun akumulasi tersedia</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat akumulasi penyusutan aktiva tetap</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Pembayaran *</label>
                            <select x-model="form.payment_account_id" 
                                    :required="form.action_type === 'to_fixed_asset'"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun Pembayaran</option>
                                <template x-for="account in paymentAccounts" :key="account.id">
                                    <option :value="account.disabled ? '' : account.id" 
                                            :disabled="account.disabled"
                                            :class="account.disabled ? 'font-semibold text-slate-600 bg-slate-100' : 'text-slate-900'"
                                            x-text="getAccountDisplayText(account)"></option>
                                </template>
                                <option x-show="paymentAccounts.length === 0" value="" disabled>Tidak ada akun pembayaran tersedia</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun kas/bank untuk pembayaran aktiva tetap</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Aktiva Tetap *</label>
                            <input type="text" 
                                   x-model="form.asset_name" 
                                   :required="form.action_type === 'to_fixed_asset'"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Masukkan nama aktiva tetap">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Kategori Aktiva</label>
                            <select x-model="form.asset_category" 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Kategori</option>
                                <option value="building">Bangunan</option>
                                <option value="equipment">Peralatan</option>
                                <option value="vehicle">Kendaraan</option>
                                <option value="furniture">Furniture</option>
                                <option value="computer">Komputer & IT</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Lokasi</label>
                            <input type="text" 
                                   x-model="form.asset_location" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Lokasi penempatan aktiva">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Perolehan *</label>
                            <input type="date" 
                                   x-model="form.acquisition_date" 
                                   :required="form.action_type === 'to_fixed_asset'"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Harga Perolehan *</label>
                            <input type="number" 
                                   x-model="form.acquisition_cost" 
                                   :required="form.action_type === 'to_fixed_asset'"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="0.00">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nilai Sisa</label>
                                <input type="number" 
                                       x-model="form.salvage_value" 
                                       step="0.01"
                                       min="0"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Masa Manfaat (Tahun) *</label>
                                <input type="number" 
                                       x-model="form.useful_life" 
                                       :required="form.action_type === 'to_fixed_asset'"
                                       min="1"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="5">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Metode Penyusutan</label>
                            <select x-model="form.depreciation_method" 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="straight_line">Garis Lurus</option>
                                <option value="declining_balance">Saldo Menurun</option>
                                <option value="double_declining">Saldo Menurun Ganda</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                            <textarea x-model="form.asset_description" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Deskripsi tambahan untuk aktiva tetap"></textarea>
                        </div>
                    </div>
                </div>

                
                <div x-show="form.action_type === 'to_journal'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Buku Akuntansi *</label>
                            <select x-model="form.journal_book_id" 
                                    :required="form.action_type === 'to_journal'"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Buku</option>
                                <template x-for="book in books" :key="book.id">
                                    <option :value="book.id" x-text="book.nama"></option>
                                </template>
                                <option x-show="books.length === 0" value="" disabled>Tidak ada buku akuntansi tersedia</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Buku akuntansi untuk mencatat jurnal umum (difilter berdasarkan outlet)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Jurnal *</label>
                            <input type="date" 
                                   x-model="form.journal_date" 
                                   :required="form.action_type === 'to_journal'"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan Jurnal *</label>
                            <textarea x-model="form.journal_description" 
                                      :required="form.action_type === 'to_journal'"
                                      rows="2"
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Keterangan untuk jurnal umum"></textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-slate-700">Entri Jurnal *</label>
                                <button type="button" 
                                        @click="addJournalEntry()"
                                        class="px-3 py-1 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                                    <i class='bx bx-plus mr-1'></i>
                                    Tambah Baris
                                </button>
                            </div>

                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <div class="grid grid-cols-12 gap-2 text-xs font-medium text-slate-600">
                                        <div class="col-span-5">Akun</div>
                                        <div class="col-span-2">Debit</div>
                                        <div class="col-span-2">Kredit</div>
                                        <div class="col-span-2">Keterangan</div>
                                        <div class="col-span-1">Aksi</div>
                                    </div>
                                </div>

                                <div class="max-h-60 overflow-y-auto">
                                    <template x-for="(entry, index) in form.journal_entries" :key="index">
                                        <div class="px-4 py-3 border-b border-slate-100 last:border-b-0">
                                            <div class="grid grid-cols-12 gap-2 items-start">
                                                <!-- Account Selection -->
                                                <div class="col-span-5">
                                                    <select x-model="entry.account_id" 
                                                            :required="form.action_type === 'to_journal'"
                                                            class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                                                        <option value="">Pilih Akun</option>
                                                        <template x-for="account in journalAccounts" :key="account.id">
                                                            <option :value="account.disabled ? '' : account.id" 
                                                                    :disabled="account.disabled"
                                                                    :class="account.disabled ? 'font-semibold text-slate-600 bg-slate-100' : 'text-slate-900'"
                                                                    x-text="getAccountDisplayText(account)"></option>
                                                        </template>
                                                    </select>
                                                </div>

                                                <!-- Debit Amount -->
                                                <div class="col-span-2">
                                                    <input type="number" 
                                                           x-model="entry.debit" 
                                                           @input="calculateJournalTotals(); entry.credit = entry.debit > 0 ? '' : entry.credit"
                                                           step="0.01"
                                                           min="0"
                                                           class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                                                           placeholder="0.00">
                                                </div>

                                                <!-- Credit Amount -->
                                                <div class="col-span-2">
                                                    <input type="number" 
                                                           x-model="entry.credit" 
                                                           @input="calculateJournalTotals(); entry.debit = entry.credit > 0 ? '' : entry.debit"
                                                           step="0.01"
                                                           min="0"
                                                           class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                                                           placeholder="0.00">
                                                </div>

                                                <!-- Description -->
                                                <div class="col-span-2">
                                                    <input type="text" 
                                                           x-model="entry.description" 
                                                           class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                                                           placeholder="Keterangan">
                                                </div>

                                                <!-- Actions -->
                                                <div class="col-span-1">
                                                    <button type="button" 
                                                            @click="removeJournalEntry(index)"
                                                            x-show="form.journal_entries.length > 1"
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                        <i class='bx bx-trash text-sm'></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Totals -->
                                <div class="bg-slate-50 px-4 py-2 border-t border-slate-200">
                                    <div class="grid grid-cols-12 gap-2 text-sm font-medium">
                                        <div class="col-span-5 text-slate-700">Total:</div>
                                        <div class="col-span-2 text-slate-900" x-text="formatCurrency(journalTotals.debit)"></div>
                                        <div class="col-span-2 text-slate-900" x-text="formatCurrency(journalTotals.credit)"></div>
                                        <div class="col-span-3">
                                            <span x-show="journalTotals.debit !== journalTotals.credit" 
                                                  class="text-red-600 text-xs">
                                                Tidak Balance!
                                            </span>
                                            <span x-show="journalTotals.debit === journalTotals.credit && journalTotals.debit > 0" 
                                                  class="text-green-600 text-xs">
                                                Balance ✓
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div x-show="form.action_type === 'to_fixed_asset'" style="display: none;">
                    <!-- This section is now integrated above -->
                </div>

                
                <div x-show="form.action_type === 'to_purchase_order'">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class='bx bx-info-circle text-blue-600 text-lg mt-0.5'></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Informasi Purchase Order</p>
                                <p>Setelah persetujuan, sistem akan otomatis membuat Purchase Order dengan status "Permintaan Pembelian" yang dapat diproses lebih lanjut di modul Pembelian.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="form.action_type === 'to_fixed_asset'">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class='bx bx-info-circle text-green-600 text-lg mt-0.5'></i>
                            <div class="text-sm text-green-800">
                                <p class="font-medium mb-1">Informasi Aktiva Tetap</p>
                                <p>Setelah persetujuan, sistem akan membuat draft aktiva tetap yang dapat dilengkapi dan diaktifkan di modul Keuangan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="form.action_type === 'to_journal'">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class='bx bx-info-circle text-purple-600 text-lg mt-0.5'></i>
                            <div class="text-sm text-purple-800">
                                <p class="font-medium mb-1">Informasi Jurnal Umum</p>
                                <p>Setelah persetujuan, sistem akan otomatis membuat jurnal umum berdasarkan entri yang Anda buat. Pastikan jurnal balance (total debit = total kredit).</p>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Persetujuan</label>
                    <textarea x-model="form.catatan_approval" 
                              rows="3"
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Tambahkan catatan atau instruksi khusus (opsional)"></textarea>
                </div>
            </form>
        </div>

        
        <div class="flex items-center justify-between p-6 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button type="button" 
                    @click="closeModal()" 
                    class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Batal
            </button>
            
            <button type="button" 
                    @click="submitApproval()" 
                    :disabled="submitting || !form.action_type"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!submitting">Setujui Permintaan</span>
                <span x-show="submitting">Memproses...</span>
            </button>
        </div>
    </div>
</div>

<script>
function approvalPermintaanApp() {
    return {
        selectedItem: null,
        form: {
            action_type: 'approve_only',
            supplier_id: '',
            book_id: '',
            catatan_approval: '',
            // Fixed Asset fields
            asset_name: '',
            asset_category: '',
            asset_location: '',
            acquisition_date: '',
            acquisition_cost: '',
            salvage_value: '',
            useful_life: 5,
            depreciation_method: 'straight_line',
            asset_description: '',
            asset_account_id: '',
            depreciation_expense_account_id: '',
            accumulated_depreciation_account_id: '',
            payment_account_id: '',
            // Journal fields
            journal_book_id: '',
            journal_date: '',
            journal_description: '',
            journal_entries: [
                {
                    account_id: '',
                    debit: '',
                    credit: '',
                    description: ''
                }
            ]
        },
        suppliers: [],
        books: [],
        assetAccounts: [],
        expenseAccounts: [],
        accumulatedDepreciationAccounts: [],
        paymentAccounts: [],
        journalAccounts: [],
        journalTotals: {
            debit: 0,
            credit: 0
        },
        submitting: false,
        hasPurchasableItems: false,

        init() {
            // Watch for modal opening using Alpine store
            this.$watch('$store.permintaanBarang.showApprovalModal', (isOpen) => {
                if (isOpen && this.$store.permintaanBarang.selectedItem) {
                    this.handleModalOpened(this.$store.permintaanBarang.selectedItem);
                }
            });
        },

        async initModal() {
            await this.loadSuppliers();
            await this.loadBooks();
        },

        async handleModalOpened(selectedItem) {
            console.log('Approval modal opened with item:', selectedItem);
            this.selectedItem = selectedItem;
            
            // Check if items contain purchasable types (produk or bahan)
            this.checkPurchasableItems();
            
            // Load data when modal opens
            await this.loadSuppliers();
            await this.loadBooks();
            await this.loadAssetAccounts();
            await this.loadExpenseAccounts();
            await this.loadAccumulatedDepreciationAccounts();
            await this.loadPaymentAccounts();
            await this.loadJournalAccounts();
            
            // Initialize journal date to today
            this.form.journal_date = new Date().toISOString().split('T')[0];
        },

        checkPurchasableItems() {
            this.hasPurchasableItems = false;
            
            if (this.selectedItem && this.selectedItem.items) {
                this.hasPurchasableItems = this.selectedItem.items.some(item => 
                    item.tipe_item === 'produk' || item.tipe_item === 'bahan'
                );
            }
            
            console.log('Has purchasable items:', this.hasPurchasableItems);
        },

        closeModal() {
            // Use $dispatch to communicate with parent
            this.$dispatch('close-approval-modal');
        },

        async loadSuppliers() {
            try {
                // Get outlet_id from selected item
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.suppliers")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.suppliers = result.data || result; // Handle different response formats
                } else {
                    console.warn('Suppliers API returned error:', response.status);
                    this.suppliers = [];
                }
            } catch (error) {
                console.error('Error loading suppliers:', error);
                // Use empty array if API fails
                this.suppliers = [];
            }
        },

        async loadBooks() {
            try {
                // Get outlet_id from selected item
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.books")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.books = result.data || result; // Handle different response formats
                } else {
                    console.warn('Books API returned error:', response.status);
                    this.books = [];
                }
            } catch (error) {
                console.error('Error loading books:', error);
                // Use empty array if API fails
                this.books = [];
            }
        },

        async loadAssetAccounts() {
            try {
                // Get outlet_id from selected item
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.asset-accounts")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.assetAccounts = result.data || result; // Handle different response formats
                } else {
                    console.warn('Asset accounts API returned error:', response.status);
                    this.assetAccounts = [];
                }
            } catch (error) {
                console.error('Error loading asset accounts:', error);
                // Use empty array if API fails
                this.assetAccounts = [];
            }
        },

        async loadExpenseAccounts() {
            try {
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.expense-accounts")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.expenseAccounts = result.data || result;
                } else {
                    console.warn('Expense accounts API returned error:', response.status);
                    this.expenseAccounts = [];
                }
            } catch (error) {
                console.error('Error loading expense accounts:', error);
                this.expenseAccounts = [];
            }
        },

        async loadAccumulatedDepreciationAccounts() {
            try {
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.accumulated-depreciation-accounts")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.accumulatedDepreciationAccounts = result.data || result;
                } else {
                    console.warn('Accumulated depreciation accounts API returned error:', response.status);
                    this.accumulatedDepreciationAccounts = [];
                }
            } catch (error) {
                console.error('Error loading accumulated depreciation accounts:', error);
                this.accumulatedDepreciationAccounts = [];
            }
        },

        async loadPaymentAccounts() {
            try {
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.payment-accounts")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.paymentAccounts = result.data || result;
                } else {
                    console.warn('Payment accounts API returned error:', response.status);
                    this.paymentAccounts = [];
                }
            } catch (error) {
                console.error('Error loading payment accounts:', error);
                this.paymentAccounts = [];
            }
        },

        async loadJournalAccounts() {
            try {
                const outletId = this.selectedItem?.outlet_id || this.selectedItem?.outlet?.id;
                const url = new URL('<?php echo e(route("admin.supply-chain.permintaan-barang.journal-accounts")); ?>');
                
                if (outletId) {
                    url.searchParams.append('outlet_id', outletId);
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const result = await response.json();
                    this.journalAccounts = result.data || result;
                } else {
                    console.warn('Journal accounts API returned error:', response.status);
                    this.journalAccounts = [];
                }
            } catch (error) {
                console.error('Error loading journal accounts:', error);
                this.journalAccounts = [];
            }
        },

        addJournalEntry() {
            this.form.journal_entries.push({
                account_id: '',
                debit: '',
                credit: '',
                description: ''
            });
        },

        removeJournalEntry(index) {
            if (this.form.journal_entries.length > 1) {
                this.form.journal_entries.splice(index, 1);
                this.calculateJournalTotals();
            }
        },

        calculateJournalTotals() {
            this.journalTotals.debit = this.form.journal_entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.debit) || 0);
            }, 0);
            
            this.journalTotals.credit = this.form.journal_entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.credit) || 0);
            }, 0);
        },

        async submitApproval() {
            if (this.submitting) return;
            
            console.log('Submit approval called with selectedItem:', this.selectedItem);
            
            // Check if selectedItem exists and has ID
            if (!this.selectedItem || !this.selectedItem.id) {
                console.error('No selected item or item ID found:', this.selectedItem);
                alert('Error: Data permintaan tidak ditemukan. Silakan tutup modal dan coba lagi.');
                return;
            }
            
            // Validation
            if (!this.form.action_type) {
                alert('Mohon pilih tindakan setelah persetujuan');
                return;
            }

            if (this.form.action_type === 'to_purchase_order' && (!this.form.supplier_id || this.suppliers.length === 0)) {
                if (this.suppliers.length === 0) {
                    alert('Tidak ada supplier tersedia. Silakan pilih opsi lain atau buat supplier terlebih dahulu.');
                } else {
                    alert('Mohon pilih supplier untuk Purchase Order');
                }
                return;
            }

            if (this.form.action_type === 'to_fixed_asset' && (!this.form.book_id || this.books.length === 0)) {
                if (this.books.length === 0) {
                    alert('Tidak ada buku akuntansi tersedia. Silakan pilih opsi lain atau buat buku akuntansi terlebih dahulu.');
                } else {
                    alert('Mohon pilih buku akuntansi untuk Aktiva Tetap');
                }
                return;
            }

            if (this.form.action_type === 'to_fixed_asset') {
                if (!this.form.asset_name) {
                    alert('Mohon isi nama aktiva tetap');
                    return;
                }
                if (!this.form.asset_account_id) {
                    alert('Mohon pilih akun aktiva tetap');
                    return;
                }
                if (!this.form.depreciation_expense_account_id) {
                    alert('Mohon pilih akun beban penyusutan');
                    return;
                }
                if (!this.form.accumulated_depreciation_account_id) {
                    alert('Mohon pilih akun akumulasi penyusutan');
                    return;
                }
                if (!this.form.payment_account_id) {
                    alert('Mohon pilih akun pembayaran');
                    return;
                }
                if (!this.form.acquisition_date) {
                    alert('Mohon isi tanggal perolehan');
                    return;
                }
                if (!this.form.acquisition_cost || this.form.acquisition_cost <= 0) {
                    alert('Mohon isi harga perolehan yang valid');
                    return;
                }
                if (!this.form.useful_life || this.form.useful_life <= 0) {
                    alert('Mohon isi masa manfaat yang valid');
                    return;
                }
            }

            if (this.form.action_type === 'to_journal') {
                if (!this.form.journal_book_id) {
                    alert('Mohon pilih buku akuntansi untuk jurnal');
                    return;
                }
                if (!this.form.journal_date) {
                    alert('Mohon isi tanggal jurnal');
                    return;
                }
                if (!this.form.journal_description) {
                    alert('Mohon isi keterangan jurnal');
                    return;
                }
                if (this.form.journal_entries.length === 0) {
                    alert('Mohon tambahkan minimal satu entri jurnal');
                    return;
                }
                
                // Validate journal entries
                let hasValidEntry = false;
                for (let entry of this.form.journal_entries) {
                    if (!entry.account_id) {
                        alert('Mohon pilih akun untuk semua entri jurnal');
                        return;
                    }
                    if (!entry.debit && !entry.credit) {
                        alert('Mohon isi debit atau kredit untuk semua entri jurnal');
                        return;
                    }
                    if (entry.debit && entry.credit) {
                        alert('Entri jurnal tidak boleh memiliki debit dan kredit sekaligus');
                        return;
                    }
                    hasValidEntry = true;
                }
                
                if (!hasValidEntry) {
                    alert('Mohon tambahkan minimal satu entri jurnal yang valid');
                    return;
                }
                
                // Check if journal is balanced
                this.calculateJournalTotals();
                if (this.journalTotals.debit !== this.journalTotals.credit) {
                    alert('Jurnal tidak balance! Total debit harus sama dengan total kredit');
                    return;
                }
                
                if (this.journalTotals.debit === 0) {
                    alert('Total jurnal tidak boleh nol');
                    return;
                }
            }

            this.submitting = true;
            
            try {
                // Prepare form data - only send required fields based on action_type
                const formData = {
                    action_type: this.form.action_type,
                    catatan_approval: this.form.catatan_approval
                };

                // Add conditional fields based on action_type
                if (this.form.action_type === 'to_purchase_order') {
                    formData.supplier_id = this.form.supplier_id;
                }

                if (this.form.action_type === 'to_fixed_asset') {
                    formData.book_id = this.form.book_id;
                    formData.asset_name = this.form.asset_name;
                    formData.asset_category = this.form.asset_category;
                    formData.asset_location = this.form.asset_location;
                    formData.acquisition_date = this.form.acquisition_date;
                    formData.acquisition_cost = this.form.acquisition_cost;
                    formData.salvage_value = this.form.salvage_value;
                    formData.useful_life = this.form.useful_life;
                    formData.depreciation_method = this.form.depreciation_method;
                    formData.asset_description = this.form.asset_description;
                    formData.asset_account_id = this.form.asset_account_id;
                    formData.depreciation_expense_account_id = this.form.depreciation_expense_account_id;
                    formData.accumulated_depreciation_account_id = this.form.accumulated_depreciation_account_id;
                    formData.payment_account_id = this.form.payment_account_id;
                }

                if (this.form.action_type === 'to_journal') {
                    formData.book_id = this.form.journal_book_id;
                    formData.journal_date = this.form.journal_date;
                    formData.journal_description = this.form.journal_description;
                    formData.journal_entries = this.form.journal_entries;
                }

                console.log('Sending form data:', formData);

                const response = await fetch(`<?php echo e(route('admin.supply-chain.permintaan-barang.approve', ':id')); ?>`.replace(':id', this.selectedItem.id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('Error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    this.$dispatch('close-approval-modal');
                    this.$dispatch('refresh-data');
                    this.$dispatch('show-notification', { message: result.message, type: 'success' });
                    
                    // Handle redirect for journal entry
                    if (this.form.action_type === 'to_journal' && result.data.redirect_url) {
                        if (confirm('Permintaan berhasil disetujui. Apakah Anda ingin langsung ke halaman jurnal?')) {
                            window.location.href = result.data.redirect_url;
                        }
                    }
                    
                    // Reset form
                    this.form = {
                        action_type: 'approve_only',
                        supplier_id: '',
                        book_id: '',
                        catatan_approval: '',
                        // Fixed Asset fields
                        asset_name: '',
                        asset_category: '',
                        asset_location: '',
                        acquisition_date: '',
                        acquisition_cost: '',
                        salvage_value: '',
                        useful_life: 5,
                        depreciation_method: 'straight_line',
                        asset_description: '',
                        asset_account_id: '',
                        depreciation_expense_account_id: '',
                        accumulated_depreciation_account_id: '',
                        payment_account_id: '',
                        // Journal fields
                        journal_book_id: '',
                        journal_date: new Date().toISOString().split('T')[0],
                        journal_description: '',
                        journal_entries: [
                            {
                                account_id: '',
                                debit: '',
                                credit: '',
                                description: ''
                            }
                        ]
                    };
                    
                    // Reset journal totals
                    this.journalTotals = {
                        debit: 0,
                        credit: 0
                    };
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error submitting approval:', error);
                alert('Terjadi kesalahan saat memproses persetujuan: ' + error.message);
            } finally {
                this.submitting = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        getAccountDisplayText(account) {
            // Add indentation for child accounts
            const indent = account.level > 0 ? '    '.repeat(account.level) : '';
            const prefix = account.is_parent ? '📁 ' : '📄 ';
            return `${indent}${prefix}${account.code} - ${account.name}`;
        }
    }
}
</script><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\supply-chain\permintaan-barang\modals\approval.blade.php ENDPATH**/ ?>