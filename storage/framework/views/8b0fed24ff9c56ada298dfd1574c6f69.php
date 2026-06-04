<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['href', 'icon', 'active' => false]));

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

foreach (array_filter((['href', 'icon', 'active' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<a href="<?php echo e($href); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'flex items-center px-4 py-3 text-sm transition-colors',
    'bg-green-800 text-white' => $active,
    'text-green-100 hover:bg-green-600' => !$active,
]); ?>">
    <i class="<?php echo e($icon); ?> w-6 text-center mr-3"></i>
    <span><?php echo e($slot); ?></span>
    <?php if($active): ?>
        <span class="ml-auto w-1 h-6 bg-white rounded-l"></span>
    <?php endif; ?>
</a>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components_old\investor\nav-link.blade.php ENDPATH**/ ?>