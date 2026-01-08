<div class="p-6 space-y-6">
    <!-- Page Title (Web View Only) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ __('Laporan Stok & Nilai') }}
            </h2>
            <p class="text-sm text-gray-500 font-medium">Informasi ketersediaan barang dan valuasi inventaris secara real-time.</p>
        </div>
    </div>

    <!-- Print Header (Simplified) -->
    <div class="hidden print:block">
        <div class="border-b-2 border-gray-900 pb-2 text-center uppercase">
            <h1 class="text-xl font-bold tracking-widest text-gray-950">LAPORAN STOK</h1>
            <div class="text-[12px] font-bold mt-1">
                PERIODE: {{ $startExpiry ? \Carbon\Carbon::parse($startExpiry)->format('d-m-Y') : 'AWAL' }} s/d {{ $endExpiry ? \Carbon\Carbon::parse($endExpiry)->format('d-m-Y') : 'AKHIR' }}
            </div>
            <div class="text-[9px] text-gray-500 mt-1 italic normal-case">Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }}</div>
        </div>
    </div>

            <!-- Summary Cards (Original Colors) [no-print] -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 no-print">
                <!-- Value Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">Estimasi Nilai Barang (HPP)</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-xs font-semibold text-gray-400">Rp</span>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalInventoryValue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stock Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Total Unit Stok</p>
                        <div class="flex items-baseline gap-1">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalStock, 0) }}</p>
                            <span class="text-xs font-semibold text-gray-400">item</span>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-indigo-900 rounded-lg shadow-md p-6 text-white flex items-center justify-between">
                    <div class="truncate mr-2">
                        <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-wider mb-1 opacity-70">Pencarian & Filter</p>
                        @if($search || $startExpiry || $endExpiry)
                            <p class="text-lg font-bold truncate">
                                @if($search) "{{ $search }}" @else Filter Aktif @endif
                            </p>
                        @else
                            <p class="text-lg font-bold">Semua Data</p>
                        @endif
                    </div>
                    <div class="bg-white/10 rounded-full p-2 flex-shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Main Content Area: Filter & Table Unified in one Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden print:rounded-none print:shadow-none print:border-none border border-gray-200 dark:border-gray-700">
                <!-- Filter Header (Hidden in Print) -->
                <div class="px-6 py-4 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 no-print">
                    <div class="flex flex-row items-center gap-3">
                        <!-- Search Box -->
                        <div class="w-48 flex-shrink-0">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama/Barcode..." class="block w-full pl-8 pr-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <!-- Date Range Selection -->
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-700 rounded-lg px-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider pl-1">Kadaluarsa:</span>
                            <input wire:model.live="startExpiry" type="date" class="bg-transparent border-none text-sm text-gray-900 dark:text-white focus:ring-0 p-1.5 w-36">
                            <span class="text-gray-300">/</span>
                            <input wire:model.live="endExpiry" type="date" class="bg-transparent border-none text-sm text-gray-900 dark:text-white focus:ring-0 p-1.5 w-36">
                        </div>

                        <!-- Print Button -->
                        <div class="flex-shrink-0 ml-auto">
                            <button onclick="window.print()" class="btn btn-lg btn-secondary">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak A4
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse custom-print-table">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 print:bg-transparent">
                                <th class="px-6 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900">NO.</th>
                                <th class="px-4 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900">KODE BARANG</th>
                                <th class="px-4 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900">NAMA BARANG</th>
                                <th class="px-4 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900">SATUAN</th>
                                <th class="px-4 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900">STOK</th>
                                <th class="px-4 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900 uppercase">Harga Beli</th>
                                <th class="px-6 py-3 print:px-2 print:py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right border-b border-gray-200 dark:border-gray-700 print:border-t print:border-gray-800 print:text-gray-900 uppercase">Saldo</th>
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
                                    <td colspan="4" class="px-6 py-4 print:px-2 print:py-1 text-xs text-gray-400 print:text-gray-900 uppercase tracking-widest text-center">TOTAL KESELURUHAN</td>
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
