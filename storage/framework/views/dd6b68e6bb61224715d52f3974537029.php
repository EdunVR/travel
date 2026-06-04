<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Trend',
    'labels' => 'Jan,Feb,Mar,Apr,May,Jun,Jul',
    // path d svg (contoh)
    'path' => 'M0,30 L30,28 L60,26 L90,22 L120,24 L150,18 L180,14 L210,12',
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
    'title' => 'Trend',
    'labels' => 'Jan,Feb,Mar,Apr,May,Jun,Jul',
    // path d svg (contoh)
    'path' => 'M0,30 L30,28 L60,26 L90,22 L120,24 L150,18 L180,14 L210,12',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <div class="flex items-center justify-between gap-3">
        <div class="font-medium text-sm sm:text-base"><?php echo e($title); ?></div>
        <div class="text-[10px] sm:text-xs text-slate-400 truncate"><?php echo e($labels); ?></div>
    </div>

    <div class="mt-3 overflow-hidden">
        
        <svg viewBox="0 0 210 40" preserveAspectRatio="none" class="w-full h-20 sm:h-24">
            
            <path d="<?php echo e($path); ?> L210,40 L0,40 Z" fill="rgba(47,134,255,0.10)"></path>
            <path d="<?php echo e($path); ?>" fill="none" stroke="rgb(47,134,255)" stroke-width="2"></path>
        </svg>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\chart-sparkline.blade.php ENDPATH**/ ?>