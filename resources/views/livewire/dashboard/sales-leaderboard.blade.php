<div x-data="{ showFullLeaderboard: false }">
@php
    $canTodaySales   = auth()->user()->can('view dashboard today sales');
    $canSalesTrend   = auth()->user()->can('view dashboard sales trend');
    $canLeaderboard  = auth()->user()->can('view dashboard staff leaderboard');
    $hasLeftContent  = $canTodaySales || $canSalesTrend;
    $hasRightContent = $canLeaderboard;
    // Grid span logic
    $gridClass       = ($hasLeftContent && $hasRightContent) ? 'grid grid-cols-1 lg:grid-cols-3 gap-6' : 'grid grid-cols-1 gap-6';
    $leftSpan        = ($hasLeftContent && $hasRightContent) ? 'lg:col-span-2 space-y-6' : 'space-y-6';
@endphp
    @if($hasLeftContent || $hasRightContent)
    <div class="{{ $gridClass }}">
    <!-- Left Column: Summary Cards & Daily Sales Trend Chart -->
    @if($hasLeftContent)
    <div class="{{ $leftSpan }}">
        <!-- Monthly Metrics Cards -->
        @can('view dashboard today sales')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Daily Turnover Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Omset Hari Ini</span>
                        <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Rp {{ number_format($dailyTurnover, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                        @if($dailyTurnover > 0)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Kasir Aktif
                            </span>
                        @else
                            <span class="text-[10px] font-medium text-gray-400">Belum Ada Transaksi</span>
                        @endif
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 100%;"></div>
                </div>
            </div>

            <!-- Daily Transactions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaksi Hari Ini</span>
                        <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                    <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ number_format($dailyTransactions, 0, ',', '.') }}
                        <span class="text-xs font-normal text-gray-400">Transaksi</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                        @if($dailyTransactions > 0)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                Transaksi Masuk
                            </span>
                        @else
                            <span class="text-[10px] font-medium text-gray-400">Belum Ada Transaksi</span>
                        @endif
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-indigo-600 rounded-full" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        @endcan

        <!-- Daily Turnover Trend Chart Card -->
        @can('view dashboard sales trend')
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
                <select wire:model.live="chartPeriod" class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 py-1.5 pl-3 pr-8 min-w-[140px] transition-all cursor-pointer">
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly">Bulanan</option>
                </select>
            </div>

            <!-- Chart Canvas Container -->
            <div wire:ignore
                 wire:key="sales-trend-chart-{{ $chartPeriod }}"
                 x-data="{
                    chart: null,
                    labels: [],
                    data: [],
                    initChart() {
                        if (this.chart) {
                            this.chart.destroy();
                            this.chart = null;
                        }
                        if (!this.labels || !this.data || this.labels.length === 0) return;
                        const canvas = this.$refs.turnoverChart;
                        if (!canvas) return;
                        const ctx = canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 256);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                        const numericData = this.data.map(Number);

                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: this.labels,
                                datasets: [{
                                    label: 'Omset Harian (Rp)',
                                    data: numericData,
                                    backgroundColor: gradient,
                                    borderColor: '#10b981',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#10b981',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#10b981',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                    pointHitRadius: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                // animations (plural) = kontrol per-properti di Chart.js v3+
                                // y.from: 0  → setiap titik data mulai dari baseline (y=0)
                                //             dan naik ke nilai aslinya (efek grow-from-zero)
                                animations: {
                                    y: {
                                        from: 0,
                                        duration: 1200,
                                        easing: 'easeOutQuart',
                                        delay: (context) => {
                                            if (context.type === 'data' && context.mode === 'default') {
                                                return context.dataIndex * 40;
                                            }
                                            return 0;
                                        }
                                    }
                                },
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        titleFont: { size: 14, weight: 'bold' },
                                        bodyFont: { size: 13 },
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) label += ': ';
                                                if (context.parsed.y !== null) {
                                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                                        ticks: {
                                            color: '#64748b',
                                            font: { size: 10 },
                                            callback: function(value) {
                                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(value);
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
                    },
                    updateChart(newLabels, newData) {
                        this.labels = newLabels !== undefined ? newLabels : @js($chartLabels);
                        this.data   = newData   !== undefined ? newData   : @js($chartData);
                        this.initChart();
                    }
                 }"
                 x-init="setTimeout(() => updateChart(), 400)"
                 @chart-data-updated.window="updateChart($event.detail.labels, $event.detail.data)"
                 class="h-64 mt-4 relative">
                <canvas x-ref="turnoverChart"></canvas>
            </div>
        </div>
        @endcan
    </div>
    @endif

    <!-- Right Column: Cashier Leaderboard -->
    @can('view dashboard staff leaderboard')
    @php
        $totalMonthlyLeaderboardSales = $leaderboard->sum('total_sales');
        $leaderSales = $leaderboard->first()?->total_sales ?? 1;
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-slate-100 dark:border-gray-700 shadow-xl overflow-hidden flex flex-col transition-all">
        <div>
            <!-- Header with Gradient Icon & Live Badge -->
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-amber-50/50 via-slate-50/30 to-indigo-50/40 dark:from-gray-900/40 dark:to-gray-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-md shadow-amber-500/20"
                         style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.334 1.334l-.8 1.599L18.677 11H20a1 1 0 110 2h-1.323l-1.582 3.954.8 1.599a1 1 0 01-1.334 1.334l-1.599-.8L11 20.677V22a1 1 0 11-2 0v-1.323l-3.954-1.582-1.599.8a1 1 0 01-1.334-1.334l.8-1.599L1.323 13H0a1 1 0 110-2h1.323l1.582-3.954-.8-1.599a1 1 0 011.334-1.334l1.599.8L9 3.323V2a1 1 0 011-1zm0 5a5 5 0 100 10 5 5 0 000-10z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-gray-100 text-sm tracking-tight flex items-center gap-2">
                            Papan Peringkat Kinerja Staf
                        </h3>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium tracking-wide">
                            Klasemen Penjualan Kasir • {{ now()->locale('id')->isoFormat('MMMM Y') }}
                        </p>
                    </div>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider shadow-sm"
                     style="background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Aktif</span>
                </div>
            </div>

            <!-- Spotlight for Rank 1 (Champion Card) -->
            @if($leaderboard->isNotEmpty())
                @php
                    $rank1 = $leaderboard->first();
                    $rank1Name = $rank1->user->name ?? 'Kasir';
                    $rank1Avatar = $rank1->user->profile_photo_path
                        ? asset('storage/' . $rank1->user->profile_photo_path)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($rank1Name) . '&background=fef3c7&color=b45309&size=128&bold=true';
                    $words = explode(' ', trim($rank1Name));
                    $initials = (count($words) >= 2)
                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                        : strtoupper(substr($rank1Name, 0, 2));

                    $rank1Share = $totalMonthlyLeaderboardSales > 0 ? round(($rank1->total_sales / $totalMonthlyLeaderboardSales) * 100, 1) : 0;
                    $rank1Aov = $rank1->total_transactions > 0 ? round($rank1->total_sales / $rank1->total_transactions) : 0;
                @endphp
                <div class="px-5 pt-5">
                    <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 40%, #fde68a 100%); border: 1.5px solid #fcd34d; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.2), 0 4px 6px -2px rgba(245, 158, 11, 0.1);"
                         class="rounded-3xl p-5 relative overflow-hidden group flex flex-col gap-4 text-left"
                         x-data="{
                            confettiCanvas: null,
                            particles: [],
                            animationId: null,
                            intervalId: null,
                            colors: ['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#f97316'],
                            init() {
                                this.$nextTick(() => {
                                    this.confettiCanvas = this.$refs.confettiCanvas;
                                    if (!this.confettiCanvas) return;
                                    this.resizeCanvas();
                                    this.launchConfetti();
                                    this.intervalId = setInterval(() => this.launchConfetti(), 9000);
                                });
                            },
                            destroy() {
                                if (this.intervalId) clearInterval(this.intervalId);
                                if (this.animationId) cancelAnimationFrame(this.animationId);
                            },
                            resizeCanvas() {
                                if (!this.confettiCanvas || !this.$el) return;
                                this.confettiCanvas.width = this.$el.offsetWidth;
                                this.confettiCanvas.height = this.$el.offsetHeight;
                            },
                            launchConfetti() {
                                if (!this.confettiCanvas) return;
                                this.resizeCanvas();
                                const w = this.confettiCanvas.width;
                                const h = this.confettiCanvas.height;
                                for (let i = 0; i < 45; i++) {
                                    this.particles.push({
                                        x: Math.random() * w,
                                        y: -10 - Math.random() * 40,
                                        w: 4 + Math.random() * 4,
                                        h: 6 + Math.random() * 6,
                                        color: this.colors[Math.floor(Math.random() * this.colors.length)],
                                        vx: (Math.random() - 0.5) * 3,
                                        vy: 1.2 + Math.random() * 2,
                                        rotation: Math.random() * 360,
                                        rotationSpeed: (Math.random() - 0.5) * 8,
                                        opacity: 1,
                                        decay: 0.002 + Math.random() * 0.003
                                    });
                                }
                                if (!this.animationId) this.animate();
                            },
                            animate() {
                                if (!this.confettiCanvas) { this.animationId = null; return; }
                                const ctx = this.confettiCanvas.getContext('2d');
                                ctx.clearRect(0, 0, this.confettiCanvas.width, this.confettiCanvas.height);
                                this.particles = this.particles.filter(p => p.opacity > 0.01 && p.y < this.confettiCanvas.height + 20);
                                this.particles.forEach(p => {
                                    p.x += p.vx;
                                    p.y += p.vy;
                                    p.vy += 0.03;
                                    p.vx *= 0.99;
                                    p.rotation += p.rotationSpeed;
                                    p.opacity -= p.decay;
                                    ctx.save();
                                    ctx.globalAlpha = Math.max(0, p.opacity);
                                    ctx.translate(p.x, p.y);
                                    ctx.rotate(p.rotation * Math.PI / 180);
                                    ctx.fillStyle = p.color;
                                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                                    ctx.restore();
                                });
                                if (this.particles.length > 0) {
                                    this.animationId = requestAnimationFrame(() => this.animate());
                                } else {
                                    this.animationId = null;
                                }
                            }
                         }">
                        <!-- Confetti Canvas -->
                        <canvas x-ref="confettiCanvas" class="absolute inset-0 w-full h-full pointer-events-none" style="z-index: 1;"></canvas>

                        <!-- Background Ambient Trophy Watermark -->
                        <div class="absolute pointer-events-none select-none z-0 right-2 -bottom-4 opacity-15">
                            <svg width="110" height="110" viewBox="0 0 24 24" fill="#d97706">
                                <path d="M5 3h14v2H5V3zm0 4h14v2h-1.07A7.002 7.002 0 0113 14.93V17h3v2H8v-2h3v-2.07A7.002 7.002 0 016.07 9H5V7zm13 2H6c0 3.31 2.69 6 6 6s6-2.69 6-6z"/>
                            </svg>
                        </div>

                        <!-- Top Ribbon: Champion Title & Share -->
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase text-white shadow-sm"
                                 style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                                <span>👑</span>
                                <span>Peringkat 1 Bulan Ini</span>
                            </div>

                            <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                                 style="background: rgba(255, 255, 255, 0.7); color: #92400e; border: 1px solid #fde68a;">
                                <span>⚡ {{ $rank1Share }}% Pangsa Omset</span>
                            </div>
                        </div>

                        <!-- Champion Profile row -->
                        <div class="relative z-10 flex items-center gap-4">
                            <!-- Avatar with Royal Gold Crown & Ring -->
                            <div class="relative shrink-0">
                                <!-- 3D Gold Crown overlay -->
                                <div class="absolute -top-3.5 -left-3.5 z-20 drop-shadow-md transform -rotate-12">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 17h16a1 1 0 011 1v1a1 1 0 01-1 1H4a1 1 0 01-1-1v-1a1 1 0 011-1z" fill="#b45309" />
                                        <path d="M3 16l2.5-8 3.5 4.5 3-8 3 8 3.5-4.5 2.5 8H3z" fill="#fbbf24" stroke="#d97706" stroke-width="0.5" />
                                        <circle cx="3" cy="8" r="1.2" fill="#ef4444" />
                                        <circle cx="12" cy="4" r="1.4" fill="#3b82f6" />
                                        <circle cx="21" cy="8" r="1.2" fill="#ef4444" />
                                        <circle cx="7.5" cy="12.5" r="0.9" fill="#10b981" />
                                        <circle cx="16.5" cy="12.5" r="0.9" fill="#10b981" />
                                    </svg>
                                </div>

                                @if($rank1->user?->profile_photo_path)
                                    <img src="{{ $rank1Avatar }}" alt="{{ $rank1Name }}" 
                                         class="w-16 h-16 rounded-2xl object-cover shadow-md ring-4 ring-amber-400 ring-offset-2 ring-offset-amber-100" />
                                @else
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-md ring-4 ring-amber-400 ring-offset-2 ring-offset-amber-100 text-white font-black text-2xl tracking-wider select-none"
                                         style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>

                            <!-- Name & Rank Details -->
                            <div class="min-w-0 flex-1">
                                <h4 class="text-base font-black text-amber-950 truncate tracking-tight" title="{{ $rank1Name }}">
                                    {{ $rank1Name }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-800 bg-amber-200/60 px-2 py-0.5 rounded-md">
                                        <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Kasir Bintang Bulan Ini
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Sleek Frosted Metric KPI Chips -->
                        <div class="relative z-10 grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1">
                            <!-- Stat 1: Total Omset -->
                            <div class="col-span-2 sm:col-span-1 rounded-2xl p-2.5 bg-white/70 backdrop-blur-md border border-amber-200/80 shadow-xs">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-800/80">Total Omset</div>
                                <div class="text-sm font-black text-amber-950 mt-0.5 truncate" title="Rp {{ number_format($rank1->total_sales, 0, ',', '.') }}">
                                    Rp {{ number_format($rank1->total_sales, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Stat 2: Total Transaksi -->
                            <div class="rounded-2xl p-2.5 bg-white/70 backdrop-blur-md border border-amber-200/80 shadow-xs">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-800/80">Transaksi</div>
                                <div class="text-sm font-black text-amber-950 mt-0.5">
                                    {{ number_format($rank1->total_transactions) }} <span class="text-[10px] font-semibold text-amber-700">Trx</span>
                                </div>
                            </div>

                            <!-- Stat 3: AOV -->
                            <div class="rounded-2xl p-2.5 bg-white/70 backdrop-blur-md border border-amber-200/80 shadow-xs">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-800/80">Rata-rata/Trx</div>
                                <div class="text-sm font-black text-amber-950 mt-0.5 truncate" title="Rp {{ number_format($rank1Aov, 0, ',', '.') }}">
                                    Rp {{ number_format($rank1Aov, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Runners-Up Race List -->
            <div class="p-5">
                <div class="flex items-center justify-between mb-3 px-1">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Peringkat Selanjutnya</span>
                    <span class="text-[10px] font-semibold text-slate-400">Kontribusi Penjualan</span>
                </div>

                <div class="space-y-2.5">
                    @forelse($leaderboard->skip(1)->take(3) as $row)
                        @php
                            $rank = $loop->index + 2;
                            $isRank2 = $rank === 2;
                            $isRank3 = $rank === 3;
                            $userName = $row->user->name ?? 'Kasir';
                            $avatarUrl = $row->user->profile_photo_path
                                ? asset('storage/' . $row->user->profile_photo_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=f1f5f9&color=475569&size=64&bold=true';
                            $words = explode(' ', trim($userName));
                            $rowInitials = (count($words) >= 2)
                                ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                : strtoupper(substr($userName, 0, 2));

                            $relativePercent = $leaderSales > 0 ? min(100, round(($row->total_sales / $leaderSales) * 100)) : 0;
                            $sharePercent = $totalMonthlyLeaderboardSales > 0 ? round(($row->total_sales / $totalMonthlyLeaderboardSales) * 100, 1) : 0;
                        @endphp

                        <div class="group relative overflow-hidden rounded-2xl p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 border"
                             @if($isRank2)
                                style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-color: #cbd5e1;"
                             @elseif($isRank3)
                                style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #fed7aa;"
                             @else
                                style="background: #ffffff; border-color: #e2e8f0;"
                             @endif>
                            
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Rank Icon / Badge -->
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 font-black text-xs shadow-xs"
                                         @if($isRank2)
                                            style="background: linear-gradient(135deg, #e2e8f0 0%, #94a3b8 100%); color: #1e293b;"
                                         @elseif($isRank3)
                                            style="background: linear-gradient(135deg, #fed7aa 0%, #ea580c 100%); color: #ffffff;"
                                         @else
                                            style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;"
                                         @endif>
                                        @if($isRank2) 🥈 @elseif($isRank3) 🥉 @else #{{ $rank }} @endif
                                    </div>

                                    <!-- User Avatar -->
                                    <div class="relative shrink-0">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs select-none shadow-xs"
                                             @if($isRank2)
                                                style="background: #e2e8f0; color: #334155; border: 1.5px solid #cbd5e1;"
                                             @elseif($isRank3)
                                                style="background: #ffedd5; color: #9a3412; border: 1.5px solid #fed7aa;"
                                             @else
                                                style="background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0;"
                                             @endif>
                                            {{ $rowInitials }}
                                        </div>
                                    </div>

                                    <!-- Name & Transaction info -->
                                    <div class="min-w-0">
                                        <h5 class="font-extrabold text-xs text-gray-900 dark:text-gray-100 truncate max-w-[130px]" title="{{ $userName }}">
                                            {{ $userName }}
                                        </h5>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                            {{ $row->total_transactions }} Transaksi • <span class="font-bold text-slate-700 dark:text-slate-300">{{ $sharePercent }}%</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="text-right shrink-0">
                                    <div class="font-black text-xs text-slate-900 dark:text-white">
                                        Rp {{ number_format($row->total_sales, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[9px] font-semibold text-slate-400">
                                        {{ $relativePercent }}% dari #1
                                    </div>
                                </div>
                            </div>

                            <!-- Competitive Progress Bar -->
                            <div class="mt-2.5 w-full bg-slate-200/70 dark:bg-slate-700/50 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700"
                                     style="width: {{ $relativePercent }}%; @if($isRank2) background: linear-gradient(90deg, #94a3b8, #475569); @elseif($isRank3) background: linear-gradient(90deg, #f59e0b, #d97706); @else background: linear-gradient(90deg, #6366f1, #4f46e5); @endif"></div>
                            </div>
                        </div>
                    @empty
                        @if($leaderboard->count() <= 1)
                            @if($leaderboard->isEmpty())
                                <div class="py-6 text-center text-gray-400 dark:text-gray-500 italic text-xs">
                                    Belum ada data transaksi bulan ini.
                                </div>
                            @else
                                <div class="py-6 text-center text-gray-400 dark:text-gray-500 italic text-xs">
                                    Tidak ada kasir lain bulan ini.
                                </div>
                            @endif
                        @endif
                    @endforelse
                </div>
            </div>

            <!-- View More Button -->
            @if($leaderboard->count() > 4)
                <div class="px-5 pb-5 pt-1">
                    <button @click="$dispatch('open-leaderboard-modal')"
                            class="w-full text-center text-xs font-extrabold transition-all duration-200 flex items-center justify-center gap-2 py-3 rounded-2xl shadow-xs hover:shadow-md hover:-translate-y-0.5 border"
                            style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); color: #4338ca; border-color: #e0e7ff;">
                        <span>Lihat Klasemen Lengkap ({{ $leaderboard->count() }} Kasir)</span>
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>

<!-- Full Leaderboard Modal -->
<div x-data="{ open: false }"
     @open-leaderboard-modal.window="open = true"
     @close-leaderboard-modal.window="open = false"
     class="relative z-[150]" x-cloak>
    <!-- Backdrop with fade transition -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity z-[150]" 
         @click="open = false" style="display: none;"></div>

    <!-- Modal Wrapper -->
    <div x-show="open" style="display: none;" class="fixed inset-0 z-[160] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <!-- Modal Content with scale/fade transition -->
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 dark:border-gray-700">
                 <!-- Modal Header -->
                 <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-amber-50/40 via-slate-50/40 to-indigo-50/40 dark:from-gray-900/40 dark:to-gray-800/40">
                     <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-md shadow-amber-500/20"
                              style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                             <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                 <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.334 1.334l-.8 1.599L18.677 11H20a1 1 0 110 2h-1.323l-1.582 3.954.8 1.599a1 1 0 01-1.334 1.334l-1.599-.8L11 20.677V22a1 1 0 11-2 0v-1.323l-3.954-1.582-1.599.8a1 1 0 01-1.334-1.334l.8-1.599L1.323 13H0a1 1 0 110-2h1.323l1.582-3.954-.8-1.599a1 1 0 011.334-1.334l1.599.8L9 3.323V2a1 1 0 011-1zm0 5a5 5 0 100 10 5 5 0 000-10z" clip-rule="evenodd"></path>
                             </svg>
                         </div>
                         <div>
                             <h3 class="text-sm font-black text-gray-900 dark:text-white" id="modal-title">Klasemen Lengkap Staf Kasir</h3>
                             <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Periode: {{ now()->locale('id')->isoFormat('MMMM Y') }} • Total: Rp {{ number_format($totalMonthlyLeaderboardSales ?? 0, 0, ',', '.') }}</p>
                         </div>
                     </div>
                     <button @click="open = false" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                         </svg>
                     </button>
                 </div>

                 <!-- Modal Body (Scrollable List) -->
                 <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3">
                     @foreach($leaderboard as $index => $row)
                         @php
                             $rank = $index + 1;
                             $isRank1 = $rank === 1;
                             $isRank2 = $rank === 2;
                             $isRank3 = $rank === 3;
                             $userName = $row->user->name ?? 'Kasir';
                             $words = explode(' ', trim($userName));
                             $modalInitials = (count($words) >= 2)
                                 ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                 : strtoupper(substr($userName, 0, 2));

                             $relativePercent = $leaderSales > 0 ? min(100, round(($row->total_sales / $leaderSales) * 100)) : 0;
                             $sharePercent = ($totalMonthlyLeaderboardSales ?? 0) > 0 ? round(($row->total_sales / $totalMonthlyLeaderboardSales) * 100, 1) : 0;
                         @endphp
                         <div class="p-3.5 rounded-2xl border transition-all duration-200"
                              @if($isRank1)
                                 style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #fcd34d;"
                              @elseif($isRank2)
                                 style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-color: #cbd5e1;"
                              @elseif($isRank3)
                                 style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #fed7aa;"
                              @else
                                 style="background: #ffffff; border-color: #e2e8f0;"
                              @endif>
                             
                             <div class="flex items-center justify-between gap-3">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <!-- Rank Badge -->
                                     <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 font-black text-xs shadow-xs"
                                          @if($isRank1)
                                             style="background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: #ffffff;"
                                          @elseif($isRank2)
                                             style="background: linear-gradient(135deg, #e2e8f0 0%, #94a3b8 100%); color: #1e293b;"
                                          @elseif($isRank3)
                                             style="background: linear-gradient(135deg, #fed7aa 0%, #ea580c 100%); color: #ffffff;"
                                          @else
                                             style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;"
                                          @endif>
                                         @if($isRank1) 🥇 @elseif($isRank2) 🥈 @elseif($isRank3) 🥉 @else #{{ $rank }} @endif
                                     </div>

                                     <!-- Avatar -->
                                     <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs select-none shadow-xs shrink-0"
                                          @if($isRank1)
                                             style="background: #fef3c7; color: #b45309; border: 1.5px solid #fcd34d;"
                                          @elseif($isRank2)
                                             style="background: #e2e8f0; color: #334155; border: 1.5px solid #cbd5e1;"
                                          @elseif($isRank3)
                                             style="background: #ffedd5; color: #9a3412; border: 1.5px solid #fed7aa;"
                                          @else
                                             style="background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0;"
                                          @endif>
                                         {{ $modalInitials }}
                                     </div>

                                     <!-- Cashier Info -->
                                     <div class="min-w-0">
                                         <h4 class="text-xs font-black text-gray-900 dark:text-white truncate">
                                             {{ $userName }}
                                         </h4>
                                         <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                             {{ $row->total_transactions }} Transaksi • <span class="font-bold text-slate-700 dark:text-slate-300">{{ $sharePercent }}%</span>
                                         </p>
                                     </div>
                                 </div>

                                 <!-- Turnover Contribution -->
                                 <div class="text-right shrink-0">
                                     <div class="font-black text-xs"
                                          @if($isRank1) style="color: #b45309;" @else style="color: #0f172a;" @endif>
                                         Rp {{ number_format($row->total_sales, 0, ',', '.') }}
                                     </div>
                                     <div class="text-[9px] font-semibold text-slate-400">
                                         {{ $relativePercent }}% dari #1
                                     </div>
                                 </div>
                             </div>

                             <!-- Progress bar in modal -->
                             <div class="mt-2.5 w-full bg-slate-200/70 dark:bg-slate-700/50 rounded-full h-1.5 overflow-hidden">
                                 <div class="h-full rounded-full"
                                      style="width: {{ $relativePercent }}%; @if($isRank1) background: linear-gradient(90deg, #fbbf24, #d97706); @elseif($isRank2) background: linear-gradient(90deg, #94a3b8, #475569); @elseif($isRank3) background: linear-gradient(90deg, #f59e0b, #d97706); @else background: linear-gradient(90deg, #6366f1, #4f46e5); @endif"></div>
                             </div>
                         </div>
                     @endforeach
                 </div>

                 <!-- Modal Footer -->
                 <div class="p-5 border-t border-gray-100 dark:border-gray-700 flex justify-end bg-gray-50/50 dark:bg-gray-900/20">
                     <button @click="open = false" 
                             class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-all shadow-xs">
                         Tutup
                     </button>
                 </div>
            </div>
        </div>
    </div>
    @endcan
    </div>
    @endif
</div>
