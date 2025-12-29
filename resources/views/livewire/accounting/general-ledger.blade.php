<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Buku Besar (General Ledger)
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row gap-4 items-center">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pilih Akun</label>
                <select wire:model.live="accountId" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Pilih Akun --</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>

        <div class="p-4">
            @if($accountId)
                <div class="mb-4 flex justify-between items-center bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div>
                        <h3 class="text-lg font-bold text-blue-900">Detail Transaksi</h3>
                        <p class="text-sm text-blue-700">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-blue-600 uppercase font-bold tracking-wider">Saldo Awal</p>
                        <p class="font-bold text-xl text-blue-900 font-mono">{{ number_format($openingBalance, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Jurnal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Debit (Rp)</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Kredit (Rp)</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider bg-gray-100">Saldo (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Opening Balance Row -->
                            <tr class="bg-gray-50 italic">
                                <td class="px-6 py-4 text-sm text-gray-500" colspan="3">Saldo Awal Periode</td>
                                <td class="px-6 py-4 text-sm text-gray-500 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-gray-500 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-gray-800 text-right font-bold font-mono bg-gray-50">{{ number_format($openingBalance, 0, ',', '.') }}</td>
                            </tr>

                            <!-- Transactions -->
                            @forelse($ledgerLines as $line)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $line->journalEntry->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                    {{ $line->journalEntry->entry_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="font-medium">{{ $line->journalEntry->description }}</div>
                                    @if($line->notes)
                                        <div class="text-xs text-gray-500 italic mt-0.5">{{ $line->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-mono">
                                    {{ $line->debit > 0 ? number_format($line->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-mono">
                                    {{ $line->credit > 0 ? number_format($line->credit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold font-mono bg-gray-50">
                                    {{ number_format($line->running_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                    Tidak ada transaksi pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right text-gray-800 font-bold uppercase tracking-wider">Saldo Akhir</td>
                                <td class="px-6 py-4 text-right text-blue-700 text-xl font-bold font-mono bg-blue-50">{{ number_format($endingBalance, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-20 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada akun dipilih</h3>
                    <p class="mt-1 text-sm text-gray-500">Silakan pilih akun di filter atas untuk melihat rincian Buku Besar.</p>
                </div>
            @endif
        </div>
    </div>
</div>
