<ul class="sub-menu">
    <?php if(in_array('Produksi', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('produksi.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('produksi.index')); ?>">
                <i data-feather="zap"></i> <span>Produksi</span>
            </a>
        </li>
    <?php endif; ?>
    <!-- Sub-menu yang belum ada -->
    <li class="unavailable">
        <a href="#">
            <i data-feather="clipboard"></i> <span>Perencanaan Produksi</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Manajemen Bill of Materials (BOM)</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="map"></i> <span>Pelacakan Produksi</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="check-circle"></i> <span>Manajemen Kualitas</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\production.blade.php ENDPATH**/ ?>