<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">Ringkasan Keuangan</h2>
        </div>
        <div class="flex items-center gap-2 bg-white dark:bg-gray-800 p-1 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <x-date-picker wire:model.live="startDate" class="bg-transparent border-none text-xs focus:ring-0 dark:text-gray-300"></x-date-picker>
            <span class="text-gray-300 dark:text-gray-600">-</span>
            <x-date-picker wire:model.live="endDate" class="bg-transparent border-none text-xs focus:ring-0 dark:text-gray-300"></x-date-picker>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Total Cash & Bank -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Saldo (Kas & Bank)</span>
                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Rp {{ number_format($totalCash, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Dompet kasir & rekening operasional
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Total Debt -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Kewajiban (Hutang)</span>
                    <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Rp {{ number_format($totalDebt, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Tagihan supplier & pinjaman
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-rose-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Net Balance -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posisi Kas Bersih</span>
                    <div class="w-8 h-8 rounded-full {{ $netPosition >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' }} flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold {{ $netPosition >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }} tracking-tight">
                    Rp {{ number_format($netPosition, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Likuiditas dana bersih tersedia
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full {{ $netPosition >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full" style="width: 100%;"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details Lists -->
        <div class="space-y-6">
            <!-- Cash & Bank Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <div class="w-1.5 h-4 bg-blue-600 rounded-full"></div>
                        Detail Kas & Bank
                    </h3>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($cashAndBank as $acc)
                        <div class="p-4 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $acc->code }}</div>
                                <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $acc->name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                                </div>
                                <a href="{{ route('accounting.ledger', ['accountId' => $acc->id]) }}" wire:navigate class="text-[10px] text-gray-400 hover:text-blue-500 underline flex items-center justify-end gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Buku Besar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Debt Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                        Detail Kewajiban
                    </h3>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($debts as $acc)
                        <div class="p-4 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $acc->code }}</div>
                                <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $acc->name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-orange-600 dark:text-orange-400 tracking-tight">
                                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                                </div>
                                <a href="{{ route('accounting.ledger', ['accountId' => $acc->id]) }}" wire:navigate class="text-[10px] text-gray-400 hover:text-orange-500 underline flex items-center justify-end gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Buku Besar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Mutasi Finansial Terakhir
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 text-left">Tanggal</th>
                            <th class="px-6 py-4 text-left">Akun</th>
                            <th class="px-6 py-4 text-left">Keterangan</th>
                            <th class="px-6 py-4 text-right">Debit</th>
                            <th class="px-6 py-4 text-right">Kredit</th>
                            <th class="px-6 py-4 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($recentTransactions as $line)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($line->journalEntry->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $line->account->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $line->account->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $line->notes ?? $line->journalEntry->description }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($line->debit > 0)
                                        <span class="text-emerald-600 dark:text-emerald-400 tracking-tight">
                                            + {{ number_format($line->debit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($line->credit > 0)
                                        <span class="text-red-600 dark:text-red-400 tracking-tight text-opacity-80">
                                            - {{ number_format($line->credit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="text-gray-900 dark:text-gray-200 tracking-tight">
                                        {{ number_format($line->running_balance, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <x-empty-table colspan="6" message="Belum ada transaksi pada periode ini." />
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentTransactions->count() >= 15)
                <div class="p-4 bg-gray-50 dark:bg-gray-900 text-center">
                    <a href="{{ route('accounting.journals.index') }}" wire:navigate class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">
                        Lihat Seluruh Jurnal Umum &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
