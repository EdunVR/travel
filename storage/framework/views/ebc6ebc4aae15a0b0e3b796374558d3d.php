<!-- Modal HPP Management -->
<div x-show="showHppModal" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
  <div x-on:click.outside="!showEditHppModal && closeHppModal()" class="w-full max-w-4xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-lg">Kelola HPP & Stok</h3>
        <p class="text-sm text-slate-600" x-text="'Produk: ' + (selectedProduct?.name || '')"></p>
      </div>
      <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeHppModal()">
        <i class='bx bx-x text-xl'></i>
      </button>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto">
      <!-- Product Info -->
      <div class="px-5 py-4 bg-slate-50 border-b border-slate-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
          <div>
            <span class="text-slate-500">SKU:</span>
            <span class="font-mono ml-2" x-text="selectedProduct?.sku"></span>
          </div>
          <div>
            <span class="text-slate-500">Outlet:</span>
            <span class="ml-2" x-text="selectedProduct?.outlet"></span>
          </div>
          <div>
            <span class="text-slate-500">Stok Saat Ini:</span>
            <span class="ml-2 font-semibold" x-text="selectedProduct?.stock || 0"></span>
          </div>
          <div>
            <span class="text-slate-500">Harga Jual:</span>
            <span class="ml-2 font-semibold text-primary-700" x-text="formatCurrency(selectedProduct?.price || 0)"></span>
          </div>
        </div>
      </div>

      <!-- HPP History Table -->
      <div class="px-5 py-4">
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-semibold">Riwayat HPP & Stok</h4>
          <button x-on:click="openAddHppStock()" 
                  class="inline-flex items-center gap-2 rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
            <i class='bx bx-plus'></i> Tambah Stok
          </button>
        </div>

        <!-- Loading State -->
        <div x-show="loadingHppData" class="text-center py-8">
          <div class="inline-flex items-center gap-2 text-slate-600">
            <i class='bx bx-loader-alt bx-spin text-xl'></i>
            <span>Memuat data HPP...</span>
          </div>
        </div>

        <!-- HPP Table -->
        <div x-show="!loadingHppData" class="rounded-xl border border-slate-200 overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-700">
              <tr>
                <th class="text-left px-4 py-3">Tanggal</th>
                <th class="text-left px-4 py-3">Jenis</th>
                <th class="text-right px-4 py-3">Jumlah</th>
                <th class="text-right px-4 py-3">HPP/Unit</th>
                <th class="text-right px-4 py-3">Total Nilai</th>
                <th class="text-right px-4 py-3">Stok Akhir</th>
                <th class="text-center px-4 py-3 w-24">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="hpp in hppData" :key="hpp.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="formatDate(hpp.created_at)"></td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs"
                          :class="hpp.type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                      <i :class="hpp.type === 'in' ? 'bx bx-plus' : 'bx bx-minus'"></i>
                      <span x-text="hpp.type === 'in' ? 'Masuk' : 'Keluar'"></span>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right font-mono" x-text="hpp.quantity"></td>
                  <td class="px-4 py-3 text-right font-mono" x-text="formatCurrency(hpp.hpp_per_unit)"></td>
                  <td class="px-4 py-3 text-right font-mono font-semibold" x-text="formatCurrency(hpp.total_value)"></td>
                  <td class="px-4 py-3 text-right font-mono font-semibold" x-text="hpp.stock_after"></td>
                  <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                      <button x-on:click="openEditHpp(hpp)" 
                              class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50"
                              title="Edit">
                        <i class='bx bx-edit text-sm'></i>
                      </button>
                      <button x-on:click="confirmDeleteHpp(hpp)" 
                              class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                              title="Hapus">
                        <i class='bx bx-trash text-sm'></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
              <tr x-show="hppData.length === 0">
                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                  Belum ada data HPP. Klik "Tambah Stok" untuk menambah data pertama.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Summary -->
        <div x-show="hppData.length > 0" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Total Stok Masuk</div>
            <div class="text-lg font-semibold text-green-700" x-text="hppSummary.total_in"></div>
          </div>
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Total Stok Keluar</div>
            <div class="text-lg font-semibold text-red-700" x-text="hppSummary.total_out"></div>
          </div>
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">HPP Rata-rata</div>
            <div class="text-lg font-semibold text-primary-700" x-text="formatCurrency(hppSummary.avg_hpp)"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
      <button class="rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50" 
              x-on:click="closeHppModal()">
        Tutup
      </button>
    </div>
  </div>
</div>

