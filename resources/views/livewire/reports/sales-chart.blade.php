<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Laporan Penjualan
        </h2>

    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-500/30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-600 to-red-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Transaksi</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($totalTransactions, 0, ',', '.') }}</p>
                </div>
                <div class="bg-orange-500/30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-600 to-green-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">Rata-rata per Transaksi</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($overallAverage, 0, ',', '.') }}</p>
                </div>
                <div class="bg-emerald-500/30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-lg font-bold text-gray-900">Grafik Penjualan</h3>
            <div class="flex flex-wrap items-center gap-3">
                @if($period === 'custom')
                    <div class="flex items-center gap-2">
                         <input type="date" wire:model.live="startDate" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                         <span class="text-gray-500">-</span>
                         <input type="date" wire:model.live="endDate" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    </div>
                @endif
                <select wire:model.live="period" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="daily">Harian (30 Hari Terakhir)</option>
                    <option value="weekly">Mingguan (12 Minggu Terakhir)</option>
                    <option value="monthly">Bulanan (12 Bulan Terakhir)</option>
                    <option value="yearly">Tahunan (5 Tahun Terakhir)</option>
                    <option value="custom">Custom Tanggal</option>
                </select>
            </div>
        </div>

        <!-- Chart.js Visualization -->
        <div class="relative h-96 bg-gray-50 border border-gray-200 rounded p-4 mb-8"
             x-data="{
                chart: null,
                labels: [],
                data: [],
                initChart() {
                    // Destroy existing chart
                    if (this.chart) {
                        this.chart.destroy();
                        this.chart = null;
                    }
                    
                    // Wait for next tick to ensure DOM is ready
                    this.$nextTick(() => {
                        // Validate data
                        if (!this.labels || !this.data || this.labels.length === 0) {
                            console.log('No data to display');
                            return;
                        }
                        
                        const ctx = this.$refs.canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                        // Ensure data is numeric
                        const numericData = this.data.map(Number);

                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: this.labels,
                                datasets: [{
                                    label: 'Total Penjualan',
                                    data: numericData,
                                    backgroundColor: gradient,
                                    borderColor: '#3b82f6',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#3b82f6',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#3b82f6',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 2,
                                    pointHitRadius: 10
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
                                            delay = context.dataIndex * 50;
                                        }
                                        return delay;
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
                                                if (label) {
                                                    label += ': ';
                                                }
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
                                             callback: function(value, index, values) {
                                                 return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(value);
                                             }
                                        }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            color: '#64748b',
                                            maxRotation: 45,
                                            minRotation: 45,
                                            font: { weight: '600' }
                                        }
                                    }
                                }
                            }
                        });
                    });
                },
                updateChart() {
                    this.labels = @js($dates);
                    this.data = @js($totals);
                    this.initChart();
                }
             }"
             x-init="setTimeout(() => updateChart(), 400)"
             @chart-data-updated.window="updateChart()"
             wire:key="sales-chart-{{ $period }}-{{ $startDate }}-{{ $endDate }}"
        >
            <canvas x-ref="canvas"></canvas>
        </div>
        
        <div class="mt-8">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Rincian Data Penjualan</h3>
            <div class="bg-white overflow-hidden border border-gray-200 sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Periode</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Transaksi</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Total Penjualan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['label'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ number_format($item['count'], 0, ',', '.') }} transaksi
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">Rp {{ number_format($item['average'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-sm font-medium">Tidak ada data penjualan untuk periode ini</p>
                                        <p class="text-xs mt-1">Silakan pilih periode lain atau lakukan transaksi penjualan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($data->count() > 0)
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                        <tr class="font-bold">
                            <td class="px-6 py-4 text-sm text-gray-900">TOTAL</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    {{ number_format($totalTransactions, 0, ',', '.') }} transaksi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-900 text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-purple-900 text-right">Rp {{ number_format($overallAverage, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
