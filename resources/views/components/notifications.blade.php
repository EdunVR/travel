{{-- Toast Notification Component --}}
<div x-data 
     x-show="$store.notifications.items.length > 0"
     class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full pointer-events-none">
  
  <template x-for="notification in $store.notifications.items" :key="notification.id">
    <div x-show="notification.visible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-x-full opacity-0"
         x-transition:enter-end="transform translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="transform translate-x-0 opacity-100"
         x-transition:leave-end="transform translate-x-full opacity-0"
         class="pointer-events-auto rounded-xl shadow-lg border overflow-hidden"
         :class="{
           'bg-green-50 border-green-200': notification.type === 'success',
           'bg-red-50 border-red-200': notification.type === 'error',
           'bg-blue-50 border-blue-200': notification.type === 'info',
           'bg-yellow-50 border-yellow-200': notification.type === 'warning'
         }">
      
      <div class="p-4 flex items-start gap-3">
        {{-- Icon --}}
        <div class="flex-shrink-0">
          <template x-if="notification.type === 'success'">
            <i class="bx bx-check-circle text-2xl text-green-600"></i>
          </template>
          <template x-if="notification.type === 'error'">
            <i class="bx bx-error-circle text-2xl text-red-600"></i>
          </template>
          <template x-if="notification.type === 'info'">
            <i class="bx bx-info-circle text-2xl text-blue-600"></i>
          </template>
          <template x-if="notification.type === 'warning'">
            <i class="bx bx-error text-2xl text-yellow-600"></i>
          </template>
        </div>

        {{-- Message --}}
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium"
             :class="{
               'text-green-800': notification.type === 'success',
               'text-red-800': notification.type === 'error',
               'text-blue-800': notification.type === 'info',
               'text-yellow-800': notification.type === 'warning'
             }"
             x-text="notification.message"></p>
        </div>

        {{-- Close Button --}}
        <button @click="$store.notifications.remove(notification.id)"
                class="flex-shrink-0 rounded-lg p-1 hover:bg-black/5 transition-colors"
                :class="{
                  'text-green-600': notification.type === 'success',
                  'text-red-600': notification.type === 'error',
                  'text-blue-600': notification.type === 'info',
                  'text-yellow-600': notification.type === 'warning'
                }">
          <i class="bx bx-x text-xl"></i>
        </button>
      </div>
    </div>
  </template>
</div>