<!-- Modal Add HPP Stock -->
<div x-show="showAddHppModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
  <div x-on:click.outside="closeAddHppModal()" class="w-full max-w-md bg-white rounded-2xl shadow-float">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="font-semibold">Tambah Stok & HPP</div>
      <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeAddHppModal()">
        <i class='bx bx-x text-xl'></i>
      </button>
    </div>

    <!-- Body -->
    <div class="px-5 py-4 space-y-4">
      <div class="text-sm text-slate-600">
        Produk: <span class="font-medium" x-text="selectedProduct?.name"></span>
      </div>
      
      <div>
        <label class="text-sm text-slate-600">Jenis Transaksi <span class="text-red-500">*</span></label>
        <select x-model="hppForm.type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2">
          <option value="in">Stok Masuk (Pembelian/Produksi)</option>
          <option value="out">Stok Keluar (Penjualan/Penggunaan)</option>
        </select>
        <div x-show="hppErrors.type" class="text-red-500 text-xs mt-1" x-text="hppErrors.type"></div>
      </div>

      <div>
        <label class="text-sm text-slate-600">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" min="1" x-model.number="hppForm.quantity" 
               class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" 
               placeholder="Masukkan jumlah">
        <div x-show="hppErrors.quantity" class="text-red-500 text-xs mt-1" x-text="hppErrors.quantity"></div>
      </div>

      <div x-show="hppForm.type === 'in'">
        <label class="text-sm text-slate-600">HPP per Unit <span class="text-red-500">*</span></label>
        <input type="number" min="0" step="0.01" x-model.number="hppForm.hpp_per_unit" 
               class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" 
               placeholder="Masukkan HPP per unit">
        <div class="text-xs text-slate-500 mt-1">Harga beli atau biaya produksi per unit</div>
        <div x-show="hppErrors.hpp_per_unit" class="text-red-500 text-xs mt-1" x-text="hppErrors.hpp_per_unit"></div>
      </div>

      <div>
        <label class="text-sm text-slate-600">Keterangan</label>
        <textarea x-model="hppForm.notes" rows="2" 
                  class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" 
                  placeholder="Keterangan tambahan (opsional)"></textarea>
      </div>

      <div class="bg-slate-50 rounded-lg p-3">
        <div class="text-sm text-slate-600">Ringkasan:</div>
        <div class="mt-1 space-y-1 text-sm">
          <div class="flex justify-between">
            <span>Jenis:</span>
            <span x-text="hppForm.type === 'in' ? 'Stok Masuk' : 'Stok Keluar'"></span>
          </div>
          <div class="flex justify-between">
            <span>Jumlah:</span>
            <span x-text="hppForm.quantity || 0"></span>
          </div>
          <div x-show="hppForm.type === 'in'" class="flex justify-between">
            <span>HPP per unit:</span>
            <span x-text="formatCurrency(hppForm.hpp_per_unit || 0)"></span>
          </div>
          <div x-show="hppForm.type === 'in'" class="flex justify-between font-medium border-t border-slate-200 pt-1">
            <span>Total Nilai:</span>
            <span x-text="formatCurrency((hppForm.quantity || 0) * (hppForm.hpp_per_unit || 0))"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-5 py-4 border-t border-slate-100 flex gap-2 justify-end">
      <button class="rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50" 
              x-on:click="closeAddHppModal()">
        Batal
      </button>
      <button class="rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700" 
              x-on:click="submitHppForm()" 
              :disabled="savingHpp"
              :class="savingHpp ? 'opacity-50 cursor-not-allowed' : ''">
        <span x-show="!savingHpp">Simpan</span>
        <span x-show="savingHpp" class="flex items-center gap-2">
          <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
        </span>
      </button>
    </div>
  </div>
</div>

<!-- Modal Confirm Delete HPP -->
<div x-show="hppToDelete" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
  <div x-on:click.outside="hppToDelete = null" class="w-full max-w-md bg-white rounded-2xl shadow-float">
    <div class="px-5 py-4">
      <div class="font-semibold">Hapus Data HPP?</div>
      <p class="text-slate-600 mt-1">Data HPP akan dihapus secara permanen dan stok akan disesuaikan.</p>
      <div x-show="hppToDelete" class="mt-3 p-3 rounded-lg bg-slate-50 border border-slate-200">
        <div class="text-sm">
          <div class="flex justify-between">
            <span>Tanggal:</span>
            <span x-text="hppToDelete ? formatDate(hppToDelete.created_at) : ''"></span>
          </div>
          <div class="flex justify-between">
            <span>Jenis:</span>
            <span x-text="hppToDelete ? (hppToDelete.type === 'in' ? 'Stok Masuk' : 'Stok Keluar') : ''"></span>
          </div>
          <div class="flex justify-between">
            <span>Jumlah:</span>
            <span x-text="hppToDelete ? hppToDelete.quantity : ''"></span>
          </div>
        </div>
      </div>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
      <button class="rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50" 
              x-on:click="hppToDelete = null">
        Batal
      </button>
      <button x-on:click="deleteHppNow()" 
              :disabled="deletingHpp" 
              class="rounded-lg bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
        <span x-show="!deletingHpp">Hapus</span>
        <span x-show="deletingHpp" class="flex items-center gap-2">
          <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
        </span>
      </button>
    </div>
  </div>
