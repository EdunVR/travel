<ul class="sub-menu">
    <?php if(in_array('User', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('user.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('user.index')); ?>">
                <i data-feather="users"></i> <span>Management User Aplikasi</span>
            </a>
        </li>
    <?php endif; ?>
    <!-- Sub-menu yang belum ada -->
    <li
            class="<?php echo e(request()->routeIs('hrm.recruitment.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('hrm.recruitment.index')); ?>">
                <i data-feather="user-plus"></i> <span>Kepegawaian & Rekrutmen</span>
            </a>
        </li>
    <li
            class="<?php echo e(request()->routeIs('hrm.payroll.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('hrm.payroll.index')); ?>">
                <i data-feather="dollar-sign"></i> <span>Penggajian / Payroll</span>
            </a>
        </li>
    <li
            class="<?php echo e(request()->routeIs('hrm.performance.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('hrm.performance.index')); ?>">
                <i data-feather="trending-up"></i> <span>Manajemen Kinerja</span>
            </a>
        </li>
    <li
            class="<?php echo e(request()->routeIs('hrm.training.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('hrm.training.index')); ?>">
                <i data-feather="dollar-sign"></i> <span>Pelatihan dan Pengembangan</span>
            </a>
        </li>
    <li
            class="<?php echo e(request()->routeIs('hrm.attendance.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('hrm.attendance.index')); ?>">
                <i data-feather="dollar-sign"></i> <span>Manajemen Absensi dan Waktu Kerja</span>
            </a>
        </li>
    
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\hrm.blade.php ENDPATH**/ ?>