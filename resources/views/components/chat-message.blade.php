{{-- Chat Message Component --}}
@props([
    'message',
    'isSender' => false,
    'type' => 'user', // 'user', 'superadmin', or 'chatbot'
    'showSenderName' => false
])

<div 
    class="flex gap-2"
    :class="{ 'flex-row-reverse': {{ $isSender ? 'true' : 'false' }} }"
>
    {{-- Avatar --}}
    <div 
        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white"
        @class([
            'bg-primary-600' => $isSender,
            'bg-slate-600' => !$isSender && $type === 'superadmin',
            'bg-purple-600' => $type === 'chatbot'
        ])
    >
        @if($type === 'chatbot')
            {{-- Chatbot Icon --}}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        @else
            {{-- User Initial --}}
            <span>{{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}</span>
        @endif
    </div>
    
    {{-- Message Content --}}
    <div 
        class="flex-1 max-w-[75%]"
        @class([
            'flex flex-col items-end' => $isSender
        ])
    >
        {{-- Sender Name (for received messages or superadmin view) --}}
        @if($showSenderName && !$isSender)
            <div class="text-xs font-medium text-slate-600 mb-1 px-1">
                {{ $message->sender->name ?? 'Unknown' }}
            </div>
        @endif
        
        {{-- Message Bubble --}}
        <div 
            class="px-4 py-2 rounded-2xl break-words"
            @class([
                'bg-primary-600 text-white rounded-br-sm' => $isSender,
                'bg-white text-slate-900 rounded-bl-sm shadow-sm' => !$isSender && $type === 'superadmin',
                'bg-purple-100 text-purple-900 rounded-bl-sm border border-purple-200' => $type === 'chatbot'
            ])
        >
            <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
            
            {{-- Chatbot Badge (optional, inside bubble) --}}
            @if($type === 'chatbot' && !$isSender)
                <div class="flex items-center gap-1 mt-1 pt-1 border-t border-purple-200">
                    <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs font-medium text-purple-600">AI Assistant</span>
                </div>
            @endif
        </div>
        
        {{-- Message Metadata --}}
        <div 
            class="flex items-center gap-1.5 mt-1 px-1"
            @class([
                'justify-end' => $isSender
            ])
        >
            {{-- Timestamp --}}
            <span class="text-xs text-slate-500">
                {{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}
            </span>
            
            {{-- Read Status (for sent messages) --}}
            @if($isSender)
                <span>
                    @if($message->is_read)
                        {{-- Double checkmark for read --}}
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{-- Single checkmark for delivered --}}
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </span>
            @endif
        </div>
    </div>
</div>
