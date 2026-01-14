<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan PPN</h2>
            <p class="text-sm text-gray-500 mt-1">Pajak Pertambahan Nilai (Keluaran & Masukan)</p>
        </div>
        <button wire:click="exportPdf" class="btn btn-lg btn-danger">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span class="hidden md:inline">Export PDF</span>
        </button>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Filter Periode</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select wire:model="month" class="w-full border-gray-300 rounded-lg shadow-sm">
                    @foreach($months as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select wire:model="year" class="w-full border-gray-300 rounded-lg shadow-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="generateReport" class="btn btn-primary w-full">
                    Generate
                </button>
            </div>
        </div>
    </div>

    @if($reportData)
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- PPN Keluaran --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-bold text-gray-500 uppercase">PPN Keluaran</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($reportData['total_ppn_keluaran'], 0, ',', '.') }}</p>
            <p class="text-sm mt-2 text-gray-500">Dari {{ number_format($reportData['ppn_keluaran_details']->count()) }} transaksi</p>
        </div>

        {{-- PPN Masukan --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-bold text-gray-500 uppercase">PPN Masukan</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($reportData['total_ppn_masukan'], 0, ',', '.') }}</p>
            <p class="text-sm mt-2 text-gray-500">Dari {{ number_format($reportData['ppn_masukan_details']->count()) }} transaksi</p>
        </div>

        {{-- Kurang/Lebih Bayar --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $reportData['status'] === 'kurang_bayar' ? 'border-red-500' : ($reportData['status'] === 'lebih_bayar' ? 'border-yellow-500' : 'border-gray-500') }}">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-bold text-gray-500 uppercase">
                    @if($reportData['status'] === 'kurang_bayar')
                        Kurang Bayar
                    @elseif($reportData['status'] === 'lebih_bayar')
                        Lebih Bayar
                    @else
                        Nihil
                    @endif
                </h3>
                <div class="p-2 {{ $reportData['status'] === 'kurang_bayar' ? 'bg-red-100' : ($reportData['status'] === 'lebih_bayar' ? 'bg-yellow-100' : 'bg-gray-100') }} rounded-lg">
                    <svg class="w-6 h-6 {{ $reportData['status'] === 'kurang_bayar' ? 'text-red-500' : ($reportData['status'] === 'lebih_bayar' ? 'text-yellow-600' : 'text-gray-500') }}" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-bold {{ $reportData['status'] === 'kurang_bayar' ? 'text-red-600' : ($reportData['status'] === 'lebih_bayar' ? 'text-yellow-600' : 'text-gray-800') }}">
                Rp {{ number_format(abs($reportData['kurang_lebih']), 0, ',', '.') }}
            </p>
            <p class="text-sm mt-2 text-gray-500">Selisih PPN</p>
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
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">DPP</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PPN 11%</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reportData['ppn_keluaran_details'] as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sale->invoice_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($sale->dpp, 0, ',', '.') }}</td>
                       <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">Rp {{ number_format($sale->ppn_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada transaksi penjualan dengan PPN pada periode ini.
                        </td>
                    </tr>
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
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">DPP</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PPN 11%</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reportData['ppn_masukan_details'] as $purchase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $purchase->delivery_note_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($purchase->dpp, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-blue-600">Rp {{ number_format($purchase->ppn_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">Rp {{ number_format($purchase->dpp + $purchase->ppn_amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada transaksi pembelian dengan PPN pada periode ini.
                        </td>
                    </tr>
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
