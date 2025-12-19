<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                {{ $purchaseOrder ? 'Edit Pesanan' : 'Buat Pesanan Baru' }}
            </h2>
            <a href="{{ route('procurement.purchase-orders.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi PO</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. PO</label>
                            <input type="text" wire:model="po_number" class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" wire:model="date" class="mt-1 block w-full rounded-lg border-gray-300">
                            @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <select wire:model="supplier_id" class="mt-1 block w-full rounded-lg border-gray-300">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full rounded-lg border-gray-300">
                                <option value="draft">Draft</option>
                                <option value="ordered">Ordered</option>
                                <option value="cancelled">Cancelled</option>
                                @if($purchaseOrder && $purchaseOrder->status == 'received')
                                    <option value="received" disabled>Received</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Item Pesanan</h3>
                        <button type="button" wire:click="addItem" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            + Tambah Barang
                        </button>
                    </div>

                    @error('items') <div class="text-red-500 text-sm mb-2">{{ $message }}</div> @enderror

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Harga Satuan</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Subtotal</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <select wire:model.live="items.{{ $index }}.product_id" class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Produk</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                                @endforeach
                                            </select>
                                            @error("items.{$index}.product_id") <span class="text-red-500 text-xs text-nowrap">Wajib pilih</span> @enderror
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" wire:model.live="items.{{ $index }}.qty" class="w-full text-sm rounded-lg border-gray-300 text-center" min="1">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" wire:model.live="items.{{ $index }}.unit_price" class="w-full text-sm rounded-lg border-gray-300 text-right">
                                        </td>
                                        <td class="px-3 py-2 text-right font-bold text-gray-900">
                                            {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-3 py-3 text-right font-bold text-gray-700">Total Est.</td>
                                    <td class="px-3 py-3 text-right font-bold text-gray-900 text-lg">
                                        Rp {{ number_format(collect($items)->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold shadow-lg">
                        Simpan Pesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
