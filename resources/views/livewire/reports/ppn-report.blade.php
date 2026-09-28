<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Laporan PPN</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
            <button wire:click="setThisMonth" class="btn btn-secondary">
                Bulan Ini
            </button>
            <button wire:click="setLastMonth" class="btn btn-secondary">
                Bulan Lalu
            </button>
            <a href="{{ route('pdf.ppn-report', ['month' => $month, 'year' => $year]) }}" target="_blank" class="btn btn-export-pdf" title="Export PDF">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6 relative">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Filter Periode</h3>
        <div class="flex flex-col md:flex-row items-end gap-4">
            <div class="w-full md:w-auto">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                <select wire:model.live="month" class="w-full md:w-32 border-gray-300 rounded-lg shadow-sm text-sm py-2">
                    @foreach($months as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                <select wire:model.live="year" class="w-full md:w-24 border-gray-300 rounded-lg shadow-sm text-sm py-2">
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-auto">
                <button wire:click="generateReport" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Generate</span>
                </button>
            </div>
        </div>
    </div>

    @if($reportData)
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        {{-- PPN Keluaran --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PPN Keluaran</span>
                    <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($reportData['total_ppn_keluaran'], 0, ',', '.') }}">
                    Rp {{ number_format($reportData['total_ppn_keluaran'], 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Dari {{ number_format($reportData['ppn_keluaran_details']->count()) }} transaksi penjualan
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        {{-- PPN Masukan --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PPN Masukan</span>
                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($reportData['total_ppn_masukan'], 0, ',', '.') }}">
                    Rp {{ number_format($reportData['total_ppn_masukan'], 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Dari {{ number_format($reportData['ppn_masukan_details']->count()) }} faktur pembelian
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        {{-- Kurang/Lebih Bayar --}}
        @php
            $statusColor = $reportData['status'] === 'kurang_bayar' ? 'rose' : ($reportData['status'] === 'lebih_bayar' ? 'amber' : 'gray');
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $reportData['status'] === 'kurang_bayar' ? 'Kurang Bayar' : ($reportData['status'] === 'lebih_bayar' ? 'Lebih Bayar' : 'Nihil') }}
                    </span>
                    <div class="w-8 h-8 rounded-full bg-{{ $statusColor }}-50 dark:bg-{{ $statusColor }}-900/30 text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold {{ $reportData['status'] === 'kurang_bayar' ? 'text-rose-600 dark:text-rose-400' : ($reportData['status'] === 'lebih_bayar' ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white') }} tracking-tight truncate" title="Rp {{ number_format(abs($reportData['kurang_lebih']), 0, ',', '.') }}">
                    Rp {{ number_format(abs($reportData['kurang_lebih']), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Selisih neto PPN periode berjalan
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-{{ $statusColor }}-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>
    </div>

    {{-- PPN Keluaran Table --}}
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="p-6 border-b bg-green-50">
            <h3 class="text-lg font-bold text-gray-900">PPN Keluaran (Output Tax)</h3>
            <p class="text-sm text-gray-600 mt-1">Penjualan dengan PPN</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-left">No. Invoice</th>
                        <th class="px-6 py-4 text-right">DPP</th>
                        <th class="px-6 py-4 text-right">PPN 11%</th>
                        <th class="px-6 py-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reportData['ppn_keluaran_details'] as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->invoice_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($sale->dpp, 0, ',', '.') }}</td>
                       <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">Rp {{ number_format($sale->ppn_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                        <x-empty-table colspan="5" />
                    @endforelse
                </tbody>
                @if($reportData['ppn_keluaran_details']->count() > 0)
                <tfoot class="bg-green-50 font-bold">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm text-gray-900">TOTAL PPN KELUARAN:</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">Rp {{ number_format($reportData['total_dpp_keluaran'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-green-600">Rp {{ number_format($reportData['total_ppn_keluaran'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">Rp {{ number_format($reportData['total_dpp_keluaran'] + $reportData['total_ppn_keluaran'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- PPN Masukan Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-blue-50">
            <h3 class="text-lg font-bold text-gray-900">PPN Masukan (Input Tax)</h3>
            <p class="text-sm text-gray-600 mt-1">Pembelian dengan PPN</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-left">No. Surat Jalan</th>
                        <th class="px-6 py-4 text-right">DPP</th>
                        <th class="px-6 py-4 text-right">PPN 11%</th>
                        <th class="px-6 py-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reportData['ppn_masukan_details'] as $purchase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $purchase->delivery_note_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($purchase->dpp, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">Rp {{ number_format($purchase->ppn_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($purchase->dpp + $purchase->ppn_amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                        <x-empty-table colspan="5" />
                    @endforelse
                </tbody>
                @if($reportData['ppn_masukan_details']->count() > 0)
                <tfoot class="bg-blue-50 font-bold">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm text-gray-900">TOTAL PPN MASUKAN:</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">Rp {{ number_format($reportData['total_dpp_masukan'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-blue-600">Rp {{ number_format($reportData['total_ppn_masukan'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">Rp {{ number_format($reportData['total_dpp_masukan'] + $reportData['total_ppn_masukan'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>
