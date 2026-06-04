
<?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'inventory')): ?>
<ul class="sub-menu">
    <?php if (\Illuminate\Support\Facades\Blade::check('hasAnyPermission', 'sistem.outlets.view', 'inventory.barang.view')): ?>
    <li class="<?php echo e(request()->routeIs('admin.inventaris.outlet.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.outlet.index')); ?>">
            <i data-feather="crosshair"></i> <span>Outlet</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'inventory.kategori.view')): ?>
    <li class="<?php echo e(request()->routeIs('admin.inventaris.kategori.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.kategori.index')); ?>">
            <i data-feather="grid"></i> <span>Kategori Umum</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasAnyPermission', 'inventory.barang.view', 'inventory.kategori.view')): ?>
    <li class="<?php echo e(request()->routeIs('admin.inventaris.satuan.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.satuan.index')); ?>">
            <i data-feather="pocket"></i> <span>Satuan</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'inventory.barang.view')): ?>
    <li class="<?php echo e(request()->routeIs('admin.inventaris.produk.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.produk.index')); ?>">
            <i data-feather="package"></i> <span>Produk</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'inventory.barang.view')): ?>
    <li class="<?php echo e(request()->routeIs('admin.inventaris.bahan.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.bahan.index')); ?>">
            <i data-feather="layers"></i> <span>Bahan</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'inventory.stok.view')): ?>
    <li class="<?php echo e(request()->routeIs('admin.inventaris.inventori.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.inventori.index')); ?>">
            <i data-feather="database"></i> <span>Inventori/Stok</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'inventory.transfer.view')): ?>
    <li class="<?php echo e(request()->routeIs('manajemen-gudang.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('manajemen-gudang.index')); ?>">
            <i data-feather="send"></i> <span>Transfer Gudang</span>
        </a>
    </li>
    <?php endif; ?>
    
    
    
    <li class="unavailable">
        <a href="#">
            <i data-feather="bar-chart-2"></i> <span>Analisis Inventaris</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\inventory.blade.php ENDPATH**/ ?>