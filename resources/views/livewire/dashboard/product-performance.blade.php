<div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="{ 
    initCharts() {
        // Ensure data is numeric
        const topData = @js($topSellingChart['data']).map(Number);
        const slowData = @js($slowMovingChart['data']).map(Number);

        // Top Selling Chart
        const topCanvas = document.getElementById('topSellingChart');
        if (!topCanvas) return;
        const topCtx = topCanvas.getContext('2d');
        const topGradient = topCtx.createLinearGradient(0, 0, 0, 300);
        topGradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
        topGradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

        if (this.topChart) this.topChart.destroy();
        this.topChart = new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: @js($topSellingChart['labels']),
                datasets: [{
                    label: 'Unit Terjual',
                    data: topData,
                    backgroundColor: topGradient,
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
                        displayColors: false
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

        // Slow Moving Chart
        const slowCanvas = document.getElementById('slowMovingChart');
        if (!slowCanvas) return;
        const slowCtx = slowCanvas.getContext('2d');
        const slowGradient = slowCtx.createLinearGradient(0, 0, 0, 300);
        slowGradient.addColorStop(0, 'rgba(244, 63, 94, 0.8)');
        slowGradient.addColorStop(1, 'rgba(244, 63, 94, 0.05)');

        if (this.slowChart) this.slowChart.destroy();
        this.slowChart = new Chart(slowCtx, {
            type: 'bar',
            data: {
                labels: @js($slowMovingChart['labels']),
                datasets: [{
                    label: 'Unit Terjual',
                    data: slowData,
                    backgroundColor: slowGradient,
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
                        displayColors: false
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
    topChart: null,
    slowChart: null
}" x-init="setTimeout(() => initCharts(), 400); Livewire.on('chart-update', () => { setTimeout(() => initCharts(), 100) })">
    <!-- Top Selling Products -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-slate-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Produk Paling Laku</h3>
            </div>
            <select wire:model.live="period" class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-blue-500 py-1 transition-all">
                <option value="daily">Harian</option>
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
            </select>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="topSellingChart"></canvas>
            </div>
            
            <!-- Description Removed -->
        </div>
    </div>

    <!-- Slowest Moving Products -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-slate-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Produk Paling Lambat</h3>
            </div>
            <div class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Update Real-time</div>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="slowMovingChart"></canvas>
            </div>
            
            <!-- Description Removed -->
        </div>
    </div>
</div>
