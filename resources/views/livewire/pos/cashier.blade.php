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

        <!-- MAIN POS CONTAINER (2 Column Split Screen with Fixed Cart) -->
        <div class="absolute inset-0 flex flex-row gap-4 p-4 overflow-hidden">
            
            <!-- LEFT: Keranjang/Order (Fixed & Sticky) -->
            <div class="w-[400px] lg:w-[450px] shrink-0 bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden border border-gray-200 z-10 h-full">
                
                <!-- Cart Header -->
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Keranjang</h2>
                    <p class="text-sm text-gray-500">{{ count($cart) }} Item</p>
                </div>

                <!-- Cart Items (Scrollable Table) -->
                <div class="flex-1 overflow-y-auto bg-white">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-2 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($cart as $id => $item)
                            <tr class="hover:bg-blue-50 group transition-colors" x-data="{ showDiscount: false, showNotes: false }">
                                <td class="px-4 py-3 align-top">
                                    <div class="font-bold text-gray-900 line-clamp-2">{{ $item['name'] }}</div>
                                    
                                    <!-- Price & Discount Display -->
                                    <div class="text-xs text-gray-500 mt-0.5" x-show="!showDiscount">
                                        @ {{ number_format($item['price'], 0, ',', '.') }}
                                        @if($item['discount_amount'] > 0)
                                         <span class="text-amber-600 font-medium ml-1">(-{{ number_format($item['discount_amount'], 0, ',', '.') }})</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Discount Input -->
                                    <div x-show="showDiscount" class="mt-1" style="display: none;">
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs text-amber-600 font-bold">Disc: Rp</span>
                                            <input type="number" 
                                                wire:model.blur="cart.{{ $id }}.discount_amount"
                                                class="w-24 text-xs py-1 px-1 border border-amber-300 rounded focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                                @blur="showDiscount = false"
                                                @keyup.enter="$event.target.blur()"
                                                x-ref="discInput"
                                            >
                                        </div>
                                    </div>

                                    <!-- Notes Display -->
                                    @if($item['notes'] && !$item['discount_amount']) <!-- Hide notes if editing disc to prevent clutter, logic adjustable -->
                                    <div class="text-xs text-blue-600 mt-1 italic flex items-start gap-1" x-show="!showNotes">
                                        <span>📝</span> {{ $item['notes'] }}
                                    </div>
                                    @endif

                                    <!-- Notes Input -->
                                    <div x-show="showNotes" class="mt-2" style="display: none;">
                                        <input type="text" 
                                            wire:model.blur="cart.{{ $id }}.notes"
                                            class="w-full text-xs py-1 px-2 border border-blue-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Catatan..."
                                            @blur="showNotes = false"
                                            @keyup.enter="$event.target.blur()"
                                            x-ref="noteInput"
                                        >
                                    </div>
                                    
                                    <!-- Inline Actions -->
                                    <div class="flex gap-3 mt-2 opacity-60 group-hover:opacity-100 transition-opacity" x-show="!showDiscount && !showNotes">
                                        <button type="button" @click="showDiscount = true; $nextTick(() => $refs.discInput.focus())" class="text-xs font-semibold text-amber-600 hover:text-amber-700 hover:underline">
                                            Diskon
                                        </button>
                                        <button type="button" @click="showNotes = true; $nextTick(() => $refs.noteInput.focus())" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                                            Catatan
                                        </button>
                                        <button type="button" wire:click="removeFromCart({{ $id }})" class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                                <td class="px-2 py-3 align-top">
                                     <div class="flex items-center justify-center border border-gray-200 rounded-lg bg-white overflow-hidden w-24 mx-auto">
                                        <button wire:click="updateQty({{ $id }}, {{ $item['qty'] - 1 }})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">−</button>
                                        <input type="number" wire:model.blur="cart.{{ $id }}.qty" wire:change="calculateTotal" class="w-8 text-center border-0 p-0 text-sm font-bold focus:ring-0 text-gray-900" min="1">
                                        <button wire:click="updateQty({{ $id }}, {{ $item['qty'] + 1 }})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top text-right font-bold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-20 text-center text-gray-400">
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
                            Bayar (F9)
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Katalog Produk (Flexible) -->
            <div class="flex-1 min-w-0 bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden border border-gray-100">
                
                <!-- Search & Filter Header -->
                <div class="p-6 border-b border-gray-100 space-y-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Cari produk / Scan barcode (F2)..." 
                            id="pos-search-input">
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
                <div class="flex-1 p-6 overflow-y-auto">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        @forelse($products as $product)
                        <div wire:click="addToCart({{ $product->id }})" 
                             class="bg-white border-2 border-gray-100 rounded-xl p-4 hover:border-blue-500 hover:shadow-lg cursor-pointer transition-all group relative">
                            
                            <!-- Stock Badge (Top Right Corner) -->
                            <div class="absolute top-2 right-2 z-10">
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

                            <!-- Product Icon -->
                            <div class="h-24 bg-gray-50 rounded-lg mb-3 flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                                <svg class="w-10 h-10 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            
                            <!-- Product Info -->
                            <h4 class="font-bold text-sm text-gray-900 mb-1 line-clamp-2 h-10">{{ $product->name }}</h4>
                            <p class="text-xs text-blue-600 font-bold mb-2">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                            
                            <!-- Stock Info Text -->

                            
                            <!-- Add Button -->
                            <button class="w-full py-2 {{ $product->total_stock > 0 ? 'bg-gray-900 group-hover:bg-blue-600' : 'bg-gray-400 cursor-not-allowed' }} text-white rounded-lg text-sm font-semibold transition-colors">
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

    <!-- PAYMENT MODAL -->
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                
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
                                    placeholder="0" autofocus>
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
                    <div class="p-12 text-center bg-gray-50 rounded-xl">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12v4.01M16 20h1M16 12h1m-4-8h1m-1 4h1m-2 0h1m4-4h1m-1 4h1m-9 8h1"></path></svg>
                        <p class="text-sm font-semibold text-gray-500">Scan QRIS customer</p>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-100 space-y-3">
                    <button wire:click="processPayment" 
                        class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">
                        Cetak Struk Transaksi
                    </button>
                    <button wire:click="$set('showPaymentModal', false)" 
                        class="w-full py-3 text-gray-600 hover:text-gray-900 font-semibold">
                        Kembali
                    </button>
                </div>
            </div>
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
