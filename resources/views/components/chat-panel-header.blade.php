<div class="flex-shrink-0 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-4 py-3"
     :class="{ 'rounded-t-2xl': !isSuperadmin || !showUserList, 'rounded-tr-2xl': isSuperadmin && showUserList }">
    <div class="flex items-center justify-between mb-2">
        <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold" x-show="!isSuperadmin || !selectedUser">Chat</h3>
            <div x-show="isSuperadmin && selectedUser" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-white/20">
                    <span x-text="selectedUser ? selectedUser.name.charAt(0).toUpperCase() : ''"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold truncate" x-text="selectedUser ? selectedUser.name : ''"></h3>
                    <p class="text-xs text-white/80" x-text="selectedUser ? selectedUser.email : ''"></p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            {{-- Connection Status --}}
            <div class="flex items-center gap-1.5">
                <span 
                    class="w-2 h-2 rounded-full transition-colors duration-300"
                    :class="{
                        'bg-green-400': connectionStatus === 'online',
                        'bg-red-400': connectionStatus === 'offline',
                        'animate-pulse': connectionStatus === 'offline'
                    }"
                ></span>
                <span class="text-xs font-medium" x-text="connectionStatus === 'online' ? 'Online' : 'Offline'"></span>
            </div>
            
            {{-- Sound Toggle Button --}}
            <button
                @click="toggleSound()"
                type="button"
                class="p-1 hover:bg-white/20 rounded-lg transition-colors duration-200"
                :aria-label="soundEnabled ? 'Matikan suara notifikasi' : 'Nyalakan suara notifikasi'"
                :title="soundEnabled ? 'Matikan suara notifikasi' : 'Nyalakan suara notifikasi'"
            >
                <svg x-show="soundEnabled" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
                <svg x-show="!soundEnabled" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                </svg>
            </button>
            
            {{-- Close Button --}}
            <button
                @click="closePanel()"
                type="button"
                class="p-1 hover:bg-white/20 rounded-lg transition-colors duration-200"
                aria-label="Close chat"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    
    {{-- Mode Toggle Switch (Regular Users) --}}
    <div x-show="!isSuperadmin" class="flex items-center gap-2 bg-white/10 rounded-lg p-1">
        <button
            @click="switchMode('superadmin')"
            type="button"
            class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200"
            :class="{
                'bg-white text-primary-600 shadow-sm': mode === 'superadmin',
                'text-white/80 hover:text-white': mode !== 'superadmin'
            }"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Superadmin
        </button>
        
        <button
            @click="switchMode('chatbot')"
            type="button"
            class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200"
            :class="{
                'bg-white text-primary-600 shadow-sm': mode === 'chatbot',
                'text-white/80 hover:text-white': mode !== 'chatbot'
            }"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Chatbot
        </button>
    </div>
    
    {{-- Conversation Tab Switcher (Superadmin Only) --}}
    <div x-show="isSuperadmin && selectedUser" class="flex items-center gap-2 bg-white/10 rounded-lg p-1">
        <button
            @click="switchChatbotTab('messages')"
            type="button"
            class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200"
            :class="{
                'bg-white text-primary-600 shadow-sm': chatbotTab === 'messages',
                'text-white/80 hover:text-white': chatbotTab !== 'messages'
            }"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Pesan Superadmin
        </button>
        
        <button
            @click="switchChatbotTab('chatbot')"
            type="button"
            class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200"
            :class="{
                'bg-white text-primary-600 shadow-sm': chatbotTab === 'chatbot',
                'text-white/80 hover:text-white': chatbotTab !== 'chatbot'
            }"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Pesan Chatbot
        </button>
    </div>
</div>
