<div class="p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Laporan Laba Rugi
            </h2>
            <p class="text-sm text-gray-500 mt-1">Laporan Kinerja Keuangan & Analisis Laba</p>
        </div>
        <button wire:click="export" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md transition duration-200 font-bold text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span class="hidden sm:inline">Cetak PDF</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
        <div class="flex flex-wrap gap-6 items-end">
            <div class="w-full md:w-64">
                <label class="block text-sm font-bold text-gray-700 mb-1">Periode Laporan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <select wire:model.live="period" class="block w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm font-medium">
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="custom">Custom Tanggal</option>
                    </select>
                </div>
            </div>
            
            @if($period === 'custom')
                <div class="flex gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Mulai</label>
                        <input type="date" wire:model.live="startDate" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Sampai</label>
                        <input type="date" wire:model.live="endDate" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            @else
                <div class="h-10 flex items-center px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 font-bold">
                    {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
            @endif


            
            <div wire:loading class="pb-2 text-blue-600 text-sm font-bold italic flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memperbarui data...
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        {{-- Penjualan Bersih --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #4338ca; border-color: #3730a3;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Penjualan Bersih</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-white/60 mt-1 italic">Total - (Diskon + PPN)</p>
        </div>

        {{-- Total HPP --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #b45309; border-color: #92400e;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Total HPP</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($cogs, 0, ',', '.') }}</p>
            <p class="text-[10px] text-white/60 mt-1 italic">Modal Barang Terjual</p>
        </div>

        {{-- Laba Kotor --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #0e7490; border-color: #155e75;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Laba Kotor</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
            <p class="text-[10px] text-white/60 mt-1 italic">Penjualan - HPP</p>
        </div>

        {{-- Beban --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #be123c; border-color: #9f1239;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Total Beban</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($expenses, 0, ',', '.') }}</p>
            <p class="text-[10px] text-white/60 mt-1 italic">Operasional & Lainnya</p>
        </div>

        {{-- Laba Bersih --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.1] transition-all duration-300 border-b-4 {{ $netProfit >= 0 ? '' : 'animate-pulse' }}" style="background-color: {{ $netProfit >= 0 ? '#059669' : '#b91c1c' }}; border-color: {{ $netProfit >= 0 ? '#047857' : '#991b1b' }};">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <p class="text-[10px] font-bold tracking-wider uppercase opacity-80">Laba Bersih</p>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
            <p class="text-[10px] text-white/60 mt-1 italic">Hasil Akhir</p>
        </div>
    </div>

    <!-- Details Tabs / Sections -->
    <div class="space-y-8">
        <!-- Sales Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Detail Penjualan</h3>
                <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $salesDetails->count() }} Transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">No. Ref</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                            <th class="px-6 py-4 text-right">Diskon</th>
                            <th class="px-6 py-4 text-right">PPN (Pajak)</th>
                            <th class="px-6 py-4 text-right">Total Netto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($salesDetails as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $sale->invoice_no }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-rose-600">-Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-blue-600">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">Rp {{ number_format($sale->total_amount - $sale->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="2" class="px-6 py-4">TOTAL</td>
                            <td class="px-6 py-4 text-right underline underline-offset-4 decoration-blue-500 text-blue-600">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-rose-600">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-blue-700">Rp {{ number_format($totalTax, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right bg-blue-50">Rp {{ number_format($revenue - $totalDiscount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- COGS Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Detail HPP (FIFO)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-4">Tanggal Jual</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Harga Beli</th>
                            <th class="px-6 py-4 text-right">Total HPP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($cogsDetails as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($item->sale_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold">{{ $item->product_name }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-gray-500">Rp {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($item->quantity * $item->cost_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="4" class="px-6 py-4">TOTAL HPP</td>
                            <td class="px-6 py-4 text-right bg-orange-50 text-orange-700">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Expenses Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Detail Beban Operasional</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($expenseDetails as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">{{ $expense->description }}</td>
                            <td class="px-6 py-4 uppercase text-xs font-bold text-gray-400">{{ $expense->category }}</td>
                            <td class="px-6 py-4 text-right font-bold text-rose-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="3" class="px-6 py-4">TOTAL BEBAN</td>
                            <td class="px-6 py-4 text-right bg-rose-50 text-rose-700">Rp {{ number_format($expenses, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Footer Summary -->
    <div class="mt-8 p-6 bg-gray-900 rounded-xl text-white shadow-xl border border-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center text-center md:text-left">
            <div>
                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest mb-1">Status Laba Rugi</p>
                @if($netProfit >= 0)
                    <h4 class="text-3xl font-bold text-emerald-400 flex items-center justify-center md:justify-start gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        PROFIT
                    </h4>
                @else
                    <h4 class="text-3xl font-bold text-rose-400 flex items-center justify-center md:justify-start gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                        LOSS
                    </h4>
                @endif
            </div>
            <div class="md:border-x border-gray-800 px-8">
                <p class="text-gray-400 text-xs font-bold uppercase mb-1">Perhitungan Akhir</p>
                <div class="text-sm font-bold space-y-1">
                    <div class="flex justify-between">
                        <span class="opacity-60">Laba Kotor</span>
                        <span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-60">Beban Ops</span>
                        <span class="text-rose-400">- Rp {{ number_format($expenses, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center md:items-end">
                <p class="text-gray-400 text-sm font-bold uppercase mb-1">Laba Bersih Setelah Pajak</p>
                <div class="text-4xl font-bold tracking-tighter">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

