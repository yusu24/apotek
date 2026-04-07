<div class="relative" x-data="{ open: false }">
    <!-- Menu-style Item -->
    <button @click="open = !open" 
            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-gray-800 hover:text-white transition-colors group">
        <div class="relative flex items-center justify-center">
            <svg class="w-5 h-5 shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            @if($this->unreadCount > 0)
                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] items-center justify-center text-white font-bold border-2 border-gray-900">
                        {{ $this->unreadCount }}
                    </span>
                </span>
            @endif
        </div>
        
        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Notifikasi</span>
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="absolute bottom-full left-0 mb-2 w-72 bg-gray-900 border border-gray-800 rounded-xl shadow-2xl z-[100] overflow-hidden"
         :class="$store.sidebar.collapsed ? 'xl:left-full xl:bottom-0 xl:mb-0 xl:ml-2' : ''">
        
        <div class="p-3 border-b border-gray-800 bg-gray-950/50 flex justify-between items-center">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Notifikasi</h3>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-blue-400 hover:text-blue-300 transition-colors">Semua Dibaca</button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto scrollbar-hide">
            @forelse($this->recentNotifications as $notification)
                <div class="p-3 border-b border-gray-800 hover:bg-gray-800/50 transition-colors cursor-pointer group {{ $notification->read_at ? 'opacity-60' : '' }}"
                     wire:click="markAsRead('{{ $notification->id }}')">
                    <div class="flex gap-3">
                        <div class="shrink-0 mt-0.5">
                            @if($notification->data['type'] === 'piutang')
                                <div class="w-8 h-8 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-orange-500/20 text-orange-500 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-200 leading-normal whitespace-normal break-words">{{ $notification->data['message'] }}</p>
                            <p class="text-[10px] text-gray-500 mt-1 italic">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @unless($notification->read_at)
                            <div class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-1.5"></div>
                        @endunless
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-10 h-10 text-gray-800 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-xs text-gray-600 italic">Tidak ada notifikasi baru.</p>
                </div>
            @endforelse
        </div>
        
        @if($this->unreadCount > 0 || $this->recentNotifications->count() > 0)
            <div class="p-2 border-t border-gray-800 bg-gray-950/30 text-center">
                <span class="text-[10px] text-gray-500 font-medium">Notifikasi akan diperiksa secara berkala otomatis.</span>
            </div>
        @endif
    </div>
</div>
