<ul class="sub-menu">
    <?php if(in_array('User', Auth::user()->akses ?? [])): ?>
        <li class="<?php echo e(request()->routeIs('user.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('user.index')); ?>">
                <i data-feather="users"></i> <span>User</span>
            </a>
        </li>
    <?php endif; ?>
    
    
    <li class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.users.index')); ?>">
            <i data-feather="user-check"></i> <span>User Management</span>
        </a>
    </li>
    
    
    <li class="<?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.roles.index')); ?>">
            <i data-feather="shield"></i> <span>Role & Permission</span>
        </a>
    </li>
    
    <?php if(in_array('Pengaturan', Auth::user()->akses ?? [])): ?>
        <li class="<?php echo e(request()->routeIs('setting.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('setting.index')); ?>">
                <i data-feather="settings"></i> <span>Pengaturan</span>
            </a>
        </li>
    <?php endif; ?>
    
    <?php if(in_array('Pengaturan COA', Auth::user()->akses ?? [])): ?>
        <li class="<?php echo e(request()->routeIs('settings.coa.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('settings.coa.index')); ?>">
                <i data-feather="sliders"></i> <span>Pengaturan COA</span>
            </a>
        </li>
    <?php endif; ?>
    
    
    <li class="<?php echo e(request()->routeIs('admin.inventaris.admin.audit.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.inventaris.admin.audit.index')); ?>">
            <i data-feather="file-text"></i> <span>Audit Logs</span>
        </a>
    </li>
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\system.blade.php ENDPATH**/ ?>