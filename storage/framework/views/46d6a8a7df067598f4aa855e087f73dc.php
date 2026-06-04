<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Performa',
    // contoh: "6,8,7,10,12,9,14"
    'data' => '6,8,7,10,12,9,14',
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
    'title' => 'Performa',
    // contoh: "6,8,7,10,12,9,14"
    'data' => '6,8,7,10,12,9,14',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $vals = collect(explode(',', $data))->map(fn($v)=> (int)trim($v));
    $max = max($vals->all()) ?: 1;
?>

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <div class="font-medium text-sm sm:text-base"><?php echo e($title); ?></div>

    
    <div class="mt-3 h-20 sm:h-24 flex items-end gap-1.5 min-w-0">
        <?php $__currentLoopData = $vals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $h = max(6, round(($v / $max) * 96)); ?>
            <div
                class="flex-1 basis-0 min-w-[10px] max-w-[22px] rounded-md bg-primary-200/80 hover:bg-primary-300 transition"
                style="height: <?php echo e($h); ?>px">
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\chart-bars.blade.php ENDPATH**/ ?>