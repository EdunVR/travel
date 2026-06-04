
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        
        <li class="nav-item">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'travel')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/travel/*') || request()->is('admin/inventaris/flight/*') || request()->is('admin/inventaris/hotel/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/travel/*') || request()->is('admin/inventaris/flight/*') || request()->is('admin/inventaris/hotel/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-plane-departure"></i>
                <p>
                    Travel Management
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.travel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'crm')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/crm/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/crm/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>
                    CRM
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.customer-service', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'sales')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/sales/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/sales/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>
                    Sales
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.sales', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'finance')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/finance/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/finance/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-coins"></i>
                <p>
                    Finance
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.finance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'inventory')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/inventory/*') || request()->is('admin/inventaris/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/inventory/*') || request()->is('admin/inventaris/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-boxes"></i>
                <p>
                    Inventory
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.inventory', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'procurement')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/procurement/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/procurement/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                    Procurement
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.procurement', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'production')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/production/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/production/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-industry"></i>
                <p>
                    Production
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.production', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'hrm')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/hrm/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/hrm/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>
                    HRM
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.hrm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'project')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/project/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/project/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-project-diagram"></i>
                <p>
                    Projects
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.project-management', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'services')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/services/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/services/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-wrench"></i>
                <p>
                    Services
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.services', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'pos')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/pos/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/pos/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-cash-register"></i>
                <p>
                    POS
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.pos', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'analytics')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/analytics/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/analytics/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                    Analytics
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.analytics', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasModuleAccess', 'system')): ?>
        <li class="nav-item <?php echo e(request()->is('admin/system/*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link <?php echo e(request()->is('admin/system/*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                    System
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <?php echo $__env->make('partials.sidebar.system', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
        <?php endif; ?>

    </ul>
</nav>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\main.blade.php ENDPATH**/ ?>