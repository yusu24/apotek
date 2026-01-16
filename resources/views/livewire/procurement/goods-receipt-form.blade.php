<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-normal text-gray-800">
             {{ $isEdit ? 'Edit Penerimaan Pesanan' : 'Penerimaan Pesanan' }}
        </h2>
        <a href="{{ route('procurement.goods-receipts.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-normal flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form wire:submit="save" x-data="{ showNotesModal: false }">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="space-y-3">
                
                <!-- Row 1: PO & Payment -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-3">
                    <!-- PO -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">Terima Dari</label>
                        <div class="flex-1 relative max-w-sm">
                            <select wire:model.live="purchase_order_id" 
                                {{ $isEdit ? 'disabled' : '' }}
                                class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal bg-gray-50 py-1.5 h-8 {{ $isEdit ? 'opacity-70 cursor-not-allowed' : '' }}">
                                <option value="">-- Tanpa PO / Langsung --</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name ?? '' }}</option>
                                @endforeach
                            </select>
                            @if($isEdit)
                                <input type="hidden" wire:model="purchase_order_id">
                            @endif
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">Metode Bayar <span class="text-red-500">*</span></label>
                        <div class="flex-1 flex gap-2 max-w-sm">
                            <select wire:model.live="payment_method" class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal bg-gray-50 py-1.5 h-8">
                                <option value="">-- Pilih --</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="due_date">Jatuh Tempo</option>
                            </select>
                            
                            @if($payment_method === 'transfer')
                                <select wire:model="bank_account_id" class="block w-40 rounded-md border-gray-300 text-xs bg-gray-50 py-1.5 h-8">
                                    <option value="">- Pilih Bank -</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($payment_method === 'due_date')
                                <select wire:model="due_date_weeks" class="block w-32 rounded-md border-gray-300 text-xs bg-gray-50 py-1.5 h-8">
                                    <option value="">- Tempo -</option>
                                    <option value="1">1 Minggu</option>
                                    <option value="2">2 Minggu</option>
                                    <option value="3">3 Minggu</option>
                                    <option value="4">4 Minggu</option>
                                </select>
                            @endif
                        </div>
                        @error('payment_method') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                        @error('bank_account_id') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Row 2: Delivery & Date -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-3">
                     <!-- Delivery Note -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">No. Surat Jalan <span class="text-red-500">*</span></label>
                        <div class="flex-1 max-w-sm">
                            <input type="text" wire:model="delivery_note_number" class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal bg-gray-50 py-1.5 h-8" placeholder="Nomor Bukti">
                            @error('delivery_note_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">Tgl Terima <span class="text-red-500">*</span></label>
                        <div class="flex-1 max-w-xs">
                            <input type="date" wire:model="received_date" class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal bg-gray-50 py-1.5 h-8">
                            @error('received_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            
    <div class="mt-8" x-data="{ activeTab: 'items' }">
        <!-- Tabs Navigation -->
        <div class="px-3 pt-3 border-b border-gray-200 bg-gray-50/50 rounded-t-xl overflow-hidden shadow-sm">
            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl w-fit mb-3">
                <button type="button" 
                    @click="activeTab = 'items'" 
                    :class="activeTab === 'items' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg font-normal transition flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Items
                </button>
                <button type="button" 
                    @click="activeTab = 'notes'" 
                    :class="activeTab === 'notes' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg font-normal transition flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Catatan
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="relative">
            <!-- Items Content -->
            <div x-show="activeTab === 'items'">

        <!-- Items Table Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gray-50/50 h-16 flex items-center">
                <div class="relative w-full max-w-xl">
                    <div class="relative">
                        <input type="text" 
                            wire:model.live.debounce.300ms="productSearch"
                            wire:keydown.arrow-down.prevent="incrementHighlight"
                            wire:keydown.arrow-up.prevent="decrementHighlight"
                            wire:keydown.enter="selectHighlighted"
                            class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2 pl-10 pr-4"
                            placeholder="Cari produk atau scan barcode untuk menambah item...">
                        
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <!-- Dropdown List -->
                    @if(!empty($productSearch) && count($searchResults) > 0)
                    <div class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            @foreach($searchResults as $index => $p)
                                <li wire:click="selectProduct({{ $p->id }})"
                                    class="px-4 py-2 {{ $highlightIndex === $index ? 'bg-blue-100' : 'hover:bg-blue-50' }} cursor-pointer flex justify-between items-center group transition-colors border-b border-gray-50 last:border-0">
                                    <div>
                                        <div class="text-xs font-bold text-gray-800">{{ $p->name }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $p->barcode }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-gray-200 border-collapse" style="table-layout: fixed !important; min-width: 1100px;">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-auto min-w-[200px]">Produk</th>
                            <th class="px-3 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-28">Batch No</th>
                            <th class="px-3 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-32">Exp Date</th>
                            <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-20">Qty</th>
                            <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-24">Satuan</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-40">Harga Beli</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-40">Harga Jual</th>
                            <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-24">Margin</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-gray-100 w-44">Total</th>
                            <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $index => $item)
                            <tr wire:key="item-{{ $index }}" 
                                x-data="{ 
                                    qty: $wire.entangle('items.{{ $index }}.qty_received').live, 
                                    buy_price: $wire.entangle('items.{{ $index }}.buy_price').live, 
                                    sell_price: $wire.entangle('items.{{ $index }}.sell_price').live,
                                    get margin() {
                                        let buy = parseFloat(this.buy_price) || 0;
                                        let sell = parseFloat(this.sell_price) || 0;
                                        if (buy <= 0) return 0;
                                        return ((sell - buy) / buy) * 100;
                                    }
                                }"
                                class="group hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 align-top border-r border-gray-100">
                                    @if(!empty($item['po_info']) || ($isEdit && $purchase_order_id))
                                        <div class="text-sm font-normal text-gray-800">{{ $item['product_name'] }}</div>
                                        @php $selectedProduct = $products->firstWhere('id', $item['product_id']); @endphp
                                        <div class="text-[10px] text-gray-500 mt-0.5">{{ $selectedProduct->barcode ?? '-' }}</div>

                                        <input type="hidden" wire:model="items.{{ $index }}.product_id">
                                    @else
                                        <select wire:model="items.{{ $index }}.product_id" class="w-full text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 font-normal">
                                            <option value="">Pilih Produk</option>
                                            @foreach($products as $prod)
                                                <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                            @endforeach
                                        </select>
                                        @if(!empty($item['product_id']))
                                            @php $selectedProduct = $products->firstWhere('id', $item['product_id']); @endphp
                                            <div class="text-[10px] text-gray-500 mt-0.5">{{ $selectedProduct->barcode ?? '-' }}</div>
                                        @endif
                                        @error("items.{$index}.product_id") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-top border-r border-gray-100">
                                    <input type="text" wire:model="items.{{ $index }}.batch_no" class="w-full text-xs rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 uppercase font-medium text-gray-700" placeholder="BATCH">
                                    @error("items.{$index}.batch_no") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-3 py-3 align-top border-r border-gray-100">
                                    <input type="date" wire:model="items.{{ $index }}.expired_date" class="w-full text-xs rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 font-normal">
                                    @error("items.{$index}.expired_date") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-3 py-3 align-top border-r border-gray-100">
                                    <input type="number" x-model="qty" class="w-full text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center py-1.5 px-2 font-normal text-gray-900" placeholder="0" min="0">
                                    @error("items.{$index}.qty_received") <span class="text-red-500 text-[10px] mt-1 block text-center leading-tight">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-3 py-3 align-top border-r border-gray-100">
                                     @php $selectedProduct = $products->firstWhere('id', $item['product_id']); @endphp
                                     @if($selectedProduct)
                                        <select wire:model.live="items.{{ $index }}.unit_id" class="w-full text-xs rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 font-normal text-gray-700">
                                            @if($selectedProduct->unit_id) <option value="{{ $selectedProduct->unit_id }}">{{ $selectedProduct->unit?->name ?? 'N/A' }}</option> @endif
                                            @foreach($selectedProduct->unitConversions as $conversion)
                                                @if($conversion->to_unit_id == $selectedProduct->unit_id)
                                                    <option value="{{ $conversion->from_unit_id }}">{{ $conversion->fromUnit?->name ?? 'N/A' }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                     @else
                                        <div class="text-xs text-gray-400 text-center">-</div>
                                     @endif
                                </td>
                                <td class="px-4 py-3 align-top border-r border-gray-100 text-right">
                                    <div class="relative" x-data="money(buy_price)" x-modelable="value" x-model="buy_price">
                                        <span class="absolute left-1.5 top-2 text-[9px] text-gray-400">Rp</span>
                                        <input type="text" x-bind="input" placeholder="0" class="w-full text-xs rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-right pr-1 pl-5 py-1.5 font-normal text-gray-700">
                                    </div>
                                    @error("items.{$index}.buy_price") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-4 py-3 align-top border-r border-gray-100 text-right">
                                    <div class="relative" x-data="money(sell_price)" x-modelable="value" x-model="sell_price">
                                        <span class="absolute left-1.5 top-2 text-[9px] text-gray-400">Rp</span>
                                        <input type="text" x-bind="input" placeholder="0" class="w-full text-xs rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-right pr-1 pl-5 py-1.5 font-normal text-gray-700">
                                    </div>
                                    @error("items.{$index}.sell_price") <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-3 py-3 align-top border-r border-gray-100">
                                    <div class="flex items-center justify-center h-8">
                                        <div class="flex flex-col items-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border"
                                                :class="margin < 0 ? 'bg-red-100 text-red-700 border-red-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200'"
                                                x-text="new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(margin) + '%'">
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top text-right text-sm font-bold text-gray-900 border-r border-gray-100">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format((parseFloat(qty) || 0) * (parseFloat(buy_price) || 0))"></span>
                                </td>
                                <td class="px-3 py-3 align-middle text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" wire:click="removeItem({{ $index }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/50 border-t border-gray-100">
                        <tr>
                            <td colspan="7" class="px-6 py-4">
                                <div class="flex items-center justify-start gap-4">
                                    <p class="text-[10px] text-yellow-700 italic flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Penerimaan barang otomatis menambah stok.
                                    </p>
                                </div>
                            </td>
                            <td colspan="3" class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total Nilai Penerimaan</span>
                                    <span class="text-xl font-bold text-blue-700 bg-blue-50 px-6 py-2 rounded-xl border-2 border-blue-100 shadow-sm flex items-center gap-3">
                                        <div class="p-1.5 bg-blue-600 rounded-lg text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        Rp {{ number_format(collect($items)->sum(fn($i) => (float)($i['qty_received'] ?? 0) * (float)($i['buy_price'] ?? 0)), 0, ',', '.') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        </div>
        </div>

        <!-- Notes Content -->
        <div x-show="activeTab === 'notes'" class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200 overflow-hidden" style="display: none;">
            <div class="p-6">
                <div class="bg-blue-50/50 border border-blue-100 rounded-lg p-4 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <p class="text-[10px] text-blue-600/70">Tambahkan catatan khusus terkait penerimaan barang ini jika diperlukan.</p>
                </div>
                <textarea wire:model="notes" rows="8" 
                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm transition-all p-4" 
                    placeholder="Tulis catatan tambahan di sini..."></textarea>
            </div>
        </div>
    </div>

    <!-- Final Footer Actions -->
    <div class="mt-8 flex items-center justify-end gap-4">
        <div class="flex items-center gap-3">
            @if(session()->has('message'))
                <span class="text-green-600 text-sm font-bold bg-green-50 px-3 py-1.5 rounded-lg border border-green-100">{{ session('message') }}</span>
            @endif
            <button type="submit" 
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm w-fit shrink-0">
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update' : 'Simpan' }}</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </div>
    </form>
</div>
