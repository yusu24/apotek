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
            <div class="bg-emerald-50/70 border border-emerald-100/80 dark:bg-emerald-950/20 dark:border-emerald-900/30 rounded-2xl p-5 shadow-sm">

                <p class="text-gray-500 dark:text-gray-400 font-semibold text-xs tracking-wide mb-2">Omset Hari Ini</p>
                <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-emerald-200 leading-none">
                    Rp {{ number_format($dailyTurnover, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-normal mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>

                <div class="mt-3">
                    @if($dailyTurnover > 0)
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 border border-emerald-200/60 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 border border-gray-200/60 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Belum Ada Transaksi
                        </span>
                    @endif
                </div>
            </div>

            <!-- Daily Transactions Card -->
            <div class="bg-indigo-50/70 border border-indigo-100/80 dark:bg-indigo-950/20 dark:border-indigo-900/30 rounded-2xl p-5 shadow-sm">

                <p class="text-gray-500 dark:text-gray-400 font-semibold text-xs tracking-wide mb-2">Transaksi Hari Ini</p>
                <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-indigo-200 leading-none">
                    {{ number_format($dailyTransactions, 0, ',', '.') }}
                    <span class="text-sm font-bold text-gray-500 dark:text-indigo-400">Transaksi</span>
                </h3>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-normal mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>

                <div class="mt-3">
                    @if($dailyTransactions > 0)
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/40 border border-indigo-200/60 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 border border-gray-200/60 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Belum Ada Transaksi
                        </span>
                    @endif
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

            <!-- Spotlight for Rank 1 -->
            @if($leaderboard->isNotEmpty())
                @php
                    $rank1 = $leaderboard->first();
                    $rank1Name = $rank1->user->name ?? 'Kasir';
                    $rank1Avatar = $rank1->user->profile_photo_path
                        ? asset('storage/' . $rank1->user->profile_photo_path)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($rank1Name) . '&background=f1f5f9&color=1e293b&size=128&bold=true';
                @endphp
                <div class="px-5 pt-4">
                    <div style="background: linear-gradient(135deg, #fffdf5 0%, #fffbeb 100%); border: 1px solid #fde68a; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.05);" class="rounded-3xl p-5 relative overflow-hidden group flex flex-col md:flex-row items-center md:items-center gap-5 md:gap-6 text-left"
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
                                    this.intervalId = setInterval(() => this.launchConfetti(), 8000);
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
                                for (let i = 0; i < 60; i++) {
                                    this.particles.push({
                                        x: Math.random() * w,
                                        y: -10 - Math.random() * 50,
                                        w: 4 + Math.random() * 5,
                                        h: 6 + Math.random() * 8,
                                        color: this.colors[Math.floor(Math.random() * this.colors.length)],
                                        vx: (Math.random() - 0.5) * 4,
                                        vy: 1.2 + Math.random() * 2,
                                        rotation: Math.random() * 360,
                                        rotationSpeed: (Math.random() - 0.5) * 10,
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
                                    p.vy += 0.04;
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

                        <!-- Trophy Background Illustration -->
                        <div class="absolute right-4 bottom-1/2 translate-y-1/2 pointer-events-none select-none z-0 opacity-90">
                            <svg class="w-28 h-28 md:w-32 md:h-32 text-amber-300/40 dark:text-amber-500/10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 4h12v8c0 3.314-2.686 6-6 6s-6-2.686-6-6V4z" fill="#fcd34d" />
                                <path d="M8 4h8v8c0 2.209-1.791 4-4 4s-4-1.791-4-4V4z" fill="#fbbf24" />
                                <path d="M6 6H3v4c0 1.5 1 2.5 2.5 3M18 6h3v4c0 1.5-1 2.5-2.5 3" stroke="#fcd34d" stroke-width="2" stroke-linecap="round" fill="none" />
                                <path d="M12 7.5l0.8 1.6 1.8 0.3-1.3 1.3 0.3 1.8-1.6-0.8-1.6 0.8 0.3-1.8-1.3-1.3 1.8-0.3L12 7.5z" fill="#fff" />
                                <path d="M11 18h2v2h-2v-2z" fill="#f59e0b" />
                                <rect x="9" y="20" width="6" height="2" rx="0.5" fill="#d97706" />
                                <rect x="8" y="21" width="8" height="2" rx="0.5" fill="#b45309" />
                            </svg>
                        </div>

                        <!-- Initials Box with Crown (Left Column) -->
                        @php
                            $words = explode(' ', trim($rank1Name));
                            if (count($words) >= 2) {
                                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                            } else {
                                $initials = strtoupper(substr($rank1Name, 0, 2));
                            }
                        @endphp
                        <div class="relative w-24 h-24 md:w-24 md:h-24 shrink-0 mx-auto md:mx-0 rounded-2xl flex items-center justify-center shadow-md overflow-visible" style="background: linear-gradient(135deg, #ffca28 0%, #ff8f00 100%); z-index: 2;">
                            <!-- Crown on top-left of the initials box -->
                            <div class="absolute -top-3.5 -left-3.5 rotate-[-15deg] drop-shadow-md z-10">
                                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 17h16a1 1 0 011 1v1a1 1 0 01-1 1H4a1 1 0 01-1-1v-1a1 1 0 011-1z" fill="#d97706" />
                                    <path d="M3 16l2.5-8 3.5 4.5 3-8 3 8 3.5-4.5 2.5 8H3z" fill="#fbbf24" />
                                    <path d="M12 4.5l-2.5 5.8 2.5 1.7 2.5-1.7L12 4.5z" fill="#f59e0b" opacity="0.6" />
                                    <circle cx="3" cy="8" r="1" fill="#ef4444" />
                                    <circle cx="9" cy="12.5" r="0.8" fill="#3b82f6" />
                                    <circle cx="12" cy="4" r="1" fill="#ef4444" />
                                    <circle cx="15" cy="12.5" r="0.8" fill="#3b82f6" />
                                    <circle cx="21" cy="8" r="1" fill="#ef4444" />
                                    <circle cx="6" cy="18.5" r="0.6" fill="#fff" />
                                    <circle cx="12" cy="18.5" r="0.6" fill="#fff" />
                                    <circle cx="18" cy="18.5" r="0.6" fill="#fff" />
                                </svg>
                            </div>

                            @if($rank1->user?->profile_photo_path)
                                <img src="{{ $rank1Avatar }}" alt="{{ $rank1Name }}" class="w-full h-full rounded-2xl object-cover" />
                            @else
                                <span class="text-white text-4xl font-extrabold tracking-wider select-none">{{ $initials }}</span>
                            @endif
                        </div>

                        <!-- Details (Right Column) -->
                        <div class="flex-1 min-w-0 w-full" style="z-index: 2;">
                            <div class="font-extrabold text-[#1e3a8a] dark:text-emerald-200 flex items-center justify-center md:justify-start gap-1.5 mb-2.5 text-sm">
                                <span>👑 Peringkat 1 Bulan Ini</span>
                            </div>

                            <table class="w-full text-xs text-slate-600 dark:text-slate-300 border-collapse">
                                <tbody>
                                    <tr class="align-middle">
                                        <td class="py-1.5 w-5 shrink-0">
                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </td>
                                        <td class="text-slate-500 dark:text-slate-400 py-1.5 w-[110px] font-medium pl-1">Nama</td>
                                        <td class="text-slate-400 py-1.5 w-4 text-center">:</td>
                                        <td class="font-extrabold text-[#0f172a] dark:text-white py-1.5 truncate max-w-[150px]" title="{{ $rank1Name }}">{{ $rank1Name }}</td>
                                    </tr>
                                    <tr class="align-middle">
                                        <td class="py-1.5 w-5 shrink-0">
                                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 100-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                                            </svg>
                                        </td>
                                        <td class="text-slate-500 dark:text-slate-400 py-1.5 font-medium pl-1">Jumlah Transaksi</td>
                                        <td class="text-slate-400 py-1.5 w-4 text-center">:</td>
                                        <td class="font-extrabold text-[#0f172a] dark:text-white py-1.5">{{ $rank1->total_transactions }}</td>
                                    </tr>
                                    <tr class="align-middle">
                                        <td class="py-1.5 w-5 shrink-0">
                                            <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </td>
                                        <td class="text-slate-500 dark:text-slate-400 py-1.5 font-medium pl-1">Total kontribusi</td>
                                        <td class="text-slate-400 py-1.5 w-4 text-center">:</td>
                                        <td class="font-extrabold text-[#0f172a] dark:text-white py-1.5">Rp {{ number_format($rank1->total_sales, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-5">
                <div class="space-y-3">
                    @forelse($leaderboard->skip(1)->take(3) as $row)
                        @php
                            $rank = $loop->index + 2;
                            $isRank2 = $rank === 2;
                            $isRank3 = $rank === 3;
                            $userName = $row->user->name ?? 'Kasir';
                            $avatarUrl = $row->user->profile_photo_path
                                ? asset('storage/' . $row->user->profile_photo_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=f1f5f9&color=475569&size=64&bold=true';
                        @endphp

                        <div class="flex items-center justify-between p-3 bg-slate-50/50 dark:bg-slate-900/20 border border-slate-100/50 dark:border-slate-800/50 rounded-xl transition-all duration-300 hover:translate-x-1">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-6 h-6 shrink-0">
                                    @if($isRank2)
                                        <span class="text-base" title="Juara 2">🥈</span>
                                    @elseif($isRank3)
                                        <span class="text-base" title="Juara 3">🥉</span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">#{{ $rank }}</span>
                                    @endif
                                </div>

                                <img src="{{ $avatarUrl }}" alt="{{ $userName }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm shrink-0" />

                                <div class="min-w-0">
                                    <h5 class="font-bold text-xs text-gray-900 dark:text-gray-100 truncate max-w-[100px]">{{ $userName }}</h5>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500 font-normal mt-0.5">
                                        {{ $row->total_transactions }} Transaksi
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-[8px] text-gray-400 dark:text-gray-500 font-normal tracking-wide">Kontribusi</p>
                                <p class="font-black text-xs text-indigo-600 dark:text-indigo-400">
                                    Rp {{ number_format($row->total_sales, 0, ',', '.') }}
                                </p>
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
                <div class="px-5 pb-5 pt-1 border-t border-gray-100 dark:border-gray-700">
                    <button @click="$dispatch('open-leaderboard-modal')" class="w-full text-center text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors flex items-center justify-center gap-1 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80">
                        Lihat Klasemen Lengkap
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
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
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 dark:border-gray-700">
                 <!-- Modal Header -->
                 <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
                     <div class="flex items-center gap-3">
                         <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                             <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                             </svg>
                         </div>
                         <div>
                             <h3 class="text-sm font-bold text-gray-900 dark:text-white" id="modal-title">Klasemen Lengkap Staf</h3>
                             <p class="text-[10px] text-gray-400 dark:text-gray-500 font-normal">Kinerja Penjualan Kasir Bulan Ini</p>
                         </div>
                     </div>
                     <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
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
                         @endphp
                         <div @class([
                             'flex items-center justify-between py-2.5 px-4 rounded-xl border transition-all duration-300',
                             'bg-amber-50/60 border-amber-100/70 dark:bg-amber-950/10 dark:border-amber-900/30' => $isRank1,
                             'bg-slate-50/50 border-slate-100/70 dark:bg-slate-900/20 dark:border-slate-800/50' => !$isRank1,
                         ])>
                             <div class="flex items-center gap-3.5">
                                 <!-- Rank Badge -->
                                 <div class="w-7 h-7 flex items-center justify-center shrink-0">
                                     @if($isRank1)
                                         <span class="text-lg">🥇</span>
                                     @elseif($isRank2)
                                         <span class="text-lg">🥈</span>
                                     @elseif($isRank3)
                                         <span class="text-lg">🥉</span>
                                     @else
                                         <span class="text-xs font-bold text-gray-400 dark:text-gray-500">#{{ $rank }}</span>
                                     @endif
                                 </div>

                                 <!-- Avatar -->
                                 <div class="relative shrink-0">
                                     @if($row->user->profile_photo_path ?? null)
                                         <img src="{{ asset('storage/' . $row->user->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                                     @else
                                         <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950/50 flex items-center justify-center text-xs font-extrabold text-indigo-600 dark:text-indigo-400 ring-2 ring-white dark:ring-gray-800">
                                             {{ strtoupper(substr($row->user->name ?? 'K', 0, 2)) }}
                                         </div>
                                     @endif
                                 </div>

                                 <!-- Cashier Info -->
                                 <div>
                                     <h4 @class([
                                         'text-xs font-bold text-gray-900 dark:text-white',
                                         'text-amber-900 dark:text-amber-300' => $isRank1,
                                     ])>{{ $row->user->name ?? 'Kasir' }}</h4>
                                     <p class="text-[9px] text-gray-400 dark:text-gray-500 font-medium">{{ $row->total_transactions }} Transaksi</p>
                                 </div>
                             </div>

                             <!-- Turnover Contribution -->
                             <div class="text-right">
                                 <p class="text-[8px] text-gray-400 dark:text-gray-500 font-semibold tracking-wider uppercase">Kontribusi</p>
                                 <p @if($isRank1) style="color: #b45309;" @endif
                                    @class([
                                     'font-black text-xs',
                                     'text-indigo-600 dark:text-indigo-400' => !$isRank1,
                                 ])>
                                     Rp {{ number_format($row->total_sales, 0, ',', '.') }}
                                 </p>
                             </div>
                         </div>
                     @endforeach
                 </div>

                 <!-- Modal Footer -->
                 <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end bg-gray-50/50 dark:bg-gray-900/20">
                     <button @click="open = false" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-xl transition-all">
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
