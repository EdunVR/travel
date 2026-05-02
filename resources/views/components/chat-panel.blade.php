{{-- Chat Panel Component --}}
<script>
// Chat configuration
window.chatConfig = {
    isSuperadmin: {{ auth()->user()->role_id == 1 ? 'true' : 'false' }},
    currentUserId: {{ auth()->id() }},
    routes: {
        users: '{{ route('admin.chat.users') }}',
        messages: '{{ route('admin.chat.messages') }}',
        sendMessage: '{{ route('admin.chat.send') }}',
        markRead: '{{ route('admin.chat.mark-read') }}',
        unreadCount: '{{ route('admin.chat.unread-count') }}'
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
    {{-- User List Sidebar (Superadmin Only) --}}
    @include('components.chat-panel-sidebar')

    {{-- Main Chat Area --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Panel Header --}}
        @include('components.chat-panel-header')
        
        {{-- Message List Area --}}
        @include('components.chat-panel-messages')
        
        {{-- Error Message --}}
        @include('components.chat-panel-error')
        
        {{-- Message Input Area --}}
        @include('components.chat-panel-input')
    </div>
</div>
