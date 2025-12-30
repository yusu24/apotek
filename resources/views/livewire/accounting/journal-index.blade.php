<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Jurnal Umum</h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat Transaksi Akuntansi</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create journal')
            <a href="{{ route('accounting.journals.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition flex items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden sm:inline">Jurnal Manual</span>
            </a>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Cari Jurnal</label>
                <input type="text" wire:model.live="search" placeholder="No. Jurnal / Deskripsi..." class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sumber</label>
                <select wire:model.live="sourceFilter" class="w-full border-gray-300 rounded-lg">
                    <option value="">Semua Sumber</option>
                    <option value="manual">Manual</option>
                    <option value="sale">Penjualan</option>
                    <option value="purchase">Pembelian</option>
                    <option value="stock_adjustment">Penyesuaian Stok</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="w-full border-gray-300 rounded-lg">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button wire:click="resetFilters" class="text-sm text-blue-600 font-bold hover:underline">Reset Filter</button>
        </div>
    </div>

    {{-- Journal List --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Tanggal & No. Jurnal</th>
                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Detail Akun</th>
                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-right">Debit</th>
                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-right">Kredit</th>
                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($journals as $journal)
                        <tr class="bg-gray-50 border-b-2 border-gray-100 italic">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $journal->date->format('d/m/Y') }}</div>
                                <div class="text-xs text-blue-600 font-semibold">{{ $journal->entry_number }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800" colspan="3">
                                {{ $journal->description }}
                                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-700 text-[10px] rounded uppercase">{{ $journal->source }}</span>
                            </td>
                            <td class="px-6 py-4" colspan="2"></td>
                        </tr>
                        @foreach($journal->lines as $line)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-2" colspan="2"></td>
                            <td class="px-6 py-2">
                                <div class="{{ $line->credit > 0 ? 'ml-8' : '' }} flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">{{ $line->account->code }} - {{ $line->account->name }}</span>
                                    @if($line->notes)
                                        <span class="text-[10px] text-gray-500 italic">{{ $line->notes }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-2 text-right">
                                @if($line->debit > 0)
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($line->debit, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-2 text-right">
                                @if($line->credit > 0)
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($line->credit, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-2 text-center">
                                 @if($loop->first)
                                    @if($journal->is_posted)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            POSTED
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800">
                                            DRAFT
                                        </span>
                                    @endif
                                 @endif
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                Tidak ada jurnal yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($journals->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-6 py-4">
                            {{ $journals->links() }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
