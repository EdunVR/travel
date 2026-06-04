
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'message',
    'isSender' => false,
    'type' => 'user', // 'user', 'superadmin', or 'chatbot'
    'showSenderName' => false
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
    'message',
    'isSender' => false,
    'type' => 'user', // 'user', 'superadmin', or 'chatbot'
    'showSenderName' => false
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div 
    class="flex gap-2"
    :class="{ 'flex-row-reverse': <?php echo e($isSender ? 'true' : 'false'); ?> }"
>
    
    <div 
        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'bg-primary-600' => $isSender,
            'bg-slate-600' => !$isSender && $type === 'superadmin',
            'bg-purple-600' => $type === 'chatbot'
        ]); ?>"
    >
        <?php if($type === 'chatbot'): ?>
            
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        <?php else: ?>
            
            <span><?php echo e(strtoupper(substr($message->sender->name ?? 'U', 0, 1))); ?></span>
        <?php endif; ?>
    </div>
    
    
    <div 
        class="flex-1 max-w-[75%]"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'flex flex-col items-end' => $isSender
        ]); ?>"
    >
        
        <?php if($showSenderName && !$isSender): ?>
            <div class="text-xs font-medium text-slate-600 mb-1 px-1">
                <?php echo e($message->sender->name ?? 'Unknown'); ?>

            </div>
        <?php endif; ?>
        
        
        <div 
            class="px-4 py-2 rounded-2xl break-words"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'bg-primary-600 text-white rounded-br-sm' => $isSender,
                'bg-white text-slate-900 rounded-bl-sm shadow-sm' => !$isSender && $type === 'superadmin',
                'bg-purple-100 text-purple-900 rounded-bl-sm border border-purple-200' => $type === 'chatbot'
            ]); ?>"
        >
            <p class="text-sm whitespace-pre-wrap"><?php echo e($message->content); ?></p>
            
            
            <?php if($type === 'chatbot' && !$isSender): ?>
                <div class="flex items-center gap-1 mt-1 pt-1 border-t border-purple-200">
                    <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs font-medium text-purple-600">AI Assistant</span>
                </div>
            <?php endif; ?>
        </div>
        
        
        <div 
            class="flex items-center gap-1.5 mt-1 px-1"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'justify-end' => $isSender
            ]); ?>"
        >
            
            <span class="text-xs text-slate-500">
                <?php echo e(\Carbon\Carbon::parse($message->created_at)->format('H:i')); ?>

            </span>
            
            
            <?php if($isSender): ?>
                <span>
                    <?php if($message->is_read): ?>
                        
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    <?php else: ?>
                        
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\chat-message.blade.php ENDPATH**/ ?>