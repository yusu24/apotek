<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Ringkasan Keuangan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ikhtisar saldo Kas, Bank, dan Hutang saat ini.</p>
        </div>
        <div class="flex items-center gap-2 bg-white dark:bg-gray-800 p-1 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-xs focus:ring-0 dark:text-gray-300">
            <span class="text-gray-300 dark:text-gray-600">-</span>
            <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-xs focus:ring-0 dark:text-gray-300">
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Cash & Bank -->
        <div class="rounded-3xl shadow-[0_10px_30px_rgba(0,0,0,0.2)] p-6 text-white relative overflow-hidden group border-2 border-white dark:border-gray-800" style="background-color: #1e40af;">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white rounded-xl text-blue-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold tracking-widest uppercase text-white">Total Saldo (Kas & Bank)</span>
                </div>
                <div class="text-3xl font-bold mb-1 flex items-baseline gap-1">
                    <span class="text-sm opacity-80">Rp</span>
                    <span>{{ number_format($totalCash, 0, ',', '.') }}</span>
                </div>
                <div class="text-[10px] font-bold bg-white/20 inline-block px-2 py-0.5 rounded-lg text-white">Dompet Kasir & Rekening</div>
            </div>
        </div>

        <!-- Total Debt -->
        <div class="rounded-3xl shadow-[0_10px_30px_rgba(0,0,0,0.2)] p-6 text-white relative overflow-hidden group border-2 border-white dark:border-gray-800" style="background-color: #b91c1c;">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white rounded-xl text-red-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold tracking-widest uppercase text-white">Total Kewajiban (Hutang)</span>
                </div>
                <div class="text-3xl font-bold mb-1 flex items-baseline gap-1">
                    <span class="text-sm opacity-80">Rp</span>
                    <span>{{ number_format($totalDebt, 0, ',', '.') }}</span>
                </div>
                <div class="text-[10px] font-bold bg-white/20 inline-block px-2 py-0.5 rounded-lg text-white">Tagihan Supplier / Pinjaman</div>
            </div>
        </div>

        <!-- Net Balance -->
        <div class="rounded-3xl shadow-2xl p-6 text-white relative overflow-hidden group border-2 border-white dark:border-gray-800" style="background-color: #000000 !important;">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white rounded-xl text-black">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <span class="text-xs font-bold tracking-widest uppercase text-white">Posisi Kas Bersih</span>
                </div>
                <div class="text-3xl font-bold mb-1 flex items-baseline gap-1">
                    <span class="text-sm opacity-80">Rp</span>
                    <span>{{ number_format($netPosition, 0, ',', '.') }}</span>
                </div>
                <div class="text-[10px] font-bold bg-white/30 inline-block px-2 py-0.5 rounded-lg text-white">Likuiditas Dana Tersedia</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details Lists -->
        <div class="space-y-6">
            <!-- Cash & Bank Details -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
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
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Mutasi Finansial Terakhir
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 dark:bg-gray-900/20 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold tracking-widest">
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
                                    <div class="font-medium text-gray-700 dark:text-gray-300">{{ $line->account->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $line->account->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $line->notes ?? $line->journalEntry->description }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($line->debit > 0)
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400 tracking-tight">
                                            + {{ number_format($line->debit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($line->credit > 0)
                                        <span class="font-bold text-red-600 dark:text-red-400 tracking-tight text-opacity-80">
                                            - {{ number_format($line->credit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="font-bold text-gray-900 dark:text-gray-200 tracking-tight">
                                        {{ number_format($line->running_balance, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-full">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-400 dark:text-gray-500 italic">Belum ada transaksi pada periode ini.</p>
                                    </div>
                                </td>
                            </tr>
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
