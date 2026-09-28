<x-app-layout>
    @canany(['view dashboard', 'view reports', 'view sales reports', 'view stock', 'view financial overview', 'view profit loss', 'view balance sheet', 'view income statement', 'view general ledger'])
    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Dashboard') }}
            </h2>
        </div>

        <div class="space-y-6">
            <!-- Dashboard Welcome Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-indigo-700 to-slate-900 p-6 md:p-8 text-white shadow-xl"
                 style="background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 50%, #0f172a 100%); color: #ffffff;">
                <!-- Ambient Glow & Pattern Elements -->
                <div class="absolute -right-16 -top-16 h-72 w-72 rounded-full bg-blue-400/20 blur-3xl pointer-events-none" style="background-color: rgba(96, 165, 250, 0.2); filter: blur(48px);"></div>
                <div class="absolute -left-16 -bottom-16 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl pointer-events-none" style="background-color: rgba(168, 85, 247, 0.2); filter: blur(48px);"></div>
                
                {{-- Decorative SVG Geometric Lines --}}
                <svg class="absolute right-0 top-0 h-full w-1/3 opacity-10 pointer-events-none" viewBox="0 0 400 400" fill="none">
                    <circle cx="200" cy="200" r="160" stroke="currentColor" stroke-width="1.5" stroke-dasharray="8 8" />
                    <circle cx="200" cy="200" r="120" stroke="currentColor" stroke-width="1.5" />
                    <circle cx="200" cy="200" r="80" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" />
                </svg>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <!-- Left: Welcome & Status -->
                    <div class="space-y-3">
                        <!-- Live Status Pill & Date -->
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-medium text-blue-100 shadow-sm"
                             style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25);">
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
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-2" style="color: #ffffff;">
                                {{ $greeting }}, {{ auth()->user()->name }}! 
                                <span class="inline-block animate-bounce">👋</span>
                            </h1>
                            <p class="mt-1 text-sm text-blue-100/90 max-w-2xl leading-relaxed" style="color: rgba(219, 234, 254, 0.95);">
                                Selamat datang di <span class="font-bold text-white underline decoration-cyan-400 decoration-2 underline-offset-4">Apotek.POS</span>. Pantau arus stok obat, performa penjualan kasir, dan keuangan apotek secara *real-time*.
                            </p>
                        </div>
                    </div>

                    <!-- Right: Quick Badges / Actions -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/15 text-xs text-blue-200"
                              style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <svg class="w-3.5 h-3.5 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Role: <strong class="text-white capitalize">{{ $roleName }}</strong>
                        </span>

                        @can('view stock')
                        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 backdrop-blur-sm border border-blue-400/30 text-xs text-blue-100 hover:text-white transition duration-150"
                           style="background: rgba(59, 130, 246, 0.25); border: 1px solid rgba(96, 165, 250, 0.35);">
                            <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Cek Stok
                        </a>
                        @endcan

                        @can('view sales reports')
                        <a href="{{ route('reports.sales') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 backdrop-blur-sm border border-indigo-400/30 text-xs text-indigo-100 hover:text-white transition duration-150"
                           style="background: rgba(99, 102, 241, 0.25); border: 1px solid rgba(129, 140, 248, 0.35);">
                            <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Laporan Penjualan
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Clean Metric Cards Grid (Matching User Reference) -->
            @php
                $totalProducts = \App\Models\Product::count();
                $lowStockCount = \App\Models\Product::lowStock()->count();
                $todaySalesCount = \App\Models\Sale::whereDate('date', today())->where('status', 'completed')->count();
                $todaySalesTotal = \App\Models\Sale::whereDate('date', today())->where('status', 'completed')->sum('grand_total');
                $thisMonthSales = \App\Models\Sale::whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->where('status', 'completed')->sum('grand_total');
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Total Katalog -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Katalog Obat</span>
                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                            {{ number_format($totalProducts) }}
                        </div>
                        <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            Unit obat & alkes terdata
                        </div>
                    </div>
                    <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- Card 2: Stok Menipis (Alert) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $lowStockCount > 0 ? 'border-amber-200/80 dark:border-amber-900/50' : 'border-slate-100 dark:border-gray-700' }} shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold {{ $lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400' }} uppercase tracking-wider">Stok Menipis (Alert)</span>
                            <div class="w-8 h-8 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold {{ $lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }} tracking-tight">
                            {{ number_format($lowStockCount) }}
                            <span class="text-xs font-normal text-gray-400">Batch</span>
                        </div>
                        <div class="mt-1 text-xs {{ $lowStockCount > 0 ? 'text-amber-600 font-semibold' : 'text-gray-400' }}">
                            {{ $lowStockCount > 0 ? 'Perlu restock segera →' : 'Semua stok dalam batas aman' }}
                        </div>
                    </div>
                    <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                        <div class="h-full {{ $lowStockCount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }} rounded-full" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- Card 3: Penjualan Hari Ini -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Penjualan Hari Ini</span>
                            <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                            {{ number_format($todaySalesCount) }}
                            <span class="text-xs font-normal text-gray-400">Transaksi</span>
                        </div>
                        <div class="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: 100%;"></div>
                    </div>
                </div>

                <!-- Card 4: Omset Bulan Ini -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Omset Bulan Ini</span>
                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($thisMonthSales, 0, ',', '.') }}">
                            Rp {{ number_format($thisMonthSales, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            Periode {{ now()->translatedFormat('F Y') }}
                        </div>
                    </div>
                    <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                        <div class="h-full bg-indigo-600 rounded-full" style="width: 100%;"></div>
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
