<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center max-w-screen-2xl mx-auto">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                Laporan Grafik Penjualan
            </h2>
             <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>Visualisasi Data</span>
            </div>
        </div>
    </x-slot>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-lg font-medium text-gray-900">Total Periode Ini: Rp {{ number_format($totals->sum(), 0, ',', '.') }}</h3>
            <div class="flex flex-wrap items-center gap-3">
                @if($period === 'custom')
                    <div class="flex items-center gap-2">
                         <input type="date" wire:model.live="startDate" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                         <span class="text-gray-500">-</span>
                         <input type="date" wire:model.live="endDate" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    </div>
                @endif
                <select wire:model.live="period" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
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
                labels: @js($dates),
                data: @js($totals),
                initChart() {
                    if (this.chart) this.chart.destroy();
                    
                    const ctx = this.$refs.canvas.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Total Penjualan',
                                data: this.data,
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: 'rgba(59, 130, 246, 1)',
                                pointHoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                                pointHoverBorderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#e5e7eb' },
                                    ticks: {
                                         callback: function(value, index, values) {
                                             return new Intl.NumberFormat('id-ID', { compactDisplay: 'short', notation: 'compact' }).format(value);
                                         }
                                    }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
             }"
             x-init="initChart()"
             x-effect="labels = @js($dates); data = @js($totals); initChart();"
        >
            <canvas x-ref="canvas"></canvas>
        </div>
        
        <div class="mt-8">
            <h3 class="font-bold text-lg mb-4">Rincian Data</h3>
            <div class="bg-white overflow-hidden border border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dates as $index => $date)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($totals[$index], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada data penjualan untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
