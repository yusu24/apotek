<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Retur Pembelian</h2>
        <p class="text-sm text-gray-500">Kelola pengembalian barang ke supplier.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-4 items-center">
            <button wire:click="openModal" class="btn btn-warning">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Retur
            </button>
            <div class="relative w-full md:w-1/3">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari No. Retur atau Nama Supplier..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">No. Retur</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4 text-right">Total Amount</th>
                        <th class="px-6 py-4 text-center">User</th>
                        <th class="px-6 py-4 text-center">Tanggal</th>
                        <th class="px-6 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $return)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-orange-600">{{ $return->return_no }}</td>
                            <td class="px-6 py-4 font-medium">{{ $return->supplier->name }}</td>
                            <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($return->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $return->user->name }}</td>
                            <td class="px-6 py-4 text-center">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs italic">{{ $return->notes ?: '-' }}</td>
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
                        <h3 class="text-xl font-bold text-gray-900">Tambah Retur Pembelian</h3>
                        <button type="button" wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Supplier <span class="text-red-500">*</span></label>
                                <select wire:model.live="selectedSupplierId" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSupplierId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Cari Barang (Batch) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </span>
                                    <input type="text" wire:model.live="productSearch" placeholder="Ketik nama produk..." class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                                </div>
                                
                                @if(!empty($foundBatches))
                                    <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                        @foreach($foundBatches as $batch)
                                            <button wire:click="addBatchToReturn({{ $batch->id }})" class="w-full text-left p-3 hover:bg-orange-50 border-b border-gray-50 flex justify-between items-center transition-colors">
                                                <div>
                                                    <div class="font-bold text-gray-800 text-sm">{{ $batch->product->name }}</div>
                                                    <div class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Batch: {{ $batch->batch_no }} | Exp: {{ $batch->expired_date ? $batch->expired_date->format('d/m/Y') : '-' }}</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm font-bold text-orange-600">Stok: {{ $batch->stock_current }}</div>
                                                    <div class="text-[10px] text-gray-400 font-bold">Rp {{ number_format($batch->buy_price, 0, ',', '.') }}</div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                <p class="mt-1 text-xs text-gray-400 italic font-medium">Hanya menampilkan batch yang masih memiliki stok.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-gray-100 rounded-xl">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                                    <tr>
                                        <th class="px-4 py-3">Produk / Batch</th>
                                        <th class="px-4 py-3 text-center">Stok Sisa</th>
                                        <th class="px-4 py-3 text-center w-32">Qty Retur</th>
                                        <th class="px-4 py-3 text-right">Harga Beli</th>
                                        <th class="px-4 py-3 text-right">Subtotal</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($returnItems as $batchId => $item)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-800">{{ $item['product_name'] }}</div>
                                                <div class="text-xs text-gray-500 font-medium uppercase tracking-tighter">No. Batch: {{ $item['batch_no'] }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold text-gray-600">{{ $item['max_quantity'] }}</td>
                                            <td class="px-4 py-3">
                                                <input type="number" wire:model.live="returnItems.{{ $batchId }}.quantity" class="w-full h-9 rounded-lg border-gray-300 text-sm text-center focus:ring-orange-500 focus:border-orange-500" min="0" max="{{ $item['max_quantity'] }}">
                                                @error("returnItems.{$batchId}.quantity") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="px-4 py-3 text-right text-gray-500 italic">
                                                Rp {{ number_format($item['cost_price'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-orange-600">
                                                Rp {{ number_format(($item['quantity'] ?: 0) * $item['cost_price'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="removeItem({{ $batchId }})" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(empty($returnItems))
                                        <tr>
                                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 italic">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                    <p class="font-medium">Belum ada barang yang dipilih untuk diretur</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if(!empty($returnItems))
                                    <tfoot class="bg-orange-50/30">
                                        <tr class="font-bold">
                                            <td colspan="4" class="px-4 py-4 text-right uppercase tracking-widest text-[10px] text-gray-500">Total Nilai Retur</td>
                                            <td class="px-4 py-4 text-right text-lg text-orange-700 font-bold">Rp {{ number_format(collect($returnItems)->sum(fn($i) => ($i['quantity'] ?: 0) * $i['cost_price']), 0, ',', '.') }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan / Alasan Retur</label>
                            <textarea wire:model="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 transition-all" placeholder="Tuliskan alasan pengembalian barang..."></textarea>
                        </div>
                        
                        @error('error') <div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm font-medium border border-red-100 italic">{{ $message }}</div> @enderror
                        @error('returnItems') <div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm font-medium border border-red-100 italic">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button wire:click="saveReturn" class="btn btn-lg btn-primary bg-gray-800 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" @if(empty($returnItems)) disabled @endif>
                        Simpan Retur
                    </button>
                    <button wire:click="$set('showModal', false)" class="btn btn-secondary">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
