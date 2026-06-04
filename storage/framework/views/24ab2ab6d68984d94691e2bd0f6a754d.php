<div>
    <!-- Tab Navigation -->
    <div class="tab-nav">
        <button class="tab-link active" onclick="openTab(event, 'produk-<?php echo e($target); ?>')">Produk</button>
        <button class="tab-link" onclick="openTab(event, 'bahan-<?php echo e($target); ?>')">Bahan</button>
        <button class="tab-link" onclick="openTab(event, 'inventori-<?php echo e($target); ?>')">Inventori</button>
    </div>

    <!-- Tab Content -->
    <div id="produk-<?php echo e($target); ?>" class="tab-content active">
        <ul class="list-group mt-3">
            <?php $__currentLoopData = $produk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex align-items-center">
                    <?php if($target === 'asal'): ?>
                        <?php
                            $permintaanMenunggu = \App\Models\PermintaanPengiriman::where('id_produk', $item->id_produk)
                                ->where('status', 'menunggu')
                                ->exists();
                        ?>
                        <button onclick="<?php echo e($permintaanMenunggu ? '' : "pilihItem('produk', {$item->id_produk}, '{$item->nama_produk}')"); ?>" 
                                class="btn btn-xs <?php echo e($permintaanMenunggu ? 'btn-secondary disabled' : 'btn-primary'); ?> me-2">
                            <?php echo e($permintaanMenunggu ? 'Menunggu Disetujui' : 'Pilih'); ?>

                        </button>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo e($item->nama_produk); ?></strong> 
                        <span class="text-muted">(Stok: <?php echo e($item->hpp_produk_sum_stok ?? 0); ?>)</span>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div id="bahan-<?php echo e($target); ?>" class="tab-content">
        <ul class="list-group mt-3">
            <?php $__currentLoopData = $bahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex align-items-center">
                    <?php if($target === 'asal'): ?>
                        <?php
                            $permintaanMenunggu = \App\Models\PermintaanPengiriman::where('id_bahan', $item->id_bahan)
                                ->where('status', 'menunggu')
                                ->exists();
                        ?>
                        <button onclick="<?php echo e($permintaanMenunggu ? '' : "pilihItem('bahan', {$item->id_bahan}, '{$item->nama_bahan}')"); ?>" 
                                class="btn btn-xs <?php echo e($permintaanMenunggu ? 'btn-secondary disabled' : 'btn-primary'); ?> me-2">
                            <?php echo e($permintaanMenunggu ? 'Menunggu Disetujui' : 'Pilih'); ?>

                        </button>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo e($item->nama_bahan); ?></strong> 
                        <span class="text-muted">(Stok: <?php echo e($item->harga_bahan_sum_stok ?? 0); ?>)</span>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div id="inventori-<?php echo e($target); ?>" class="tab-content">
        <ul class="list-group mt-3">
            <?php $__currentLoopData = $inventori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex align-items-center">
                    <?php if($target === 'asal'): ?>
                        <?php
                            $permintaanMenunggu = \App\Models\PermintaanPengiriman::where('id_inventori', $item->id_inventori)
                                ->where('status', 'menunggu')
                                ->exists();
                        ?>
                        <button onclick="<?php echo e($permintaanMenunggu ? '' : "pilihItem('inventori', {$item->id_inventori}, '{$item->nama_barang}')"); ?>" 
                                class="btn btn-xs <?php echo e($permintaanMenunggu ? 'btn-secondary disabled' : 'btn-primary'); ?> me-2">
                            <?php echo e($permintaanMenunggu ? 'Menunggu Disetujui' : 'Pilih'); ?>

                        </button>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo e($item->nama_barang); ?></strong> 
                        <span class="text-muted">(Stok: <?php echo e($item->stok); ?>)</span>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>

<script>
    // Fungsi untuk membuka tab
    function openTab(event, tabName) {
        // Sembunyikan semua tab content dalam parent yang sama
        const parent = event.currentTarget.closest('.tab-nav').parentElement;
        const tabContents = parent.querySelectorAll('.tab-content');
        tabContents.forEach(tab => tab.classList.remove('active'));

        // Tampilkan tab content yang dipilih
        parent.querySelector(`#${tabName}`).classList.add('active');

        // Hapus class 'active' dari semua tab link dalam parent yang sama
        const tabLinks = parent.querySelectorAll('.tab-link');
        tabLinks.forEach(link => link.classList.remove('active'));

        // Tambahkan class 'active' ke tab link yang diklik
        event.currentTarget.classList.add('active');
    }
</script>

<style>
    /* Style untuk tab navigation */
    .tab-nav {
        display: flex;
        border-bottom: 1px solid #dee2e6;
    }

    .tab-link {
        padding: 10px 20px;
        cursor: pointer;
        border: 1px solid transparent;
        border-bottom: none;
        background-color: #f8f9fa;
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
        color: #495057;
    }

    .tab-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        color: #0d6efd;
    }

    .tab-link:hover {
        background-color: #e9ecef;
    }

    /* Style untuk tab content */
    .tab-content {
        display: none;
        padding: 20px;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 5px 5px;
    }

    .tab-content.active {
        display: block;
    }
</style>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\gudang\partials\item-list.blade.php ENDPATH**/ ?>