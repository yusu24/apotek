<div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="{ 
    initCharts() {
        // Top Selling Chart
        const topCtx = document.getElementById('topSellingChart');
        if (this.topChart) this.topChart.destroy();
        this.topChart = new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: @js($topSellingChart['labels']),
                datasets: [{
                    label: 'Unit Terjual',
                    data: @js($topSellingChart['data']),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Slow Moving Chart
        const slowCtx = document.getElementById('slowMovingChart');
        if (this.slowChart) this.slowChart.destroy();
        this.slowChart = new Chart(slowCtx, {
            type: 'bar',
            data: {
                labels: @js($slowMovingChart['labels']),
                datasets: [{
                    label: 'Unit Terjual',
                    data: @js($slowMovingChart['data']),
                    backgroundColor: 'rgba(244, 63, 94, 0.8)',
                    borderColor: 'rgb(244, 63, 94)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    },
    topChart: null,
    slowChart: null
}" x-init="initCharts(); Livewire.on('chart-update', () => { setTimeout(() => initCharts(), 100) })">
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
        </div>
    </div>
</div>
