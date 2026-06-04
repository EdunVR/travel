
<?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'crm')): ?>
<ul class="sub-menu">
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'crm.tipe.view')): ?>
    <li class="<?php echo e(request()->routeIs('tipe.index') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('tipe.index')); ?>">
            <i data-feather="tag"></i> <span>Tipe & Diskon Customer</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'crm.pelanggan.view')): ?>
    <li class="<?php echo e(request()->routeIs('member.index') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('member.index')); ?>">
            <i data-feather="users"></i> <span>Manajemen Pelanggan</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'crm.leads.view')): ?>
    <li class="<?php echo e(request()->routeIs('prospek.index') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('prospek.index')); ?>">
            <i data-feather="user-plus"></i> <span>Manajemen Prospek & Lead</span>
        </a>
    </li>
    <?php endif; ?>
    
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Pembuatan & Pelacakan Sales Order</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\customer-service.blade.php ENDPATH**/ ?>