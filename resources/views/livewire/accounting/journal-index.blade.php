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
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="No. Jurnal / Deskripsi..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
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
                <x-date-picker wire:model.live="startDate" class="w-full border-gray-300 rounded-lg"></x-date-picker>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                <x-date-picker wire:model.live="endDate" class="w-full border-gray-300 rounded-lg"></x-date-picker>
            </div>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Tampilkan</span>
                <select wire:model.live="perPage" class="border-gray-300 rounded-lg py-1.5 pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <button wire:click="resetFilters" class="text-sm text-blue-600 font-bold hover:underline">Reset Filter</button>
        </div>
    </div>

    {{-- Journal List --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                    <tr>
                        <th class="px-4 py-4 text-left">Tgl & No</th>
                        <th class="px-4 py-4 text-left">Deskripsi</th>
                        <th class="px-4 py-4 text-left">Akun</th>
                        <th class="px-4 py-4 text-right">Debit</th>
                        <th class="px-4 py-4 text-right">Kredit</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($journals as $journal)
                        {{-- Journal Header Row --}}
                        <tr class="bg-gray-100 border-t-2 border-gray-300">
                            <td class="px-4 py-3 align-top">
                                <div class="font-bold text-gray-900">{{ $journal->date->format('d/m/y') }}</div>
                                <div class="text-[10px] text-blue-600 font-bold uppercase">{{ $journal->entry_number }}</div>
                            </td>
                            <td class="px-4 py-3 align-top font-bold text-gray-800">
                                {{ $journal->description }}
                                <span class="block text-[9px] text-gray-500 font-normal mt-1">S: {{ $journal->source }}</span>
                            </td>
                            <td class="px-4 py-3" colspan="3"></td>
                            <td class="px-4 py-3 text-center align-top">
                                @if($journal->is_posted)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 border">POSTED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-800 border">DRAFT</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center align-top whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    @can('edit journals')
                                    <a href="{{ route('accounting.journals.edit', $journal->id) }}" 
                                        class="text-blue-600 hover:text-blue-900 transition duration-150" title="Edit Jurnal" @click.stop>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    @endcan

                                    @can('delete journals')
                                    <button wire:click.stop="confirmDelete({{ $journal->id }})" 
                                        class="text-red-600 hover:text-red-900 transition duration-150" title="Hapus Jurnal">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        {{-- Journal Lines --}}
                        @foreach($journal->lines as $line)
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100 italic bg-white">
                            <td class="px-4 py-2" colspan="2"></td>
                            <td class="px-4 py-2">
                                <div class="{{ $line->credit > 0 ? 'ml-6' : '' }} flex flex-col">
                                    <span class="text-xs font-bold text-gray-700">{{ $line->account->code }} - {{ $line->account->name }}</span>
                                    @if($line->notes)
                                        <span class="text-[9px] text-gray-500">{{ $line->notes }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right">
                                @if($line->debit > 0)
                                    <span class="text-xs font-bold text-gray-900">{{ number_format($line->debit, 0, ',', '.') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                @if($line->credit > 0)
                                    <span class="text-xs font-bold text-gray-900">{{ number_format($line->credit, 0, ',', '.') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-2 py-2" colspan="2"></td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">Data Tidak Ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($journals->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="7" class="px-6 py-4">
                            @include('components.custom-pagination', ['items' => $journals])
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Source Transaction Modal --}}
    @if($showSourceModal && $selectedJournal)
    <div wire:key="source-modal-{{ $selectedJournal->id }}" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeSourceModal"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-gray-100 animate-fade-in-up">
                {{-- Modal Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Detail Transaksi Sumber</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ ucfirst($selectedJournal->source) }} - {{ $selectedJournal->entry_number }}</p>
                    </div>
                    <button wire:click="closeSourceModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                {{-- Modal Content --}}
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    @if($selectedJournal->source === 'sale' && $sourceData)
                        @include('livewire.accounting.partials.source-sale', ['sale' => $sourceData])
                    @elseif($selectedJournal->source === 'purchase' && $sourceData)
                        @include('livewire.accounting.partials.source-purchase', ['receipt' => $sourceData])
                    @elseif($selectedJournal->source === 'expense' && $sourceData)
                        @include('livewire.accounting.partials.source-expense', ['expense' => $sourceData])
                    @else
                        <p class="text-center text-gray-500 py-8">Data transaksi tidak ditemukan.</p>
                    @endif
                </div>
                
                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <button wire:click="closeSourceModal" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition shadow-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal && $journalToDelete)
    <div class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cancelDelete"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 py-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-bold text-gray-900">Hapus Jurnal</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    Anda yakin ingin menghapus jurnal ini?
                                </p>
                                @if($journalToDelete->is_posted)
                                <p class="text-[10px] text-orange-600 font-bold mt-1 uppercase">⚠️ Jurnal ini sudah posted. Menghapus akan membatalkan pengaruh saldonya.</p>
                                @endif
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-bold text-gray-700">{{ $journalToDelete->entry_number }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $journalToDelete->description }}</p>
                                </div>
                                <p class="text-xs text-red-600 mt-3 font-semibold">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end">
                    <button wire:click="cancelDelete" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition">
                        Batal
                    </button>
                    <button wire:click="deleteJournal" type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition shadow-sm">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


</div>
