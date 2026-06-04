
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => '',
    'help' => '',
    'placeholder' => '',
    'value' => ''
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
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => '',
    'help' => '',
    'placeholder' => '',
    'value' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="mb-4">
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700 mb-1">
            <?php echo e($label); ?>

            <?php if($required): ?>
                <span class="text-red-500">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <input type="<?php echo e($type); ?>" 
           id="<?php echo e($name); ?>" 
           name="<?php echo e($name); ?>"
           value="<?php echo e($value); ?>"
           placeholder="<?php echo e($placeholder); ?>"
           <?php if($required): ?> required <?php endif; ?>
           <?php echo e($attributes->merge([
               'class' => 'w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors' . ($error ? ' border-red-500' : '')
           ])); ?>>
    
    <?php if($error): ?>
        <p class="mt-1 text-sm text-red-600"><?php echo e($error); ?></p>
    <?php endif; ?>
    
    <?php if($help): ?>
        <p class="mt-1 text-sm text-gray-500"><?php echo e($help); ?></p>
    <?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\hm\resources\views\components\ui\input.blade.php ENDPATH**/ ?>