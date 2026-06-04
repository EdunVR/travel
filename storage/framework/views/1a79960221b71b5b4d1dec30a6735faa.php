
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'headers' => [],
    'responsive' => true,
    'striped' => true,
    'hoverable' => true
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
    'headers' => [],
    'responsive' => true,
    'striped' => true,
    'hoverable' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="<?php if($responsive): ?> overflow-x-auto <?php endif; ?>">
    <table class="min-w-full divide-y divide-gray-200">
        <?php if(!empty($headers)): ?>
            <thead class="bg-gray-50">
                <tr>
                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo e($header); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
        <?php endif; ?>
        
        <tbody class="bg-white divide-y divide-gray-200 <?php if($striped): ?> divide-y divide-gray-200 <?php endif; ?>">
            <?php echo e($slot); ?>

        </tbody>
    </table>
</div>

<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\ui\table.blade.php ENDPATH**/ ?>