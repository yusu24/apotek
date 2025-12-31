<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $purchaseOrder ? 'Edit Pesanan' : 'Buat Pesanan Baru' }}
        </h2>
        <a href="{{ route('procurement.purchase-orders.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi PO</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. PO <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="po_number" class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100" readonly placeholder="Auto Generated">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="date" class="mt-1 block w-full rounded-lg border-gray-300 {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier <span class="text-red-500">*</span></label>
                            <select wire:model="supplier_id" class="mt-1 block w-full rounded-lg border-gray-300 {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : '' }}" {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full rounded-lg border-gray-300 {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : '' }}" {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="draft">Draf</option>
                                <option value="ordered">Dipesan</option>
                                <option value="cancelled">Dibatalkan</option>
                                @if($purchaseOrder && $purchaseOrder->status == 'received')
                                    <option value="received" disabled>Diterima</option>
                                @endif
                                @if($purchaseOrder && $purchaseOrder->status == 'partial')
                                    <option value="partial" disabled>Sebagian</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 {{ $isReadOnly ? 'bg-gray-100 pointer-events-none' : '' }}" placeholder="Catatan tambahan..." {{ $isReadOnly ? 'readonly' : '' }}></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Item Pesanan</h3>
                        @if(!$isReadOnly)
                        <button type="button" wire:click="openModal" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
                            + Tambah Barang
                        </button>
                        @endif
                    </div>

                    @error('items') <div class="text-red-500 text-sm mb-2">{{ $message }}</div> @enderror

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Harga Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Subtotal</th>
                                    <th class="px-4 py-3 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($items as $index => $item)
                                    @php
                                        $product = $products->firstWhere('id', $item['product_id']);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->barcode ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="text-sm text-gray-900">{{ $item['qty'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
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
                                            <div class="text-xs text-gray-500">{{ $unitName }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-900">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            @if(!empty($item['has_ppn']))
                                                <div class="text-[10px] text-gray-500 font-normal">+PPN 12%</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if(!$isReadOnly)
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" wire:click="openModal({{ $index }})" class="text-blue-600 hover:text-blue-800 p-1 hover:bg-blue-50 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-800 p-1 hover:bg-red-50 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                            Belum ada barang yang ditambahkan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-gray-700">Total Pesanan:</td>
                                    <td class="px-4 py-3 text-right text-blue-800 text-lg">
                                        Rp {{ number_format(collect($items)->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    @if(!$isReadOnly)
                    <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold shadow-lg">
                        Simpan Pesanan
                    </button>
                    @endif

                    @if($purchaseOrder && ($status === 'ordered' || $status === 'partial'))
                    <a href="{{ route('procurement.goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}"
                       class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-bold shadow-lg flex items-center gap-2 transition duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Proses Penerimaan
                    </a>
                    @endif
 
                    @if($purchaseOrder && $status === 'partial')
                    <button type="button" wire:click="markAsDone" wire:confirm="Yakin ingin menyelesaikan PO ini? Item yang belum diterima akan dianggap batal."
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Selesaikan PO
                    </button>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Goods Receipts History --}}
        @if($purchaseOrder && in_array($status, ['partial', 'received']))
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                                            <h4 class="font-bold text-gray-900">{{ $receipt->delivery_note_number }}</h4>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($receipt->received_date)->format('d M Y') }} • oleh {{ $receipt->user->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($receipt->notes)
                                    <p class="text-sm text-gray-600 ml-11 mb-2">{{ $receipt->notes }}</p>
                                    @endif
                                    
                                    <div class="ml-11 text-sm">
                                        <span class="text-gray-500">Jumlah Item:</span>
                                        <span class="font-semibold text-gray-900">{{ $receipt->items->count() }} produk</span>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
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
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-cloak>
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
                            search: '',
                            selectedId: @entangle('modalProductId').live,
                            products: {{ $products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'barcode' => $p->barcode])->toJson() }},
                            get filteredProducts() {
                                if (this.search === '') return this.products;
                                return this.products.filter(p => 
                                    p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                    (p.barcode && p.barcode.toLowerCase().includes(this.search.toLowerCase()))
                                );
                            },
                            selectProduct(product) {
                                this.selectedId = product.id;
                                this.search = product.name;
                                this.open = false;
                            },
                            init() {
                                this.$watch('selectedId', (value) => {
                                    if(!value) { this.search = ''; return; }
                                    const product = this.products.find(p => p.id == value);
                                    if (product) this.search = product.name;
                                });
                                // Initial State
                                if(this.selectedId) {
                                    const product = this.products.find(p => p.id == this.selectedId);
                                    if (product) this.search = product.name;
                                }
                            }
                        }" class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                            
                            <div class="relative">
                                <input type="text" 
                                    x-model="search"
                                    @focus="open = true"
                                    @click.away="open = false"
                                    @keydown.escape="open = false"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2 pl-4 pr-10"
                                    placeholder="Ketik nama produk atau scan barcode..."
                                >
                                
                                <!-- Search Icon / Loading -->
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>

                                <!-- Dropdown List -->
                                <div x-show="open && filteredProducts.length > 0" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto"
                                     style="display: none;">
                                    <ul class="py-1">
                                        <template x-for="product in filteredProducts" :key="product.id">
                                            <li @click="selectProduct(product)" 
                                                class="px-4 py-2 hover:bg-blue-50 cursor-pointer flex justify-between items-center group transition-colors">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-800" x-text="product.name"></div>
                                                    <div class="text-xs text-gray-500" x-text="product.barcode"></div>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <!-- No Results -->
                                <div x-show="open && filteredProducts.length === 0" 
                                     class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 p-4 text-center text-gray-500 text-sm"
                                     style="display: none;">
                                    Produk tidak ditemukan.
                                </div>
                            </div>
                            @error('modalProductId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Product Details (Read Only) -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                                <label class="block text-[10px] font-semibold text-gray-500 uppercase">Kode</label>
                                <div class="font-semibold text-xs text-gray-800">{{ $modalProductCode ?: '-' }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <select wire:model.live="modalUnitId" wire:key="unit-select-{{ $modalProductId }}" class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold">
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
                                    <input type="number" wire:model.live="modalQty" class="flex-1 min-w-0 block w-full px-2 border-gray-300 text-center font-bold focus:ring-blue-500 focus:border-blue-500 text-sm" min="1">
                                    <button type="button" wire:click="incrementQty" class="px-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-200 font-bold text-gray-600">+</button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Harga Beli Satuan <span class="text-red-500">*</span></label>
                                <div class="relative rounded-lg shadow-sm h-9" x-data="money($wire.entangle('modalPrice').live)">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Rp</span>
                                    </div>
                                    <input type="text" x-bind="input" class="block w-full pl-8 pr-3 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-right font-bold text-sm" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- Subtotal Display -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between pt-2">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="modalPpn" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Kenakan PPN 12%</span>
                                </label>
                            </div>

                            <div class="bg-blue-50 p-3 rounded-lg flex items-center justify-between border border-blue-100">
                                <span class="text-blue-800 text-sm font-semibold">Subtotal</span>
                                <span class="text-lg font-bold text-blue-900">Rp {{ number_format($modalSubtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 flex justify-end gap-2 border-t border-gray-200">
                        <button wire:click="closeModal" type="button" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 font-medium transition text-gray-700">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow transition">
                            Simpan Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
