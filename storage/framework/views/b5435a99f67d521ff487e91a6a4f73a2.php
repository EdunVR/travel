
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
    'icon' => ''
]));

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

foreach (array_filter(([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
    'icon' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$variantClasses = [
    'primary' => 'bg-primary-600 hover:bg-primary-700 text-white border-transparent',
    'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white border-transparent',
    'success' => 'bg-green-600 hover:bg-green-700 text-white border-transparent',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white border-transparent',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white border-transparent',
    'info' => 'bg-blue-600 hover:bg-blue-700 text-white border-transparent',
    'outline' => 'bg-transparent hover:bg-gray-50 text-gray-700 border-gray-300'
];

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base'
];

$variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
?>

<button type="<?php echo e($type); ?>"
        <?php if($disabled || $loading): ?> disabled <?php endif; ?>
        <?php echo e($attributes->merge([
            'class' => 'inline-flex items-center justify-center border font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed ' . $variantClass . ' ' . $sizeClass
        ])); ?>>
        
    <?php if($loading): ?>
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading...
    <?php else: ?>
        <?php if($icon): ?>
            <i class="<?php echo e($icon); ?> mr-2"></i>
        <?php endif; ?>
        <?php echo e($slot); ?>

    <?php endif; ?>
</button><?php /**PATH C:\xampp\htdocs\hm\resources\views\components\ui\button.blade.php ENDPATH**/ ?>