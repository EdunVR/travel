<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'square',
    'class' => 'w-5 h-5',
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
    'name' => 'square',
    'class' => 'w-5 h-5',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php switch($name):
    
    case ('menu'): ?>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?> aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
        </svg>
    <?php break; ?>
    <?php case ('layout-dashboard'): ?>
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h6.5v6.5h-6.5zM13.75 4.5h6.5v6.5h-6.5zM3.75 14.5h6.5v6.5h-6.5zM13.75 14.5h6.5v6.5h-6.5z"/>
        </svg>
    <?php break; ?>
    <?php case ('cube'): ?>
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5 12 3 3 7.5m18 0-9 4.5m9-4.5v9l-9 4.5m0-9L3 7.5m9 4.5v9M3 7.5v9l9 4.5"/>
        </svg>
    <?php break; ?>

    
    <?php case ('boxes'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25h7.5V3H3v5.25zM13.5 8.25H21V3h-7.5v5.25zM3 21h7.5v-7.5H3V21zM13.5 21H21v-7.5h-7.5V21z"/>
        </svg>
    <?php break; ?>
    <?php case ('hand-coins'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.5h19.5v9H2.25zM6 9.75a2.25 2.25 0 1 0 0 4.5m12-4.5a2.25 2.25 0 1 1 0 4.5M12 9.75a2.25 2.25 0 1 0 0 4.5"/>
        </svg>
    <?php break; ?>
    <?php case ('users'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.125a6.375 6.375 0 0 0-12.75 0M20.25 19.125a4.125 4.125 0 0 0-8.25 0M12 10.5a3.375 3.375 0 1 0 0-6.75 3.375 3.375 0 0 0 0 6.75z"/>
        </svg>
    <?php break; ?>
    <?php case ('shopping-cart'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3.75h2.5l1.5 12.5h12.5l1.5-9.5H6.75M9 21a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5zm8 0a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5z"/>
        </svg>
    <?php break; ?>
    <?php case ('wallet'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5v9.5A2.25 2.25 0 0 1 19.5 20H4.5A2.25 2.25 0 0 1 2.25 17.75v-9.5zM2.25 8.25V7A2.25 2.25 0 0 1 4.5 4.75H15M15 12h4.5"/>
        </svg>
    <?php break; ?>
    <?php case ('id-card'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5v10.5H3.75zM8.25 12a2.25 2.25 0 1 0 4.5 0m-6 3h7.5"/>
        </svg>
    <?php break; ?>
    <?php case ('receipt-text'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75h3.75L18 9v7.5A2.25 2.25 0 0 1 15.75 18H8.25A2.25 2.25 0 0 1 6 15.75V8.25A1.5 1.5 0 0 1 7.5 6.75H12zM9 12h6M9 15h4"/>
        </svg>
    <?php break; ?>
    <?php case ('truck'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h9.5v7.5H3.75zM13.25 10.5h4.25L21 13.5v1.5h-7.75M6.75 19.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm9.5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
        </svg>
    <?php break; ?>
    <?php case ('factory'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4.5 21V8.25l7.5 2.25V6.75l7.5 2.25V21M8.25 12h.01M12 13.5h.01M15.75 15h.01"/>
        </svg>
    <?php break; ?>
    <?php case ('git-branch'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6a2.25 2.25 0 1 1 0 4.5A2.25 2.25 0 0 1 6 6zm0 7.5A2.25 2.25 0 1 1 6 18a2.25 2.25 0 0 1 0-4.5zM18 6a2.25 2.25 0 1 1 0 4.5A2.25 2.25 0 0 1 18 6zM8.25 8.25h5.5a2.25 2.25 0 0 1 2.25 2.25V12"/>
        </svg>
    <?php break; ?>
    <?php case ('wrench'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.5 8.25 3-3m-6 1.5 3 3M5.25 18.75l6-6"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21 9 16.5"/>
        </svg>
    <?php break; ?>
    <?php case ('line-chart'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 19.5h16.5M6 16.5V9.75M10.5 16.5V6.75M15 16.5v-5.25"/>
        </svg>
    <?php break; ?>
    <?php case ('settings'): ?> 
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5m-7.5 16.5h10.5M3.75 12h16.5M12 8.25v7.5"/>
        </svg>
    <?php break; ?>

    
    <?php case ('arrow-right'): ?>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m-6-6 6 6-6 6"/>
        </svg>
    <?php break; ?>

    
    <?php default: ?>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" <?php echo e($attributes->merge(['class' => $class])); ?>>
            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
        </svg>
<?php endswitch; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/components/icon.blade.php ENDPATH**/ ?>