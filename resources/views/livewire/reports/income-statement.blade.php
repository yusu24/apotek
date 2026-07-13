<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Laporan Laba Rugi</h2>
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

            <div class="flex gap-2 w-full md:w-auto justify-end shrink-0 mt-4 md:mt-0">
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
                        <a href="{{ route('excel.income-statement', ['startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" @click="open = false" class="dropdown-item">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                                <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                            </svg>
                            Excel (.xlsx)
                        </a>
                        <a href="{{ route('pdf.profit-loss', ['startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" @click="open = false" class="dropdown-item">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            PDF (.pdf)
                        </a>
                    </div>
                </div>
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
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Penerimaan Kas (Revenue) --}}
        <div class="rounded-xl shadow-lg p-4 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #4338ca; border-color: #3730a3;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium opacity-80">Total Pendapatan</p>
            </div>
            <p class="text-2xl font-bold">Rp. {{ number_format($reportData['total_revenue'], 0, ',', '.') }},-</p>
        </div>
 
        {{-- Laba Kotor --}}
        <div class="rounded-xl shadow-lg p-4 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #0e7490; border-color: #155e75;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <p class="text-sm font-medium opacity-80">Laba Kotor</p>
            </div>
            <p class="text-2xl font-bold">Rp. {{ number_format($reportData['gross_profit'], 0, ',', '.') }},-</p>
        </div>
 
        {{-- Arus Kas Bersih (Net Income) --}}
        <div class="rounded-xl shadow-lg p-4 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4 {{ $reportData['net_income'] >= 0 ? '' : 'animate-pulse' }}" style="background-color: {{ $reportData['net_income'] >= 0 ? '#059669' : '#b91c1c' }}; border-color: {{ $reportData['net_income'] >= 0 ? '#047857' : '#991b1b' }};">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <p class="text-sm font-medium opacity-80">Laba Bersih</p>
            </div>
            <p class="text-2xl font-bold">Rp. {{ number_format($reportData['net_income'], 0, ',', '.') }},-</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-indigo-900 px-6 py-4">
            <h3 class="text-xl font-bold text-white uppercase">DETAIL LABA RUGI</h3>
            <p class="text-sm text-indigo-200 mt-1 italic">Rincian Pendapatan dan Beban Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        </div>
        
        <div class="p-6 space-y-6">
            {{-- REVENUE --}}
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">PENDAPATAN</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['revenue_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp. {{ number_format($account->amount, 0, ',', '.') }},-</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Pendapatan</span>
                    <span class="text-lg font-bold text-green-700">Rp. {{ number_format($reportData['total_revenue'], 0, ',', '.') }},-</span>
                </div>
            </div>

            {{-- COGS --}}
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">HARGA POKOK PENJUALAN (HPP)</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['cogs_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp. {{ number_format($account->amount, 0, ',', '.') }},-</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total HPP</span>
                    <span class="text-lg font-bold text-red-700">(Rp. {{ number_format($reportData['total_cogs'], 0, ',', '.') }},-)</span>
                </div>
            </div>

            {{-- GROSS PROFIT --}}
            <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">LABA KOTOR</span>
                    <span class="text-2xl font-bold text-blue-700">Rp. {{ number_format($reportData['gross_profit'], 0, ',', '.') }},-</span>
                </div>
            </div>

            {{-- OPERATING EXPENSES --}}
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">BEBAN OPERASIONAL</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['operating_expense_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp. {{ number_format($account->amount, 0, ',', '.') }},-</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Beban Operasional</span>
                    <span class="text-lg font-bold text-red-700">(Rp. {{ number_format($reportData['total_operating_expenses'], 0, ',', '.') }},-)</span>
                </div>
            </div>

            {{-- OTHER EXPENSES --}}
            @if($reportData['other_expense_accounts']->count() > 0)
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">BEBAN LAIN-LAIN</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['other_expense_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp. {{ number_format($account->amount, 0, ',', '.') }},-</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Beban Lain-lain</span>
                    <span class="text-lg font-bold text-red-700">(Rp. {{ number_format($reportData['total_other_expenses'], 0, ',', '.') }},-)</span>
                </div>
            </div>
            @endif

            {{-- TAX EXPENSES --}}
            @if(isset($reportData['tax_accounts']) && $reportData['tax_accounts']->count() > 0)
            
            {{-- NET INCOME BEFORE TAX --}}
            <div class="bg-indigo-50 p-4 rounded-lg border-2 border-indigo-200">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">LABA SEBELUM PAJAK</span>
                    <span class="text-2xl font-bold text-indigo-700">Rp. {{ number_format($reportData['net_income_before_tax'], 0, ',', '.') }},-</span>
                </div>
            </div>

            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">BEBAN PAJAK (TAX)</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['tax_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp. {{ number_format($account->amount, 0, ',', '.') }},-</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Beban Pajak</span>
                    <span class="text-lg font-bold text-red-700">(Rp. {{ number_format($reportData['total_tax_expenses'], 0, ',', '.') }},-)</span>
                </div>
            </div>
            @endif

            {{-- NET INCOME --}}
            <div class="bg-gradient-to-r from-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-50 to-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-100 p-4 sm:p-6 rounded-lg border-2 border-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-300">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                    <span class="text-xl sm:text-2xl font-bold text-gray-900 text-center sm:text-left">{{ $reportData['net_income'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</span>
                    <span class="text-2xl sm:text-3xl font-bold text-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-700">
                        Rp. {{ number_format(abs($reportData['net_income']), 0, ',', '.') }},-
                    </span>
                </div>
            </div>

            {{-- Margin Analysis --}}
            @if($reportData['total_revenue'] > 0)
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-sm font-medium text-gray-600">Gross Profit Margin (Laba Kotor)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format(($reportData['gross_profit'] / $reportData['total_revenue']) * 100, 2) }}%
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-sm font-medium text-gray-600">Net Profit Margin (Laba Bersih)</p>
                    <p class="text-2xl font-bold text-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-700 mt-1">
                        {{ number_format(($reportData['net_income'] / $reportData['total_revenue']) * 100, 2) }}%
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

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