</div>

<!-- Modal Edit HPP -->
<div x-show="showEditHppModal" x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 p-3">
  <div x-on:click.outside="closeEditHppModal()" class="w-full max-w-md bg-white rounded-2xl shadow-float">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="font-semibold">Edit Data HPP</div>
      <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeEditHppModal()">
        <i class='bx bx-x text-xl'></i>
      </button>
    </div>

    <!-- Body -->
    <div class="px-5 py-4 space-y-4">
      <div class="text-sm text-slate-600">
        Produk: <span class="font-medium" x-text="selectedProduct?.name"></span>
      </div>
      
      <!-- Display general errors -->
      <div x-show="editHppErrors.general" class="p-3 rounded-lg bg-red-50 border border-red-200">
        <div class="text-red-700 text-sm" x-text="editHppErrors.general"></div>
      </div>
      
      <div>
        <label class="text-sm text-slate-600">Jenis Transaksi <span class="text-red-500">*</span></label>
        <select x-model="editHppForm.type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2">
          <option value="in">Stok Masuk (Pembelian/Produksi)</option>
          <option value="out">Stok Keluar (Penjualan/Penggunaan)</option>
        </select>
        <div x-show="editHppErrors.type" class="text-red-500 text-xs mt-1" x-text="editHppErrors.type"></div>
      </div>

      <div>
        <label class="text-sm text-slate-600">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" min="1" x-model.number="editHppForm.quantity" 
               class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" 
               placeholder="Masukkan jumlah">
        <div x-show="editHppErrors.quantity" class="text-red-500 text-xs mt-1" x-text="editHppErrors.quantity"></div>
      </div>

      <div x-show="editHppForm.type === 'in'">
        <label class="text-sm text-slate-600">HPP per Unit <span class="text-red-500">*</span></label>
        <input type="number" min="0" step="0.01" x-model.number="editHppForm.hpp_per_unit" 
               class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" 
               placeholder="Masukkan HPP per unit">
        <div class="text-xs text-slate-500 mt-1">Harga beli atau biaya produksi per unit</div>
        <div x-show="editHppErrors.hpp_per_unit" class="text-red-500 text-xs mt-1" x-text="editHppErrors.hpp_per_unit"></div>
      </div>

      <div>
        <label class="text-sm text-slate-600">Keterangan</label>
        <textarea x-model="editHppForm.notes" rows="2" 
                  class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" 
                  placeholder="Keterangan tambahan (opsional)"></textarea>
      </div>

      <div class="bg-slate-50 rounded-lg p-3">
        <div class="text-sm text-slate-600">Ringkasan:</div>
        <div class="mt-1 space-y-1 text-sm">
          <div class="flex justify-between">
            <span>Jenis:</span>
            <span x-text="editHppForm.type === 'in' ? 'Stok Masuk' : 'Stok Keluar'"></span>
          </div>
          <div class="flex justify-between">
            <span>Jumlah:</span>
            <span x-text="editHppForm.quantity || 0"></span>
          </div>
          <div x-show="editHppForm.type === 'in'" class="flex justify-between">
            <span>HPP per unit:</span>
            <span x-text="formatCurrency(editHppForm.hpp_per_unit || 0)"></span>
          </div>
          <div x-show="editHppForm.type === 'in'" class="flex justify-between font-medium border-t border-slate-200 pt-1">
            <span>Total Nilai:</span>
            <span x-text="formatCurrency((editHppForm.quantity || 0) * (editHppForm.hpp_per_unit || 0))"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-5 py-4 border-t border-slate-100 flex gap-2 justify-end">
      <button class="rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50" 
              x-on:click="closeEditHppModal()">
        Batal
      </button>
      <button class="rounded-lg bg-blue-600 text-white px-4 py-2 hover:bg-blue-700" 
              x-on:click="submitEditHppForm()" 
              :disabled="updatingHpp"
              :class="updatingHpp ? 'opacity-50 cursor-not-allowed' : ''">
        <span x-show="!updatingHpp">Update</span>
        <span x-show="updatingHpp" class="flex items-center gap-2">
          <i class='bx bx-loader-alt bx-spin'></i> Mengupdate...
        </span>
      </button>
    </div>
  </div>
</div><?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/inventaris/produk/hpp-modal.blade.php ENDPATH**/ ?>