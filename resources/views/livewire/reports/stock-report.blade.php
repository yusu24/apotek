<div class="p-6 space-y-6">
    <!-- Page Title (Web View Only) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ __('Laporan Stok & Nilai') }}
            </h2>
            <p class="text-sm text-gray-500 font-medium">Informasi ketersediaan barang dan valuasi inventaris secara real-time.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportExcel" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 shadow-md font-bold flex items-center justify-center gap-2 transition duration-200 text-sm" title="Export Excel">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Excel</span>
            </button>
            <a href="{{ route('pdf.stock-report', ['search' => $search, 'category' => $categoryFilter, 'stockStatus' => $stockStatus]) }}" target="_blank" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Print Header (Simplified) -->
    <div class="hidden print:block">
        <div class="border-b-2 border-gray-900 pb-2 text-center uppercase">
            <h1 class="text-xl font-bold text-gray-950">LAPORAN STOK</h1>
            <div class="text-[12px] font-bold mt-1">
                PERIODE: {{ $startExpiry ? \Carbon\Carbon::parse($startExpiry)->format('d-m-Y') : 'AWAL' }} s/d {{ $endExpiry ? \Carbon\Carbon::parse($endExpiry)->format('d-m-Y') : 'AKHIR' }}
            </div>
            <div class="text-[9px] text-gray-500 mt-1 italic normal-case">Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }}</div>
        </div>
    </div>


    <!-- Header Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 no-print">
        <div class="flex flex-wrap md:flex-nowrap gap-4 md:gap-6 items-center">
            <div class="flex items-center gap-2 flex-1 md:flex-none order-1">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Cari Produk</label>
                <input wire:model.live.debounce.300ms="search" 
                    type="text" placeholder="Nama atau barcode..." 
                    class="w-full md:w-48 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <!-- Category Filter (Order 2) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-2">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Kategori</label>
                <select wire:model.live="categoryFilter" class="w-full md:w-32 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 !pr-12 focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">Semua</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Status Filter (Order 3) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-3">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Status</label>
                <button wire:click="toggleStockStatus" 
                    class="w-full md:w-32 px-3 py-2 rounded-lg border text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2
                    {{ $stockStatus === 'low' 
                        ? 'bg-orange-500 text-white border-orange-600 shadow-inner' 
                        : 'bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-100' }}"
                    title="{{ $stockStatus === 'low' ? 'Tampilkan Semua' : 'Tampilkan Stok Menipis' }}">
                    <div class="w-2 h-2 rounded-full {{ $stockStatus === 'low' ? 'bg-white animate-pulse' : 'bg-gray-400' }}"></div>
                    {{ $stockStatus === 'low' ? 'Menipis' : 'Semua' }}
                </button>
            </div>

            <!-- Expiry Periode (Order 4) -->
            <div class="flex items-center gap-2 w-full md:w-auto order-4">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Kadaluarsa</label>
                <div class="flex items-center gap-1.5 flex-1 md:flex-none">
                    <input type="date" wire:model.live="startExpiry" class="flex-1 md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="date" wire:model.live="endExpiry" class="flex-1 md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <!-- Reset (Order 5) -->
            <div class="order-5 md:ml-auto">
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
                    <table class="w-full text-left border-collapse custom-print-table">
                        <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs print:bg-transparent">
                            <tr>
                                <th class="px-6 py-4 text-left">NO.</th>
                                <th class="px-4 py-4 text-left">KODE BARANG</th>
                                <th class="px-4 py-4 text-left">NAMA BARANG</th>
                                <th class="px-4 py-4 text-left">SATUAN</th>
                                <th class="px-4 py-4 text-right">STOK</th>
                                <th class="px-4 py-4 text-right">Harga Beli</th>
                                <th class="px-6 py-4 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 print:divide-none">
                            @php $index = ($batches->currentPage() - 1) * $batches->perPage() + 1; @endphp
                            @forelse($batches as $batch)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/40 transition-colors print:border-none">
                                    <td class="px-6 py-4 print:px-2 print:py-1 text-sm print:text-[10px] text-gray-900 dark:text-white">
                                        {{ $index++ }}
                                    </td>
                                    <td class="px-4 py-4 print:px-2 print:py-1 text-sm print:text-[10px] font-mono text-gray-600 dark:text-gray-300">
                                        {{ $batch->product->barcode }}
                                    </td>
                                    <td class="px-4 py-4 print:px-2 print:py-1 text-sm print:text-[10px] text-gray-900 dark:text-white uppercase font-medium">
                                        {{ $batch->product->name }}
                                        <div class="text-[11px] text-gray-400 no-print">{{ $batch->batch_no }} - Kadaluarsa: {{ $batch->expired_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-4 print:px-2 print:py-1 text-sm print:text-[10px] text-gray-500 dark:text-gray-400 uppercase">
                                        {{ $batch->product->unit->name }}
                                    </td>
                                    <td class="px-4 py-4 print:px-2 print:py-1 text-right text-sm print:text-[10px] text-gray-900 dark:text-white">
                                        {{ number_format($batch->stock_current, 0) }}
                                    </td>
                                    <td class="px-4 py-4 print:px-2 print:py-1 text-right text-sm print:text-[10px] text-gray-600 dark:text-gray-400 tabular-nums">
                                        {{ number_format($batch->buy_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 print:px-2 print:py-1 text-right text-sm print:text-[10px] text-gray-900 dark:text-white tabular-nums">
                                        {{ number_format($batch->stock_current * $batch->buy_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-12 text-center text-gray-400 italic text-sm">
                                        Data tidak ditemukan untuk kriteria filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($batches->count() > 0)
                            <tfoot class="bg-gray-50 dark:bg-gray-900/80 print:bg-transparent">
                                <tr class="font-bold border-t-2 border-gray-100 dark:border-gray-800 print:border-t-2 print:border-gray-800">
                                    <td colspan="4" class="px-6 py-4 print:px-2 print:py-1 text-xs text-gray-400 print:text-gray-900 uppercase text-center">TOTAL KESELURUHAN</td>
                                    <td class="px-4 py-4 print:px-2 print:py-1 text-right tabular-nums">
                                        <div class="text-sm print:text-[10px] text-gray-900 dark:text-white">{{ number_format($totalStock, 0) }}</div>
                                    </td>
                                    <td></td>
                                    <td class="px-6 py-4 print:px-2 print:py-1 text-right border-l border-gray-100 dark:border-gray-800 print:border-l-0 tabular-nums">
                                        <div class="text-sm print:text-[10px] text-gray-900 dark:text-white">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</div>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Footer Pagination [no-print] -->
                <div class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex justify-end no-print">
                    {{ $batches->links() }}
                </div>
            </div>
    <style>
        .tabular-nums { font-variant-numeric: tabular-nums; }
        
        @media print {
            @page {
                size: A4;
                margin: 1cm 1.5cm;
            }
            .no-print { display: none !important; }
            header, nav, #sidebar, x-slot[name="header"] { display: none !important; }
            .max-w-screen-2xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
            
            body { 
                background: white !important; 
                color: black !important;
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
                font-family: Arial, sans-serif !important;
            }
            
            .bg-gray-50, .dark\:bg-gray-950, .dark\:bg-gray-900, .bg-indigo-900 { background-color: transparent !important; }
            .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; }
            .border, .border-gray-200, .dark\:border-gray-700 { border-color: transparent !important; }
            
            .custom-print-table { 
                width: 100% !important; 
                border-collapse: collapse !important; 
                margin-top: 20px !important;
            }
            
            .custom-print-table th { 
                border-top: 1.5pt solid black !important; 
                border-bottom: 1.5pt solid black !important; 
                padding: 8px 4px !important;
                text-align: left !important;
                font-size: 9pt !important;
                color: black !important;
                background: transparent !important;
            }
            
            .custom-print-table td { 
                padding: 6px 4px !important;
                font-size: 9pt !important;
                color: black !important;
                border: none !important;
            }
            
            .custom-print-table tfoot tr {
                border-top: 1.5pt solid black !important;
                border-bottom: 1.5pt solid black !important;
            }

            .flex-shrink-0, .ml-auto { display: none !important; }
        }
    </style>
</div>
