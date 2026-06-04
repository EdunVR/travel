<ul class="sub-menu">
            <?php if(in_array('Investor', Auth::user()->akses ?? [])): ?>
                <li class="<?php echo e(request()->routeIs('irp.investor.index') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('irp.investor.index')); ?>">
                        <i data-feather="users"></i> <span>Profil Investor</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(in_array('Profit Management', Auth::user()->akses ?? [])): ?>
                <li class="<?php echo e(request()->routeIs('irp.profit-management.index') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('irp.profit-management.index')); ?>">
                        <i data-feather="file-text"></i> <span>Kelompok Bagi Hasil</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(in_array('Withdrawal Management', Auth::user()->akses ?? [])): ?>
                <li class="<?php echo e(request()->routeIs('irp.withdrawal-management.index') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('irp.withdrawal-management.index')); ?>">
                        <i data-feather="file-text"></i> <span>List Pencairan</span>
                    </a>
                </li>
            <?php endif; ?>
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Agenda & Komunikasi</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Pengaturan</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\irp.blade.php ENDPATH**/ ?>