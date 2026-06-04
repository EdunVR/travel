<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'service.mesin.create')): ?>
<div>PERMISSION CHECK PASSED - Button should show</div>
<?php else: ?>
<div>PERMISSION CHECK FAILED - Button will be hidden</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\test-permission.blade.php ENDPATH**/ ?>