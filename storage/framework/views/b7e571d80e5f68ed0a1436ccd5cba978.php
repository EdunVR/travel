<ul class="sub-menu">
    <?php if(in_array('Invoice Service', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('service.invoice.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('service.invoice.index')); ?>">
                <i data-feather="file-text"></i> <span>Invoice Service</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if(in_array('History Service', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('service.invoice.history') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('service.invoice.history')); ?>">
                <i data-feather="layers"></i> <span>History Invoice</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if(in_array('Ongkir Service', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('service.ongkos-kirim.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('service.ongkos-kirim.index')); ?>">
                <i data-feather="truck"></i> <span>Ongkir Service</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if(in_array('Mesin Customer', Auth::user()->akses ?? [])): ?>
        <li
            class="<?php echo e(request()->routeIs('service.mesin-customer.index') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('service.mesin-customer.index')); ?>">
                <i data-feather="award"></i> <span>Mesin Customer</span>
            </a>
        </li>
    <?php endif; ?>
</ul>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\sidebar\services.blade.php ENDPATH**/ ?>