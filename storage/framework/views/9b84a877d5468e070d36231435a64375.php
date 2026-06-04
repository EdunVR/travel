
<ul class="nav nav-treeview">
    
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.airline.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.airline.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Maskapai</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.airport.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.airport.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Bandara</p>
        </a>
    </li>

    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.flight.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.flight.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Penerbangan</p>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.hotel.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.hotel.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.hotel.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Hotel</p>
        </a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.transport.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.transport.*') ? 'active' : ''); ?>" style="padding-left: 2.5rem;">
            <i class="far fa-dot-circle nav-icon" style="font-size:0.7rem;"></i>
            <p style="font-size:0.85rem;">↳ Transportasi Saudi</p>
        </a>
    </li>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.package.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.travel.package.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.travel.package.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Paket Perjalanan</p>
        </a>
    </li>
    <?php endif; ?>
    
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.travel.catalog.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.travel.catalog.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Katalog Paket</p>
        </a>
    </li>
    
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.keberangkatan.view')): ?>
    
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.booking.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.booking.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.booking.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Booking Jamaah</p>
        </a>
    </li>
    <?php endif; ?>
    
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.tasks.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.travel.tasks.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.travel.tasks.index') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Task Management</p>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.tasks.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.travel.tasks.my-tasks')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.travel.tasks.my-tasks') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>My Tasks</p>
        </a>
    </li>
    <?php endif; ?>
    
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.communication.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.travel.communication.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.travel.communication.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Komunikasi</p>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.report.view')): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.travel.report.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.travel.report.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Laporan</p>
        </a>
    </li>
    <?php endif; ?>

    
    <li class="nav-item">
        <a href="<?php echo e(route('admin.inventaris.affiliate.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.inventaris.affiliate.*') ? 'active' : ''); ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Mitra
                <?php
                    $pendingAff = \App\Models\Affiliator::where('status','pending')->count();
                    $pendingPayout = \App\Models\AffiliatePayout::where('status','pending')->count();
                ?>
                <?php if($pendingAff + $pendingPayout > 0): ?>
                <span class="badge badge-warning badge-sm ml-1"><?php echo e($pendingAff + $pendingPayout); ?></span>
                <?php endif; ?>
            </p>
        </a>
    </li>
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\travel.blade.php ENDPATH**/ ?>