{{-- Floating Chat Button Component --}}
<div 
    x-data="{
        isOpen: false,
        unreadCount: 0,
        hasNewMessage: false,
        soundEnabled: true,
        
        init() {
            // Load sound preference from localStorage
            const savedSoundPref = localStorage.getItem('chat_sound_enabled');
            if (savedSoundPref !== null) {
                this.soundEnabled = savedSoundPref === 'true';
            }
            
            // Load unread count on initialization
            this.loadUnreadCount();
            
            // Listen for chat-message-received event
            window.addEventListener('chat-message-received', (event) => {
                this.handleNewMessage(event.detail);
            });
            
            // Listen for chat panel state changes
            window.addEventListener('chat-panel-opened', () => {
                this.isOpen = true;
            });
            
            window.addEventListener('chat-panel-closed', () => {
                this.isOpen = false;
            });
            
            // Listen for unread count updates
            window.addEventListener('chat-unread-updated', (event) => {
                this.unreadCount = event.detail.count || 0;
            });
            
            // Listen for sound toggle from chat panel
            window.addEventListener('chat-sound-toggled', (event) => {
                this.soundEnabled = event.detail.enabled;
            });
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            
            // Dispatch custom event
            const eventName = this.isOpen ? 'chat-panel-open' : 'chat-panel-close';
            window.dispatchEvent(new CustomEvent(eventName));
            
            // If opening, mark messages as read
            if (this.isOpen) {
                this.hasNewMessage = false;
            }
        },
        
        handleNewMessage(message) {
            // Only update if chat is closed
            if (!this.isOpen) {
                this.unreadCount++;
                this.hasNewMessage = true;
                
                // Play notification sound if enabled
                if (this.soundEnabled) {
                    this.playNotificationSound();
                }
                
                // Show visual indicator
                this.showNewMessageIndicator();
            }
        },
        
        playNotificationSound() {
            if (!this.soundEnabled) return;
            
            try {
                const audio = new Audio('/sounds/beep-beep.mp3');
                audio.volume = 0.3;
                audio.play().catch(err => {
                    console.log('Could not play notification sound:', err);
                });
            } catch (error) {
                console.log('Notification sound error:', error);
            }
        },
        
        showNewMessageIndicator() {
            // Trigger pulse animation
            this.hasNewMessage = true;
            
            // Reset after animation
            setTimeout(() => {
                this.hasNewMessage = false;
            }, 3000);
        },
        
        async loadUnreadCount() {
            try {
                const url = window.chatConfig?.routes?.unreadCount || '{{ route('admin.chat.unread-count') }}';
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.unreadCount = data.unread_count || 0;
                }
            } catch (error) {
                console.error('Failed to load unread count:', error);
            }
        },
        
        updateUnreadCount(count) {
            this.unreadCount = count;
        }
    }"
    class="fixed bottom-6 right-6 z-50"
>
    {{-- Floating Chat Button --}}
    <button
        @click="toggleChat()"
        type="button"
        class="chat-button relative w-14 h-14 rounded-full bg-primary-600 text-white shadow-float hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300 transition-all duration-300 flex items-center justify-center group"
        :class="{ 
            'scale-110': hasNewMessage,
            'animate-pulse': hasNewMessage 
        }"
        aria-label="Toggle chat"
    >
        {{-- Chat Icon --}}
        <svg 
            class="w-6 h-6 transition-transform duration-300"
            :class="{ 'rotate-12': hasNewMessage }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
        </svg>
        
        {{-- Unread Count Badge --}}
        <span
            x-show="unreadCount > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-50"
            class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full border-2 border-white shadow-sm"
            x-text="unreadCount > 99 ? '99+' : unreadCount"
        ></span>
        
        {{-- New Message Indicator (Pulse Ring) --}}
        <span
            x-show="hasNewMessage"
            class="absolute inset-0 rounded-full bg-primary-400 animate-ping opacity-75"
        ></span>
        
        {{-- Hover Tooltip --}}
        <span 
            class="absolute bottom-full right-0 mb-2 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none"
        >
            <span x-show="!isOpen">Buka Chat</span>
            <span x-show="isOpen">Tutup Chat</span>
            <svg class="absolute top-full right-4 w-2 h-2 text-slate-900" viewBox="0 0 8 8">
                <path fill="currentColor" d="M0 0l4 4 4-4z"/>
            </svg>
        </span>
    </button>
</div>

<style>
    /* Custom shadow for floating effect */
    .shadow-float {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Ensure button stays on top */
    .chat-button {
        position: relative;
        z-index: 9999;
    }
    
    /* Smooth animations */
    @keyframes pulse-ring {
        0% {
            transform: scale(1);
            opacity: 0.75;
        }
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }
    
    .animate-pulse-ring {
        animation: pulse-ring 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
