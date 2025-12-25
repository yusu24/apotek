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
            
            <!-- Chart Description -->
            <div class="mt-4 p-4 bg-blue-50/50 dark:bg-blue-900/10 rounded-lg border border-blue-100 dark:border-blue-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 mb-1">Tentang Diagram Ini</p>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Diagram ini menampilkan <strong>5 produk dengan penjualan tertinggi</strong> dalam periode yang dipilih. 
                            Produk dengan batang tertinggi adalah produk yang paling banyak terjual. Gunakan informasi ini untuk 
                            memastikan stok produk populer selalu tersedia dan merencanakan strategi penjualan yang lebih baik.
                        </p>
                    </div>
                </div>
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
            
            <!-- Chart Description -->
            <div class="mt-4 p-4 bg-rose-50/50 dark:bg-rose-900/10 rounded-lg border border-rose-100 dark:border-rose-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 mb-1">Tentang Diagram Ini</p>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Diagram ini menampilkan <strong>5 produk dengan penjualan terendah</strong> yang masih memiliki riwayat transaksi. 
                            Produk dengan batang terendah adalah produk yang paling lambat terjual. Pertimbangkan untuk 
                            mengurangi stok produk ini, mengadakan promosi khusus, atau evaluasi kelayakan produk untuk tetap dijual.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
