<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Summary Cards & Daily Sales Trend Chart -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Monthly Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Monthly Turnover Card -->
            <div class="bg-emerald-50/70 border border-emerald-100/80 dark:bg-emerald-950/20 dark:border-emerald-900/30 rounded-2xl p-6 flex justify-between items-center relative overflow-hidden group shadow-sm">
                <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition duration-500 text-emerald-800 dark:text-emerald-400">
                    <svg class="w-36 h-36" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="text-gray-900 dark:text-gray-100 font-semibold text-xs tracking-wide">Omset Bulan Ini</p>
                    <h3 class="text-3xl font-black tracking-tight text-gray-900 dark:text-emerald-200">Rp {{ number_format($monthlyTurnover, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-normal mt-1">Berdasarkan transaksi selesai di bulan {{ now()->translatedFormat('F Y') }}</p>
                </div>
                <div class="bg-emerald-200/50 dark:bg-emerald-900/40 p-3 rounded-2xl shrink-0 text-emerald-700 dark:text-emerald-400 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Monthly Transactions Card -->
            <div class="bg-indigo-50/70 border border-indigo-100/80 dark:bg-indigo-950/20 dark:border-indigo-900/30 rounded-2xl p-6 flex justify-between items-center relative overflow-hidden group shadow-sm">
                <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition duration-500 text-indigo-800 dark:text-indigo-400">
                    <svg class="w-36 h-36" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="text-gray-900 dark:text-gray-100 font-semibold text-xs tracking-wide">Total Transaksi Bulan Ini</p>
                    <h3 class="text-3xl font-black tracking-tight text-gray-900 dark:text-indigo-200">
                        {{ number_format($monthlyTransactions, 0, ',', '.') }}
                        <span class="text-lg font-bold text-gray-700 dark:text-indigo-400">Transaksi</span>
                    </h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-normal mt-1">Volume penjualan kasir terdaftar</p>
                </div>
                <div class="bg-indigo-200/50 dark:bg-indigo-900/40 p-3 rounded-2xl shrink-0 text-indigo-700 dark:text-indigo-400 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Daily Turnover Trend Chart Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-lg p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-gray-800 dark:text-gray-200">Tren Omset Harian</h3>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-normal tracking-wide mt-0.5">{{ $chartTitle }}</p>
                    </div>
                </div>

                <!-- Period Filter -->
                <select wire:model.live="chartPeriod" class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 py-1.5 px-2 transition-all cursor-pointer">
                    <option value="current_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="7_days">7 Hari Terakhir</option>
                    <option value="30_days">30 Hari Terakhir</option>
                </select>
            </div>

            <!-- Chart Canvas Container -->
            <div x-data="{
                initChart() {
                    const canvas = document.getElementById('monthlyTurnoverChart');
                    if (!canvas) return;
                    if (canvas.chart) { canvas.chart.destroy(); }
                    
                    const ctx = canvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                    canvas.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js(json_decode($chartLabels)),
                            datasets: [{
                                label: 'Omset Harian (Rp)',
                                data: @js(json_decode($chartData)),
                                borderColor: '#10b981',
                                borderWidth: 3,
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart',
                                delay: (context) => {
                                    let delay = 0;
                                    if (context.type === 'data' && context.mode === 'default') {
                                        delay = context.dataIndex * 30;
                                    }
                                    return delay;
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let value = context.raw;
                                            return 'Omset: Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    grid: {
                                        color: 'rgba(148, 163, 184, 0.1)',
                                        borderDash: [5, 5]
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: { size: 10 },
                                        callback: function(value) {
                                            if (value >= 1000000) return (value / 1000000) + 'jt';
                                            if (value >= 1000) return (value / 1000) + 'rb';
                                            return value;
                                        }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        color: '#64748b',
                                        font: { size: 10, weight: '600' }
                                    }
                                }
                            }
                        }
                    });
                }
            }"
            x-init="setTimeout(() => initChart(), 400)"
            wire:key="chart-{{ $chartPeriod }}"
            x-effect="initChart()"
            class="h-64 mt-4 relative">
                <canvas id="monthlyTurnoverChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right Column: Cashier Leaderboard -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-lg overflow-hidden flex flex-col">
        <div>
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-gray-800 dark:text-gray-200">Papan Peringkat Kinerja Staf</h3>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-normal tracking-wide mt-0.5">Klasemen Penjualan Kasir (Bulan Ini)</p>
                    </div>
                </div>
                <div class="text-[10px] bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-200/50 px-2 py-1 rounded-full font-bold uppercase tracking-wider shrink-0">
                    🔥 Aktif
                </div>
            </div>

            <div class="p-5">
                <div class="space-y-4">
                    @forelse($leaderboard as $index => $row)
                        @php
                            $rank = $index + 1;
                            $isRank1 = $rank === 1;
                            $isRank2 = $rank === 2;
                            $isRank3 = $rank === 3;
                            $userName = $row->user->name ?? 'Kasir';
                            
                            $initials = collect(explode(' ', $userName))
                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                ->take(2)
                                ->join('');
                        @endphp

                        <div @class([
                            'flex items-center justify-between p-4 rounded-xl transition-all duration-300 hover:translate-x-1',
                            'bg-gradient-to-r from-amber-50 to-yellow-50/80 dark:from-amber-950/20 dark:to-yellow-900/10 shadow-sm' => $isRank1,
                            'bg-slate-50/30 dark:bg-slate-900/10' => !$isRank1,
                        ])>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-7 h-7 shrink-0">
                                    @if($isRank1)
                                        <span class="text-xl animate-bounce" title="Juara 1">🥇</span>
                                    @elseif($isRank2)
                                        <span class="text-xl" title="Juara 2">🥈</span>
                                    @elseif($isRank3)
                                        <span class="text-xl" title="Juara 3">🥉</span>
                                    @else
                                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500">#{{ $rank }}</span>
                                    @endif
                                </div>

                                <div @class([
                                    'w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shadow-inner shrink-0',
                                    'bg-gradient-to-br from-yellow-400 to-amber-500 text-white ring-2 ring-yellow-200 dark:ring-yellow-900/30' => $isRank1,
                                    'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' => !$isRank1,
                                ])>
                                    {{ $initials }}
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span @class([
                                            'font-bold text-sm',
                                            'text-amber-900 dark:text-amber-200' => $isRank1,
                                            'text-gray-900 dark:text-gray-100' => !$isRank1,
                                        ])>
                                            {{ $userName }}
                                        </span>
                                        @if($isRank1)
                                            <span class="text-sm" title="Top Cashier">👑</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-normal mt-0.5">
                                        {{ $row->total_transactions }} Transaksi
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 font-normal tracking-wide">Kontribusi</p>
                                <p @class([
                                    'font-black text-sm',
                                    'text-amber-700 dark:text-amber-400' => $isRank1,
                                    'text-indigo-600 dark:text-indigo-400' => !$isRank1,
                                ])>
                                    Rp {{ number_format($row->total_sales, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 dark:text-gray-500 italic text-xs">
                            Belum ada data transaksi bulan ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
