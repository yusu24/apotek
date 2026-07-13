<div class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                Laporan Penjualan (Grafik)
            </h2>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
            <a href="{{ route('reports.sales') }}" wire:navigate class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 shadow-sm font-bold flex items-center justify-center gap-2 transition duration-200 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <span>Lihat Tabel Transaksi</span>
            </a>
            <button x-data @click="$dispatch('open-import-omset-modal')" class="btn btn-import no-print" title="Import Omset Excel">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span class="hidden sm:inline">Import Omset</span>
            </button>
            <div class="relative btn-export-dropdown" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="btn btn-export-excel" title="Export">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="hidden sm:inline ml-1">Export</span>
                    <svg class="w-3 h-3 ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="dropdown-menu" x-show="open" x-cloak style="display:none">
                    <a href="{{ route('excel.sales-report', ['startDate' => $startDate, 'endDate' => $endDate, 'paymentMethod' => 'all', 'search' => '']) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                        Excel (.xlsx)
                    </a>
                    <a href="{{ route('pdf.sales-report', ['startDate' => $startDate, 'endDate' => $endDate, 'paymentMethod' => 'all', 'search' => '']) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Pendapatan</p>
                    <p class="text-2xl font-bold mt-1">Rp. {{ number_format($totalRevenue, 0, ',', '.') }},-</p>
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
                    <p class="text-2xl font-bold mt-1">Rp. {{ number_format($overallAverage, 0, ',', '.') }},-</p>
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
                         <x-date-picker wire:model.live="startDate" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"></x-date-picker>
                         <span class="text-gray-500">-</span>
                         <x-date-picker wire:model.live="endDate" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"></x-date-picker>
                    </div>
                @endif
                <select wire:model.live="period" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="active_month">Bulan Aktif</option>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">Rp. {{ number_format($item['total'], 0, ',', '.') }},-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">Rp. {{ number_format($item['average'], 0, ',', '.') }},-</td>
                            </tr>
                        @empty
                            <x-empty-table colspan="4" message="Tidak ada data penjualan untuk periode ini" subheader="Silakan pilih periode lain atau lakukan transaksi penjualan" />
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
                            <td class="px-6 py-4 text-sm text-blue-900 text-right">Rp. {{ number_format($totalRevenue, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-sm text-purple-900 text-right">Rp. {{ number_format($overallAverage, 0, ',', '.') }},-</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Import Omset Modal (Standalone) -->
    <div x-data="{ openImportOmset: false }" @open-import-omset-modal.window="openImportOmset = true" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div x-show="openImportOmset" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openImportOmset = false"></div>

        <div x-show="openImportOmset" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form action="{{ route('import.omset') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Import Data Omset Historis</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Unduh template Excel, isi data omset historis Anda (tanggal atau tahun, omset, hpp, dan laba), lalu upload kembali di sini.
                                        </p>
                                        
                                        <div class="mb-4">
                                            <a href="{{ route('import.download-omset-template') }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download Template Excel
                                            </a>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Upload File Excel</label>
                                            <input type="file" name="file" accept=".xlsx, .xls" required class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-blue-50 file:text-blue-700
                                                hover:file:bg-blue-100
                                            "/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="btn btn-success w-full sm:w-auto sm:ml-3">Import Sekarang</button>
                            <button type="button" @click="openImportOmset = false" class="btn btn-secondary w-full sm:w-auto mt-3 sm:mt-0">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
