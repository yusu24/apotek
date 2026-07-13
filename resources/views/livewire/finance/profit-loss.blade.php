<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Laporan Laba Rugi</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
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
                    <button wire:click="exportExcel(true)" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                        Excel - Perbandingan Periode
                    </button>
                    <button wire:click="exportExcel(false)" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                        Excel - Periode Ini Saja
                    </button>
                    <a href="{{ route('pdf.laba-rugi', ['startDate' => $startDate, 'endDate' => $endDate, 'compare' => 1]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF - Perbandingan Periode
                    </a>
                    <a href="{{ route('pdf.laba-rugi', ['startDate' => $startDate, 'endDate' => $endDate, 'compare' => 0]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF - Periode Ini Saja
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm mb-8 border border-gray-100">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row flex-wrap gap-4 items-end">
            <div class="flex flex-col shrink-0">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Periode Laporan</label>
                <div class="relative">
                    <select wire:model.live="period" class="block w-full pl-3 pr-8 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm text-sm py-2">
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="custom">Custom Tanggal</option>
                    </select>
                </div>
            </div>

            @if($period === 'custom')
                <div class="flex items-center gap-2">
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mulai</label>
                        <x-date-picker wire:model.live="startDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white"></x-date-picker>
                    </div>
                    <span class="text-gray-400 font-bold self-end mb-2">-</span>
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai</label>
                        <x-date-picker wire:model.live="endDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white"></x-date-picker>
                    </div>
                </div>
            @else
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Rentang</label>
                    <div class="h-9 flex items-center px-3 bg-white border border-gray-300 rounded-lg text-sm text-gray-600 font-medium shadow-sm">
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        {{-- Penjualan Bersih --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #4338ca; border-color: #3730a3;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-indigo-100 text-sm font-medium">Penjualan Bersih</p>
            </div>
            <p class="text-2xl font-bold mt-1">Rp. {{ number_format($revenue, 0, ',', '.') }},-</p>
            <p class="text-xs text-indigo-200/70 mt-1 italic">DPP({{ number_format($grossRevenue, 0, ',', '.') }}) - Retur({{ number_format($totalReturns, 0, ',', '.') }})</p>
        </div>

        {{-- Total HPP --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #b45309; border-color: #92400e;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <p class="text-amber-100 text-sm font-medium">Total HPP</p>
            </div>
            <p class="text-2xl font-bold mt-1">Rp. {{ number_format($cogs, 0, ',', '.') }},-</p>
            <p class="text-xs text-amber-200/70 mt-1 italic">Modal Barang Terjual</p>
        </div>

        {{-- Laba Kotor --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #0e7490; border-color: #155e75;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <p class="text-cyan-100 text-sm font-medium">Gross Profit Margin (Laba Kotor)</p>
            </div>
            <p class="text-2xl font-bold mt-1">Rp. {{ number_format($grossProfit, 0, ',', '.') }},-</p>
            <p class="text-xs text-cyan-200/70 mt-1 italic">Penjualan - HPP</p>
        </div>

        {{-- Beban --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #be123c; border-color: #9f1239;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-rose-100 text-sm font-medium">Operating Expense (Beban)</p>
            </div>
            <p class="text-2xl font-bold mt-1">Rp. {{ number_format($expenses, 0, ',', '.') }},-</p>
            <p class="text-xs text-rose-200/70 mt-1 italic">Operasional & Lainnya</p>
        </div>

        {{-- Laba Sebelum Pajak --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #d97706; border-color: #b45309;">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-amber-100 text-sm font-medium">Pre-Tax Profit (Laba Sebelum Pajak)</p>
            </div>
            <p class="text-2xl font-bold mt-1">Rp. {{ number_format($netProfitBeforeTax, 0, ',', '.') }},-</p>
            <p class="text-xs text-amber-200/70 mt-1 italic">Sebelum Potong Pajak</p>
        </div>

        {{-- Laba Bersih --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.1] transition-all duration-300 border-b-4 {{ $netProfit >= 0 ? '' : 'animate-pulse' }}" style="background-color: {{ $netProfit >= 0 ? '#059669' : '#b91c1c' }}; border-color: {{ $netProfit >= 0 ? '#047857' : '#991b1b' }};">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <p class="{{ $netProfit >= 0 ? 'text-emerald-100' : 'text-red-100' }} text-sm font-medium">Net Profit Margin (Laba Bersih)</p>
            </div>
            <p class="text-2xl font-bold mt-1">Rp. {{ number_format($netProfit, 0, ',', '.') }},-</p>
            <p class="text-xs {{ $netProfit >= 0 ? 'text-emerald-200/70' : 'text-red-200/70' }} mt-1 italic">Setelah Pajak</p>
        </div>
    </div>

    <!-- Details Tabs / Sections -->
    <div class="space-y-8">
        <!-- Sales Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Detail Penjualan</h3>
                <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $salesDetails->total() }} Transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs tracking-wider border-b">
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
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $sale->invoice_no }}</td>
                            <td class="px-6 py-4 text-right">Rp. {{ number_format($sale->total_amount, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right text-rose-600">-Rp. {{ number_format($sale->discount, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right text-blue-600">Rp. {{ number_format($sale->tax, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">Rp. {{ number_format($sale->total_amount - $sale->discount, 0, ',', '.') }},-</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="2" class="px-6 py-4">TOTAL DPP</td>
                            <td class="px-6 py-4 text-right underline underline-offset-4 decoration-blue-500 text-blue-600">Rp. {{ number_format($grossRevenue, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right text-rose-600">Rp. {{ number_format($totalDiscount, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right text-blue-700">Rp. {{ number_format($totalTax, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right bg-blue-50">Rp. {{ number_format($grossRevenue - $totalDiscount, 0, ',', '.') }},-</td>
                        </tr>
                        @if($totalReturns > 0)
                        <tr class="bg-rose-50">
                            <td colspan="5" class="px-6 py-3 text-rose-600">Retur Penjualan</td>
                            <td class="px-6 py-3 text-right text-rose-600">-Rp. {{ number_format($totalReturns, 0, ',', '.') }},-</td>
                        </tr>
                        <tr class="bg-indigo-50">
                            <td colspan="5" class="px-6 py-3 text-indigo-700">PENDAPATAN BERSIH (DPP - Retur)</td>
                            <td class="px-6 py-3 text-right text-indigo-700">Rp. {{ number_format($revenue, 0, ',', '.') }},-</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                @include('components.custom-pagination', ['items' => $salesDetails, 'pageName' => 'salesPage'])
            </div>
        </div>

        <!-- COGS Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Detail HPP (FIFO)</h3>
                <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $cogsDetails->total() }} Item</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs tracking-wider border-b">
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
                            <td class="px-6 py-4 text-right text-gray-500">Rp. {{ number_format($item->cost_price, 0, ',', '.') }},-</td>
                            <td class="px-6 py-4 text-right font-bold">Rp. {{ number_format($item->quantity * $item->cost_price, 0, ',', '.') }},-</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="4" class="px-6 py-4">TOTAL HPP</td>
                            <td class="px-6 py-4 text-right bg-orange-50 text-orange-700">Rp. {{ number_format($cogs, 0, ',', '.') }},-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                @include('components.custom-pagination', ['items' => $cogsDetails, 'pageName' => 'cogsPage'])
            </div>
        </div>

        <!-- Expenses Detail -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Detail Beban Operasional</h3>
                <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $expenseDetails->total() }} Beban</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs tracking-wider border-b">
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
                            <td class="px-6 py-4 text-right font-bold text-rose-600">Rp. {{ number_format($expense->amount, 0, ',', '.') }},-</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="3" class="px-6 py-4">TOTAL BEBAN</td>
                            <td class="px-6 py-4 text-right bg-rose-50 text-rose-700">Rp. {{ number_format($expenses, 0, ',', '.') }},-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                @include('components.custom-pagination', ['items' => $expenseDetails, 'pageName' => 'expensePage'])
            </div>
        </div>
        
        <!-- Tax Expenses Detail -->
        @if($taxDetails->total() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Detail Beban Pajak (PPh)</h3>
                <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $taxDetails->total() }} Transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs tracking-wider border-b">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($taxDetails as $tax)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($tax->date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">{{ $tax->description }}</td>
                            <td class="px-6 py-4 text-right font-bold text-rose-600">Rp. {{ number_format($tax->amount, 0, ',', '.') }},-</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-900 border-t-2">
                        <tr>
                            <td colspan="2" class="px-6 py-4">TOTAL PAJAK</td>
                            <td class="px-6 py-4 text-right bg-rose-50 text-rose-700">Rp. {{ number_format($taxExpenses, 0, ',', '.') }},-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                @include('components.custom-pagination', ['items' => $taxDetails, 'pageName' => 'taxPage'])
            </div>
        </div>
        @endif
    </div>
    
    <!-- Footer Summary -->
    <div class="mt-8 p-6 bg-gray-900 rounded-xl text-white shadow-xl border border-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center text-center md:text-left">
            <div>
                <p class="text-gray-400 text-sm font-medium mb-1">Status Laba Rugi</p>
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
                <p class="text-gray-400 text-sm font-medium mb-1">Perhitungan Akhir</p>
                <div class="text-sm font-bold space-y-1">
                    <div class="flex justify-between">
                        <span class="opacity-60">Laba Kotor</span>
                        <span>Rp. {{ number_format($grossProfit, 0, ',', '.') }},-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-60">Beban Ops</span>
                        <span class="text-rose-400">- Rp. {{ number_format($expenses, 0, ',', '.') }},-</span>
                    </div>
                    <div class="border-t border-gray-700 my-1 pt-1 flex justify-between">
                        <span class="opacity-80">Laba Sblm Pajak</span>
                        <span class="{{ $netProfitBeforeTax >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">Rp. {{ number_format($netProfitBeforeTax, 0, ',', '.') }},-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-60">Beban Pajak PPh</span>
                        <span class="text-rose-400">- Rp. {{ number_format($taxExpenses, 0, ',', '.') }},-</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center md:items-end">
                <p class="text-gray-400 text-sm font-medium mb-1">Laba Bersih Setelah Pajak</p>
                <div class="text-4xl font-bold tracking-tighter">
                    Rp. {{ number_format($netProfit, 0, ',', '.') }},-
                </div>
            </div>
        </div>
    </div>

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

