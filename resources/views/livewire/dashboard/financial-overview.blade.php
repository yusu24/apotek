<div>
    @can('view financial overview')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        @can('view dashboard receivables')
        <!-- Receivables Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-gray-700 flex items-center justify-between bg-slate-50/50 dark:bg-gray-900/20">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                    Piutang Jatuh Tempo
                </h3>
                <a href="{{ route('finance.aging-report') }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-semibold flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-gray-800 text-slate-500 dark:text-gray-400 font-semibold border-b border-slate-100 dark:border-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Due Date</th>
                            <th class="px-5 py-3 text-right">Sisa Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                        @forelse($topReceivables as $ar)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="text-gray-900 dark:text-gray-100">{{ $ar['name'] }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $ar['ref'] }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="{{ $ar['is_overdue'] ? 'text-rose-600' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $ar['due_date'] }}
                                    </div>
                                    @if($ar['is_overdue'])
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] bg-rose-50 text-rose-600 border border-rose-100 tracking-wider mt-0.5">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($ar['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <x-empty-table colspan="3" message="Tidak ada tagihan yang mendekati jatuh tempo." />
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Footer Total -->
            <div class="px-5 py-4 border-t border-slate-100 dark:border-gray-700 bg-slate-50/80 dark:bg-gray-800/80 flex justify-between items-center mt-auto">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Piutang Usaha</span>
                <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</span>
            </div>
        </div>
        @endcan

        @can('view dashboard payables')
        <!-- Payables Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-gray-700 flex items-center justify-between bg-slate-50/50 dark:bg-gray-900/20">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm shadow-rose-200"></span>
                    Hutang Pembelian
                </h3>
                <a href="{{ route('finance.aging-report') }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-semibold flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-gray-800 text-slate-500 dark:text-gray-400 font-semibold border-b border-slate-100 dark:border-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Supplier</th>
                            <th class="px-5 py-3">Due Date</th>
                            <th class="px-5 py-3 text-right">Sisa Hutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                        @forelse($topPayables as $ap)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="text-gray-900 dark:text-gray-100">{{ $ap['name'] }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $ap['ref'] }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="{{ $ap['is_overdue'] ? 'text-rose-600' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $ap['due_date'] }}
                                    </div>
                                    @if($ap['is_overdue'])
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] bg-rose-50 text-rose-600 border border-rose-100 tracking-wider mt-0.5">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($ap['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <x-empty-table colspan="3" message="Tidak ada hutang yang mendekati jatuh tempo." />
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Footer Total -->
            <div class="px-5 py-4 border-t border-slate-100 dark:border-gray-700 bg-slate-50/80 dark:bg-gray-800/80 flex justify-between items-center mt-auto">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Hutang Usaha</span>
                <span class="text-base font-extrabold text-rose-600 dark:text-rose-400">Rp {{ number_format($totalPayables, 0, ',', '.') }}</span>
            </div>
        </div>
        @endcan

    </div>

    @endcan
</div>
