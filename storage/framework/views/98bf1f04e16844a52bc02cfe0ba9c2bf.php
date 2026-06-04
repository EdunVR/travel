<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => 'Metric',
    'value' => '0',
    'delta' => '+0.0%',
    'positive' => true,
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
    'label' => 'Metric',
    'value' => '0',
    'delta' => '+0.0%',
    'positive' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <div class="text-xs sm:text-sm text-slate-500 leading-snug"><?php echo e($label); ?></div>
    <div class="mt-2 flex items-end gap-2">
        <div class="text-xl sm:text-2xl font-bold tracking-tight break-words"><?php echo e($value); ?></div>
        <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full <?php echo e($positive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'); ?>">
            <?php echo e($delta); ?>

        </span>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\stat-card.blade.php ENDPATH**/ ?>