<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Laporan Penjualan
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar transaksi penjualan terperinci per periode.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.sales-chart') }}" wire:navigate class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 shadow-sm font-bold flex items-center justify-center gap-2 transition duration-200 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                <span>Lihat Grafik</span>
            </a>
            <a href="{{ route('excel.sales-report', ['startDate' => $startDate, 'endDate' => $endDate, 'paymentMethod' => $paymentMethod, 'search' => $search]) }}" target="_blank" class="btn btn-export-excel">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Excel</span>
            </a>
            <a href="{{ route('pdf.sales-report', ['startDate' => $startDate, 'endDate' => $endDate, 'paymentMethod' => $paymentMethod, 'search' => $search]) }}" target="_blank" class="btn btn-export-pdf">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Filters & Stats Card -->
    <div class="bg-white rounded-xl shadow border overflow-hidden no-print">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-end gap-4">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1 md:items-end">
                <div class="flex flex-col shrink-0">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Tampilkan</label>
                    <select wire:model.live="perPage" class="border-gray-300 rounded-lg py-2 content-center pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all bg-white">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px] w-full">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Cari Invois</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input wire:model.live.debounce.300ms="search" 
                            type="text" placeholder="No Invois/Kasir..." 
                            class="w-full pl-10 pr-4 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                    </div>
                </div>

                <div class="w-full md:w-32 shrink-0">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Metode</label>
                    <select wire:model.live="paymentMethod" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 pr-8 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                        <option value="all">Semua</option>
                        <option value="cash">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                    <div class="flex flex-col">
                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Periode Dari</label>
                        <x-date-picker wire:model.live="startDate" class="w-full md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm"></x-date-picker>
                    </div>
                    <span class="text-gray-400 font-bold self-end mb-2">-</span>
                    <div class="flex flex-col">
                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Sampai</label>
                        <x-date-picker wire:model.live="endDate" class="w-full md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm"></x-date-picker>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex items-center">
                <button wire:click="resetFilters" class="text-xs text-blue-600 font-bold hover:text-blue-800 transition py-2" title="Reset Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left">No. Invois</th>
                                <th class="px-6 py-4 text-left">Tanggal</th>
                                <th class="px-6 py-4 text-left">Kasir</th>
                                <th class="px-6 py-4 text-left">Metode</th>
                                <th class="px-6 py-4 text-right">Total</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $sale->invoice_no }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        {{ $sale->user->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-medium uppercase bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                            {{ $sale->payment_method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right no-print">
                                        <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="text-gray-500 hover:text-gray-700 transition-colors" title="Print Struk">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 italic">Data Tidak Ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900/40 font-bold border-t-2 border-gray-100 dark:border-gray-800">
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-gray-500 dark:text-gray-400 text-right uppercase tracking-wider text-[10px]">Total Penjualan Kotor</td>
                                <td class="px-6 py-3 text-right text-gray-900 dark:text-white text-sm">
                                    Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 no-print"></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-red-600 dark:text-red-400 text-right uppercase tracking-wider text-[10px]">Total Retur</td>
                                <td class="px-6 py-3 text-right text-red-600 dark:text-red-300 text-sm">
                                    - Rp {{ number_format($stats['total_returns'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 no-print"></td>
                            </tr>
                            <tr class="bg-emerald-50/50 dark:bg-emerald-900/20 border-t border-emerald-100 dark:border-emerald-800">
                                <td colspan="4" class="px-6 py-4 text-emerald-700 dark:text-emerald-400 text-right uppercase tracking-wider text-xs">Total Penjualan Bersih</td>
                                <td class="px-6 py-4 text-right text-emerald-700 dark:text-emerald-300 text-lg">
                                    Rp {{ number_format($stats['net_sales'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 no-print"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    @include('components.custom-pagination', ['items' => $sales])
                </div>
            </div>
</div>
