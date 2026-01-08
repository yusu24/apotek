<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Umur Hutang</h2>
            <p class="text-sm text-gray-500 mt-1">Accounts Payable Aging Report</p>
        </div>
        <button wire:click="exportPdf" class="btn btn-lg btn-danger">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Export PDF
        </button>
    </div>

    @if($reportData)
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach(['0-30' => 'green', '31-60' => 'blue', '61-90' => 'yellow', '>90' => 'red'] as $key => $color)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-{{ $color }}-500 cursor-pointer hover:bg-gray-50 transition" wire:click="setActiveTab('{{ $key }}')">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-gray-500 uppercase">{{ $key }} Hari</h3>
                    <div class="p-1.5 bg-{{ $color }}-100 rounded-lg">
                        <svg class="w-4 h-4 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($reportData['summary'][$key], 0, ',', '.') }}</p>
                <p class="text-xs mt-1 text-gray-500">Total Outstanding</p>
            </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex" aria-label="Tabs">
                <button wire:click="setActiveTab('all')" 
                    class="w-1/5 py-4 px-1 text-center border-b-2 font-bold text-sm {{ $activeTab === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua
                    <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        Rp {{ number_format($reportData['summary']['total'], 0, ',', '.') }}
                    </span>
                </button>
               @foreach(['0-30', '31-60', '61-90', '>90'] as $key)
                    <button wire:click="setActiveTab('{{ $key }}')" 
                        class="w-1/5 py-4 px-1 text-center border-b-2 font-bold text-sm {{ $activeTab === $key ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ $key }} Hari
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Umur (Hari)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Hutang</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $displayData = [];
                        if ($activeTab === 'all') {
                            $displayData = array_merge(
                                $reportData['>90'], 
                                $reportData['61-90'], 
                                $reportData['31-60'], 
                                $reportData['0-30']
                            );
                        } else {
                            $displayData = $reportData[$activeTab];
                        }
                    @endphp

                    @forelse($displayData as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['supplier'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['invoice_number'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item['due_date'] ? \Carbon\Carbon::parse($item['due_date'])->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item['age'] > 90 ? 'bg-red-100 text-red-800' : 
                                   ($item['age'] > 60 ? 'bg-yellow-100 text-yellow-800' : 
                                   ($item['age'] > 30 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                {{ $item['age'] }} Hari
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">Rp {{ number_format($item['total_amount'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">Rp {{ number_format($item['outstanding'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada data hutang pada kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
