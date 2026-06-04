<!-- COA Settings Modal -->
<div id="coa-settings-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Setting COA Penjualan Antar Outlet</h2>
                <p class="text-slate-600 text-sm mt-1">Konfigurasi akun untuk pencatatan transaksi penjualan antar outlet</p>
            </div>
            <button onclick="closeCoaModal()" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bx bx-x text-xl text-slate-500"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
            <!-- Loading State -->
            <div id="coa-modal-loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                <p class="mt-2 text-slate-600">Memuat data...</p>
            </div>

            <!-- Form Content -->
            <div id="coa-modal-content" class="hidden">
                <form id="coa-settings-form">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Outlet Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
                        <select name="outlet_id" id="coa-outlet-select" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Outlet</option>
                        </select>
                    </div>

                    <!-- Accounting Book -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Buku Akuntansi <span class="text-red-500">*</span></label>
                        <select name="accounting_book_id" id="coa-accounting-book" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Buku Akuntansi</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Akun Piutang Antar Outlet -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Piutang Antar Outlet <span class="text-red-500">*</span></label>
                            <select name="akun_piutang_antar_outlet" id="coa-piutang" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat piutang dari outlet tujuan</p>
                        </div>

                        <!-- Akun Pendapatan Antar Outlet -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Pendapatan Antar Outlet <span class="text-red-500">*</span></label>
                            <select name="akun_pendapatan_antar_outlet" id="coa-pendapatan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat pendapatan dari penjualan antar outlet</p>
                        </div>

                        <!-- Akun HPP -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun HPP (Harga Pokok Penjualan)</label>
                            <select name="akun_hpp" id="coa-hpp" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun (Opsional)</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat harga pokok penjualan</p>
                        </div>

                        <!-- Akun Persediaan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun Persediaan</label>
                            <select name="akun_persediaan" id="coa-persediaan" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun (Opsional)</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat pengurangan persediaan</p>
                        </div>

                        <!-- Akun PPN -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Akun PPN</label>
                            <select name="akun_ppn" id="coa-ppn" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Akun (Opsional)</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat PPN keluaran</p>
                        </div>
                    </div>

                    <!-- Information Panel -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="bx bx-info-circle text-blue-600 text-lg mt-0.5"></i>
                            <div>
                                <h4 class="font-medium text-blue-900 mb-2">Informasi Jurnal Otomatis</h4>
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p><strong>Jurnal yang akan dibuat:</strong></p>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>Piutang Antar Outlet (Debit) - Pendapatan Antar Outlet (Kredit)</li>
                                        <li>HPP (Debit) - Persediaan (Kredit) <em>(jika diatur)</em></li>
                                        <li>PPN Keluaran (Kredit) <em>(jika ada PPN)</em></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200">
            <button type="button" onclick="closeCoaModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                Batal
            </button>
            <button type="button" onclick="resetCoaForm()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors">
                Reset
            </button>
            <button type="button" onclick="saveCoaSettings()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="submit-text">Simpan Pengaturan</span>
                <span class="submit-loading hidden">
                    <i class="bx bx-loader-alt animate-spin mr-2"></i>Menyimpan...
                </span>
            </button>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\inter-outlet\coa-settings.blade.php ENDPATH**/ ?>