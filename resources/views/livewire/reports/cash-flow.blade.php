<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Arus Kas (Cash Flow)</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
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
                    <a href="{{ route('pdf.cash-flow', ['startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden no-print mb-6 relative">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1 md:items-center">
                <div class="w-full md:w-40">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mulai</label>
                    <x-date-picker wire:model.live="startDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white"></x-date-picker>
                </div>
                <div class="w-full md:w-40">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai</label>
                    <x-date-picker wire:model.live="endDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white"></x-date-picker>
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto justify-end shrink-0">
                <button wire:click="generateReport" class="btn btn-primary" title="Generate Report">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="hidden sm:inline text-sm">Generate</span>
                </button>
            </div>
        </div>

        <div class="px-4 py-3 bg-white flex flex-wrap gap-2">
            <button wire:click="setThisMonth" class="btn btn-xs btn-secondary">Bulan Ini</button>
            <button wire:click="setLastMonth" class="btn btn-xs btn-secondary">Bulan Lalu</button>
            <button wire:click="setThisYear" class="btn btn-xs btn-secondary">Tahun Ini</button>
        </div>
    </div>

    @if(!empty($reportData))
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Operating --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-4 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operasional</span>
                    <div class="w-8 h-8 rounded-full bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="{{ format_accounting_paren($reportData['net_cash_operating']) }}">
                    {{ format_accounting_paren($reportData['net_cash_operating']) }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate">
                    Arus kas operasional
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-cyan-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        {{-- Investing --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-4 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Investasi</span>
                    <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="{{ format_accounting_paren($reportData['net_cash_investing']) }}">
                    {{ format_accounting_paren($reportData['net_cash_investing']) }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate">
                    Arus kas investasi aset
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        {{-- Financing --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-4 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pendanaan</span>
                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="{{ format_accounting_paren($reportData['net_cash_financing']) }}">
                    {{ format_accounting_paren($reportData['net_cash_financing']) }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate">
                    Arus kas pendanaan/modal
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-indigo-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        {{-- Net Increase --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-4 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kenaikan Bersih</span>
                    <div class="w-8 h-8 rounded-full {{ $reportData['net_increase'] >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' }} flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="text-xl lg:text-2xl font-bold {{ $reportData['net_increase'] >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }} tracking-tight truncate" title="{{ format_accounting_paren($reportData['net_increase']) }}">
                    {{ format_accounting_paren($reportData['net_increase']) }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate">
                    Total selisih kas netto
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
                <div class="h-full {{ $reportData['net_increase'] >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        {{-- Ending Balance --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-4 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo Akhir</span>
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-300 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="{{ format_accounting_paren($reportData['ending_balance']) }}">
                    {{ format_accounting_paren($reportData['ending_balance']) }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 truncate">
                    Posisi kas akhir periode
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>
    </div>
    
    {{-- Main Table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden print:hidden">
        <div class="bg-gray-900 px-6 py-4">
            <h3 class="text-xl font-bold text-white uppercase">DETAIL ARUS KAS</h3>
             <p class="text-sm text-gray-300 mt-1 italic">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Operating --}}
            <div>
                 <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">AKTIVITAS OPERASIONAL</h4>
                 <div class="space-y-2 ml-4">
                     {{-- Rows --}}
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Penerimaan dari pelanggan</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['receipts_from_customers']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pembayaran ke pemasok</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['payments_to_suppliers']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pengeluaran operasional</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['payments_for_expenses']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pendapatan lainnya</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['other_operating']) }}</span>
                    </div>
                 </div>
                 <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Kas Bersih dari Operasional</span>
                    <span class="text-lg font-bold text-blue-700">{{ format_accounting_paren($reportData['net_cash_operating']) }}</span>
                </div>
            </div>

            {{-- Investing --}}
            <div>
                 <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">AKTIVITAS INVESTASI</h4>
                 <div class="space-y-2 ml-4">
                     {{-- Rows --}}
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Perolehan/Penjualan Aset</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['sale_assets'] + $reportData['purchase_assets']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Investasi Lainnya</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['other_investing']) }}</span>
                    </div>
                 </div>
                 <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Kas Bersih dari Investasi</span>
                    <span class="text-lg font-bold text-amber-700">{{ format_accounting_paren($reportData['net_cash_investing']) }}</span>
                </div>
            </div>

            {{-- Financing --}}
             <div>
                 <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">AKTIVITAS PENDANAAN</h4>
                 <div class="space-y-2 ml-4">
                     {{-- Rows --}}
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pinjaman</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['loans']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Ekuitas/Modal</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting_paren($reportData['equity']) }}</span>
                    </div>
                 </div>
                 <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Kas Bersih dari Pendanaan</span>
                    <span class="text-lg font-bold text-indigo-700">{{ format_accounting_paren($reportData['net_cash_financing']) }}</span>
                </div>
            </div>
            
            {{-- Summary --}}
            <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200 mt-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">Saldo Kas Awal</span>
                    <span class="text-base font-bold text-gray-800">{{ format_accounting_paren($reportData['beginning_balance']) }}</span>
                </div>
                 <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">Kenaikan/Penurunan Bersih</span>
                    <span class="text-base font-bold text-{{ $reportData['net_increase'] >= 0 ? 'green' : 'red' }}-700">{{ format_accounting_paren($reportData['net_increase']) }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                    <span class="text-lg font-bold text-gray-900 uppercase">Saldo Kas Akhir</span>
                    <span class="text-xl font-bold text-gray-900">{{ format_accounting_paren($reportData['ending_balance']) }}</span>
                </div>
            </div>

        </div>
    </div>
    @endif
    



    @php
    function format_accounting_paren($number) {
        if ($number < 0) {
            return '( ' . number_format(abs($number), 0, ',', '.') . ' )';
        }
        return number_format($number, 0, ',', '.');
    }
    @endphp

</div>
