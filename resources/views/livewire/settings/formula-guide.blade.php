<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-8 shadow-xl border border-blue-800/40">
        <!-- Decorative Glow -->
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-12 w-60 h-60 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold backdrop-blur-sm">
                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Transparansi Perhitungan & Logika Bisnis</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Kamus Rumus & Logika Perhitungan Sistem
                </h1>
                <p class="text-sm sm:text-base text-blue-100/90 leading-relaxed">
                    Panduan lengkap rumus matematis dan sumber data yang digunakan pada setiap menu aplikasi apotek. Pelajari bagaimana angka Pendapatan, HPP, Laba Bersih, Beban Operasional, Margin Obat, hingga Aging Hutang-Piutang dihitung secara akurat.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('guide.index') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs sm:text-sm font-semibold transition-all backdrop-blur-sm shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Panduan</span>
                </a>
            </div>
        </div>

        <!-- Search Bar Inside Banner -->
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
                    placeholder="Cari nama rumus, istilah, atau menu... (contoh: Laba Bersih, HPP, PPh Final, ROP, Aging, Margin)" 
                    class="w-full pl-11 pr-24 py-3 rounded-xl bg-white/10 hover:bg-white/15 focus:bg-white text-white focus:text-gray-900 placeholder-blue-200/70 focus:placeholder-gray-400 border border-white/20 focus:border-blue-500 shadow-inner focus:outline-none focus:ring-4 focus:ring-blue-500/30 text-sm transition-all backdrop-blur-md">
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

    <!-- Category Filter Tabs (Wrap Tanpa Scroll Samping) -->
    <div class="flex flex-wrap items-center gap-2 pt-1">
        @foreach($categories as $cat)
            @php
                $isActive = $activeCategory === $cat['key'];
            @endphp
            <button 
                type="button"
                wire:click="setCategory('{{ $cat['key'] }}')"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 whitespace-nowrap flex items-center gap-2 cursor-pointer shadow-xs
                    {{ $isActive 
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30 ring-2 ring-blue-400/50' 
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 hover:border-gray-300' }}">
                <span>{{ $cat['label'] }}</span>
            </button>
        @endforeach
    </div>

    <!-- Stats & Result Indicator -->
    <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500 dark:text-gray-400 px-1">
        <div>
            Menampilkan <span class="font-bold text-gray-900 dark:text-white">{{ $filteredCount }}</span> dari total <span class="font-bold text-gray-900 dark:text-white">{{ $totalCount }}</span> rumus perhitungan sistem
            @if($search)
                untuk pencarian "<span class="font-bold text-blue-600 dark:text-blue-400">{{ $search }}</span>"
            @endif
        </div>
        @if($search || $activeCategory !== 'all')
            <button 
                type="button" 
                wire:click="resetFilters" 
                class="text-blue-600 dark:text-blue-400 hover:underline font-semibold text-xs cursor-pointer">
                Reset Semua Filter
            </button>
        @endif
    </div>

    <!-- Formula Cards Grid -->
    @if(count($formulas) > 0)
    <div class="grid grid-cols-1 gap-6">
        @foreach($formulas as $item)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group">
            <!-- Card Header -->
            <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-750 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-gray-50/50 to-white dark:from-gray-800 dark:to-gray-800">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700/60">
                            {{ $item['category_label'] }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Menu: <strong class="text-gray-700 dark:text-gray-200 font-semibold">{{ $item['menu_name'] }}</strong>
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $item['title'] }}
                    </h3>
                </div>

                @if(!empty($item['menu_route']) && Route::has($item['menu_route']))
                <a href="{{ route($item['menu_route']) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800 transition-colors shrink-0">
                    <span>Buka Menu</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
                @endif
            </div>

            <!-- Card Body -->
            <div class="p-5 sm:p-6 space-y-6">
                <!-- Formula Highlight Box -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        Formula / Rumus Matematis:
                    </label>
                    <div class="p-4 rounded-xl bg-slate-900 text-slate-100 font-mono text-sm sm:text-base border border-slate-800 shadow-inner flex items-center justify-between gap-4 overflow-x-auto">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                            <span class="font-semibold text-emerald-300 select-all">{{ $item['formula'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ $item['description'] }}
                    </p>
                </div>

                <!-- Variables Breakdown Table -->
                @if(!empty($item['variables']))
                <div class="bg-gray-50 dark:bg-gray-750/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2.5">
                        Komponen & Sumber Data:
                    </label>
                    <div class="space-y-2">
                        @foreach($item['variables'] as $var)
                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-3 text-xs sm:text-sm">
                            <span class="font-bold text-gray-800 dark:text-gray-200 sm:w-48 shrink-0 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                {{ $var['name'] }}
                            </span>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $var['desc'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Realistic Example Scenario -->
                @if(!empty($item['example']))
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/60 dark:bg-emerald-950/20 p-4">
                    <div class="flex items-center gap-2 mb-2 text-emerald-800 dark:text-emerald-300 font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Contoh Simulasi Nyata</span>
                    </div>
                    <p class="text-xs sm:text-sm text-emerald-950 dark:text-emerald-200 leading-relaxed mb-2">
                        {{ $item['example']['scenario'] }}
                    </p>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-emerald-200/80 dark:border-emerald-800/40 text-xs sm:text-sm">
                        <div class="font-mono text-emerald-900 dark:text-emerald-300">
                            <strong>Perhitungan:</strong> {{ $item['example']['calc'] }}
                        </div>
                        <div class="font-bold text-emerald-700 dark:text-emerald-400 bg-white dark:bg-emerald-900/60 px-3 py-1 rounded-lg border border-emerald-300 dark:border-emerald-700 self-start sm:self-auto">
                            Hasil: {{ $item['example']['result'] }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Note / Tip -->
                @if(!empty($item['note']))
                <div class="flex items-start gap-2.5 text-xs text-amber-800 dark:text-amber-300 bg-amber-50/80 dark:bg-amber-950/30 p-3 rounded-lg border border-amber-200/80 dark:border-amber-800/50">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <strong class="font-semibold">Catatan Farmasi & Akuntansi:</strong> {{ $item['note'] }}
                    </div>
                </div>
                @endif
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
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tidak ada rumus yang cocok</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                Tidak ditemukan rumus yang sesuai dengan kriteria pencarian "{{ $search }}". Silakan gunakan kata kunci lain atau pilih kategori Semua Rumus.
            </p>
        </div>
        <button 
            type="button" 
            wire:click="resetFilters" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shadow-sm">
            <span>Reset Pencarian</span>
        </button>
    </div>
    @endif
</div>
