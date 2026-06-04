<ul class="sub-menu">
    <?php if(in_array('Pembelian', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('pembelian.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('pembelian.index')); ?>">
                <i data-feather="shopping-bag"></i> <span>Pembelian</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if(in_array('Supplier', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('supplier.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('supplier.index')); ?>">
                <i data-feather="truck"></i> <span>Manajemen Supplier</span>
            </a>
        </li>
    <?php endif; ?>
    <!-- Sub-menu yang belum ada -->
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Purchase Order Management</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="file"></i> <span>Manajemen Kontrak Pembelian</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\procurement.blade.php ENDPATH**/ ?>