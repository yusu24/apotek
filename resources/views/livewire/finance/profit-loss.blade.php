<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                Laporan Laba Rugi
            </h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6">
        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-end border border-gray-100">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <button wire:click="export" class="p-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 shadow-md transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </button>
                <div class="relative flex-1 md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <select wire:model.live="period" class="block w-full pl-10 pr-10 py-2.5 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>
            
            @if($period === 'custom')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" wire:model.live="startDate" class="border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" wire:model.live="endDate" class="border-gray-300 rounded-md shadow-sm">
                </div>
            @else
                <div class="pb-2 text-gray-600 font-medium">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </div>
            @endif
            
            <div wire:loading class="pb-2 text-blue-600 text-sm">Loading...</div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenue -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <h3 class="text-sm font-medium text-gray-500">Pendapatan Bersih (Revenue)</h3>
                <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Total Penjualan - Pajak</p>
            </div>

            <!-- COGS -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-400">
                <h3 class="text-sm font-medium text-gray-500">Harga Pokok Penjualan (HPP)</h3>
                <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($cogs, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Estimasi HPP (FIFO)</p>
            </div>

            <!-- Gross Profit -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-sm font-medium text-gray-500">Laba Kotor (Gross Profit)</h3>
                <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Pendapatan - HPP</p>
            </div>
            
             <!-- Expenses -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-400">
                <h3 class="text-sm font-medium text-gray-500">Beban Operasional</h3>
                <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($expenses, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Total Pengeluaran</p>
            </div>

            <!-- Net Profit -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 {{ $netProfit >= 0 ? 'border-green-600' : 'border-red-600' }} md:col-span-2 lg:col-span-2">
                <h3 class="text-sm font-medium text-gray-500">Laba Bersih (Net Profit)</h3>
                <p class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Laba Kotor - Beban Operasional</p>
            </div>
        </div>
        
        <!-- Detailed Info Placeholder -->
        <div class="bg-blue-50 border border-blue-200 rounded p-4 text-sm text-blue-800">
            <h4 class="font-bold mb-1">Informasi Perhitungan</h4>
            <ul class="list-disc ml-5">
                <li><strong>Pendapatan:</strong> Total penjualan kotor dikurangi pajak (PPN).</li>
                <li><strong>HPP:</strong> Dihitung berdasarkan harga beli (batch) dari setiap produk yang terjual pada periode ini (Metode FIFO).</li>
                <li><strong>Beban:</strong> Akumulasi data dari menu Pengeluaran.</li>
            </ul>
        </div>
    </div>
</div>
