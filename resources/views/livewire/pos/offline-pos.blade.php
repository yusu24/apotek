<!-- OFFLINE POS ENGINE & PWA CONTROLLER -->
<div x-data="offlinePosApp()" x-init="initOfflinePos()" class="relative z-[45]">

    <!-- TOP FLOATING STATUS BAR (Always visible in Cashier) -->
    <div class="fixed top-2 right-3 z-[55] flex items-center gap-2 flex-wrap justify-end pointer-events-auto">
        
        <!-- Pending Offline Queue Badge -->
        <template x-if="offlineQueue.length > 0">
            <div class="flex items-center gap-1.5 bg-amber-500 text-white px-3 py-1.5 rounded-full shadow-lg text-xs font-bold animate-pulse">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span x-text="offlineQueue.length + ' Transaksi Offline'"></span>
                <button @click="showQueueModal = true" class="underline ml-1 hover:text-amber-100">Lihat</button>
                <button x-show="isOnline && !isSyncing" @click="syncQueue()" class="bg-white text-amber-700 px-2 py-0.5 rounded-full text-[11px] font-extrabold hover:bg-amber-50 ml-1 shadow-sm">
                    Sinkronkan
                </button>
                <span x-show="isSyncing" class="text-[11px] italic ml-1">Menyinkronkan...</span>
            </div>
        </template>

        <!-- Connection Status & Mode Toggle Pill -->
        <div class="flex items-center gap-2 bg-white/95 backdrop-blur border border-gray-200 px-3 py-1.5 rounded-full shadow-md text-xs">
            <span class="w-2.5 h-2.5 rounded-full" :class="isOnline ? 'bg-emerald-500' : 'bg-red-500 animate-ping'"></span>
            <span class="font-bold text-gray-700" x-text="isOnline ? 'Online' : 'Internet Putus'"></span>
            <span class="text-gray-300">|</span>
            <span class="text-[11px] text-gray-500 hidden sm:inline" x-text="catalog.products.length + ' Obat Tersimpan'"></span>
            <button @click="toggleOfflineMode()" 
                    class="px-2.5 py-0.5 rounded-full font-bold transition text-[11px]"
                    :class="isOfflineMode ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                <span x-text="isOfflineMode ? '⚡ Mode Offline Aktif' : 'Mode Offline'"></span>
            </button>
        </div>
    </div>

    <!-- TOAST NOTIFICATION FOR OFFLINE POS -->
    <div x-show="toast.show" x-cloak
         x-transition
         class="fixed top-14 left-1/2 -translate-x-1/2 z-[120] w-11/12 max-w-md pointer-events-none">
        <div :class="toast.type === 'success' ? 'bg-emerald-600' : (toast.type === 'warning' ? 'bg-amber-600' : 'bg-red-600')"
             class="text-white px-5 py-3.5 rounded-xl shadow-2xl flex items-center gap-3">
            <p class="text-xs sm:text-sm font-bold flex-1" x-text="toast.message"></p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- FULL SCREEN OFFLINE POS INTERFACE (Active when internet drops or toggled) -->
    <!-- ========================================================================= -->
    <div x-show="isOfflineMode" x-cloak class="fixed inset-0 z-[50] bg-gray-100 flex flex-col overflow-hidden">
        
        <!-- OFFLINE HEADER BAR -->
        <div class="bg-gradient-to-r from-red-700 via-red-600 to-amber-600 text-white px-4 py-2.5 shadow-md flex items-center justify-between gap-2 shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="bg-white/20 p-1.5 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                <div class="truncate">
                    <div class="flex items-center gap-2">
                        <h1 class="text-sm sm:text-base font-extrabold tracking-tight truncate">KASIR MODE OFFLINE</h1>
                        <span class="bg-white/20 text-[10px] px-2 py-0.5 rounded-full font-semibold hidden sm:inline" x-text="catalog.settings.store_name || 'Apotek'"></span>
                    </div>
                    <p class="text-[11px] text-red-100 truncate">Data tersimpan aman di memori HP/PC walaupun di-refresh atau browser ditutup.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button @click="showQueueModal = true" class="bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                    <span>Antrean</span>
                    <span class="bg-amber-400 text-gray-900 px-1.5 py-0.2 rounded-full text-[11px] font-extrabold" x-text="offlineQueue.length"></span>
                </button>
                <button x-show="isOnline" @click="isOfflineMode = false" class="bg-white text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg text-xs font-extrabold shadow-sm transition">
                    Kembali Online
                </button>
            </div>
        </div>

        <!-- MAIN OFFLINE POS BODY (Side-by-side on Desktop, Stacked on HP) -->
        <div class="flex-1 flex flex-col md:flex-row gap-3 p-3 overflow-hidden pb-20 md:pb-3">
            
            <!-- LEFT SIDEBAR: KERANJANG BELANJA (Desktop Fixed, Mobile Slide-Up Modal) -->
            <div :class="mobileCartDrawer ? 'fixed inset-0 z-[70] flex bg-black/50 p-2' : 'hidden md:flex md:w-[360px] lg:w-[400px] shrink-0'"
                 class="flex-col h-full">
                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 flex flex-col w-full h-full overflow-hidden">
                    <!-- Cart Header -->
                    <div class="px-4 py-3 bg-blue-950 text-white flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-bold text-sm">Keranjang Offline (<span x-text="cartItems.length"></span>)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button x-show="cartItems.length > 0" @click="clearCart()" class="text-xs text-red-300 hover:text-red-100 font-semibold">Kosongkan</button>
                            <button @click="mobileCartDrawer = false" class="md:hidden text-gray-300 hover:text-white p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Cart Items List -->
                    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 p-2">
                        <template x-if="cartItems.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 py-12">
                                <svg class="w-12 h-12 mb-2 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <p class="text-sm font-semibold">Keranjang Masih Kosong</p>
                                <p class="text-xs">Klik produk untuk menambahkan</p>
                            </div>
                        </template>

                        <template x-for="(item, idx) in cartItems" :key="item.id">
                            <div class="p-2.5 hover:bg-blue-50/40 rounded-xl transition">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-gray-900 leading-snug" x-text="item.name"></p>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            <!-- Unit Selector -->
                                            <select @change="changeUnit(idx, $event.target.value)"
                                                    class="text-[11px] border-gray-300 rounded py-0.5 pl-1.5 pr-5 bg-gray-50 font-semibold text-blue-700 focus:ring-blue-500">
                                                <template x-for="u in Object.values(item.available_units || {})" :key="u.id">
                                                    <option :value="u.id" :selected="u.id == item.unit_id" x-text="u.name + ' (@ ' + formatNumber(u.price) + ')'"></option>
                                                </template>
                                            </select>
                                            <!-- Item Discount (%) -->
                                            <div class="flex items-center gap-1">
                                                <span class="text-[10px] text-gray-400">Disc%:</span>
                                                <input type="number" min="0" max="100"
                                                       x-model.number="item.discount_percent"
                                                       @input="recalculate()"
                                                       class="w-12 text-[11px] border-gray-200 rounded px-1 py-0.5 text-center"
                                                       placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="removeItem(idx)" class="text-red-400 hover:text-red-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-gray-100">
                                    <!-- Qty Stepper -->
                                    <div class="flex items-center gap-1">
                                        <button @click="updateQty(idx, item.qty - 1)" class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center text-xs">-</button>
                                        <input type="number" min="1" x-model.number="item.qty" @input="recalculate()"
                                               class="w-12 text-center text-xs font-bold border-gray-200 rounded py-0.5">
                                        <button @click="updateQty(idx, item.qty + 1)" class="w-6 h-6 rounded bg-blue-100 hover:bg-blue-200 font-bold text-blue-700 flex items-center justify-center text-xs">+</button>
                                    </div>
                                    <span class="text-xs font-extrabold text-gray-900" x-text="'Rp ' + formatNumber(item.subtotal)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Cart Summary & Checkout Button -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2 shrink-0">
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-bold" x-text="'Rp ' + formatNumber(summary.subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-600">
                            <span>Diskon Global (Rp)</span>
                            <input type="number" min="0" x-model.number="globalDiscount" @input="recalculate()"
                                   class="w-28 text-right text-xs border-gray-300 rounded px-2 py-1 font-bold" placeholder="0">
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-600">
                            <button @click="cyclePpnMode()" class="text-blue-600 hover:underline font-semibold">
                                PPN (<span x-text="ppnMode.toUpperCase()"></span>)
                            </button>
                            <span class="font-bold" x-text="'Rp ' + formatNumber(summary.tax)"></span>
                        </div>
                        <div class="pt-2 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Total Bayar</span>
                            <span class="text-xl font-extrabold text-blue-900" x-text="'Rp ' + formatNumber(summary.grandTotal)"></span>
                        </div>

                        <button @click="openPaymentModal()"
                                :disabled="cartItems.length === 0"
                                :class="cartItems.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 shadow-lg'"
                                class="w-full py-3 rounded-xl text-white font-extrabold text-sm transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>PROSES PEMBAYARAN</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT MAIN AREA: SEARCH & PRODUCT GRID -->
            <div class="flex-1 flex flex-col bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- Search & Category Bar -->
                <div class="p-3 sm:p-4 border-b border-gray-100 space-y-2.5 shrink-0">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" x-model="searchQuery"
                               @keydown.enter.prevent="addFirstFilteredProduct()"
                               placeholder="Cari nama obat atau scan barcode (Mode Offline)..."
                               class="w-full pl-11 pr-10 py-2.5 rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium">
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Category Filter Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide pb-1">
                        <button @click="selectedCat = 'all'"
                                :class="selectedCat === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                class="px-3 py-1 rounded-full text-xs font-bold shrink-0 transition">
                            Semua
                        </button>
                        <template x-for="cat in catalog.categories" :key="cat.id">
                            <button @click="selectedCat = cat.id"
                                    :class="selectedCat == cat.id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                    class="px-3 py-1 rounded-full text-xs font-bold shrink-0 transition"
                                    x-text="cat.name">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto p-3 sm:p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2.5 sm:gap-3">
                        <template x-for="product in filteredProducts()" :key="product.id">
                            <div @click="addToCart(product)"
                                 class="group bg-white border border-gray-200 hover:border-blue-500 rounded-xl p-3 cursor-pointer shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden">
                                <div>
                                    <div class="flex justify-between items-start gap-1 mb-1.5">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                              :class="product.total_stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                              x-text="'Stok: ' + product.total_stock + ' ' + product.unit_name">
                                        </span>
                                    </div>
                                    <h3 class="text-xs sm:text-sm font-bold text-gray-800 group-hover:text-blue-600 line-clamp-2 leading-snug" x-text="product.name"></h3>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100 flex items-baseline justify-between">
                                    <span class="text-xs sm:text-sm font-extrabold text-blue-700" x-text="'Rp ' + formatNumber(product.sell_price)"></span>
                                    <span class="text-[10px] text-gray-400" x-text="'/' + product.unit_name"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- MOBILE BOTTOM FLOATING CART BAR (HP Only) -->
        <div class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 px-4 py-2.5 shadow-2xl flex items-center justify-between gap-3 z-[60]">
            <div @click="mobileCartDrawer = true" class="flex items-center gap-2.5 cursor-pointer">
                <div class="relative bg-blue-50 text-blue-600 p-2 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center" x-text="cartItems.length"></span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-medium">Total Tagihan</p>
                    <p class="text-base font-extrabold text-gray-900" x-text="'Rp ' + formatNumber(summary.grandTotal)"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="mobileCartDrawer = true" class="px-3 py-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold">
                    Keranjang
                </button>
                <button @click="openPaymentModal()"
                        :disabled="cartItems.length === 0"
                        :class="cartItems.length === 0 ? 'bg-gray-300' : 'bg-blue-600'"
                        class="px-4 py-2.5 rounded-xl text-white text-xs font-extrabold shadow-md">
                    Bayar
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- OFFLINE PAYMENT MODAL                                                     -->
    <!-- ========================================================================= -->
    <div x-show="showPayModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 my-auto">
            <div class="px-5 py-4 bg-blue-950 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-base font-extrabold">Pembayaran (Mode Offline)</h3>
                    <p class="text-[11px] text-blue-200">Transaksi disimpan di perangkat & siap cetak struk</p>
                </div>
                <button @click="showPayModal = false" class="text-gray-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                <!-- Total Display -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 text-center">
                    <span class="text-xs font-bold text-blue-600 uppercase">Total Harus Dibayar</span>
                    <p class="text-2xl font-extrabold text-blue-950 mt-0.5" x-text="'Rp ' + formatNumber(summary.grandTotal)"></p>
                </div>

                <!-- Payment Method Selector -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="m in [
                            {id: 'cash', label: 'Tunai'},
                            {id: 'tempo', label: 'Tempo'},
                            {id: 'qris', label: 'QRIS'},
                            {id: 'transfer', label: 'Transfer'}
                        ]" :key="m.id">
                            <button type="button"
                                    @click="paymentMethod = m.id; recalculate()"
                                    :class="paymentMethod === m.id ? 'bg-blue-600 text-white ring-2 ring-blue-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="py-2 px-2 rounded-xl text-xs font-bold transition text-center"
                                    x-text="m.label">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Customer Selection for Tempo -->
                <div x-show="paymentMethod === 'tempo'" class="space-y-2.5 bg-amber-50/70 p-3 rounded-xl border border-amber-200">
                    <label class="block text-xs font-bold text-amber-900">Pilih Pelanggan (Wajib untuk Tempo)</label>
                    <select x-model="selectedCustomerId" class="w-full text-xs rounded-lg border-gray-300 py-2">
                        <option value="">-- Pilih Pelanggan Terdaftar --</option>
                        <template x-for="c in catalog.customers" :key="c.id">
                            <option :value="c.id" x-text="c.name + (c.phone ? ' (' + c.phone + ')' : '')"></option>
                        </template>
                    </select>

                    <div x-show="!selectedCustomerId" class="space-y-2 pt-1 border-t border-amber-200">
                        <p class="text-[11px] font-semibold text-amber-800">Atau ketik nama pelanggan baru:</p>
                        <input type="text" x-model="newCustomerName" placeholder="Nama Pelanggan Baru *" class="w-full text-xs rounded-lg border-gray-300 py-1.5 px-2.5">
                        <input type="text" x-model="newCustomerPhone" placeholder="No. HP / WhatsApp (Opsional)" class="w-full text-xs rounded-lg border-gray-300 py-1.5 px-2.5">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs font-semibold text-amber-900">Jatuh Tempo (Hari):</span>
                        <input type="number" x-model.number="tempoDuration" min="1" class="w-20 text-center text-xs rounded-lg border-gray-300 py-1">
                    </div>
                </div>

                <!-- Cash Received Input -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1" x-text="paymentMethod === 'tempo' ? 'Uang Muka / DP (Opsional)' : 'Nominal Uang Diterima'"></label>
                    <input type="number" x-model.number="cashAmount" @input="recalculate()"
                           placeholder="Masukkan jumlah uang..."
                           class="w-full text-base font-extrabold rounded-xl border-gray-300 py-2.5 px-3.5 focus:ring-blue-500 focus:border-blue-500">
                    
                    <!-- Quick Cash Suggestions -->
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" @click="cashAmount = summary.grandTotal; recalculate()"
                                class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">
                            Uang Pas
                        </button>
                        <button type="button" @click="cashAmount = 50000; recalculate()"
                                class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold">
                            50.000
                        </button>
                        <button type="button" @click="cashAmount = 100000; recalculate()"
                                class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold">
                            100.000
                        </button>
                    </div>
                </div>

                <!-- Change / Remaining Balance Display -->
                <div class="p-3 rounded-xl flex justify-between items-center"
                     :class="paymentMethod === 'tempo' ? 'bg-amber-50 text-amber-900' : 'bg-emerald-50 text-emerald-900'">
                    <span class="text-xs font-bold" x-text="paymentMethod === 'tempo' ? 'Sisa Piutang:' : 'Kembalian:'"></span>
                    <span class="text-lg font-extrabold"
                          x-text="paymentMethod === 'tempo' ? ('Rp ' + formatNumber(Math.max(0, summary.grandTotal - (cashAmount || 0)))) : ('Rp ' + formatNumber(Math.max(0, (cashAmount || 0) - summary.grandTotal)))">
                    </span>
                </div>

                <!-- Notes -->
                <div>
                    <input type="text" x-model="orderNotes" placeholder="Catatan transaksi (opsional)..."
                           class="w-full text-xs rounded-xl border-gray-300 py-2 px-3">
                </div>
            </div>

            <div class="px-5 py-3.5 bg-gray-50 border-t border-gray-200 flex gap-2">
                <button type="button" @click="showPayModal = false" class="flex-1 py-2.5 rounded-xl bg-gray-200 text-gray-700 font-bold text-xs">
                    Batal
                </button>
                <button type="button" @click="submitOfflineSale()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md">
                    Simpan & Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- OFFLINE RECEIPT PRINT MODAL                                               -->
    <!-- ========================================================================= -->
    <div x-show="completedOfflineTx" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col max-h-[85vh]">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Struk Transaksi (Offline)</h3>
                    <p class="text-[11px] text-emerald-600 font-semibold">✓ Tersimpan di Perangkat</p>
                </div>
                <button @click="completedOfflineTx = null" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Printable Receipt Content -->
            <div id="offline-receipt-print-area" class="p-4 overflow-y-auto font-mono text-xs text-gray-900 space-y-2 bg-white">
                <template x-if="completedOfflineTx">
                    <div>
                        <div class="text-center border-b border-dashed border-gray-400 pb-2 mb-2">
                            <p class="font-bold text-sm uppercase" x-text="catalog.settings.store_name || 'APOTEK'"></p>
                            <p class="text-[11px] text-gray-600" x-text="catalog.settings.store_address || ''"></p>
                            <p class="text-[11px] text-gray-600" x-text="catalog.settings.store_phone || ''"></p>
                        </div>
                        <div class="text-[11px] space-y-0.5 border-b border-dashed border-gray-400 pb-2 mb-2">
                            <div class="flex justify-between"><span>No:</span><span x-text="completedOfflineTx.invoice_no"></span></div>
                            <div class="flex justify-between"><span>Tgl:</span><span x-text="completedOfflineTx.display_date"></span></div>
                            <div class="flex justify-between"><span>Kasir:</span><span x-text="catalog.settings.cashier_name || 'Kasir'"></span></div>
                            <div class="flex justify-between"><span>Metode:</span><span class="uppercase" x-text="completedOfflineTx.payment_method"></span></div>
                        </div>
                        <div class="space-y-1.5 border-b border-dashed border-gray-400 pb-2 mb-2">
                            <template x-for="item in completedOfflineTx.items" :key="item.id">
                                <div>
                                    <p class="font-semibold" x-text="item.name"></p>
                                    <div class="flex justify-between text-[11px]">
                                        <span x-text="item.qty + ' ' + item.unit_name + ' x ' + formatNumber(item.price)"></span>
                                        <span x-text="formatNumber(item.subtotal)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between"><span>Subtotal</span><span x-text="'Rp ' + formatNumber(completedOfflineTx.subtotal)"></span></div>
                            <template x-if="completedOfflineTx.global_discount > 0">
                                <div class="flex justify-between"><span>Diskon</span><span x-text="'-Rp ' + formatNumber(completedOfflineTx.global_discount)"></span></div>
                            </template>
                            <template x-if="completedOfflineTx.tax > 0">
                                <div class="flex justify-between"><span>PPN</span><span x-text="'Rp ' + formatNumber(completedOfflineTx.tax)"></span></div>
                            </template>
                            <div class="flex justify-between font-bold text-sm pt-1 border-t border-dashed border-gray-400">
                                <span>TOTAL</span>
                                <span x-text="'Rp ' + formatNumber(completedOfflineTx.grand_total)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span x-text="completedOfflineTx.payment_method === 'tempo' ? 'DP / Tunai' : 'Bayar'"></span>
                                <span x-text="'Rp ' + formatNumber(completedOfflineTx.cash_amount)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span x-text="completedOfflineTx.payment_method === 'tempo' ? 'Sisa Piutang' : 'Kembali'"></span>
                                <span x-text="'Rp ' + formatNumber(completedOfflineTx.change_amount)"></span>
                            </div>
                        </div>
                        <div class="text-center text-[10px] text-gray-500 pt-3 mt-2 border-t border-dashed border-gray-300">
                            <p>Terima kasih atas kunjungan Anda</p>
                            <p>Semoga lekas sembuh</p>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-3 bg-gray-50 border-t border-gray-200 flex gap-2">
                <button @click="printOfflineReceipt()" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                    <span>Cetak Struk</span>
                </button>
                <button @click="completedOfflineTx = null" class="flex-1 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- OFFLINE QUEUE LIST MODAL                                                  -->
    <!-- ========================================================================= -->
    <div x-show="showQueueModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[85vh]">
            <div class="px-5 py-4 bg-blue-950 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-base font-extrabold">Antrean Transaksi Offline (<span x-text="offlineQueue.length"></span>)</h3>
                    <p class="text-[11px] text-blue-200">Data ini aman meskipun browser di-refresh atau ditutup</p>
                </div>
                <button @click="showQueueModal = false" class="text-gray-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 divide-y divide-gray-100">
                <template x-if="offlineQueue.length === 0">
                    <div class="py-12 text-center text-gray-400">
                        <p class="text-sm font-bold">Semua transaksi sudah tersinkronisasi ke VPS!</p>
                    </div>
                </template>

                <template x-for="tx in offlineQueue" :key="tx.offline_id">
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-blue-700" x-text="tx.invoice_no"></span>
                                <span class="text-[10px] uppercase px-2 py-0.5 rounded-full bg-gray-100 font-bold text-gray-700" x-text="tx.payment_method"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="tx.display_date + ' • ' + tx.items.length + ' item'"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-gray-900" x-text="'Rp ' + formatNumber(tx.grand_total)"></span>
                            <button @click="completedOfflineTx = tx" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-bold text-gray-700">
                                Struk
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center gap-2">
                <button @click="refreshCatalog()" x-show="isOnline" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold">
                    Perbarui Cache Obat
                </button>
                <div class="flex gap-2 ml-auto">
                    <button x-show="isOnline && offlineQueue.length > 0"
                            @click="syncQueue()"
                            :disabled="isSyncing"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow">
                        <span x-text="isSyncing ? 'Menyinkronkan...' : 'Sinkronkan ke VPS Sekarang'"></span>
                    </button>
                    <button @click="showQueueModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function offlinePosApp() {
            const STORAGE_CATALOG_KEY = 'apotek_pos_catalog_v1';
            const STORAGE_CART_KEY    = 'apotek_pos_offline_cart_v1';
            const STORAGE_QUEUE_KEY   = 'apotek_pos_offline_queue_v1';

            return {
                isOnline: navigator.onLine,
                isOfflineMode: !navigator.onLine,
                isSyncing: false,
                mobileCartDrawer: false,
                showPayModal: false,
                showQueueModal: false,
                completedOfflineTx: null,

                searchQuery: '',
                selectedCat: 'all',

                catalog: {
                    products: [],
                    categories: [],
                    customers: [],
                    settings: {
                        store_name: 'Apotek',
                        store_address: '',
                        store_phone: '',
                        ppn_rate: 11,
                        ppn_mode: 'off',
                        cashier_id: 1,
                        cashier_name: 'Kasir'
                    }
                },

                cartItems: [],
                globalDiscount: 0,
                ppnMode: 'off',
                paymentMethod: 'cash',
                cashAmount: null,
                selectedCustomerId: '',
                newCustomerName: '',
                newCustomerPhone: '',
                tempoDuration: 30,
                orderNotes: '',

                summary: {
                    subtotal: 0,
                    tax: 0,
                    dpp: 0,
                    rounding: 0,
                    grandTotal: 0
                },

                toast: { show: false, message: '', type: 'success' },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => { this.toast.show = false; }, 3500);
                },

                initOfflinePos() {
                    // 1. Register Service Worker for Offline Page Reloads
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.register('/sw.js').catch(() => {});
                    }

                    // 2. Load cached catalog, active cart, and pending queue from LocalStorage
                    try {
                        const savedCatalog = localStorage.getItem(STORAGE_CATALOG_KEY);
                        if (savedCatalog) {
                            this.catalog = JSON.parse(savedCatalog);
                            this.ppnMode = this.catalog.settings?.ppn_mode || 'off';
                        }

                        const savedCart = localStorage.getItem(STORAGE_CART_KEY);
                        if (savedCart) {
                            const parsedCart = JSON.parse(savedCart);
                            this.cartItems = parsedCart.items || [];
                            this.globalDiscount = parsedCart.globalDiscount || 0;
                            this.ppnMode = parsedCart.ppnMode || this.ppnMode;
                            this.paymentMethod = parsedCart.paymentMethod || 'cash';
                            this.recalculate(false);
                        }

                        const savedQueue = localStorage.getItem(STORAGE_QUEUE_KEY);
                        if (savedQueue) {
                            this.offlineQueue = JSON.parse(savedQueue);
                        }
                    } catch (e) {
                        console.error('Failed to read local storage:', e);
                    }

                    // 3. Fetch fresh catalog if online
                    if (this.isOnline) {
                        this.refreshCatalog(true);
                        if (this.offlineQueue.length > 0) {
                            setTimeout(() => this.syncQueue(), 1500);
                        }
                    }

                    // 4. Listen for Online / Offline events
                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        this.isOfflineMode = true;
                        this.showToast('Koneksi internet terputus! Beralih otomatis ke Mode Kasir Offline.', 'warning');
                    });

                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.showToast('Koneksi internet kembali aktif! Menyinkronkan data...', 'success');
                        this.syncQueue();
                        this.refreshCatalog(true);
                    });
                },

                offlineQueue: [],

                toggleOfflineMode() {
                    this.isOfflineMode = !this.isOfflineMode;
                    if (this.isOfflineMode) {
                        this.showToast('Mode Kasir Offline diaktifkan.', 'warning');
                    } else {
                        this.showToast('Kembali ke Mode Kasir Online.', 'success');
                    }
                },

                async refreshCatalog(silent = false) {
                    if (!navigator.onLine) return;
                    try {
                        const res = await fetch('/pos/offline-catalog', {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin'
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if (data && data.products) {
                                this.catalog = {
                                    products: data.products,
                                    categories: data.categories || [],
                                    customers: data.customers || [],
                                    settings: data.settings || this.catalog.settings
                                };
                                localStorage.setItem(STORAGE_CATALOG_KEY, JSON.stringify(this.catalog));
                                if (!silent) {
                                    this.showToast('Cache katalog produk berhasil diperbarui (' + data.products.length + ' obat).', 'success');
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('Could not refresh offline catalog:', e);
                    }
                },

                filteredProducts() {
                    const q = (this.searchQuery || '').toLowerCase().trim();
                    const cat = this.selectedCat;
                    return (this.catalog.products || []).filter(p => {
                        if (cat !== 'all' && p.category_id != cat) return false;
                        if (!q) return true;
                        return (p.name && p.name.toLowerCase().includes(q)) ||
                               (p.barcode && p.barcode.toLowerCase().includes(q));
                    }).slice(0, 40);
                },

                addFirstFilteredProduct() {
                    const list = this.filteredProducts();
                    if (list.length > 0) {
                        this.addToCart(list[0]);
                        this.searchQuery = '';
                    }
                },

                addToCart(product) {
                    if (product.total_stock <= 0) {
                        this.showToast('Stok ' + product.name + ' habis!', 'error');
                        return;
                    }

                    const existingIdx = this.cartItems.findIndex(i => i.id === product.id);
                    if (existingIdx > -1) {
                        const item = this.cartItems[existingIdx];
                        if ((item.qty + 1) * item.unit_factor > product.total_stock) {
                            this.showToast('Stok ' + product.name + ' tidak mencukupi!', 'error');
                            return;
                        }
                        item.qty += 1;
                    } else {
                        this.cartItems.push({
                            id: product.id,
                            name: product.name,
                            price: parseFloat(product.sell_price) || 0,
                            qty: 1,
                            unit_id: product.unit_id,
                            unit_name: product.unit_name || 'Pcs',
                            unit_factor: 1,
                            available_units: product.available_units || {},
                            discount_percent: 0,
                            subtotal: parseFloat(product.sell_price) || 0,
                            notes: ''
                        });
                    }
                    this.recalculate();
                },

                changeUnit(idx, newUnitId) {
                    const item = this.cartItems[idx];
                    if (!item || !item.available_units || !item.available_units[newUnitId]) return;
                    const u = item.available_units[newUnitId];
                    item.unit_id = u.id;
                    item.unit_name = u.name;
                    item.unit_factor = parseFloat(u.factor) || 1;
                    item.price = parseFloat(u.price) || 0;
                    this.recalculate();
                },

                updateQty(idx, newQty) {
                    if (newQty <= 0) {
                        this.removeItem(idx);
                        return;
                    }
                    const item = this.cartItems[idx];
                    const prod = this.catalog.products.find(p => p.id === item.id);
                    if (prod && newQty * item.unit_factor > prod.total_stock) {
                        const maxQty = Math.max(1, Math.floor(prod.total_stock / item.unit_factor));
                        this.showToast('Stok terbatas! Maksimal: ' + maxQty + ' ' + item.unit_name, 'warning');
                        item.qty = maxQty;
                    } else {
                        item.qty = newQty;
                    }
                    this.recalculate();
                },

                removeItem(idx) {
                    this.cartItems.splice(idx, 1);
                    this.recalculate();
                },

                clearCart() {
                    this.cartItems = [];
                    this.globalDiscount = 0;
                    this.cashAmount = null;
                    this.orderNotes = '';
                    this.recalculate();
                },

                cyclePpnMode() {
                    const modes = ['off', 'inclusive', 'exclusive'];
                    const next = (modes.indexOf(this.ppnMode) + 1) % modes.length;
                    this.ppnMode = modes[next];
                    this.recalculate();
                },

                recalculate(persist = true) {
                    let subtotal = 0;
                    let totalItemDiscount = 0;
                    const ppnRate = (parseFloat(this.catalog.settings?.ppn_rate) || 11) / 100;

                    this.cartItems.forEach(item => {
                        const gross = (parseFloat(item.price) || 0) * (parseFloat(item.qty) || 0);
                        const discPct = Math.max(0, Math.min(100, parseFloat(item.discount_percent) || 0));
                        const discAmt = gross * (discPct / 100);
                        item.subtotal = gross - discAmt;
                        subtotal += gross;
                        totalItemDiscount += discAmt;
                    });

                    let netBeforeTax = Math.max(0, subtotal - totalItemDiscount - (parseFloat(this.globalDiscount) || 0));
                    let tax = 0;
                    let dpp = netBeforeTax;
                    let grandTotal = netBeforeTax;

                    if (this.ppnMode === 'exclusive') {
                        tax = netBeforeTax * ppnRate;
                        dpp = netBeforeTax;
                        grandTotal = netBeforeTax + tax;
                    } else if (this.ppnMode === 'inclusive') {
                        grandTotal = netBeforeTax;
                        dpp = netBeforeTax / (1 + ppnRate);
                        tax = netBeforeTax - dpp;
                    }

                    let rounding = 0;
                    if (this.paymentMethod === 'cash') {
                        const rounded = Math.ceil(grandTotal / 100) * 100;
                        rounding = rounded - grandTotal;
                        grandTotal = rounded;
                    } else {
                        grandTotal = Math.round(grandTotal);
                    }

                    this.summary = { subtotal, tax, dpp, rounding, grandTotal };

                    if (persist) {
                        localStorage.setItem(STORAGE_CART_KEY, JSON.stringify({
                            items: this.cartItems,
                            globalDiscount: this.globalDiscount,
                            ppnMode: this.ppnMode,
                            paymentMethod: this.paymentMethod
                        }));
                    }
                },

                openPaymentModal() {
                    if (this.cartItems.length === 0) return;
                    this.mobileCartDrawer = false;
                    this.cashAmount = this.summary.grandTotal;
                    this.showPayModal = true;
                },

                submitOfflineSale() {
                    if (this.cartItems.length === 0) return;

                    if (this.paymentMethod === 'cash' && (parseFloat(this.cashAmount) || 0) < this.summary.grandTotal) {
                        this.showToast('Nominal uang tunai kurang!', 'error');
                        return;
                    }

                    if (this.paymentMethod === 'tempo' && !this.selectedCustomerId && !this.newCustomerName.trim()) {
                        this.showToast('Pilih pelanggan atau isi nama pelanggan baru untuk Tempo!', 'error');
                        return;
                    }

                    const now = new Date();
                    const pad = n => String(n).padStart(2, '0');
                    const dateStr = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
                    const displayDate = pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear() + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes());
                    const randCode = Math.random().toString(36).substring(2, 7).toUpperCase();
                    const invoiceNo = 'INV/' + now.getFullYear() + pad(now.getMonth() + 1) + pad(now.getDate()) + '/OFF-' + randCode;

                    const cashPaid = parseFloat(this.cashAmount) || 0;
                    const changeAmt = this.paymentMethod === 'tempo'
                        ? Math.max(0, this.summary.grandTotal - cashPaid)
                        : Math.max(0, cashPaid - this.summary.grandTotal);

                    const txRecord = {
                        offline_id: 'OFF-' + Date.now() + '-' + randCode,
                        invoice_no: invoiceNo,
                        created_at: dateStr,
                        display_date: displayDate,
                        cashier_id: this.catalog.settings?.cashier_id || 1,
                        subtotal: this.summary.subtotal,
                        global_discount: parseFloat(this.globalDiscount) || 0,
                        ppn_mode: this.ppnMode,
                        tax: this.summary.tax,
                        dpp: this.summary.dpp,
                        rounding: this.summary.rounding,
                        grand_total: this.summary.grandTotal,
                        payment_method: this.paymentMethod,
                        cash_amount: cashPaid,
                        change_amount: changeAmt,
                        customer_id: this.selectedCustomerId || null,
                        new_customer_name: this.newCustomerName.trim() || null,
                        new_customer_phone: this.newCustomerPhone.trim() || null,
                        tempo_duration: this.tempoDuration || 30,
                        notes: this.orderNotes,
                        items: JSON.parse(JSON.stringify(this.cartItems))
                    };

                    // 1. Deduct local cached stock
                    this.cartItems.forEach(item => {
                        const prod = this.catalog.products.find(p => p.id === item.id);
                        if (prod) {
                            prod.total_stock = Math.max(0, prod.total_stock - (item.qty * item.unit_factor));
                        }
                    });
                    localStorage.setItem(STORAGE_CATALOG_KEY, JSON.stringify(this.catalog));

                    // 2. Push to Offline Queue in LocalStorage
                    this.offlineQueue.push(txRecord);
                    localStorage.setItem(STORAGE_QUEUE_KEY, JSON.stringify(this.offlineQueue));

                    // 3. Reset Cart & Show Printable Receipt
                    this.clearCart();
                    localStorage.removeItem(STORAGE_CART_KEY);
                    this.showPayModal = false;
                    this.completedOfflineTx = txRecord;
                    this.showToast('Transaksi disimpan di perangkat! Siap cetak struk.', 'success');

                    // 4. If online, auto-sync immediately in background
                    if (navigator.onLine) {
                        setTimeout(() => this.syncQueue(), 1000);
                    }
                },

                async syncQueue() {
                    if (this.isSyncing || this.offlineQueue.length === 0 || !navigator.onLine) return;
                    this.isSyncing = true;

                    try {
                        const res = await fetch('/pos/sync-offline', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ transactions: this.offlineQueue })
                        });

                        if (res.ok) {
                            const data = await res.json();
                            const syncedIds = data.synced_ids || [];
                            if (syncedIds.length > 0) {
                                this.offlineQueue = this.offlineQueue.filter(tx => !syncedIds.includes(tx.offline_id));
                                localStorage.setItem(STORAGE_QUEUE_KEY, JSON.stringify(this.offlineQueue));
                                this.showToast(syncedIds.length + ' transaksi offline berhasil disinkronkan ke VPS!', 'success');
                                this.refreshCatalog(true);
                            }
                        } else {
                            this.showToast('Gagal sinkronisasi ke server (Status ' + res.status + '). Akan dicoba lagi.', 'error');
                        }
                    } catch (e) {
                        console.warn('Sync failed, will retry when online:', e);
                    } finally {
                        this.isSyncing = false;
                    }
                },

                printOfflineReceipt() {
                    const content = document.getElementById('offline-receipt-print-area');
                    if (!content) return;
                    const win = window.open('', '_blank', 'width=360,height=600');
                    const htmlOpen = '<' + 'html><' + 'head><' + 'title>Cetak Struk<' + '/title><' + 'style>' +
                        'body { font-family: monospace; font-size: 12px; width: 58mm; margin: 0 auto; padding: 4px; color: #000; }' +
                        '.text-center { text-align: center; }' +
                        '.flex { display: flex; justify-content: space-between; }' +
                        '.font-bold { font-weight: bold; }' +
                        '.border-b { border-bottom: 1px dashed #000; padding-bottom: 4px; margin-bottom: 4px; }' +
                        '.border-t { border-top: 1px dashed #000; padding-top: 4px; margin-top: 4px; }' +
                        'p { margin: 2px 0; }' +
                        '<' + '/style><' + '/head><' + 'body onload="window.print(); setTimeout(() => window.close(), 500);">';
                    const htmlClose = '<' + '/body><' + '/html>';
                    win.document.write(htmlOpen + content.innerHTML + htmlClose);
                    win.document.close();
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(parseFloat(num) || 0));
                }
            };
        }
    </script>
</div>
