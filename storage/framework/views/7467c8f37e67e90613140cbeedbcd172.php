
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Setting COA POS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Setting COA POS')]); ?>
<div x-data="coaSettingsApp()" x-init="init()" class="space-y-4">

  
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Setting COA Point of Sales</h1>
        <p class="text-sm text-slate-600 mt-1">Konfigurasi akun untuk integrasi jurnal otomatis POS</p>
      </div>
      <select x-model="outletId" @change="loadSettings()" class="h-10 rounded-xl border border-slate-200 px-3">
        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e($outlet->id_outlet == $outletId ? 'selected' : ''); ?>>
            <?php echo e($outlet->nama_outlet); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
  </section>

  
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <form @submit.prevent="saveSettings()">
      <div class="space-y-4">
        
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Buku Akuntansi X<span class="text-red-500">*</span>
          </label>
          <select x-model="form.accounting_book_id" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Buku Akuntansi</option>
            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($book->id); ?>"><?php echo e($book->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Kas <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_kas" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Kas (Asset)</option>
            <?php $__currentLoopData = $accountsByType['asset']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">💵 Untuk pembayaran tunai (Tipe: Asset)</p>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Bank <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_bank" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Bank (Asset)</option>
            <?php $__currentLoopData = $accountsByType['asset']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">🏦 Untuk pembayaran transfer/QRIS (Tipe: Asset)</p>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Piutang Usaha <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_piutang_usaha" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Piutang (Asset)</option>
            <?php $__currentLoopData = $accountsByType['asset']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">📋 Untuk transaksi bon/piutang (Tipe: Asset)</p>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Pendapatan Penjualan <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_pendapatan_penjualan" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Pendapatan (Revenue)</option>
            <?php $__currentLoopData = $accountsByType['revenue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">💰 Pendapatan dari penjualan (Tipe: Revenue)</p>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun PPN (Pajak Pertambahan Nilai)
          </label>
          <select x-model="form.akun_ppn"
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun PPN (Liability - Opsional)</option>
            <?php $__currentLoopData = $accountsByType['liability']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">📊 Untuk mencatat PPN 10% (Tipe: Liability)</p>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun HPP (Harga Pokok Penjualan)
          </label>
          <select x-model="form.akun_hpp"
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun HPP (Expense - Opsional)</option>
            <?php $__currentLoopData = $accountsByType['expense']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">📦 Untuk mencatat HPP produk yang terjual (Tipe: Expense)</p>
        </div>

        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Persediaan
          </label>
          <select x-model="form.akun_persediaan"
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Persediaan (Asset - Opsional)</option>
            <?php $__currentLoopData = $accountsByType['asset']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($account->code); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <p class="text-xs text-slate-500 mt-1">📦 Untuk mengurangi nilai persediaan (Tipe: Asset)</p>
        </div>

        
        <div class="flex gap-2 pt-4">
          <button type="submit" 
                  class="px-4 h-10 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50"
                  :disabled="loading">
            <span x-show="!loading">Simpan Setting</span>
            <span x-show="loading">Menyimpan...</span>
          </button>
          <a href="<?php echo e(route('admin.penjualan.pos.index')); ?>" 
             class="px-4 h-10 rounded-xl border border-slate-200 hover:bg-slate-50 inline-flex items-center">
            Kembali ke POS
          </a>
        </div>

      </div>
    </form>
  </section>

</div>

<script>
function coaSettingsApp(){
  return {
    outletId: <?php echo e($outletId); ?>,
    loading: false,
    form: {
      accounting_book_id: '<?php echo e($setting->accounting_book_id ?? ""); ?>',
      akun_kas: '<?php echo e($setting->akun_kas ?? ""); ?>',
      akun_bank: '<?php echo e($setting->akun_bank ?? ""); ?>',
      akun_piutang_usaha: '<?php echo e($setting->akun_piutang_usaha ?? ""); ?>',
      akun_pendapatan_penjualan: '<?php echo e($setting->akun_pendapatan_penjualan ?? ""); ?>',
      akun_hpp: '<?php echo e($setting->akun_hpp ?? ""); ?>',
      akun_persediaan: '<?php echo e($setting->akun_persediaan ?? ""); ?>',
      akun_ppn: '<?php echo e($setting->akun_ppn ?? ""); ?>'
    },

    init(){
      console.log('COA Settings initialized');
    },

    async loadSettings(){
      window.location.href = `<?php echo e(route('admin.penjualan.pos.coa.settings')); ?>?outlet_id=${this.outletId}`;
    },

    async saveSettings(){
      this.loading = true;
      try {
        const response = await fetch('<?php echo e(route("admin.penjualan.pos.coa.settings.update")); ?>?outlet_id=' + this.outletId, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
          },
          body: JSON.stringify(this.form)
        });

        const result = await response.json();
        
        if(result.success) {
          alert('Setting COA POS berhasil disimpan');
        } else {
          alert('Gagal menyimpan: ' + (result.message || 'Unknown error'));
        }
      } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan saat menyimpan');
      } finally {
        this.loading = false;
      }
    }
  }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\pos\coa-settings.blade.php ENDPATH**/ ?>