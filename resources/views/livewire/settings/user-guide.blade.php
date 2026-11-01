<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-8 shadow-xl border border-blue-800/40">
        <!-- Ambient Glow -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-10 w-52 h-52 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold backdrop-blur-sm">
                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Katalog Panduan Aplikasi Apotek</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Panduan Menu, Card & Cara Pakai
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Setiap kartu menu utama di bawah ini merangkum sub-menu terkait. Klik pada kartu sub-menu untuk melihat fungsi, sumber data setiap card/metrik, serta langkah operasional penggunaannya.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('guide.formulas') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-semibold transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Kamus Rumus</span>
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative mt-6 z-10">
            <div class="relative max-w-2xl">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-200/70">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Cari menu, sub-menu, card, atau cara pakai... (contoh: Laporan Penjualan, Aging, HPP, PO, Kasir)" 
                    class="w-full pl-11 pr-24 py-2.5 sm:py-3 rounded-xl bg-white/10 hover:bg-white/15 focus:bg-white text-white focus:text-gray-900 placeholder-blue-200/70 focus:placeholder-gray-400 border border-white/20 focus:border-blue-500 shadow-inner focus:outline-none focus:ring-4 focus:ring-blue-500/30 text-xs sm:text-sm transition-all backdrop-blur-md">
                @if($search)
                <button 
                    type="button" 
                    wire:click="$set('search', '')" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-blue-200 hover:text-white transition-colors cursor-pointer">
                    Hapus
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Filter Chips (Tampilan Pill Sesuai Permintaan) -->
    <div class="flex flex-wrap items-center gap-2 pt-1">
        @foreach($categories as $cat)
            @php
                $isActive = $activeCategory === $cat['key'];
            @endphp
            <button 
                type="button" 
                wire:click="setCategory('{{ $cat['key'] }}')"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center gap-1.5 cursor-pointer shadow-2xs
                    {{ $isActive 
                        ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400/50' 
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 hover:border-gray-300' }}">
                <span>{{ $cat['label'] }}</span>
            </button>
        @endforeach
    </div>

    <!-- Stats & Filters Status -->
    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 px-1">
        <div>
            Menampilkan <span class="font-bold text-gray-900 dark:text-white">{{ $matchedGroups }}</span> menu utama (<span class="font-bold text-gray-900 dark:text-white">{{ $matchedSubMenus }}</span> sub-menu)
            @if($search)
                untuk kata kunci "<span class="font-bold text-blue-600 dark:text-blue-400">{{ $search }}</span>"
            @endif
        </div>
        @if($search || $activeCategory !== 'all')
            <button 
                type="button" 
                wire:click="resetFilters" 
                class="text-blue-600 dark:text-blue-400 hover:underline font-semibold text-xs cursor-pointer">
                Reset Filter
            </button>
        @endif
    </div>

    <!-- Daftar Card Menu Utama yang di dalamnya terdapat Card Sub-Menu -->
    @if(count($groups) > 0)
    <div class="space-y-6">
        @foreach($groups as $group)
        @php
            $subCount = count($group['sub_menus']);
        @endphp
        <!-- Card Menu Utama -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/90 dark:border-gray-700 shadow-sm p-5 sm:p-6 space-y-4">
            
            <!-- Header Menu Utama -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100 dark:border-gray-750">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold shrink-0 border border-blue-100 dark:border-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 dark:text-white">
                                {{ $group['title'] }}
                            </h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700/60">
                                {{ $subCount }} Sub-Menu
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $group['description'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Grid Card-Card Sub-Menu di Dalamnya -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pt-1">
                @foreach($group['sub_menus'] as $sub)
                @php
                    $cardCount = count($sub['cards'] ?? []);
                    $stepCount = count($sub['steps'] ?? []);
                @endphp
                <div class="bg-gray-50/70 dark:bg-gray-750/30 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div class="space-y-2.5">
                        <!-- Top Tag -->
                        <div class="flex items-center justify-between gap-1 text-[11px] font-mono text-gray-400 dark:text-gray-500">
                            <span class="truncate">{{ $sub['route_url'] }}</span>
                        </div>

                        <!-- Sub-menu Title -->
                        <div>
                            <h3 class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $sub['title'] }}
                            </h3>
                            <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $sub['function'] }}
                            </p>
                        </div>

                        <!-- Indicators -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            @if($cardCount > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-semibold border border-emerald-200/60 dark:border-emerald-800/40">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                <span>{{ $cardCount }} Card</span>
                            </span>
                            @endif

                            @if($stepCount > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-semibold border border-indigo-200/60 dark:border-indigo-800/40">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span>{{ $stepCount }} Langkah</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 mt-3 border-t border-gray-200/80 dark:border-gray-700/80 flex items-center gap-1.5">
                        <button 
                            type="button" 
                            wire:click="openSubMenuDetail('{{ $sub['slug'] }}')"
                            class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition-colors cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span>Lihat Card & Cara Pakai</span>
                        </button>

                        @if(!empty($sub['route_name']) && Route::has($sub['route_name']))
                        <a href="{{ route($sub['route_name']) }}" wire:navigate class="p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white dark:hover:bg-gray-700 transition-colors shrink-0" title="Buka Menu Asli">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center space-y-4">
        <div class="w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="space-y-1">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Tidak ada menu yang cocok</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                Tidak ditemukan panduan menu untuk pencarian "{{ $search }}". Silakan gunakan kata kunci lain.
            </p>
        </div>
        <button 
            type="button" 
            wire:click="resetFilters" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors shadow-sm cursor-pointer">
            <span>Reset Pencarian</span>
        </button>
    </div>
    @endif

    <!-- Detail Modal Sub-Menu -->
    @if($selectedSubMenu)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" wire:click="closeDetail"></div>

        <div class="flex min-h-screen items-center justify-center p-3 sm:p-4 text-center">
            <div class="relative w-full max-w-4xl transform rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all border border-gray-200 dark:border-gray-700 overflow-hidden my-6">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-850 dark:to-gray-800 flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700/60">
                                Menu: {{ $selectedSubMenu['parent_title'] }}
                            </span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                {{ $selectedSubMenu['route_url'] }}
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 dark:text-white">
                            {{ $selectedSubMenu['title'] }}
                        </h2>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if(!empty($selectedSubMenu['route_name']) && Route::has($selectedSubMenu['route_name']))
                        <a href="{{ route($selectedSubMenu['route_name']) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800 transition-colors">
                            <span>Buka Halaman</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                        @endif

                        <button 
                            type="button" 
                            wire:click="closeDetail" 
                            class="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                            title="Tutup (Esc)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-5 sm:p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    
                    <!-- 1. Fungsi Menu -->
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>1. Fungsi & Kegunaan Sub-Menu Ini</span>
                        </div>
                        <div class="p-4 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 text-xs sm:text-sm text-gray-800 dark:text-gray-200 leading-relaxed">
                            {{ $selectedSubMenu['function'] }}
                        </div>
                    </div>

                    <!-- 2. Rincian Card & Asal Datanya -->
                    @if(!empty($selectedSubMenu['cards']))
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                <span>2. Rincian Card & Asal Datanya (Sumber Perhitungan)</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ count($selectedSubMenu['cards']) }} Card</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            @foreach($selectedSubMenu['cards'] as $card)
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-750/30 p-4 space-y-2.5 flex flex-col justify-between">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-xs sm:text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        {{ $card['name'] }}
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                        {{ $card['value_desc'] }}
                                    </p>
                                </div>

                                <div class="space-y-2 pt-2 border-t border-gray-200/80 dark:border-gray-700/80 text-xs">
                                    @if(!empty($card['formula']))
                                    <div class="p-2 rounded-lg bg-slate-900 text-emerald-300 font-mono text-[11px] leading-tight select-all">
                                        <strong>Rumus:</strong> {{ $card['formula'] }}
                                    </div>
                                    @endif
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        <strong>Sumber Data:</strong> {{ $card['source'] }}
                                    </div>
                                    @if(!empty($card['notes']))
                                    <div class="text-[11px] text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/30 px-2.5 py-1.5 rounded-md border border-amber-200/60 dark:border-amber-900/40">
                                        💡 {{ $card['notes'] }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 3. Cara Menggunakan (Langkah demi Langkah) -->
                    @if(!empty($selectedSubMenu['steps']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>3. Cara Menggunakannya (Langkah demi Langkah Operasional)</span>
                        </div>

                        <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/30 dark:bg-indigo-950/20 p-4 sm:p-5">
                            <ol class="space-y-2.5 text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                                @foreach($selectedSubMenu['steps'] as $idx => $step)
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div class="leading-relaxed flex-1 pt-0.5">
                                        {!! $step !!}
                                    </div>
                                </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-850 flex items-center justify-between gap-3">
                    <button 
                        type="button" 
                        wire:click="closeDetail" 
                        class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-750 transition-colors cursor-pointer">
                        Tutup
                    </button>

                    @if(!empty($selectedSubMenu['route_name']) && Route::has($selectedSubMenu['route_name']))
                    <a href="{{ route($selectedSubMenu['route_name']) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors shadow-sm">
                        <span>Buka Halaman {{ $selectedSubMenu['title'] }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
