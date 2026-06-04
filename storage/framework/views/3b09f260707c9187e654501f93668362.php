<ul class="sub-menu">
    <?php if(in_array('Transaksi', Auth::user()->akses ?? [])): ?>
            <li class="<?php echo e(request()->routeIs('transaksi.baru') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('transaksi.baru')); ?>">
                    <i data-feather="credit-card"></i> <span>Transaksi Baru</span>
                </a>
            </li>
    <?php endif; ?>
    <?php if(in_array('Kontra Bon', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('kontra_bon.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('kontra_bon.index')); ?>">
                <i data-feather="dollar-sign"></i> <span>Kontra Bon</span>
            </a>
        </li>
    <?php endif; ?>
    
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\pos.blade.php ENDPATH**/ ?>