
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'rows' => 3,
    'columns' => 4,
    'height' => 'h-4',
    'type' => 'table' // table, card, list
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
    'rows' => 3,
    'columns' => 4,
    'height' => 'h-4',
    'type' => 'table' // table, card, list
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php if($type === 'table'): ?>
    <div class="animate-pulse">
        <div class="bg-gray-200 h-8 rounded mb-4"></div>
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="grid grid-cols-<?php echo e($columns); ?> gap-4 mb-3">
                <?php for($j = 0; $j < $columns; $j++): ?>
                    <div class="bg-gray-200 <?php echo e($height); ?> rounded"></div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>
<?php elseif($type === 'card'): ?>
    <div class="animate-pulse">
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="bg-gray-200 h-6 rounded mb-3"></div>
                <div class="bg-gray-200 h-4 rounded mb-2"></div>
                <div class="bg-gray-200 h-4 rounded w-3/4"></div>
            </div>
        <?php endfor; ?>
    </div>
<?php elseif($type === 'list'): ?>
    <div class="animate-pulse">
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="flex items-center space-x-4 mb-4">
                <div class="bg-gray-200 h-10 w-10 rounded-full"></div>
                <div class="flex-1">
                    <div class="bg-gray-200 h-4 rounded mb-2"></div>
                    <div class="bg-gray-200 h-3 rounded w-2/3"></div>
                </div>
            </div>
        <?php endfor; ?>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\components\ui\skeleton.blade.php ENDPATH**/ ?>