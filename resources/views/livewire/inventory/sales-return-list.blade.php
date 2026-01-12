<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-normal text-gray-800">Retur Penjualan</h2>
        <p class="text-sm text-gray-500">Kelola pengembalian barang dari pelanggan.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-4 items-center">
            <button wire:click="openModal" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Retur
            </button>
            <div class="relative w-full md:w-1/3">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari No. Retur atau No. Invoice..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">No. Retur</th>
                        <th class="px-6 py-4">No. Invoice</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Catatan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $return)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-normal text-blue-600">{{ $return->return_no }}</td>
                            <td class="px-6 py-4">{{ $return->sale->invoice_no }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($return->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ optional($return->user)->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $return->notes ?: '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="viewDetails({{ $return->id }})" class="text-blue-600 hover:text-blue-800 transition-colors inline-block mr-2" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <a href="{{ route('inventory.returns.sales.print', $return->id) }}" target="_blank" class="text-gray-600 hover:text-gray-800 transition-colors inline-block" title="Cetak Retur">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Data retur tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $returns->links() }}
        </div>
    </div>

    <!-- Modal Tambah Retur -->
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-normal text-gray-900">Tambah Retur Penjualan Baru</h3>
                        <button type="button" wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div>
                            <label class="block text-sm font-normal text-gray-700 mb-2">Cari No. Invoice <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="invoiceSearch" placeholder="Masukkan Nomor Invoice (cth: INV/...)" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <p class="mt-1 text-xs text-gray-500 italic">Masukkan minimal 4 karakter untuk mencari.</p>
                        </div>

                        @if($selectedSale)
                            <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Customer</p>
                                        <p class="font-normal text-blue-800">{{ $selectedSale->invoice_no }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Tgl Transaksi</p>
                                        <p class="font-normal text-blue-800">{{ $selectedSale->date->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-widest">
                                        <tr>
                                            <th class="px-4 py-3">Produk</th>
                                            <th class="px-4 py-3 text-center">Qty Jual</th>
                                            <th class="px-4 py-3 text-center">Batch</th>
                                            <th class="px-4 py-3 text-center w-32">Qty Retur</th>
                                            <th class="px-4 py-3 text-right">Subtotal Retur</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($returnItems as $id => $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="font-bold text-gray-800">{{ $item['name'] }}</div>
                                                    <div class="text-xs text-gray-500 italic">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold text-gray-600">{{ $item['max_quantity'] }}</td>
                                                <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $item['batch_no'] }}</td>
                                                <td class="px-4 py-3">
                                                    <input type="number" wire:model.live="returnItems.{{ $id }}.quantity" class="w-full h-9 rounded-lg border-gray-300 text-sm text-center focus:ring-blue-500 focus:border-blue-500" min="0" max="{{ $item['max_quantity'] }}">
                                                    @error("returnItems.{$id}.quantity") <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-blue-600">
                                                    Rp {{ number_format((float)($item['quantity'] ?: 0) * (float)($item['price'] ?: 0), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Catatan</label>
                                <textarea wire:model="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Alasan retur, kondisi barang, dll..."></textarea>
                            </div>
                        @elseif($invoiceSearch && strlen($invoiceSearch) > 3)
                            <div class="p-8 text-center text-gray-500 italic bg-gray-50 rounded-xl border border-dashed">
                                Invoice tidak ditemukan atau belum dimuat.
                            </div>
                        @endif
                        
                        @error('error') <div class="p-3 bg-red-100 text-red-700 rounded-lg text-sm font-medium border border-red-200">{{ $message }}</div> @enderror
                        @error('returnItems') <div class="p-3 bg-red-100 text-red-700 rounded-lg text-sm font-medium border border-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end items-center gap-3">
                    <button type="button" wire:click="$set('showModal', false)" 
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 shadow-sm font-normal transition duration-200 text-sm">
                        Batal
                    </button>
                    <button wire:click="saveReturn" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-normal flex items-center justify-center gap-2 transition duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed" 
                        @if(!$selectedSale) disabled @endif>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedReturn)
    <div class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" wire:click="$set('showDetailModal', false)">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-normal text-gray-900">Detail Retur Penjualan</h3>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                        <div>
                            <p class="text-gray-500">No. Retur</p>
                            <p class="font-bold text-gray-900">{{ $selectedReturn->return_no }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Invoice Ref</p>
                            <p class="font-bold text-gray-900">{{ $selectedReturn->sale->invoice_no }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal</p>
                            <p class="font-bold text-gray-900">{{ $selectedReturn->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                         <div>
                            <p class="text-gray-500">Dibuat Oleh</p>
                            <p class="font-bold text-gray-900">{{ optional($selectedReturn->user)->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-100 rounded-xl mb-4">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                                <tr>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3 text-center">Batch</th>
                                    <th class="px-4 py-3 text-center">Qty</th>
                                    <th class="px-4 py-3 text-right">Harga</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($selectedReturn->items as $item)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ optional($item->product)->name ?? 'Produk Dihapus' }}</td>
                                        <td class="px-4 py-3 text-center text-gray-500">{{ $item->batch->batch_no ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center font-bold">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-blue-600">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-blue-50/30">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-600 uppercase text-xs">Total Refund</td>
                                    <td class="px-4 py-3 text-right font-bold text-lg text-blue-700">Rp {{ number_format($selectedReturn->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                     <button type="button" wire:click="$set('showDetailModal', false)" 
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 shadow-sm font-normal transition duration-200 text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
