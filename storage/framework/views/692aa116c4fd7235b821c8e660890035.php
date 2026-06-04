
<script>
// Chat configuration
window.chatConfig = {
    isSuperadmin: <?php echo e(auth()->user()->role_id == 1 ? 'true' : 'false'); ?>,
    currentUserId: <?php echo e(auth()->id()); ?>,
    routes: {
        users: '<?php echo e(route('admin.chat.users')); ?>',
        messages: '<?php echo e(route('admin.chat.messages')); ?>',
        sendMessage: '<?php echo e(route('admin.chat.send')); ?>',
        markRead: '<?php echo e(route('admin.chat.mark-read')); ?>',
        unreadCount: '<?php echo e(route('admin.chat.unread-count')); ?>'
    }
};
</script>

<div 
    x-data="chatPanel()"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-24 right-6 z-40 h-[600px] max-h-[calc(100vh-8rem)] bg-white rounded-2xl shadow-float border border-slate-200 flex overflow-hidden"
    :class="{
        'w-full sm:w-96': !isSuperadmin || !showUserList,
        'w-full sm:w-[700px]': isSuperadmin && showUserList
    }"
    style="display: none;"
>
    
    <?php echo $__env->make('components.chat-panel-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <div class="flex-1 flex flex-col min-w-0">
        
        <?php echo $__env->make('components.chat-panel-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        
        <?php echo $__env->make('components.chat-panel-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        
        <?php echo $__env->make('components.chat-panel-error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        
        <?php echo $__env->make('components.chat-panel-input', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\chat-panel.blade.php ENDPATH**/ ?>