<div class="relative" x-data="{ open: false }">
    <!-- Menu-style Item -->
    <button @click="open = !open" 
            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-gray-800 hover:text-white transition-colors group"
            wire:poll.keep-alive="30s">
        
        <div class="relative flex items-center justify-center">
            <svg class="w-5 h-5 shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            
            @if(count($onlineUsers) > 0)
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 border border-gray-900"></span>
                </span>
            @endif
        </div>
        
        <span class="truncate text-white" :class="{'xl:hidden': $store.sidebar.collapsed}">Pengguna Aktif ({{ count($onlineUsers) }})</span>
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
         class="absolute bottom-full left-0 mb-2 w-72 bg-blue-950 border border-blue-800/50 rounded-xl shadow-2xl z-[100] overflow-hidden"
         :class="$store.sidebar.collapsed ? 'xl:left-full xl:bottom-0 xl:mb-0 xl:ml-2' : ''">
        
        <div class="p-3 border-b border-blue-800/50 bg-blue-900/50 flex justify-between items-center">
            <h3 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Sedang Aktif ({{ count($onlineUsers) }})
            </h3>
        </div>

        <div class="max-h-80 overflow-y-auto scrollbar-hide py-1">
            @forelse($onlineUsers as $user)
                <div class="px-4 py-2 hover:bg-blue-800/50 transition-colors flex items-center gap-3">
                    @if ($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-blue-800/50 shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                        <p class="text-[10px] text-blue-200/70 truncate">{{ $user->email }}</p>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center">
                    <p class="text-xs text-blue-200/70 italic">Tidak ada pengguna yang aktif selain Anda.</p>
                </div>
            @endforelse
        </div>
        
    </div>
</div>
