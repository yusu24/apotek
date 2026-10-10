@php
$colorClasses = ($textColor === 'text-white')
    ? 'text-white hover:bg-gray-800/50 xl:hover:bg-gray-800 hover:text-white'
    : "$textColor hover:bg-gray-100 dark:hover:bg-gray-800";
@endphp

<div class="relative" x-data="{ open: false }">
    <!-- Menu-style Item -->
    <button @click="open = !open" 
            type="button"
            title="{{ count($onlineUsers) }} pengguna sedang aktif"
            @class([
                'rounded-xl text-sm font-medium transition-colors group relative flex items-center justify-center',
                'p-2' => $iconOnly,
                'w-full px-3 py-2 gap-3' => !$iconOnly,
                $colorClasses
            ])
            wire:poll.30s.keep-alive="$refresh">
        
        <div class="relative flex items-center justify-center">
            <svg class="w-5 h-5 shrink-0 transition-all duration-300" :class="!{{ $iconOnly ? 'true' : 'false' }} && $store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            
            @if(count($onlineUsers) > 0)
                <span style="min-width: 18px; height: 18px;" class="absolute -top-2 -right-2.5 inline-flex items-center justify-center px-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold leading-none tracking-tight shadow-sm ring-2 {{ $textColor === 'text-white' ? 'ring-blue-950' : 'ring-white dark:ring-gray-900' }}">
                    {{ count($onlineUsers) }}
                </span>
            @endif
        </div>
        
        @unless($iconOnly)
            <span class="truncate text-white" :class="{'xl:hidden': $store.sidebar.collapsed}">Pengguna Aktif ({{ count($onlineUsers) }})</span>
        @endunless
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
         class="absolute {{ $direction === 'down' ? 'top-full mt-2' : 'bottom-full mb-2' }} w-72 rounded-xl shadow-2xl z-[100] overflow-hidden border right-0 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200"
         :class="!{{ $iconOnly ? 'true' : 'false' }} && $store.sidebar.collapsed ? 'xl:left-full xl:bottom-0 xl:mb-0 xl:ml-2' : ''">
        
        <div class="p-3 border-b flex justify-between items-center border-gray-100 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
            <h3 class="text-xs font-bold tracking-widest flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Sedang aktif ({{ count($onlineUsers) }})
            </h3>
        </div>

        <div class="max-h-96 overflow-y-auto scrollbar-hide py-1">
            @forelse($onlineUsers as $user)
                <div class="px-4 py-2 transition-colors flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="relative shrink-0">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                        @else
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1.5">
                            <p class="text-sm font-medium truncate text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 shrink-0">
                                {{ $user->role_label }}
                            </span>
                        </div>
                        <p class="text-[10px] truncate text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center">
                    <p class="text-xs italic text-gray-400 dark:text-gray-500">Tidak ada pengguna yang sedang online.</p>
                </div>
            @endforelse

            @if(isset($offlineUsers) && count($offlineUsers) > 0)
                <div class="px-3 py-2 mt-1 border-t border-b flex justify-between items-center border-gray-100 bg-gray-50/70 dark:border-gray-700 dark:bg-gray-800/40">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-2 text-gray-400 dark:text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                        Offline ({{ count($offlineUsers) }})
                    </h4>
                </div>
                @foreach($offlineUsers as $user)
                    <div class="px-4 py-2 transition-colors flex items-center gap-3 opacity-70 hover:opacity-100 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="relative shrink-0">
                            @if ($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-700 grayscale">
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-gray-400 border-2 border-white dark:border-gray-800 rounded-full"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1.5">
                                <p class="text-xs font-medium truncate text-gray-700 dark:text-gray-300">{{ $user->name }}</p>
                                <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 shrink-0">
                                    {{ $user->role_label }}
                                </span>
                            </div>
                            <p class="text-[10px] truncate text-gray-400 dark:text-gray-500">
                                {{ $user->last_seen_human ? 'Aktif ' . $user->last_seen_human : $user->email }}
                            </p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
    </div>
</div>
