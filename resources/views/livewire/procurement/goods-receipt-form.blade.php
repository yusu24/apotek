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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Barang Diterima</h3>
                        @if(!$purchase_order_id)
                            <button type="button" wire:click="addItem" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
                                + Tambah Barang
                            </button>
                        @endif
                    </div>

                    @error('items') <div class="text-red-500 text-sm mb-2">{{ $message }}</div> @enderror
                    
                    @if (session()->has('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">No. Batch</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Exp Date</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Qty</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">Satuan</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Harga Satuan</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-44">Subtotal</th>
                                    <th class="px-2 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $index => $item)
                                    <tr wire:key="item-{{ $index }}" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 align-middle">
                                            @if($purchase_order_id)
                                                <div class="text-sm font-semibold text-gray-900 leading-tight">{{ $item['product_name'] }}</div>
                                                @php
                                                    $selectedProduct = $products->firstWhere('id', $item['product_id']);
                                                @endphp
                                                @if($selectedProduct && $selectedProduct->barcode)
                                                    <div class="text-[10px] text-gray-400 font-semibold">{{ $selectedProduct->barcode }}</div>
                                                @endif
                                                @if(!empty($item['po_info']))
                                                    <div class="text-[10px] text-blue-600 font-bold bg-blue-50 inline-block px-1 rounded mt-1">{{ $item['po_info'] }}</div>
                                                @endif
                                                <input type="hidden" wire:model="items.{{ $index }}.product_id">
                                            @else
                                                <select wire:model="items.{{ $index }}.product_id" 
                                                        class="w-full text-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 font-medium">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->name }}@if($prod->barcode) ({{ $prod->barcode }})@endif</option>
                                                    @endforeach
                                                </select>
                                                @error("items.{$index}.product_id") <span class="text-red-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 align-middle">
                                            <input type="text" wire:model="items.{{ $index }}.batch_no" 
                                                   class="w-full text-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 uppercase font-semibold text-gray-700" 
                                                   placeholder="BATCH#">
                                            @error("items.{$index}.batch_no") <span class="text-red-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-3 py-3 align-middle">
                                            @php
                                                $expDate = $item['expired_date'] ?? null;
                                                $isExpiringSoon = false;
                                                $isExpired = false;
                                                if ($expDate) {
                                                    $expiredDate = \Carbon\Carbon::parse($expDate);
                                                    $monthsUntilExpiry = now()->diffInMonths($expiredDate, false);
                                                    $isExpired = $expiredDate->isPast();
                                                    $isExpiringSoon = $monthsUntilExpiry <= 6 && $monthsUntilExpiry > 0;
                                                }
                                            @endphp
                                            <input type="date" wire:model="items.{{ $index }}.expired_date" 
                                                   class="w-full text-xs rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 font-semibold
                                                          @if($isExpired) text-red-600 @elseif($isExpiringSoon) text-orange-600 @else text-gray-700 @endif">
                                            
                                            @if($expDate)
                                                @if($isExpired)
                                                    <div class="text-[10px] text-red-600 font-bold uppercase mt-0.5 tracking-tight">Kadaluarsa</div>
                                                @elseif($isExpiringSoon)
                                                    <div class="text-[10px] text-orange-600 font-bold uppercase mt-0.5 tracking-tight">&lt; 6 Bulan</div>
                                                @endif
                                            @endif
                                            @error("items.{$index}.expired_date") <span class="text-red-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-3 py-3 align-middle">
                                            <input type="number" wire:model.live="items.{{ $index }}.qty_received" 
                                                   class="w-full text-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center py-1.5 px-2 font-bold text-gray-800" min="1">
                                            @error("items.{$index}.qty_received") <span class="text-red-500 text-[10px] mt-1 block text-center font-semibold leading-tight">{{ $message }}</span> @enderror
                                            
                                            @if($purchase_order_id && isset($item['max_qty_allowed']))
                                                @if($item['qty_received'] < $item['max_qty_allowed'])
                                                    <div class="mt-1 flex items-center justify-center gap-1 bg-yellow-50 text-yellow-700 text-[10px] font-bold p-1 rounded border border-yellow-200 uppercase tracking-tight">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                        Kuantitas Kurang
                                                    </div>
                                                @elseif($item['qty_received'] > $item['max_qty_allowed'])
                                                    <div class="mt-1 flex items-center justify-center gap-1 bg-red-50 text-red-700 text-[10px] font-bold p-1 rounded border border-red-200 uppercase tracking-tight">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                        Kuantitas Berlebih
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 align-middle">
                                            @php
                                                $selectedProduct = $products->firstWhere('id', $item['product_id']);
                                            @endphp
                                            @if($selectedProduct)
                                                <select wire:model.live="items.{{ $index }}.unit_id" 
                                                        class="w-full text-xs rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 font-semibold text-gray-700">
                                                    @if($selectedProduct->unit)
                                                        <option value="{{ $selectedProduct->unit_id }}">
                                                            {{ $selectedProduct->unit->name ?? 'N/A' }}
                                                        </option>
                                                    @endif
                                                    @foreach($selectedProduct->unitConversions as $conversion)
                                                        @if($conversion->to_unit_id == $selectedProduct->unit_id)
                                                            <option value="{{ $conversion->from_unit_id }}">
                                                                {{ $conversion->fromUnit->name ?? 'N/A' }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                
                                                @error("items.{$index}.unit_id") <span class="text-red-500 text-[10px] mt-1 block font-semibold leading-tight">{{ $message }}</span> @enderror
                                            @else
                                                <div class="text-xs text-gray-400 text-center font-medium">-</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 align-middle text-right">
                                            <div class="relative">
                                                <span class="absolute left-2 top-2 text-[10px] text-gray-400 font-bold">Rp</span>
                                                <input type="text" 
                                                       wire:model.live="items.{{ $index }}.buy_price" 
                                                       class="w-full text-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-right pr-2 pl-7 py-1.5 font-bold text-gray-700" 
                                                       placeholder="0"
                                                       x-data="{ formatNumber(value) { return Number(value || 0).toLocaleString('id-ID'); } }"
                                                       x-on:blur="$el.value = formatNumber($el.value.replace(/\./g, ''))">
                                            </div>
                                            @error("items.{$index}.buy_price") <span class="text-red-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-3 py-3 align-middle text-right text-sm font-bold text-gray-900 border-l border-gray-100">
                                            @php
                                                $qty = (float)($item['qty_received'] ?? 0);
                                                $price = (float)($item['buy_price'] ?? 0);
                                                $subtotal = $qty * $price;
                                            @endphp
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-2 py-3 align-middle text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})" 
                                                    class="text-gray-400 hover:text-red-600 p-1 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-gray-200">
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-right align-middle">
                                        <span class="text-sm font-bold text-gray-600 uppercase tracking-wider">Total Penerimaan:</span>
                                    </td>
                                    <td class="px-3 py-4 text-right align-middle">
                                        <div class="text-2xl font-bold text-blue-600 tracking-tight">
                                            <span class="text-sm font-bold mr-1">Rp</span>{{ number_format(collect($items)->sum(fn($i) => (float)($i['qty_received'] ?? 0) * (float)($i['buy_price'] ?? 0)), 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
