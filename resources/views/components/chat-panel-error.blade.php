<div 
    x-show="errorMessage"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="flex-shrink-0 px-4 py-2 border-t"
    :class="{
        'bg-red-50 border-red-100': !errorMessage.includes('berhasil'),
        'bg-green-50 border-green-100': errorMessage.includes('berhasil')
    }"
>
    <div class="flex items-start gap-2">
        <svg 
            class="w-5 h-5 flex-shrink-0 mt-0.5" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
            :class="{
                'text-red-500': !errorMessage.includes('berhasil'),
                'text-green-500': errorMessage.includes('berhasil')
            }"
        >
            <path 
                x-show="!errorMessage.includes('berhasil')"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
            <path 
                x-show="errorMessage.includes('berhasil')"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
        </svg>
        <div class="flex-1">
            <p 
                class="text-sm" 
                :class="{
                    'text-red-800': !errorMessage.includes('berhasil'),
                    'text-green-800': errorMessage.includes('berhasil')
                }"
                x-text="errorMessage"
            ></p>
            
            {{-- Retry Button for Queued Messages --}}
            <button
                x-show="messageQueue.length > 0 && connectionStatus === 'online'"
                @click="sendQueuedMessages()"
                type="button"
                class="mt-2 px-3 py-1 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors duration-200"
            >
                Coba Kirim Ulang (<span x-text="messageQueue.length"></span>)
            </button>
        </div>
        <button
            @click="errorMessage = ''"
            type="button"
            :class="{
                'text-red-500 hover:text-red-700': !errorMessage.includes('berhasil'),
                'text-green-500 hover:text-green-700': errorMessage.includes('berhasil')
            }"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
