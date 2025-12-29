<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter">Laporan Laba Rugi <span class="text-blue-600">(v2.0)</span></h2>
            <p class="text-sm text-gray-500 mt-1">Laporan Kinerja Keuangan • Terupdate: {{ now()->format('H:i:s') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="setThisMonth" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                Bulan Ini
            </button>
            <button wire:click="setLastMonth" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                Bulan Lalu
            </button>
            <button wire:click="setThisYear" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                Tahun Ini
            </button>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" wire:model="startDate" class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" wire:model="endDate" class="w-full border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button wire:click="generateReport" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition">
                    Generate Laporan
                </button>
            </div>
        </div>
    </div>

    @if(!empty($reportData))
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        {{-- Penjualan Bersih --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #4338ca; border-color: #3730a3;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Penjualan Bersih</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($reportData['total_revenue'], 0, ',', '.') }}</p>
        </div>

        {{-- Total HPP --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #b45309; border-color: #92400e;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Total HPP</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($reportData['total_cogs'], 0, ',', '.') }}</p>
        </div>

        {{-- Laba Kotor --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #0e7490; border-color: #155e75;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Laba Kotor</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($reportData['gross_profit'], 0, ',', '.') }}</p>
        </div>

        {{-- Beban --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #be123c; border-color: #9f1239;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Total Beban</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($reportData['total_operating_expenses'] + $reportData['total_other_expenses'], 0, ',', '.') }}</p>
        </div>

        {{-- Laba Bersih --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.1] transition-all duration-300 border-b-4 {{ $reportData['net_income'] >= 0 ? '' : 'animate-pulse' }}" style="background-color: {{ $reportData['net_income'] >= 0 ? '#059669' : '#b91c1c' }}; border-color: {{ $reportData['net_income'] >= 0 ? '#047857' : '#991b1b' }};">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Laba Bersih</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($reportData['net_income'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Income Statement Table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
            <h3 class="text-xl font-bold text-white">LAPORAN LABA RUGI</h3>
            <p class="text-sm text-gray-300 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        
        <div class="p-6 space-y-6">
            {{-- REVENUE --}}
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">PENDAPATAN</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['revenue_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Pendapatan</span>
                    <span class="text-lg font-bold text-green-700">Rp {{ number_format($reportData['total_revenue'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- COGS --}}
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">HARGA POKOK PENJUALAN (COGS)</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['cogs_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total COGS</span>
                    <span class="text-lg font-bold text-red-700">(Rp {{ number_format($reportData['total_cogs'], 0, ',', '.') }})</span>
                </div>
            </div>

            {{-- GROSS PROFIT --}}
            <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">LABA KOTOR</span>
                    <span class="text-2xl font-bold text-blue-700">Rp {{ number_format($reportData['gross_profit'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- OPERATING EXPENSES --}}
            <div>
                <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">BEBAN OPERASIONAL</h4>
                <div class="space-y-2 ml-4">
                    @foreach($reportData['operating_expense_accounts'] as $account)
                    <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">{{ $account->name }}</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Beban Operasional</span>
                    <span class="text-lg font-bold text-red-700">(Rp {{ number_format($reportData['total_operating_expenses'], 0, ',', '.') }})</span>
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
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Beban Lain-lain</span>
                    <span class="text-lg font-bold text-red-700">(Rp {{ number_format($reportData['total_other_expenses'], 0, ',', '.') }})</span>
                </div>
            </div>
            @endif

            {{-- NET INCOME --}}
            <div class="bg-gradient-to-r from-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-50 to-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-100 p-6 rounded-lg border-2 border-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-300">
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-gray-900">{{ $reportData['net_income'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</span>
                    <span class="text-3xl font-bold text-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-700">
                        Rp {{ number_format(abs($reportData['net_income']), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Margin Analysis --}}
            @if($reportData['total_revenue'] > 0)
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-sm font-semibold text-gray-600">Gross Profit Margin</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format(($reportData['gross_profit'] / $reportData['total_revenue']) * 100, 2) }}%
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-sm font-semibold text-gray-600">Net Profit Margin</p>
                    <p class="text-2xl font-bold text-{{ $reportData['net_income'] >= 0 ? 'green' : 'red' }}-700 mt-1">
                        {{ number_format(($reportData['net_income'] / $reportData['total_revenue']) * 100, 2) }}%
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
