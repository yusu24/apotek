<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Laporan Penjualan
            </h2>
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 no-print">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <div class="text-[10px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Total Transaksi</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['transaction_count']) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <div class="text-[10px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Penjualan Kotor (Uang Diterima)</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <div class="text-[10px] font-extrabold text-rose-500 uppercase tracking-wider mb-1">Total Retur Penjualan</div>
            <div class="text-xl font-bold text-rose-600">Rp {{ number_format($stats['total_returns'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 border border-emerald-500/30 bg-emerald-50/10">
            <div class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider mb-1">Total Penjualan Bersih (Pendapatan Riil)</div>
            <div class="text-xl font-bold text-emerald-700 dark:text-emerald-400">Rp {{ number_format($stats['net_sales'], 0, ',', '.') }}</div>
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
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="viewSale({{ $sale->id }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="Lihat Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="text-gray-500 hover:text-gray-700 transition-colors" title="Print Struk">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
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
                                <td colspan="4" class="px-6 py-4 text-gray-700 dark:text-gray-300 text-right uppercase text-xs">Total Penjualan Bersih (Pendapatan Riil)</td>
                                <td class="px-6 py-4 text-right text-emerald-700 dark:text-emerald-400 text-base">
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

            <!-- Financial Reconciliation and Explanation Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Info/Legend Card -->
                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-900/20 p-5 rounded-2xl border border-gray-100 dark:border-gray-800 text-xs text-gray-500 space-y-2">
                    <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Informasi & Rumus Laporan</h4>
                    <p>• <strong>Penjualan Kotor (Uang Diterima)</strong>: Total akumulasi uang yang masuk ke kasir (termasuk pajak PPN dan pembulatan kembalian).</p>
                    <p>• <strong>Subtotal Bersih (DPP)</strong>: Nilai dasar pengenaan pajak (penjualan kotor dikurangi PPN dan selisih pembulatan kasir).</p>
                    <p>• <strong>Total Retur Penjualan</strong>: Total nominal transaksi penjualan yang dibatalkan/dikembalikan oleh pelanggan pada periode ini.</p>
                    <p>• <strong>Total Penjualan Bersih (Pendapatan Riil)</strong>: Pendapatan riil yang diperoleh toko (Rumus: <strong>DPP - Retur Penjualan</strong>).</p>
                </div>

                <!-- Reconciliation Details Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-b pb-2">Rincian Rekonsiliasi Pendapatan</h4>
                    
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Penjualan Kotor</span>
                            <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 pl-3 border-l-2 border-gray-200 dark:border-gray-700 italic">
                            <span>Pajak PPN</span>
                            <span>- Rp {{ number_format($stats['total_tax'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 pl-3 border-l-2 border-gray-200 dark:border-gray-700 italic">
                            <span>Selisih Pembulatan</span>
                            <span>- Rp {{ number_format($stats['total_rounding'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between font-semibold text-blue-600 dark:text-blue-400 pt-1 border-t border-dashed border-gray-200 dark:border-gray-700">
                            <span>Subtotal Bersih (DPP)</span>
                            <span>Rp {{ number_format($stats['total_dpp'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between text-red-600 dark:text-red-400">
                            <span>Retur Penjualan</span>
                            <span>- Rp {{ number_format($stats['total_returns'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100 dark:border-gray-700 font-bold text-emerald-600 dark:text-emerald-400">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-wider">Pendapatan Riil</span>
                                <span class="text-[8px] text-gray-400 dark:text-gray-500 normal-case font-normal">Rumus: DPP - Retur</span>
                            </div>
                            <span class="text-base">Rp {{ number_format($stats['net_sales'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Sale Detail Modal -->
    @if($showDetailModal && $selectedSale)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeDetail"></div>

            <!-- Modal Content -->
            <div class="relative inline-block w-full max-w-3xl overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detail Transaksi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $selectedSale['invoice_no'] }}</p>
                    </div>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Invoice Info -->
                <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 border-b border-gray-100 dark:border-gray-700 text-sm">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ \Carbon\Carbon::parse($selectedSale['date'])->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kasir</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $selectedSale['user_name'] }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode Bayar</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $selectedSale['payment_method'] }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $selectedSale['customer_name'] ?? 'Umum' }}</span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="px-6 py-4 max-h-[50vh] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400 font-normal uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-right">Diskon</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($selectedSale['sale_items'] as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item['product_name'] }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $item['unit_name'] }}
                                        @if($item['batch_number'] !== '-')
                                            · Batch: {{ $item['batch_number'] }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">{{ $item['quantity'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($item['sell_price'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-rose-500">
                                    @if($item['discount_amount'] > 0)
                                        -Rp {{ number_format($item['discount_amount'], 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Payment Summary -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col gap-1.5 max-w-xs ml-auto text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($selectedSale['total_amount'], 0, ',', '.') }}</span>
                        </div>
                        @if(($selectedSale['discount'] ?? 0) > 0)
                        <div class="flex justify-between text-rose-500">
                            <span>Diskon</span>
                            <span>-Rp {{ number_format($selectedSale['discount'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if(($selectedSale['tax'] ?? 0) > 0)
                        <div class="flex justify-between text-gray-500">
                            <span>PPN</span>
                            <span>Rp {{ number_format($selectedSale['tax'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if(($selectedSale['service_charge_amount'] ?? 0) > 0)
                        <div class="flex justify-between text-gray-500">
                            <span>Service Charge</span>
                            <span>Rp {{ number_format($selectedSale['service_charge_amount'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-base text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span>Grand Total</span>
                            <span>Rp {{ number_format($selectedSale['grand_total'], 0, ',', '.') }}</span>
                        </div>
                        @if($selectedSale['payment_method'] === 'cash' && ($selectedSale['cash_amount'] ?? 0) > 0)
                        <div class="flex justify-between text-gray-500 text-xs">
                            <span>Bayar Tunai</span>
                            <span>Rp {{ number_format($selectedSale['cash_amount'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 text-xs font-medium">
                            <span>Kembalian</span>
                            <span>Rp {{ number_format($selectedSale['change_amount'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <a href="{{ route('pos.receipt', $selectedSale['id']) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Struk
                    </a>
                    <button wire:click="closeDetail" class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
