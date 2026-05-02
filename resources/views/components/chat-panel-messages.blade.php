<div 
    x-ref="messageList"
    class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50"
    @scroll="if ($event.target.scrollTop === 0 && hasMoreMessages) loadMoreMessages()"
>
    {{-- Load More Button --}}
    <div x-show="hasMoreMessages && !isLoading" class="text-center">
        <button
            @click="loadMoreMessages()"
            type="button"
            class="px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-colors duration-200"
        >
            Muat Pesan Sebelumnya
        </button>
    </div>
    
    {{-- Loading Indicator --}}
    <div x-show="isLoading" class="text-center py-4">
        <div class="inline-flex items-center gap-2 text-slate-500">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm">Memuat pesan...</span>
        </div>
    </div>
    
    {{-- Empty State --}}
    <div x-show="!isLoading && messages.length === 0" class="flex flex-col items-center justify-center h-full text-center py-12">
        <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <p class="text-slate-600 font-medium mb-1">Belum ada pesan</p>
        <p class="text-sm text-slate-500">
            <span x-show="mode === 'superadmin'">Mulai percakapan dengan superadmin</span>
            <span x-show="mode === 'chatbot'">Tanyakan sesuatu ke chatbot</span>
        </p>
    </div>
    
    {{-- Messages --}}
    <template x-for="(message, index) in messages" :key="message.id">
        <div>
            {{-- Date Separator --}}
            <div 
                x-show="index === 0 || formatDate(messages[index - 1].created_at) !== formatDate(message.created_at)"
                class="flex items-center justify-center my-4"
            >
                <span class="px-3 py-1 text-xs font-medium text-slate-500 bg-white rounded-full shadow-sm" x-text="formatDate(message.created_at)"></span>
            </div>
            
            {{-- Message Bubble --}}
            @include('components.chat-message-bubble')
        </div>
    </template>
    
    {{-- Typing Indicator --}}
    <div x-show="isTyping" class="flex gap-2">
        <div class="w-8 h-8 rounded-full bg-slate-300 flex items-center justify-center">
            <div class="flex gap-1">
                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        </div>
    </div>
</div>
