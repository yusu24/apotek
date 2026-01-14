<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-normal text-gray-800">
            {{ $purchaseOrder ? 'Edit Pesanan' : 'Surat Pesanan Baru' }}
        </h2>
        <a href="{{ route('procurement.purchase-orders.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-normal flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form wire:submit="save">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="space-y-3">
                
                <!-- Row 1: No. PO & Tanggal -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-3">
                    <!-- No. PO -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">No. PO <span class="text-red-500">*</span></label>
                        <div class="flex-1 max-w-sm">
                            <input type="text" wire:model="po_number" class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal bg-gray-100 py-1.5 h-8" readonly placeholder="Auto Generated">
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">Tanggal <span class="text-red-500">*</span></label>
                        <div class="flex-1 max-w-xs">
                            <input type="text" wire:model="date" 
                                   placeholder="dd/mm/yyyy" 
                                   class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : 'bg-gray-50' }} py-1.5 h-8" 
                                   {{ $isReadOnly ? 'readonly' : '' }}
                                   maxlength="10">
                            @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Row 2: Supplier & Status -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-3">
                    <!-- Supplier -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">Supplier <span class="text-red-500">*</span></label>
                        <div class="flex-1 max-w-sm">
                            <select wire:model="supplier_id" class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : 'bg-gray-50' }} py-1.5 h-8" {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <label class="w-24 text-xs font-normal text-gray-700 uppercase tracking-wide">Status</label>
                        <div class="flex-1 max-w-xs">
                            <select wire:model="status" class="block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xs font-normal {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : 'bg-gray-50' }} py-1.5 h-8" {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="draft">Draf</option>
                                <option value="ordered">Dipesan</option>
                                @if($purchaseOrder && $purchaseOrder->status == 'cancelled')
                                    <option value="cancelled">Dibatalkan</option>
                                @endif
                                @if($purchaseOrder && $purchaseOrder->status == 'received')
                                    <option value="received" disabled>Diterima</option>
                                @endif
                                @if($purchaseOrder && $purchaseOrder->status == 'partial')
                                    <option value="partial" disabled>Sebagian</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ activeTab: 'items' }">
            <!-- Tab Navigation -->
            <div class="px-3 pt-3 border-b border-gray-200 bg-gray-50/50">
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl w-fit">
                    <!-- Items Tab -->
                    <button type="button" @click="activeTab = 'items'" 
                            :class="activeTab === 'items' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 rounded-lg font-normal transition flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Items</span>
                    </button>
                    
                    <!-- Notes Tab -->
                    <button type="button" @click="activeTab = 'notes'" 
                            :class="activeTab === 'notes' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 rounded-lg font-normal transition flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>Catatan</span>
                    </button>
                </div>

                @if(!$isReadOnly)
                <!-- Product Search Box Below Tabs (Livewire Based) -->
                <div x-show="activeTab === 'items'" class="relative w-full py-3" 
                     x-data="{ 
                         open: false,
                         highlightedIndex: 0,
                         search: @entangle('productSearch').live,
                         selectItem(productId) {
                             if (productId) {
                                 $wire.openModal(null, productId);
                                 this.open = false;
                                 this.highlightedIndex = 0;
                                 this.$refs.searchInput.blur();
                             }
                         }
                     }"
                     @click.outside="open = false; highlightedIndex = 0">
                    <div class="relative max-w-xl">
                        <input type="text" 
                            x-ref="searchInput"
                            x-model="search"
                            @focus="open = true"
                            @input="open = true; highlightedIndex = 0"
                            @keydown.arrow-down.prevent="highlightedIndex = (highlightedIndex + 1) % {{ min(count($searchResults), 10) }}; open = true"
                            @keydown.arrow-up.prevent="highlightedIndex = (highlightedIndex - 1 + {{ min(count($searchResults), 10) }}) % {{ min(count($searchResults), 10) }}; open = true"
                            @keydown.enter.prevent="if(open && search.length > 0) { 
                                $refs['dropdown-item-' + highlightedIndex]?.click(); 
                            } else {
                                open = true;
                            }"
                            @keydown.escape="open = false; $refs.searchInput.blur()"
                            class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2 pl-10 pr-4"
                            placeholder="Cari produk atau scan barcode untuk menambah item...">
                        
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>

                        <!-- Dropdown List -->
                        @if(!empty($productSearch))
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto">
                            <ul class="py-1">
                                @forelse($searchResults as $index => $p)
                                    <li id="dropdown-item-{{ $index }}"
                                        x-ref="dropdown-item-{{ $index }}"
                                        @click="selectItem({{ $p->id }})"
                                        class="px-4 py-2 cursor-pointer flex justify-between items-center group transition-colors border-b border-gray-50 last:border-0 hover:bg-blue-50"
                                        :class="{ 'bg-blue-100': highlightedIndex === {{ $index }} }">
                                        <div>
                                            <div class="text-sm font-medium text-gray-800">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $p->barcode }}</div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-4 py-2 text-sm text-gray-500 text-center">Produk tidak ditemukan.</li>
                                @endforelse
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="py-2"></div>
                @endif
            </div>

                    @error('items') <div class="text-red-500 text-sm mb-2 px-4">{{ $message }}</div> @enderror

            <!-- Items Table Content -->
            <div x-show="activeTab === 'items'" class="">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200">Informasi Produk</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200 w-24">QTY</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200 w-28">Satuan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($items as $index => $item)
                                    @php
                                        $product = $tableProducts->firstWhere('id', $item['product_id']);
                                    @endphp
                                    <tr class="group hover:bg-gray-50/80 transition-all duration-150">
                                        <td class="px-6 py-5">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[13px] font-medium text-gray-900 leading-none group-hover:text-blue-700 transition-colors truncate mb-1.5">{{ $product->name ?? '-' }}</div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] text-gray-400 tracking-widest uppercase bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">{{ $product->barcode ?? 'No Barcode' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-lg group-hover:bg-blue-100 group-hover:text-blue-800 transition-colors">
                                                {{ $item['qty'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $unitName = $product->unit?->name ?? '-';
                                                if (!empty($item['unit_id'])) {
                                                    if ($product->unit_id == $item['unit_id']) {
                                                        $unitName = $product->unit?->name;
                                                    } else {
                                                        $conversion = $product->unitConversions->where('from_unit_id', $item['unit_id'])->first();
                                                        if ($conversion) {
                                                            $unitName = $conversion->fromUnit?->name;
                                                        } else {
                                                             $conversionOriginal = $product->unitConversions->first(function($c) use ($item) {
                                                                return $c->from_unit_id == $item['unit_id'];
                                                             });
                                                             if($conversionOriginal) $unitName = $conversionOriginal->fromUnit?->name;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <span class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50 px-2 py-1 rounded border border-gray-100">{{ $unitName }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if(!$isReadOnly)
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" wire:click="openModal({{ $index }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button type="button" wire:click="removeItem({{ $index }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-3 border border-gray-100">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-900">Belum ada barang</div>
                                                <div class="text-xs text-gray-400 mt-1">Gunakan kotak pencarian di atas untuk menambah barang pesanan</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50/50 border-t border-gray-100">
                                <tr>
                                    <td colspan="4" class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3 text-sm">
                                            <span class="text-gray-500 font-medium tracking-tight">Ringkasan Pesanan:</span>
                                            <span class="text-blue-700 bg-blue-50 px-4 py-1.5 rounded-lg font-medium border border-blue-100 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                {{ count($items) }} Produk Dipesan
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

            <!-- Notes Content -->
            <div x-show="activeTab === 'notes'" class="p-6" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Pesanan</label>
                <textarea wire:model="notes" rows="8" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : '' }}" placeholder="Tulis catatan tambahan di sini..." {{ $isReadOnly ? 'readonly' : '' }}></textarea>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end items-center rounded-b-lg gap-3">
                @if($purchaseOrder && ($status === 'ordered' || $status === 'partial'))
                <a href="{{ route('procurement.goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}"
                    class="px-5 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-bold shadow-md transition text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Proses Penerimaan
                </a>
                @endif

                @if(!$isReadOnly)
                <a href="{{ route('procurement.purchase-orders.index') }}" wire:navigate class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-white shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm w-fit shrink-0">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm w-fit shrink-0">
                    Simpan
                </button>
                @endif
            </div>
        </div>
        
        {{-- Goods Receipts History --}}
        @if($purchaseOrder && in_array($status, ['partial', 'received']))
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-100">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Riwayat Penerimaan Barang
                </h3>
                <p class="text-sm text-gray-600 mt-1">Daftar surat jalan dan penerimaan untuk PO ini</p>
            </div>
            
            <div class="p-6">
                @php
                    $receipts = $purchaseOrder->goodsReceipts()->with('user')->latest()->get();
                @endphp
                
                @if($receipts->count() > 0)
                    <div class="space-y-3">
                        @foreach($receipts as $index => $receipt)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 hover:bg-green-50/30 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 font-bold text-sm">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $receipt->delivery_note_number }}</h4>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($receipt->received_date)->format('d M Y') }} • oleh {{ $receipt->user->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($receipt->notes)
                                    <p class="text-sm text-gray-600 ml-11 mb-2">{{ $receipt->notes }}</p>
                                    @endif
                                    
                                    <div class="ml-11 text-sm">
                                        <span class="text-gray-500">Jumlah Item:</span>
                                        <span class="font-medium text-gray-900">{{ $receipt->items->count() }} produk</span>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Diterima
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="font-medium">Belum ada penerimaan barang</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </form>

    <!-- Modal Item moved outside main form -->
    @if($showModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
            
            <div class="flex items-center justify-center min-h-screen p-4">
                <form wire:submit.prevent="saveItem" class="relative bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all my-8">
                    
                    <!-- Header -->
                    <div class="bg-blue-900 px-5 py-3 flex justify-between items-center">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            {{ $editingItemIndex !== null ? 'Edit Item' : 'Tambah Item' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="text-blue-200 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                        <!-- Product Selection (Searchable) -->
                        <div x-data="{
                            open: false,
                            search: @entangle('modalProductName').live,
                            selectedId: @entangle('modalProductId').live,
                            selectProduct(product) {
                                // Handled by searchResults wire:click
                            }
                        }" class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Produk Terpilih</label>
                            <div class="p-3 bg-blue-50/50 border border-blue-100 rounded-lg flex justify-between items-center group">
                                <div>
                                    <div class="text-sm font-bold text-blue-900" x-text="search || 'Pilih Produk...'"></div>
                                </div>
                                <div class="text-blue-300 group-hover:text-blue-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            @error('modalProductId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Product Details (Read Only) -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                                <label class="block text-[10px] font-semibold text-gray-500 uppercase">Kode</label>
                                <div class="font-medium text-xs text-gray-800">{{ $modalProductCode ?: '-' }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <select wire:model.live="modalUnitId" wire:key="unit-select-{{ $modalProductId }}" class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium">
                                    @foreach($availableUnits as $u)
                                        <option value="{{ $u['id'] }}">
                                            {{ $u['name'] }}
                                            @if($u['factor'] > 1) 
                                                ({{ (float)$u['factor'] }} {{ $availableUnits[0]['name'] ?? 'Base' }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Input Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Qty -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
                                <div class="flex rounded-lg shadow-sm h-9">
                                    <button type="button" wire:click="decrementQty" class="px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg hover:bg-gray-200 font-bold text-gray-600">-</button>
                                    <input type="number" wire:model.live="modalQty" class="flex-1 min-w-0 block w-full px-2 border-gray-300 text-center font-medium focus:ring-blue-500 focus:border-blue-500 text-sm" min="1">
                                    <button type="button" wire:click="incrementQty" class="px-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-200 font-bold text-gray-600">+</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 flex justify-end gap-2 border-t border-gray-200">
                        <button wire:click="closeModal" type="button" class="btn btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-lg btn-primary">
                            Simpan Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
