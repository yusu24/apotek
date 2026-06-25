<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    {{-- ── TOP SELLING CHART ───────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-slate-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Produk Paling Laku</h3>
            </div>
            <select wire:model.live="period"
                    class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-blue-500 py-1 transition-all">
                <option value="daily">Harian</option>
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
            </select>
        </div>

        <div class="p-6">
            {{-- Chart — wire:ignore agar Alpine tidak di-destroy saat Livewire re-render --}}
            <div wire:ignore
                 x-data="{
                    chart: null,
                    topLabels: @js($topSellingChart['labels']),
                    topAbbreviations: @js($topSellingChart['abbreviations']),
                    topData: @js($topSellingChart['data']),

                    initChart() {
                        const canvas = this.$refs.topCanvas;
                        if (!canvas) return;

                        if (this.chart) { this.chart.destroy(); this.chart = null; }

                        const ctx = canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

                        const labels     = this.topAbbreviations;
                        const fullLabels = this.topLabels;
                        const data       = this.topData.map(Number);

                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Unit Terjual',
                                    data: data,
                                    backgroundColor: gradient,
                                    borderColor: '#3b82f6',
                                    borderWidth: 2,
                                    borderRadius: 6,
                                    hoverBackgroundColor: '#2563eb',
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
                                            delay = context.dataIndex * 100;
                                        }
                                        return delay;
                                    }
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        titleFont: { size: 14, weight: 'bold' },
                                        bodyFont: { size: 13 },
                                        cornerRadius: 8,
                                        displayColors: false,
                                        callbacks: {
                                            title: function(context) {
                                                return fullLabels[context[0].dataIndex] || context[0].label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                                        ticks: { color: '#64748b' }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: '#64748b', font: { weight: '600' } }
                                    }
                                }
                            }
                        });
                    },

                    updateData(labels, abbreviations, data) {
                        this.topLabels        = labels;
                        this.topAbbreviations = abbreviations;
                        this.topData          = data;
                        this.$nextTick(() => this.initChart());
                    }
                 }"
                 x-init="setTimeout(() => initChart(), 400)"
                 @product-chart-updated.window="updateData(
                     $event.detail.topLabels,
                     $event.detail.topAbbreviations,
                     $event.detail.topData
                 )"
                 class="h-64">
                <canvas x-ref="topCanvas"></canvas>
            </div>

            {{-- Legenda — di luar wire:ignore, Livewire update otomatis --}}
            <div class="mt-6">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Legenda Produk</h4>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($topSellingChart['abbreviations'] as $i => $abbr)
                        <div class="flex items-center gap-2 text-[11px] text-gray-600 dark:text-gray-400">
                            <span class="font-bold text-blue-600 dark:text-blue-400 w-6">{{ $abbr }}</span>
                            <span class="truncate">{{ $topSellingChart['labels'][$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── SLOW MOVING CHART ───────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-slate-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Produk Paling Lambat</h3>
            </div>
            <div class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Update Real-time</div>
        </div>

        <div class="p-6">
            {{-- Chart — wire:ignore agar Alpine tidak di-destroy saat Livewire re-render --}}
            <div wire:ignore
                 x-data="{
                    chart: null,
                    slowLabels: @js($slowMovingChart['labels']),
                    slowAbbreviations: @js($slowMovingChart['abbreviations']),
                    slowData: @js($slowMovingChart['data']),

                    initChart() {
                        const canvas = this.$refs.slowCanvas;
                        if (!canvas) return;

                        if (this.chart) { this.chart.destroy(); this.chart = null; }

                        const ctx = canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(244, 63, 94, 0.8)');
                        gradient.addColorStop(1, 'rgba(244, 63, 94, 0.05)');

                        const labels     = this.slowAbbreviations;
                        const fullLabels = this.slowLabels;
                        const data       = this.slowData.map(Number);

                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Unit Terjual',
                                    data: data,
                                    backgroundColor: gradient,
                                    borderColor: '#f43f5e',
                                    borderWidth: 2,
                                    borderRadius: 6,
                                    hoverBackgroundColor: '#e11d48',
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
                                            delay = context.dataIndex * 100;
                                        }
                                        return delay;
                                    }
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        titleFont: { size: 14, weight: 'bold' },
                                        bodyFont: { size: 13 },
                                        cornerRadius: 8,
                                        displayColors: false,
                                        callbacks: {
                                            title: function(context) {
                                                return fullLabels[context[0].dataIndex] || context[0].label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                                        ticks: { color: '#64748b' }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: '#64748b', font: { weight: '600' } }
                                    }
                                }
                            }
                        });
                    },

                    updateData(labels, abbreviations, data) {
                        this.slowLabels        = labels;
                        this.slowAbbreviations = abbreviations;
                        this.slowData          = data;
                        this.$nextTick(() => this.initChart());
                    }
                 }"
                 x-init="setTimeout(() => initChart(), 400)"
                 @product-chart-updated.window="updateData(
                     $event.detail.slowLabels,
                     $event.detail.slowAbbreviations,
                     $event.detail.slowData
                 )"
                 class="h-64">
                <canvas x-ref="slowCanvas"></canvas>
            </div>

            {{-- Legenda — di luar wire:ignore, Livewire update otomatis --}}
            <div class="mt-6">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Legenda Produk</h4>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($slowMovingChart['abbreviations'] as $i => $abbr)
                        <div class="flex items-center gap-2 text-[11px] text-gray-600 dark:text-gray-400">
                            <span class="font-bold text-rose-600 dark:text-rose-400 w-6">{{ $abbr }}</span>
                            <span class="truncate">{{ $slowMovingChart['labels'][$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
