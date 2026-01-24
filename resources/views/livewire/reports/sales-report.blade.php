<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ __('Laporan Penjualan') }}
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
            <a href="{{ route('pdf.sales-report', ['startDate' => $startDate, 'endDate' => $endDate, 'paymentMethod' => $paymentMethod, 'search' => $search]) }}" target="_blank" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold flex items-center justify-center gap-2 transition duration-200 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Filters & Stats Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 no-print">
        <div class="flex flex-wrap md:flex-nowrap gap-4 md:gap-6 items-center">
            <!-- Date Range (Order 1) -->
            <div class="flex items-center gap-2 w-full md:w-auto order-1">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Periode</label>
                <div class="flex items-center gap-1.5 flex-1 md:flex-none">
                    <input type="date" wire:model.live="startDate" class="flex-1 md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="date" wire:model.live="endDate" class="flex-1 md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <div class="flex items-center gap-2 flex-1 md:flex-none order-2">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Cari Invois</label>
                <input wire:model.live.debounce.300ms="search" 
                    type="text" placeholder="No Invois/Kasir..." 
                    class="w-full md:w-64 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <!-- Payment Method (Order 3) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-3">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Metode</label>
                <select wire:model.live="paymentMethod" class="w-full md:w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 !pr-12 focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="all">Semua</option>
                    <option value="cash">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <!-- Reset (Order 4) -->
            <div class="order-4 md:ml-auto">
                <button wire:click="resetFilters" class="px-3 md:px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 shadow-sm font-bold text-sm flex items-center justify-center gap-2 transition duration-200" title="Reset semua filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="hidden md:inline">Reset Filter</span>
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
                                        {{ \Carbon\Carbon::parse($sale->date)->format('d M Y H:i') }}
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
                        <tfoot class="bg-emerald-50/50 dark:bg-emerald-900/20 font-bold border-t-2 border-emerald-100 dark:border-emerald-800">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-emerald-700 dark:text-emerald-400 text-right uppercase tracking-wider text-xs">Total Penjualan</td>
                                <td class="px-6 py-4 text-right text-emerald-700 dark:text-emerald-300">
                                    Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 no-print"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    {{ $sales->links() }}
                </div>
            </div>
</div>
