<x-app-layout>
    @canany(['view dashboard', 'view reports', 'view sales reports', 'view stock', 'view financial overview', 'view profit loss', 'view balance sheet', 'view income statement', 'view general ledger'])
    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Dashboard') }}
            </h2>
        </div>

        <div class="space-y-8">
            <!-- Dashboard Welcome Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-indigo-700 to-slate-900 p-6 md:p-8 text-white shadow-xl">
                <!-- Ambient Glow & Pattern Elements -->
                <div class="absolute -right-16 -top-16 h-72 w-72 rounded-full bg-blue-400/20 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl pointer-events-none"></div>
                <div class="absolute top-1/2 right-1/4 h-48 w-48 rounded-full bg-cyan-400/10 blur-2xl pointer-events-none"></div>
                
                {{-- Decorative SVG Geometric Lines --}}
                <svg class="absolute right-0 top-0 h-full w-1/3 opacity-10 pointer-events-none" viewBox="0 0 400 400" fill="none">
                    <circle cx="200" cy="200" r="160" stroke="currentColor" stroke-width="1.5" stroke-dasharray="8 8" />
                    <circle cx="200" cy="200" r="120" stroke="currentColor" stroke-width="1.5" />
                    <circle cx="200" cy="200" r="80" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" />
                </svg>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <!-- Left: Welcome & Status -->
                    <div class="lg:col-span-6 xl:col-span-7 space-y-4">
                        <!-- Live Status Pill & Date -->
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-medium text-blue-100 shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            <span class="font-semibold text-emerald-300">Sistem Aktif</span>
                            <span class="text-white/40">•</span>
                            <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                        </div>

                        <!-- Greeting Title -->
                        @php
                            $hour = now()->hour;
                            $greeting = match(true) {
                                $hour < 11 => 'Selamat Pagi',
                                $hour < 15 => 'Selamat Siang',
                                $hour < 18 => 'Selamat Sore',
                                default => 'Selamat Malam',
                            };
                            $roleName = auth()->user()->getRoleNames()->first() ?? 'Staff';
                        @endphp
                        <div>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white flex items-center gap-2">
                                {{ $greeting }}, {{ auth()->user()->name }}! 
                                <span class="inline-block animate-bounce">👋</span>
                            </h1>
                            <p class="mt-2 text-sm sm:text-base text-blue-100/90 max-w-xl leading-relaxed">
                                Selamat datang kembali di <span class="font-bold text-white underline decoration-cyan-400 decoration-2 underline-offset-4">Apotek.POS</span>. Pantau arus stok, performa penjualan kasir, dan keuangan apotek secara terpadu.
                            </p>
                        </div>

                        <!-- Quick Badges / Actions -->
                        <div class="flex flex-wrap items-center gap-2.5 pt-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/10 backdrop-blur-sm border border-white/15 text-xs text-blue-200">
                                <svg class="w-3.5 h-3.5 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Role: <strong class="text-white capitalize">{{ $roleName }}</strong>
                            </span>

                            @can('view stock')
                            <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 backdrop-blur-sm border border-blue-400/30 text-xs text-blue-100 hover:text-white transition duration-150">
                                <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Cek Inventaris Stok
                            </a>
                            @endcan

                            @can('view sales reports')
                            <a href="{{ route('reports.sales') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/30 backdrop-blur-sm border border-indigo-400/30 text-xs text-indigo-100 hover:text-white transition duration-150">
                                <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Laporan Penjualan
                            </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Right: Quick Stat Glance Cards -->
                    @php
                        $totalProducts = \App\Models\Product::count();
                        $lowStockCount = \App\Models\Product::lowStock()->count();
                        $todaySalesCount = \App\Models\Sale::whereDate('date', today())->where('status', 'completed')->count();
                        $todaySalesTotal = \App\Models\Sale::whereDate('date', today())->where('status', 'completed')->sum('grand_total');
                    @endphp
                    <div class="lg:col-span-6 xl:col-span-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Stat 1: Total Katalog -->
                        <div class="group relative overflow-hidden rounded-2xl bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/20 p-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="p-2 rounded-xl bg-blue-500/20 border border-blue-400/30 text-cyan-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-200/80 bg-blue-500/20 px-2 py-0.5 rounded-full">Katalog</span>
                            </div>
                            <div class="text-2xl font-black text-white tracking-tight">{{ number_format($totalProducts) }}</div>
                            <div class="text-xs text-blue-200 mt-0.5">Total Obat & Alkes</div>
                        </div>

                        <!-- Stat 2: Stok Menipis -->
                        <div class="group relative overflow-hidden rounded-2xl bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/20 p-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="p-2 rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-500/25 border-amber-400/40 text-amber-300' : 'bg-emerald-500/20 border-emerald-400/30 text-emerald-300' }} border">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider {{ $lowStockCount > 0 ? 'text-amber-200 bg-amber-500/25' : 'text-emerald-200 bg-emerald-500/25' }} px-2 py-0.5 rounded-full">
                                    {{ $lowStockCount > 0 ? 'Alert' : 'Aman' }}
                                </span>
                            </div>
                            <div class="text-2xl font-black text-white tracking-tight">{{ number_format($lowStockCount) }}</div>
                            <div class="text-xs text-blue-200 mt-0.5">Perlu Restock</div>
                        </div>

                        <!-- Stat 3: Transaksi Hari Ini -->
                        <div class="group relative overflow-hidden rounded-2xl bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/20 p-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="p-2 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-200/90 bg-emerald-500/20 px-2 py-0.5 rounded-full">Hari Ini</span>
                            </div>
                            <div class="text-2xl font-black text-white tracking-tight">{{ number_format($todaySalesCount) }}</div>
                            <div class="text-xs text-blue-200 mt-0.5 truncate" title="Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}">
                                Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Turnover and Cashier Leaderboard -->
            <livewire:dashboard.sales-leaderboard />

            <!-- Performance Components -->
            <livewire:dashboard.product-performance />

            <!-- Financial Overview -->
            <livewire:dashboard.financial-overview />
        </div>
    </div>
    @else
    <div class="min-h-[60vh] flex items-center justify-center">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Akses Terbatas</h3>
            <p class="mt-1 text-sm text-gray-500">Anda tidak memiliki izin untuk melihat dashboard.</p>
        </div>
    </div>
    @endcan
</x-app-layout>
