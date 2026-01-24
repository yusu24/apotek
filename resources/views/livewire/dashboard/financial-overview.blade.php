<div>
    @can('view financial overview')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Receivables Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    Piutang Jatuh Tempo
                </h3>
                <a href="{{ route('finance.aging-report') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Due Date</th>
                            <th class="px-5 py-3 text-right">Sisa Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($topReceivables as $ar)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $ar['name'] }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $ar['ref'] }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="{{ $ar['is_overdue'] ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                        {{ $ar['due_date'] }}
                                    </div>
                                    @if($ar['is_overdue'])
                                        <span class="text-[9px] text-red-500 uppercase tracking-widest font-bold">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium text-gray-900">
                                    Rp {{ number_format($ar['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-gray-400 italic">
                                    Tidak ada tagihan yang mendekati jatuh tempo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Footer Total -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center mt-auto">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Piutang Usaha</span>
                <span class="text-lg font-bold text-green-700">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payables Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    Hutang Pembelian
                </h3>
                <a href="{{ route('finance.aging-report') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3">Supplier</th>
                            <th class="px-5 py-3">Due Date</th>
                            <th class="px-5 py-3 text-right">Sisa Hutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($topPayables as $ap)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $ap['name'] }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $ap['ref'] }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="{{ $ap['is_overdue'] ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                        {{ $ap['due_date'] }}
                                    </div>
                                    @if($ap['is_overdue'])
                                        <span class="text-[9px] text-red-500 uppercase tracking-widest font-bold">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium text-gray-900">
                                    Rp {{ number_format($ap['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-gray-400 italic">
                                    Tidak ada hutang yang mendekati jatuh tempo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Footer Total -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center mt-auto">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Hutang Usaha</span>
                <span class="text-lg font-bold text-red-700">Rp {{ number_format($totalPayables, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    @endcan
</div>
