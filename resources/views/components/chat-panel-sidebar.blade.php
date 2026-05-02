<div 
    x-show="isSuperadmin && showUserList"
    class="w-64 border-r border-slate-200 bg-slate-50 flex flex-col"
>
    {{-- Sidebar Header --}}
    <div class="flex-shrink-0 px-4 py-3 border-b border-slate-200 bg-white">
        <h4 class="text-sm font-semibold text-slate-700">Pengguna</h4>
        <p class="text-xs text-slate-500 mt-0.5">
            <span x-text="users.length"></span> percakapan
        </p>
    </div>
    
    {{-- User List --}}
    <div class="flex-1 overflow-y-auto">
        {{-- Loading State --}}
        <div x-show="isLoadingUsers" class="p-4 text-center">
            <div class="inline-flex items-center gap-2 text-slate-500">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm">Memuat...</span>
            </div>
        </div>
        
        {{-- Empty State --}}
        <div x-show="!isLoadingUsers && users.length === 0" class="p-4 text-center">
            <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-sm text-slate-600">Belum ada percakapan</p>
        </div>
        
        {{-- User Items --}}
        <template x-for="user in users" :key="user.id">
            <button
                @click="selectUser(user)"
                type="button"
                class="w-full px-4 py-3 flex items-start gap-3 hover:bg-white transition-colors duration-150 border-b border-slate-100"
                :class="{
                    'bg-white border-l-4 border-l-primary-600': selectedUserId === user.id,
                    'border-l-4 border-l-transparent': selectedUserId !== user.id
                }"
            >
                {{-- Avatar --}}
                <div class="flex-shrink-0 relative">
                    <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold text-white bg-gradient-to-br from-primary-500 to-primary-600"
                    >
                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                    </div>
                    
                    {{-- Unread Badge --}}
                    <span 
                        x-show="user.unread_count > 0"
                        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"
                        x-text="user.unread_count > 9 ? '9+' : user.unread_count"
                    ></span>
                </div>
                
                {{-- User Info --}}
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between mb-0.5">
                        <h5 
                            class="text-sm font-semibold text-slate-900 truncate"
                            :class="{ 'text-primary-600': selectedUserId === user.id }"
                            x-text="user.name"
                        ></h5>
                        <span 
                            x-show="user.last_message"
                            class="text-xs text-slate-500 flex-shrink-0 ml-2"
                            x-text="user.last_message ? formatTime(user.last_message.created_at) : ''"
                        ></span>
                    </div>
                    
                    <p 
                        x-show="user.last_message"
                        class="text-xs text-slate-600 truncate"
                        :class="{ 'font-medium': user.unread_count > 0 }"
                        x-text="user.last_message ? user.last_message.content : ''"
                    ></p>
                    
                    <p 
                        x-show="!user.last_message"
                        class="text-xs text-slate-400 italic"
                    >
                        Belum ada pesan
                    </p>
                </div>
            </button>
        </template>
    </div>
</div>
