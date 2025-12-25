<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
             Penerimaan Pesanan
        </h2>
        <a href="{{ route('procurement.goods-receipts.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <p class="text-sm text-yellow-700">
            Perhatian: Penerimaan barang akan <strong>otomatis menambah stok</strong> (Batch baru/update). Pastikan Nomor Batch dan Tanggal Kadaluarsa diisi dengan benar sesuai fisik barang.
        </p>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Info Surat Jalan</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referensi PO (Opsional)</label>
                            <select wire:model.live="purchase_order_id" class="mt-1 block w-full rounded-lg border-gray-300">
                                <option value="">-- Tanpa PO / Langsung --</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name ?? '' }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih PO untuk otomatis mengisi item.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Surat Jalan / Faktur</label>
                            <input type="text" wire:model="delivery_note_number" class="mt-1 block w-full rounded-lg border-gray-300">
                            @error('delivery_note_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Terima</label>
                            <input type="date" wire:model="received_date" class="mt-1 block w-full rounded-lg border-gray-300">
                            @error('received_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                        </div>

                        <div x-data="{ paymentMethod: @entangle('payment_method') }">
                            <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                            <select wire:model.live="payment_method" class="mt-1 block w-full rounded-lg border-gray-300">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="due_date">Jatuh Tempo</option>
                            </select>
                            @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                            <!-- Conditional Due Date Selector -->
                            <div x-show="paymentMethod === 'due_date'" x-cloak class="mt-3">
                                <label class="block text-sm font-medium text-gray-700">Jangka Waktu</label>
                                <select wire:model="due_date_weeks" class="mt-1 block w-full rounded-lg border-gray-300">
                                    <option value="">-- Pilih Jangka Waktu --</option>
                                    <option value="1">1 Minggu</option>
                                    <option value="2">2 Minggu</option>
                                    <option value="3">3 Minggu</option>
                                    <option value="4">4 Minggu</option>
                                </select>
                                @error('due_date_weeks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Barang Diterima</h3>
                    </div>

                    @error('items') <div class="text-red-500 text-sm mb-2">{{ $message }}</div> @enderror
                    
                    @if (session()->has('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">No. Batch</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Exp Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Satuan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Harga Beli</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($items as $index => $item)
                                    <tr wire:key="item-{{ $index }}">
                                        <td class="px-3 py-2">
                                            @if($purchase_order_id)
                                                <div class="text-sm font-medium text-gray-900">{{ $item['product_name'] }}</div>
                                                <input type="hidden" wire:model="items.{{ $index }}.product_id">
                                            @else
                                                <select wire:model="items.{{ $index }}.product_id" class="w-full text-sm rounded-lg border-gray-300">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error("items.{$index}.product_id") <span class="text-red-500 text-xs">Wajib diisi</span> @enderror
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="items.{{ $index }}.batch_no" class="w-full text-sm rounded-lg border-gray-300 p-1" placeholder="CTH: B202501">
                                            @error("items.{$index}.batch_no") <span class="text-red-500 text-xs">Wajib diisi</span> @enderror
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="date" wire:model="items.{{ $index }}.expired_date" class="w-full text-sm rounded-lg border-gray-300 p-1">
                                            @error("items.{$index}.expired_date") <span class="text-red-500 text-xs">Wajib diisi</span> @enderror
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" wire:model="items.{{ $index }}.qty_received" class="w-full text-sm rounded-lg border-gray-300 text-center" min="1">
                                            @error("items.{$index}.qty_received") <span class="text-red-500 text-xs">Wajib diisi</span> @enderror
                                        </td>
                                        <td class="px-3 py-2">
                                            @php
                                                $selectedProduct = $products->firstWhere('id', $item['product_id']);
                                            @endphp
                                            @if($selectedProduct)
                                                <select wire:model.live="items.{{ $index }}.unit_id" class="w-full text-sm rounded-lg border-gray-300 p-1">
                                                    @if($selectedProduct->unit)
                                                        <option value="{{ $selectedProduct->unit_id }}">
                                                            {{ $selectedProduct->unit->name }} (1)
                                                        </option>
                                                    @endif
                                                    @foreach($selectedProduct->unitConversions as $conversion)
                                                        @if($conversion->to_unit_id == $selectedProduct->unit_id)
                                                            <option value="{{ $conversion->from_unit_id }}">
                                                                {{ $conversion->fromUnit->name }} ({{ (float)$conversion->conversion_factor }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @else
                                                <div class="text-sm text-gray-400">-</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" wire:model="items.{{ $index }}.buy_price" class="w-full text-sm rounded-lg border-gray-300 text-right" min="0" step="0.01" placeholder="0">
                                            @error("items.{$index}.buy_price") <span class="text-red-500 text-xs">Wajib diisi</span> @enderror
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold shadow-lg">
                        Proses Penerimaan & Update Stok
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
