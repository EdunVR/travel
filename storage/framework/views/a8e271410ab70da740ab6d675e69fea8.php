
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'modal',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl, full
    'closable' => true
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
    'id' => 'modal',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl, full
    'closable' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
    'full' => 'max-w-7xl'
];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
?>

<div x-data="{ show: false }" 
     x-show="show" 
     x-on:open-modal-<?php echo e($id); ?>.window="show = true"
     x-on:close-modal-<?php echo e($id); ?>.window="show = false"
     x-on:keydown.escape.window="show = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
     
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
             @click="<?php if($closable): ?> show = false <?php endif; ?>"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block w-full <?php echo e($sizeClass); ?> p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <?php if($title || $closable): ?>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <?php if($title): ?>
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo e($title); ?></h3>
                    <?php endif; ?>
                    
                    <?php if($closable): ?>
                        <button @click="show = false" 
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\ui\modal.blade.php ENDPATH**/ ?>