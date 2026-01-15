<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Neraca (Balance Sheet)</h2>
            <p class="text-sm text-gray-500 mt-1">Laporan Posisi Keuangan</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <button wire:click="setThisMonth" class="btn btn-secondary">
                Bulan Ini
            </button>
            <button wire:click="setLastMonth" class="btn btn-secondary">
                Bulan Lalu
            </button>
            <button wire:click="setThisYear" class="btn btn-secondary">
                Tahun Ini
            </button>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" wire:model="startDate" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" wire:model="endDate" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end gap-3">
                <button wire:click="generateReport" class="btn btn-lg btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Generate Laporan</span>
                </button>
                <a href="{{ route('pdf.balance-sheet', ['startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak PDF</span>
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Aset --}}
        <div class="rounded-2xl shadow-xl p-8 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #1e40af; border-color: #1e3a8a;">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <p class="text-sm font-bold tracking-wider uppercase">Total Aset</p>
            </div>
            <p class="text-3xl font-bold">Rp {{ number_format($reportData['total_assets'], 0, ',', '.') }}</p>
        </div>

        {{-- Total Liabilitas --}}
        <div class="rounded-2xl shadow-xl p-8 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #be123c; border-color: #9f1239;">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-bold tracking-wider uppercase">Total Liabilitas</p>
            </div>
            <p class="text-3xl font-bold">Rp {{ number_format($reportData['total_liabilities'], 0, ',', '.') }}</p>
        </div>

        {{-- Total Ekuitas --}}
        <div class="rounded-2xl shadow-xl p-8 text-white transform hover:scale-[1.02] transition-all duration-300 border-b-4" style="background-color: #059669; border-color: #047857;">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <p class="text-sm font-bold tracking-wider uppercase">Total Ekuitas</p>
            </div>
            <p class="text-3xl font-bold">Rp {{ number_format($reportData['total_equity'] + $reportData['net_income'], 0, ',', '.') }}</p>
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
                    <div class="space-y-2">
                        @foreach($reportData['current_assets'] as $account)
                        <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                            <span class="text-sm text-gray-700">{{ $account->name }}</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-700">Total Aset Lancar</span>
                        <span class="text-base font-bold text-blue-700">Rp {{ number_format($reportData['total_current_assets'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Fixed Assets --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Aset Tetap</h4>
                    <div class="space-y-2">
                        @foreach($reportData['fixed_assets'] as $account)
                        <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                            <span class="text-sm text-gray-700">{{ $account->name }}</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-700">Total Aset Tetap</span>
                        <span class="text-base font-bold text-blue-700">Rp {{ number_format($reportData['total_fixed_assets'], 0, ',', '.') }}</span>
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
                {{-- Current Liabilities --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Liabilitas Lancar</h4>
                    <div class="space-y-2">
                        @foreach($reportData['current_liabilities'] as $account)
                        <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                            <span class="text-sm text-gray-700">{{ $account->name }}</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-700">Total Liabilitas Lancar</span>
                        <span class="text-base font-bold text-red-700">Rp {{ number_format($reportData['total_current_liabilities'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Long-term Liabilities --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Liabilitas Jangka Panjang</h4>
                    <div class="space-y-2">
                        @foreach($reportData['long_term_liabilities'] as $account)
                        <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                            <span class="text-sm text-gray-700">{{ $account->name }}</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-700">Total Liabilitas Jangka Panjang</span>
                        <span class="text-base font-bold text-red-700">Rp {{ number_format($reportData['total_long_term_liabilities'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Equity --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 border-b pb-2">Ekuitas</h4>
                    <div class="space-y-2">
                        @foreach($reportData['equity'] as $account)
                        <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                            <span class="text-sm text-gray-700">{{ $account->name }}</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        {{-- Add Net Income --}}
                        <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                            <span class="text-sm text-gray-700">Laba Bersih Periode Berjalan</span>
                            <span class="text-sm font-bold {{ $reportData['net_income'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                Rp {{ number_format($reportData['net_income'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200 bg-green-50 px-2 py-1 rounded">
                        <span class="text-sm font-bold text-gray-700">Total Ekuitas</span>
                        <span class="text-base font-bold text-green-700">Rp {{ number_format($reportData['total_equity'] + $reportData['net_income'], 0, ',', '.') }}</span>
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
