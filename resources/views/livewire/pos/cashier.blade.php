<div class="h-full bg-gray-50 font-sans">
    <!-- Notification -->
    <div x-data="{ 
        showAlert: false, 
        alertMsg: '',
        showAlertFn(msg) {
            this.alertMsg = msg;
            this.showAlert = true;
            setTimeout(() => this.showAlert = false, 3000);
        }
     }" @cart-error.window="showAlertFn($event.detail.message)">
        
        <div x-show="showAlert" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm">
            <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-sm font-bold" x-text="alertMsg"></p>
            </div>
        </div>

        @if (session()->has('success'))
            <div x-init="showAlertFn('{{ session('success') }}')" class="hidden"></div>
        @endif

    <!-- Zoom Image Modal -->
    <div x-data="{ 
        zoomImage: null,
        openZoom(img) {
            this.zoomImage = img;
        },
        closeZoom() {
            this.zoomImage = null;
        }
    }" 
    @open-zoom.window="openZoom($event.detail)"
    class="h-full">
        
        <!-- Modal Backdrop & Content -->
        <div x-show="zoomImage" 
             style="display: none;"
             class="fixed inset-0 z-[60] overflow-y-auto" 
             role="dialog" aria-modal="true">
            
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
                     @click="closeZoom"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"></div>

                <div class="relative inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-3xl sm:w-full"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <button @click="closeZoom" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 bg-white/50 hover:bg-white rounded-full p-2 transition-all z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="p-2 sm:p-4 bg-white flex justify-center items-center min-h-[300px]">
                        <img :src="zoomImage" alt="Zoomed Product" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN POS CONTAINER (Responsive: Stack on mobile, Side-by-side on desktop) -->
        <div class="absolute inset-x-0 bottom-0 top-16 xl:top-0 flex flex-col md:flex-row gap-4 p-4 overflow-hidden">
            
            <!-- LEFT/TOP: Keranjang/Order (Fixed on desktop, scrollable on mobile) -->
            <div class="w-full md:w-[350px] lg:w-[380px] md:shrink-0 bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden border border-gray-200 z-10 h-1/2 md:h-full">
                
                <!-- Cart Header -->
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 md:w-7 md:h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h2 class="text-lg md:text-2xl font-bold text-gray-900">Keranjang</h2>
                    </div>
                    <p class="text-xs md:text-sm text-gray-500">{{ count($cart) }} Item</p>
                </div>

                <!-- Cart Items (Scrollable Table) -->
                <div class="flex-1 overflow-auto bg-white">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-2 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-2 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($cart as $id => $item)
                            <tr class="hover:bg-blue-50 group transition-colors" x-data="{ openDisc: false, openNote: false }">
                                <td class="px-2 md:px-4 py-2 md:py-3 align-top">
                                    <div class="font-bold text-gray-900 line-clamp-2 leading-tight mb-1 text-xs md:text-sm">{{ $item['name'] }}</div>
                                    
                                    <!-- Price Display -->
                                    <div class="text-[10px] md:text-xs text-gray-500 flex items-center gap-1" x-show="!openDisc && !openNote">
                                        <span>@ {{ number_format($item['price'], 0, ',', '.') }}</span>
                                        @if(($item['discount_percent'] ?? 0) > 0)
                                            <span class="text-amber-600 font-bold">
                                                (-{{ $item['discount_percent'] }}%)
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Inline Edit Forms (Toggled) -->
                                    <div class="space-y-2 mt-1">
                                        <!-- Discount Input (Percent) -->
                                        <div x-show="openDisc" x-transition class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase w-8">Disc%</span>
                                            <input type="number" 
                                                wire:model.live.debounce.500ms="cart.{{ $id }}.discount_percent"
                                                class="w-16 px-2 py-1 text-xs text-right border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="0%"
                                                max="100"
                                                min="0"
                                                x-ref="discInput_{{ $id }}"
                                                @keydown.enter="openDisc = false"
                                                @blur="openDisc = false">
                                        </div>

                                        <!-- Note Input -->
                                        <div x-show="openNote" x-transition>
                                            <input type="text" 
                                                wire:model.blur="cart.{{ $id }}.notes"
                                                class="w-full px-2 py-1 text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                                placeholder="Tulis catatan..."
                                                x-ref="noteInput_{{ $id }}"
                                                @keydown.enter="openNote = false"
                                                @blur="openNote = false">
                                        </div>
                                    </div>

                                    <!-- Badges (Active State Indicators) -->
                                    <div class="flex flex-wrap gap-1 mt-1" x-show="!openDisc && !openNote">
                                        @if(!empty($item['notes']))
                                            <div class="mt-1 max-w-[180px] md:max-w-[220px]">
                                                <div class="text-[10px] text-gray-500 italic truncate flex items-center gap-1 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100" title="{{ $item['notes'] }}">
                                                    <span class="flex-shrink-0">📝</span> 
                                                    <span class="truncate">{{ $item['notes'] }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center align-top">
                                    <div class="w-16 mx-auto">
                                        <input type="number" 
                                                wire:model.live.debounce.300ms="cart.{{ $id }}.qty"
                                                class="w-full px-1 py-1 text-center text-sm font-bold border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                                                min="1">
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right align-top font-bold text-gray-900">
                                    {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </td>
                                <td class="px-2 py-3 text-center align-top">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Discount Toggle -->
                                        <button @click="openDisc = !openDisc; $nextTick(() => $refs.discInput_{{ $id }}.focus())" 
                                                class="p-1.5 rounded-lg transition"
                                                :class="openDisc || {{ ($item['discount_percent'] ?? 0) > 0 ? 'true' : 'false' }} ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
                                                title="Diskon (%)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 7.586V3a1 1 0 011-1zm0 6h.01"></path></svg>
                                        </button>

                                        <!-- Note Toggle -->
                                        <button @click="openNote = !openNote; $nextTick(() => $refs.noteInput_{{ $id }}.focus())" 
                                                class="p-1.5 rounded-lg transition"
                                                :class="openNote || '{{ $item['notes'] }}'.length > 0 ? 'text-blue-600 bg-blue-50 hover:bg-blue-100' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
                                                title="Catatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>



                                        <!-- Delete -->
                                        <button wire:click="removeFromCart({{ $id }})" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-20 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                        <p class="text-sm font-medium">Keranjang Kosong</p>
                                        <p class="text-xs text-gray-400 mt-1">Pilih produk di sebelah kanan</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Calculation Footer -->
                <div class="p-6 bg-gray-50 border-t border-gray-100 space-y-3">
                    <!-- Financial Summary -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($global_discount > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Diskon Global</span>
                            <span class="font-bold">- Rp {{ number_format($global_discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between">
                            <button wire:click="togglePpnMode" class="text-gray-600 hover:text-blue-600 transition-colors">
                                PPN ({{ strtoupper($ppn_mode) }})
                            </button>
                            <span class="font-bold">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>

                        @if($rounding != 0)
                        <div class="flex justify-between text-indigo-600">
                            <span>Pembulatan</span>
                            <span class="font-bold">{{ $rounding > 0 ? '+' : '' }} Rp {{ number_format($rounding, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Total -->
                    <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-gray-600 font-semibold">Total Bayar</span>
                        <span class="text-3xl font-bold text-gray-900">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                    </div>

                    <!-- Global Errors (like Stock Validation) -->
                @error('checkout')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">{{ $message }}</span>
                </div>
                @enderror

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-3">
                        <button wire:click="saveOrder" class="px-4 py-3 bg-gray-800 text-white rounded-xl font-bold hover:bg-gray-900 transition-all">
                            Simpan
                        </button>
                        <button wire:click="openPayment" 
                                class="px-4 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all {{ count($cart) == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ count($cart) == 0 ? 'disabled' : '' }}>
                            Bayar
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT/BOTTOM: Katalog Produk (Flexible, scrollable on mobile) -->
            <div class="flex-1 min-w-0 bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden border border-gray-100 h-1/2 md:h-full">
                
                <!-- Search & Filter Header -->
                <div class="p-3 md:p-6 border-b border-gray-100 space-y-3 md:space-y-4">
                    <!-- Search Bar -->
                    <!-- Search Bar with Dropdown -->
                    <div class="relative" 
                            x-data="{ 
                            open: false, 
                            highlightedIndex: 0,
                            search: @entangle('search').live,
                            selectItem(id) {
                                if (id) {
                                    $wire.addToCart(id);
                                    this.open = false;
                                    this.highlightedIndex = 0;
                                    this.$refs.searchInput.blur();
                                }
                            }
                            }"
                            @click.outside="open = false; highlightedIndex = 0">
                        
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        
                        <input type="text" 
                            x-ref="searchInput"
                            x-model="search"
                            @focus="open = true"
                            @input="open = true; highlightedIndex = 0"
                            @keydown.arrow-down.prevent="highlightedIndex = (highlightedIndex + 1) % {{ min(count($products), 10) }}; open = true"
                            @keydown.arrow-up.prevent="highlightedIndex = (highlightedIndex - 1 + {{ min(count($products), 10) }}) % {{ min(count($products), 10) }}; open = true"
                            @keydown.enter.prevent="if(open && search.length > 0) { 
                                $refs['dropdown-item-' + highlightedIndex]?.click(); 
                            } else {
                                open = true;
                            }"
                            @keydown.escape="open = false; $refs.searchInput.blur()"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Cari produk / Scan barcode (F2)..." 
                            id="pos-search-input"
                            autocomplete="off">

                        <!-- Dropdown Results -->
                        <div x-show="open && search.length > 0" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-h-96 overflow-y-auto">
                            
                            @forelse($products->take(10) as $index => $product)
                                <div id="dropdown-item-{{ $index }}"
                                        x-ref="dropdown-item-{{ $index }}"
                                        @click="selectItem({{ $product->id }})"
                                        class="p-3 cursor-pointer flex justify-between items-center border-b border-gray-50 last:border-0 hover:bg-blue-50 transition-colors"
                                        :class="{ 'bg-blue-100': highlightedIndex === {{ $index }} }">
                                    <div class="flex-1 min-w-0 mr-3">
                                        <div class="font-bold text-gray-900 truncate">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500">Stok: {{ $product->total_stock }}</div>
                                    </div>
                                    <div class="font-bold text-blue-600 text-sm whitespace-nowrap">
                                        Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Tidak ada produk ditemukan
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                        <button wire:click="$set('selectedCategory', 'all')" 
                            class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
                            {{ $selectedCategory === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Semua
                        </button>
                        @foreach($categories as $cat)
                            <button wire:click="$set('selectedCategory', {{ $cat->id }})" 
                                class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
                                {{ $selectedCategory == $cat->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Product Grid (Scrollable) -->
                <div class="flex-1 p-3 md:p-6 overflow-y-auto">
                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                        @forelse($products as $product)
                        <div class="bg-white border-2 border-gray-100 rounded-xl p-4 hover:border-blue-500 hover:shadow-lg transition-all group relative">
                            
                            <!-- Stock Badge (Top Right Corner) -->
                            <div class="absolute top-2 right-2 z-10 pointer-events-none">
                                @if($product->total_stock <= 0)
                                    <span class="text-xs bg-red-600 text-white px-2 py-1 rounded-full font-bold shadow-md">Habis</span>
                                @elseif($product->total_stock <= 5)
                                    <span class="text-xs bg-orange-500 text-white px-2 py-1 rounded-full font-bold shadow-md">{{ $product->total_stock }}</span>
                                @elseif($product->total_stock <= 20)
                                    <span class="text-xs bg-yellow-500 text-white px-2 py-1 rounded-full font-bold shadow-md">{{ $product->total_stock }}</span>
                                @else
                                    <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full font-bold shadow-md">{{ $product->total_stock }}</span>
                                @endif
                            </div>

                            <!-- Product Icon / Image - Click to Zoom -->
                            <div class="h-24 bg-gray-50 rounded-lg mb-3 flex items-center justify-center group-hover:bg-blue-50 transition-colors overflow-hidden cursor-zoom-in"
                                 @if($product->image_path)
                                     @click.stop="openZoom('{{ asset('storage/' . $product->image_path) }}')"
                                 @endif>
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-10 h-10 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                @endif
                            </div>
                            
                            <!-- Product Info - Click to Add -->
                            <div class="cursor-pointer" wire:click="addToCart({{ $product->id }})">
                                <h4 class="font-bold text-sm text-gray-900 mb-1 line-clamp-2 h-10 overflow-hidden text-ellipsis hover:text-blue-600">{{ $product->name }}</h4>
                                <p class="text-xs text-blue-600 font-bold mb-2">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                            </div>
                            
                            <!-- Add Button - Click to Add -->
                            <button wire:click="addToCart({{ $product->id }})" class="w-full py-2 {{ $product->total_stock > 0 ? 'bg-gray-900 group-hover:bg-blue-600' : 'bg-gray-400 cursor-not-allowed' }} text-white rounded-lg text-sm font-semibold transition-colors">
                                + Tambah
                            </button>
                        </div>
                        @empty
                        <div class="col-span-full py-20 flex flex-col items-center text-gray-400">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <p class="text-sm font-semibold">Produk tidak ditemukan</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- PAYMENT MODAL -->
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <form wire:submit.prevent="processPayment" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Proses Pembayaran</h3>
                        <p class="text-sm text-gray-500 mt-1">Selesaikan transaksi</p>
                    </div>
                    <button wire:click="$set('showPaymentModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    <!-- Total Display -->
                    <div class="text-center p-6 bg-gray-900 rounded-xl">
                        <p class="text-sm text-gray-400 mb-2">Total Akhir</p>
                        <p class="text-4xl font-bold text-white">Rp {{ number_format($grand_total, 0, ',', '.') }}</p>
                    </div>

                    @error('checkout')
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ $message }}</span>
                    </div>
                    @enderror

                    <!-- Payment Method -->
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="$set('payment_method', 'cash')" 
                            class="p-4 border-2 rounded-xl font-semibold transition-all {{ $payment_method == 'cash' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-200 hover:border-gray-300' }}">
                            💵 Tunai
                        </button>
                        <button wire:click="$set('payment_method', 'qris')" 
                            class="p-4 border-2 rounded-xl font-semibold transition-all {{ $payment_method == 'qris' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-200 hover:border-gray-300' }}">
                            📱 QRIS
                        </button>
                    </div>

                    <!-- Cash Input -->
                    @if($payment_method == 'cash')
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Uang Tunai</label>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-gray-400 mr-2">Rp</span>
                                <input type="number" wire:model.live="cash_amount" 
                                    class="flex-1 text-3xl font-bold border-0 bg-transparent p-0 focus:ring-0" 
                                    placeholder="1000" autofocus>
                            </div>
                            @error('cash_amount') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-between items-center p-4 bg-green-50 rounded-xl">
                            <span class="text-sm font-semibold text-gray-700">Kembalian</span>
                            <span class="text-2xl font-bold {{ $change_amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($change_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @else
                    <!-- QRIS Payment Section -->
                    @php
                        $qrisPath = \App\Models\Setting::get('store_qris_path');
                    @endphp
                    
                    @if($qrisPath)
                        <!-- QRIS Code Display -->
                        <div class="space-y-4">
                            <!-- Total Amount (Prominent) -->
                            <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">Total Pembayaran</p>
                                <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($grand_total, 0, ',', '.') }}</p>
                            </div>

                            <!-- QR Code Image -->
                            <div class="bg-white p-6 rounded-xl border-2 border-gray-200 flex justify-center">
                                <img src="{{ asset('storage/' . $qrisPath) }}" 
                                     alt="QRIS Code" 
                                     class="w-64 h-64 object-contain">
                            </div>

                            <!-- Instructions -->
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="text-sm text-blue-900">
                                        <p class="font-bold mb-2">Cara Pembayaran:</p>
                                        <ol class="list-decimal list-inside space-y-1 text-xs">
                                            <li>Minta customer scan QR Code dengan aplikasi banking mereka</li>
                                            <li>Customer konfirmasi pembayaran di aplikasi</li>
                                            <li>Verifikasi notifikasi pembayaran masuk</li>
                                            <li>Klik "Konfirmasi Pembayaran" di bawah</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- QRIS Not Configured Warning -->
                        <div class="p-8 text-center bg-yellow-50 rounded-xl border-2 border-yellow-200">
                            <svg class="w-16 h-16 mx-auto text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-lg font-bold text-yellow-800 mb-2">QRIS Belum Dikonfigurasi</p>
                            <p class="text-sm text-yellow-700 mb-4">Upload QR Code QRIS toko Anda terlebih dahulu di halaman Pengaturan.</p>
                            <a href="{{ route('settings.store') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all font-semibold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Ke Pengaturan Toko
                            </a>
                        </div>
                    @endif
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-100 space-y-3">
                    <button type="submit" 
                        class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all"
                        @if($payment_method == 'qris' && !\App\Models\Setting::get('store_qris_path')) disabled @endif>
                        {{ $payment_method == 'qris' ? 'Konfirmasi Pembayaran QRIS' : 'Cetak Struk Transaksi' }}
                    </button>
                    <button wire:click="$set('showPaymentModal', false)" 
                        class="w-full py-3 text-gray-600 hover:text-gray-900 font-semibold">
                        Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Keyboard Shortcuts -->
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'F2') {
                event.preventDefault();
                document.getElementById('pos-search-input').focus();
            }
            if (event.key === 'F9') {
                event.preventDefault();
                @this.openPayment();
            }
            if (event.key === 'Enter' && @this.showPaymentModal) {
                event.preventDefault();
                @this.processPayment();
            }
        });
    </script>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</div>
