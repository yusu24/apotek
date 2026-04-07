<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Neraca</h2>
            <p class="text-sm text-gray-500">Laporan Posisi Keuangan</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <button wire:click="setEndOfLastMonth" class="btn btn-secondary">
                Akhir Bulan Lalu
            </button>
            <button wire:click="setEndOfThisMonth" class="btn btn-secondary">
                Akhir Bulan Ini
            </button>
            <button wire:click="setEndOfThisYear" class="btn btn-secondary">
                Akhir Tahun Ini
            </button>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 relative">
        <div wire:loading wire:target="generateReport, setThisMonth, setLastMonth, setThisYear, startDate, endDate" class="absolute top-2 right-4 text-blue-600 text-sm font-bold italic flex items-center gap-2 bg-white/80 px-2 rounded opacity-90 z-20">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Memperbarui data...
        </div>
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-auto">
                <label class="block text-sm font-bold text-gray-700 mb-2">Per Tanggal</label>
                <x-date-picker wire:model.live="asOfDate" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"></x-date-picker>
            </div>
            <div class="w-full md:w-auto flex items-end gap-2">
                <button wire:click="generateReport" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Generate</span>
                </button>
                <a href="{{ route('pdf.balance-sheet', ['asOfDate' => $asOfDate]) }}" target="_blank" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>
    </div>

    @if(!empty($reportData))
    {{-- Balance Check Alert --}}
    @if(!$reportData['balance_check'])
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            </div>
            <p class="ml-3 text-sm font-bold text-red-700">Perhatian: Neraca tidak balance! Total Aset ≠ Liabilitas + Ekuitas</p>
        </div>
    </div>
    @endif

    {{-- Report Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Aset --}}
        <div class="rounded-2xl shadow-xl p-4 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #1e40af; border-color: #1e3a8a;">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <p class="text-sm font-bold tracking-wider uppercase">Total Aset</p>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($reportData['total_assets'], 0, ',', '.') }}</p>
        </div>
 
        {{-- Total Liabilitas --}}
        <div class="rounded-2xl shadow-xl p-4 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #be123c; border-color: #9f1239;">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-bold tracking-wider uppercase">Total Liabilitas</p>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($reportData['total_liabilities'], 0, ',', '.') }}</p>
        </div>
 
        {{-- Total Ekuitas --}}
        <div class="rounded-2xl shadow-xl p-4 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #059669; border-color: #047857;">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <p class="text-sm font-bold tracking-wider uppercase">Total Ekuitas</p>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($reportData['total_equity'] + $reportData['net_income'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Balance Sheet Table --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ASET (LEFT SIDE) --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">ASET</h3>
            </div>
            
            <div class="p-6 space-y-6">
                {{-- Current Assets --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Aset Lancar</h4>
                    <div class="space-y-4">
                        @foreach($reportData['current_asset_groups'] as $groupKey => $group)
                            @if($group['accounts']->count() > 0)
                            <div class="ml-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-blue-600 uppercase">{{ $group['label'] }}</span>
                                </div>
                                <div class="pl-2 border-l-2 border-blue-100 space-y-1">
                                    @foreach($group['accounts'] as $account)
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-sm text-gray-600">{{ $account->name }}</span>
                                        <span class="text-sm text-gray-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    <div class="flex justify-between items-center pt-1 border-t border-blue-50">
                                        <span class="text-sm font-bold text-gray-700">Subtotal {{ $group['label'] }}</span>
                                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($group['total'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-3 border-t-2 border-blue-200 bg-blue-50/50 p-2 rounded">
                        <span class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">Total Aset Lancar</span>
                        <span class="text-lg font-bold text-blue-800">Rp {{ number_format($reportData['total_current_assets'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Fixed Assets --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Aset Tetap</h4>
                    <div class="space-y-4">
                        @foreach($reportData['fixed_asset_groups'] as $groupKey => $group)
                            @if($group['accounts']->count() > 0)
                            <div class="ml-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-indigo-600 uppercase">{{ $group['label'] }}</span>
                                </div>
                                <div class="pl-2 border-l-2 border-indigo-100 space-y-1">
                                    @foreach($group['accounts'] as $account)
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-sm text-gray-600">{{ $account->name }}</span>
                                        <span class="text-sm text-gray-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    <div class="flex justify-between items-center pt-1 border-t border-indigo-50">
                                        <span class="text-sm font-bold text-gray-700">Subtotal {{ $group['label'] }}</span>
                                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($group['total'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-3 border-t-2 border-indigo-200 bg-indigo-50/50 p-2 rounded">
                        <span class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">Total Aset Tetap</span>
                        <span class="text-lg font-bold text-indigo-800">Rp {{ number_format($reportData['total_fixed_assets'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Total Assets --}}
                <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">TOTAL ASET</span>
                        <span class="text-2xl font-bold text-blue-700">Rp {{ number_format($reportData['total_assets'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- LIABILITAS & EKUITAS (RIGHT SIDE) --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">LIABILITAS & EKUITAS</h3>
            </div>
            
            <div class="p-6 space-y-6">
                {{-- Liabilitas Lancar --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Liabilitas Lancar</h4>
                    <div class="space-y-4">
                        @foreach($reportData['current_liability_groups'] as $groupKey => $group)
                            @if($group['accounts']->count() > 0)
                            <div class="ml-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-red-600 uppercase">{{ $group['label'] }}</span>
                                </div>
                                <div class="pl-2 border-l-2 border-red-100 space-y-1">
                                    @foreach($group['accounts'] as $account)
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-sm text-gray-600">{{ $account->name }}</span>
                                        <span class="text-sm text-gray-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    <div class="flex justify-between items-center pt-1 border-t border-red-50">
                                        <span class="text-sm font-bold text-gray-700">Subtotal {{ $group['label'] }}</span>
                                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($group['total'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-3 border-t-2 border-red-200 bg-red-50/50 p-2 rounded">
                        <span class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">Total Liabilitas Lancar</span>
                        <span class="text-lg font-bold text-red-800">Rp {{ number_format($reportData['total_current_liabilities'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Liabilitas Jangka Panjang --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Liabilitas Jangka Panjang</h4>
                    <div class="space-y-4">
                        @foreach($reportData['long_term_liability_groups'] as $groupKey => $group)
                            @if($group['accounts']->count() > 0)
                            <div class="ml-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-orange-600 uppercase">{{ $group['label'] }}</span>
                                </div>
                                <div class="pl-2 border-l-2 border-orange-100 space-y-1">
                                    @foreach($group['accounts'] as $account)
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-sm text-gray-600">{{ $account->name }}</span>
                                        <span class="text-sm text-gray-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    <div class="flex justify-between items-center pt-1 border-t border-orange-50">
                                        <span class="text-sm font-bold text-gray-700">Subtotal {{ $group['label'] }}</span>
                                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($group['total'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-3 border-t-2 border-orange-200 bg-orange-50/50 p-2 rounded">
                        <span class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">Total Liabilitas Jangka Panjang</span>
                        <span class="text-lg font-bold text-orange-800">Rp {{ number_format($reportData['total_long_term_liabilities'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Ekuitas / Modal --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Ekuitas / Modal</h4>
                    <div class="space-y-4">

                        {{-- 1. Modal Sendiri --}}
                        <div class="ml-2">
                            <span class="text-xs font-bold text-green-700 uppercase block mb-1">Modal Sendiri</span>
                            <div class="pl-2 border-l-2 border-green-100 space-y-1">
                                @foreach($reportData['equity_groups']['paid_in_capital']['accounts'] as $account)
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-sm text-gray-600">{{ $account->name }}</span>
                                    <span class="text-sm text-gray-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                <div class="flex justify-between items-center pt-1 border-t border-green-50">
                                    <span class="text-sm font-bold text-gray-700">Subtotal Modal Sendiri</span>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($reportData['equity_groups']['paid_in_capital']['total'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Laba Ditahan --}}
                        <div class="ml-2">
                            <span class="text-xs font-bold text-green-700 uppercase block mb-1">Laba Ditahan</span>
                            <div class="pl-2 border-l-2 border-green-100 space-y-1">
                                @foreach($reportData['equity_groups']['retained_earnings']['accounts'] as $account)
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-sm text-gray-600">{{ $account->name }}</span>
                                    <span class="text-sm text-gray-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                <div class="flex justify-between items-center pt-1 border-t border-green-50">
                                    <span class="text-sm font-bold text-gray-700">Subtotal Laba Ditahan</span>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($reportData['equity_groups']['retained_earnings']['total'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Laba Tahun Berjalan (otomatis dari Revenue - Expense) --}}
                        <div class="ml-2">
                            <span class="text-xs font-bold text-green-700 uppercase block mb-1">Laba Tahun Berjalan</span>
                            <div class="pl-2 border-l-2 border-green-100">
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-sm text-gray-600 italic">Laba Bersih s/d Per Tanggal</span>
                                    <span class="text-sm font-bold {{ $reportData['net_income'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                        Rp {{ number_format($reportData['net_income'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="flex justify-between items-center mt-6 pt-3 border-t-2 border-green-200 bg-green-50/50 p-2 rounded">
                        <span class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">Total Ekuitas</span>
                        <span class="text-lg font-bold text-green-800">Rp {{ number_format($reportData['total_equity'] + $reportData['net_income'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Total Liabilities + Equity --}}
                <div class="bg-red-50 p-4 rounded-lg border-2 border-red-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">TOTAL LIABILITAS + EKUITAS</span>
                        <span class="text-2xl font-bold text-red-700">Rp {{ number_format($reportData['total_liabilities'] + $reportData['total_equity'] + $reportData['net_income'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Balance Verification --}}
    <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-600">Balance Verification:</span>
            <div class="flex items-center gap-2">
                @if($reportData['balance_check'])
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-bold text-green-700">Neraca Balance ✓</span>
                @else
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-bold text-red-700">Neraca Tidak Balance ✗</span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
