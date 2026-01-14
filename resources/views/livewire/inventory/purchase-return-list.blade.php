<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-normal text-gray-800">Retur Pembelian</h2>
        <p class="text-sm text-gray-500">Kelola pengembalian barang ke supplier.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-row justify-between items-center gap-4">
            <div class="relative flex-1 sm:flex-none">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari No. Retur atau Nama Supplier..." class="block w-full sm:w-48 pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div class="shrink-0">
                <button wire:click="openModal" class="bg-blue-600 text-white p-2 sm:px-4 sm:py-2 rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm whitespace-nowrap" title="Tambah Retur">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="hidden sm:inline">Tambah Retur</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">No. Retur</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4 text-right">Total Amount</th>
                        <th class="px-6 py-4 text-center">User</th>
                        <th class="px-6 py-4 text-center">Tanggal</th>
                        <th class="px-6 py-4">Catatan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $return)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-normal text-blue-600">{{ $return->return_no }}</td>
                            <td class="px-6 py-4 font-normal">{{ optional($return->supplier)->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-normal">Rp {{ number_format($return->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ optional($return->user)->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 text-xs italic">{{ $return->notes ?: '-' }}</td>
                            <td class="px-6 py-4 text-center flex justify-center gap-2">
                                <button wire:click="viewDetails({{ $return->id }})" class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <a href="{{ route('inventory.returns.purchase.print', $return->id) }}" target="_blank" class="text-gray-600 hover:text-gray-800 transition-colors" title="Cetak Retur">
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
                        <h3 class="text-xl font-normal text-gray-900">Tambah Retur Pembelian</h3>
                        <button type="button" wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-normal text-gray-700 mb-2">Pilih Supplier <span class="text-red-500">*</span></label>
                                <select wire:model.live="selectedSupplierId" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSupplierId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-normal text-gray-700 mb-2">Pilih Surat Jalan <span class="text-red-500">*</span></label>
                                <select wire:model.live="selectedGoodsReceiptId" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" @if(!$selectedSupplierId) disabled @endif>
                                    <option value="">-- Pilih Surat Jalan --</option>
                                    @foreach($goodsReceipts as $gr)
                                        <option value="{{ $gr->id }}">{{ $gr->delivery_note_number }} ({{ \Carbon\Carbon::parse($gr->received_date)->format('d/m/Y') }})</option>
                                    @endforeach
                                </select>
                                @error('selectedGoodsReceiptId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-gray-100 rounded-xl">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px]">
                                    <tr>
                                        <th class="px-4 py-3 text-center w-10">
                                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </th>
                                        <th class="px-4 py-3">Produk / Batch</th>
                                        <th class="px-4 py-3 text-center">Qty Terima</th>
                                        <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                        <th class="px-4 py-3 text-center w-32">Qty Retur</th>
                                        <th class="px-4 py-3 text-right">Harga Beli</th>
                                        <th class="px-4 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($returnItems as $batchId => $item)
                                        <tr class="hover:bg-gray-50/50 transition-colors {{ !empty($item['selected']) ? 'bg-blue-50/20' : '' }}">
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" wire:model.live="returnItems.{{ $batchId }}.selected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-800">{{ $item['product_name'] }}</div>
                                                <div class="text-xs text-gray-500 font-medium uppercase">No. Batch: {{ $item['batch_no'] }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-500">{{ $item['gr_quantity'] }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-gray-600">{{ $item['max_quantity'] }}</td>
                                            <td class="px-4 py-3">
                                                <input type="number" wire:model.live="returnItems.{{ $batchId }}.quantity" class="w-full h-9 rounded-lg border-gray-300 text-sm text-center focus:ring-blue-500 focus:border-blue-500" min="0" max="{{ $item['max_quantity'] }}" @if(empty($item['selected'])) disabled @endif>
                                                @error("returnItems.{$batchId}.quantity") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="px-4 py-3 text-right text-gray-500 italic">
                                                Rp {{ number_format($item['cost_price'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-blue-600">
                                                Rp {{ number_format((float)($item['quantity'] ?: 0) * (float)($item['cost_price'] ?: 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(empty($returnItems))
                                        <tr>
                                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 italic">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                    <p class="font-medium">Pilih surat jalan untuk menampilkan barang</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if(!empty($returnItems))
                                    <tfoot class="bg-blue-50/30">
                                        <tr class="font-bold">
                                            <td colspan="6" class="px-4 py-4 text-right uppercase tracking-widest text-[10px] text-gray-500">Total Nilai Retur</td>
                                            <td class="px-4 py-4 text-right text-lg text-blue-700 font-bold">Rp {{ number_format(collect($returnItems)->filter(fn($i) => !empty($i['selected']))->sum(fn($i) => (float)($i['quantity'] ?: 0) * (float)($i['cost_price'] ?: 0)), 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan / Alasan Retur</label>
                            <textarea wire:model="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Tuliskan alasan pengembalian barang..."></textarea>
                        </div>
                        
                        @error('error') <div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm font-medium border border-red-100 italic">{{ $message }}</div> @enderror
                        @error('returnItems') <div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm font-medium border border-red-100 italic">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end items-center gap-3">
                    <button type="button" wire:click="$set('showModal', false)" 
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 shadow-sm font-normal transition duration-200 text-sm">
                        Batal
                    </button>
                    <button wire:click="saveReturn" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-normal flex items-center justify-center gap-2 transition duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed" 
                        @if(empty($returnItems)) disabled @endif>
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
                        <h3 class="text-xl font-normal text-gray-900">Detail Retur Pembelian</h3>
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
                            <p class="text-gray-500">Supplier</p>
                            <p class="font-bold text-gray-900">{{ optional($selectedReturn->supplier)->name ?? '-' }}</p>
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
                                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-blue-600">Rp {{ number_format($item->quantity * $item->cost_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-blue-50/30">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-600 uppercase text-xs">Total</td>
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
