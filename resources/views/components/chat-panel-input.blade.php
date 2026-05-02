<div class="flex-shrink-0 border-t border-slate-200 bg-white p-4 rounded-b-2xl">
    {{-- Read-only notice for chatbot tab --}}
    <div x-show="isSuperadmin && chatbotTab === 'chatbot'" class="text-center py-3">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Mode hanya baca - Percakapan chatbot pengguna</span>
        </div>
    </div>
    
    {{-- Input form --}}
    <div x-show="!isSuperadmin || chatbotTab !== 'chatbot'" class="flex items-end gap-2">
        {{-- Textarea --}}
        <div class="flex-1 relative">
            <textarea
                x-ref="messageInput"
                x-model="messageInput"
                @keydown="handleKeyDown($event)"
                @input="autoExpandTextarea($event)"
                placeholder="Ketik pesan..."
                rows="1"
                class="w-full px-4 py-2 pr-12 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none overflow-hidden"
                :disabled="isSending"
                style="max-height: 120px;"
            ></textarea>
            
            {{-- Character Counter & Validation --}}
            <div class="flex items-center justify-between mt-1 px-1">
                <div 
                    x-show="validationError && messageInput.length > 0"
                    x-transition
                    class="text-xs text-red-600 font-medium bg-red-50 px-2 py-1 rounded"
                    x-text="validationError"
                ></div>
                
                <div 
                    class="text-xs ml-auto"
                    :class="{
                        'text-slate-400': characterCount <= 900,
                        'text-orange-500': characterCount > 900 && characterCount <= 1000,
                        'text-red-500': characterCount > 1000
                    }"
                >
                    <span x-text="characterCount"></span>/1000
                </div>
            </div>
        </div>
        
        {{-- Send Button --}}
        <button
            @click="sendMessage()"
            type="button"
            :disabled="!canSend"
            class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200"
            :class="{
                'bg-primary-600 text-white hover:bg-primary-700 shadow-sm': canSend,
                'bg-slate-200 text-slate-400 cursor-not-allowed': !canSend
            }"
        >
            <svg x-show="!isSending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            <svg x-show="isSending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </div>
    
    {{-- Help Text --}}
    <p x-show="!isSuperadmin || chatbotTab !== 'chatbot'" class="text-xs text-slate-500 mt-2">
        Tekan <kbd class="px-1.5 py-0.5 text-xs font-semibold text-slate-800 bg-slate-100 border border-slate-200 rounded">Enter</kbd> untuk mengirim, 
        <kbd class="px-1.5 py-0.5 text-xs font-semibold text-slate-800 bg-slate-100 border border-slate-200 rounded">Shift + Enter</kbd> untuk baris baru
    </p>
</div>
