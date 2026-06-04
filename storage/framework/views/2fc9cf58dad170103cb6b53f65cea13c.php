<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['errors' => null, 'title' => 'Validasi Gagal']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['errors' => null, 'title' => 'Validasi Gagal']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $errorBag = $errors ?? $errors ?? session('errors');
?>

<?php if($errorBag && $errorBag->any()): ?>
    <div <?php echo e($attributes->merge(['class' => 'alert alert-danger alert-dismissible fade show'])); ?> role="alert">
        <h5 class="alert-heading">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo e($title); ?>

        </h5>
        <p class="mb-2">Silakan perbaiki kesalahan berikut:</p>
        <ul class="mb-0">
            <?php $__currentLoopData = $errorBag->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\validation-summary.blade.php ENDPATH**/ ?>