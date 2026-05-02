<div 
    class="flex gap-2"
    :class="{ 'flex-row-reverse': isSender(message) }"
>
    {{-- Avatar --}}
    <div 
        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white"
        :class="{
            'bg-primary-600': isSender(message),
            'bg-slate-600': !isSender(message) && message.mode === 'superadmin',
            'bg-purple-600': message.mode === 'chatbot'
        }"
    >
        <span x-show="message.mode !== 'chatbot'" x-text="(message.sender?.name || 'U').charAt(0).toUpperCase()"></span>
        <svg x-show="message.mode === 'chatbot'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    
    {{-- Message Content --}}
    <div 
        class="flex-1 max-w-[75%]"
        :class="{ 'flex flex-col items-end': isSender(message) }"
    >
        {{-- Sender Name (for received messages) --}}
        <div 
            x-show="!isSender(message)"
            class="text-xs font-medium text-slate-600 mb-1 px-1"
            x-text="message.sender?.name || 'Unknown'"
        ></div>
        
        {{-- Message Bubble --}}
        <div 
            class="px-4 py-2 rounded-2xl break-words"
            :class="{
                'bg-primary-600 text-white rounded-br-sm': isSender(message),
                'bg-white text-slate-900 rounded-bl-sm shadow-sm': !isSender(message) && message.mode === 'superadmin',
                'bg-purple-100 text-purple-900 rounded-bl-sm border border-purple-200': message.mode === 'chatbot' && !message.content.includes('beralih ke mode Superadmin'),
                'bg-orange-50 text-orange-900 rounded-bl-sm border border-orange-300': message.mode === 'chatbot' && message.content.includes('beralih ke mode Superadmin')
            }"
        >
            {{-- Error icon for chatbot error messages --}}
            <div 
                x-show="message.mode === 'chatbot' && message.content.includes('beralih ke mode Superadmin')"
                class="flex items-start gap-2 mb-2"
            >
                <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="text-xs font-semibold text-orange-800">Chatbot Tidak Tersedia</span>
            </div>
            
            <p class="text-sm whitespace-pre-wrap" x-text="message.content"></p>
        </div>
        
        {{-- Message Meta --}}
        <div 
            class="flex items-center gap-1.5 mt-1 px-1"
            :class="{ 'justify-end': isSender(message) }"
        >
            <span class="text-xs text-slate-500" x-text="formatTime(message.created_at)"></span>
            
            {{-- Read Status (for sent messages) --}}
            <span x-show="isSender(message)">
                <svg 
                    x-show="message.is_read" 
                    class="w-4 h-4 text-primary-600" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg 
                    x-show="!message.is_read" 
                    class="w-4 h-4 text-slate-400" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
        </div>
    </div>
</div>
