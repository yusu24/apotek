<!-- OFFLINE POS ENGINE & PWA CONTROLLER -->
<div x-data="offlinePosApp()"
     x-init="initOfflinePos()"
     @toggle-offline-pos.window="toggleOfflineMode()"
     @open-offline-queue.window="showQueueModal = true">

    <!-- PENDING OFFLINE QUEUE BANNER (Only visible when online and there are unsynced transactions) -->
    <template x-if="!isOfflineMode && offlineQueue.length > 0">
        <div class="fixed bottom-4 right-4 z-[45] flex items-center gap-2 bg-amber-500 text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-bold">
            <svg class="w-4 h-4 shrink-0 animate-spin" x-show="isSyncing" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            <span x-text="offlineQueue.length + ' Transaksi Offline Belum Sinkron'"></span>
            <button @click="showQueueModal = true" class="underline ml-1 hover:text-amber-100">Lihat</button>
            <button x-show="isOnline && !isSyncing" @click="syncQueue()" class="bg-white text-amber-700 px-2.5 py-1 rounded-lg text-[11px] font-extrabold hover:bg-amber-50 ml-1 shadow-sm">
                Sinkronkan
            </button>
        </div>
    </template>

    <!-- TOAST NOTIFICATION FOR OFFLINE POS -->
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-[120] w-full max-w-sm pointer-events-none">
        <div :class="toast.type === 'success' ? 'bg-blue-600' : (toast.type === 'warning' ? 'bg-amber-600' : 'bg-red-500')"
             class="text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3">
            <p class="text-sm font-bold flex-1" x-text="toast.message"></p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- UNIFIED OFFLINE POS INTERFACE (Matches Online Cashier 1:1)                -->
    <!-- ========================================================================= -->
    <div x-show="isOfflineMode"
         x-cloak
         class="absolute inset-x-0 bottom-0 top-16 xl:top-0 z-[30] bg-gray-50 flex flex-col md:flex-row gap-4 p-4 overflow-hidden">
        
        <!-- LEFT/TOP: Keranjang (Identical to Online Cashier) -->
        <div :class="mobileCartDrawer ? 'fixed inset-0 z-[70] flex bg-black/50 p-3' : 'hidden md:flex md:w-[350px] lg:w-[380px] md:shrink-0'"
             class="bg-white rounded-2xl shadow-xl flex-col overflow-hidden border border-gray-200 z-10 h-full">
            
            <!-- Cart Header -->
            <div class="p-4 md:p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 md:w-7 md:h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h2 class="text-lg md:text-2xl font-bold text-gray-900">Keranjang</h2>
                    </div>
                    <p class="text-xs md:text-sm text-gray-500"><span x-text="cartItems.length"></span> Item</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                        Mode Offline
                    </span>
                    <button @click="mobileCartDrawer = false" class="md:hidden text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Cart Items (Scrollable Table - Identical to Online Cashier) -->
            <div class="flex-1 overflow-auto bg-white">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-950 text-white font-semibold uppercase text-xs tracking-wider border-b border-blue-900 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-2 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-2 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="cartItems.length === 0">
                            <tr>
                                <td colspan="4" class="py-16 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <p class="text-sm font-bold text-gray-500">Keranjang Kosong</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Pilih produk di sebelah kanan</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="(item, idx) in cartItems" :key="item.id">
                            <tr class="hover:bg-blue-50 group transition-colors">
                                <td class="px-2 md:px-4 py-2 md:py-3 align-top text-sm">
                                    <div class="text-gray-900 line-clamp-2 leading-tight mb-1 text-xs font-medium" x-text="item.name"></div>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-normal text-gray-400 shrink-0">@</span>
                                        <span class="text-xs font-normal text-blue-700 shrink-0" x-text="formatNumber(item.price)"></span>
                                        <select @change="changeUnit(idx, $event.target.value)"
                                                class="text-[10px] border-gray-200 rounded py-0.5 pl-1 pr-4 bg-gray-50 font-semibold text-gray-700 focus:ring-blue-500">
                                            <template x-for="u in Object.values(item.available_units || {})" :key="u.id">
                                                <option :value="u.id" :selected="u.id == item.unit_id" x-text="u.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center align-top text-sm">
                                    <div class="w-16 mx-auto">
                                        <input type="number" min="1" x-model.number="item.qty" @input="recalculate()"
                                               class="w-full px-1 py-1 text-center text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right align-top text-gray-900 text-sm font-medium" x-text="formatNumber(item.subtotal)"></td>
                                <td class="px-2 py-3 text-center align-top text-sm">
                                    <button @click="removeItem(idx)" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Calculation Footer (Identical to Online Cashier) -->
            <div class="p-6 bg-gray-50 border-t border-gray-100 space-y-3">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-bold" x-text="'Rp. ' + formatNumber(summary.subtotal) + ',-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <button @click="cyclePpnMode()" class="text-gray-600 hover:text-blue-600 transition-colors">
                            PPN (<span x-text="ppnMode.toUpperCase()"></span>)
                        </button>
                        <span class="font-bold" x-text="'Rp. ' + formatNumber(summary.tax) + ',-'"></span>
                    </div>
                </div>

                <!-- Total -->
                <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-gray-600 font-semibold">Total Bayar</span>
                    <span class="text-3xl font-bold text-gray-900" x-text="'Rp. ' + formatNumber(summary.grandTotal) + ',-'"></span>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-3">
                    <button @click="clearCart()"
                            :disabled="cartItems.length === 0"
                            class="btn btn-lg btn-secondary">
                        Reset
                    </button>
                    <button @click="openPaymentModal()"
                            :disabled="cartItems.length === 0"
                            :class="cartItems.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="btn btn-lg btn-primary">
                        Bayar
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT/BOTTOM: Katalog Produk (Identical to Online Cashier) -->
        <div class="flex-1 min-w-0 bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden border border-gray-100 h-full md:h-full pb-20 md:pb-0">
            
            <!-- Search & Filter Header -->
            <div class="p-3 md:p-6 border-b border-gray-100 space-y-3 md:space-y-4">
                <div class="flex gap-2 items-start">
                    <!-- Search Bar -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text"
                               x-model="searchQuery"
                               @keydown.enter.prevent="addFirstFilteredProduct()"
                               class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Cari produk / Scan barcode (Mode Offline)..."
                               autocomplete="off">
                    </div>

                    <!-- Offline Queue Button (Matches Pending Orders Button) -->
                    <button type="button"
                            @click="showQueueModal = true"
                            class="relative shrink-0 p-3 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all h-[50px] w-[50px] flex items-center justify-center"
                            title="Antrean Transaksi Offline">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <template x-if="offlineQueue.length > 0">
                            <span style="min-width: 18px; height: 18px;" class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center px-1 rounded-full bg-amber-500 text-white text-[10px] font-bold ring-2 ring-white" x-text="offlineQueue.length"></span>
                        </template>
                    </button>

                    <!-- Switch Back to Online Mode Button -->
                    <button type="button"
                            @click="toggleOfflineMode()"
                            class="shrink-0 px-3.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl hover:bg-amber-100 focus:outline-none shadow-sm transition-all h-[50px] flex items-center justify-center gap-2"
                            title="Klik untuk kembali ke Mode Online">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="text-xs font-bold" x-text="isOnline ? 'Mode Offline (Klik Online)' : 'Offline (Internet Putus)'"></span>
                    </button>
                </div>

                <!-- Category Filter -->
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                    <button @click="selectedCat = 'all'"
                            :class="selectedCat === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all">
                        Semua
                    </button>
                    <template x-for="cat in catalog.categories" :key="cat.id">
                        <button @click="selectedCat = cat.id"
                                :class="selectedCat == cat.id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all"
                                x-text="cat.name">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Product Grid (Scrollable - Identical Card Design to Online Cashier) -->
            <div class="flex-1 p-3 md:p-6 overflow-y-auto">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div class="bg-white border-2 border-gray-100 rounded-xl p-4 hover:border-blue-500 hover:shadow-lg transition-all group relative">
                            
                            <!-- Stock Badge (Top Right Corner) -->
                            <div class="absolute top-2 right-2 z-10 pointer-events-none">
                                <span class="text-xs text-white px-2 py-1 rounded-full font-bold shadow-md"
                                      :class="product.total_stock <= 0 ? 'bg-red-600' : (product.total_stock <= 5 ? 'bg-orange-500' : (product.total_stock <= 20 ? 'bg-yellow-500' : 'bg-green-500'))"
                                      x-text="product.total_stock <= 0 ? 'Habis' : product.total_stock">
                                </span>
                            </div>

                            <!-- Product Icon / Image -->
                            <div class="h-24 bg-gray-50 rounded-lg mb-3 flex items-center justify-center group-hover:bg-blue-50 transition-colors overflow-hidden">
                                <template x-if="product.image">
                                    <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!product.image">
                                    <svg class="w-10 h-10 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </template>
                            </div>

                            <!-- Product Info -->
                            <div class="cursor-pointer" @click="addToCart(product)">
                                <h4 class="font-bold text-sm text-gray-900 mb-1 line-clamp-2 h-10 overflow-hidden text-ellipsis hover:text-blue-600" x-text="product.name"></h4>
                                <p class="text-xs text-blue-600 font-bold mb-2" x-text="'Rp. ' + formatNumber(product.sell_price) + ',-'"></p>
                            </div>

                            <!-- Add Button -->
                            <button @click="addToCart(product)"
                                    :class="product.total_stock > 0 ? 'bg-gray-900 group-hover:bg-blue-600' : 'bg-gray-400 cursor-not-allowed'"
                                    class="w-full py-2 text-white rounded-lg text-sm font-semibold transition-colors">
                                + Tambah
                            </button>
                        </div>
                    </template>
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
                    <p class="text-[10px] text-gray-500 font-medium">Total Bayar</p>
                    <p class="text-base font-extrabold text-gray-900" x-text="'Rp. ' + formatNumber(summary.grandTotal) + ',-'"></p>
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
                <button @click="completedOfflineTx = null; if(isOnline && !manualOfflineOverride && cartItems.length === 0) isOfflineMode = false;" class="flex-1 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold">
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

                    // 4. Listen for Online / Offline events (100% Automatic)
                    window.addEventListener('offline', () => {
                        this.handleConnectionLost();
                    });

                    window.addEventListener('online', () => {
                        this.checkRealConnection();
                    });

                    // 5. Real server reachability check every 15s (detects Wi-Fi connected but ISP/Internet down)
                    setInterval(() => {
                        this.checkRealConnection();
                    }, 15000);
                },

                manualOfflineOverride: false,
                offlineQueue: [],

                handleConnectionLost() {
                    const wasOnline = this.isOnline;
                    this.isOnline = false;
                    if (!this.isOfflineMode) {
                        this.isOfflineMode = true;
                        this.showToast('Koneksi internet terputus! Otomatis beralih ke Mode Kasir Offline.', 'warning');
                    } else if (wasOnline) {
                        this.showToast('Koneksi internet terputus.', 'warning');
                    }
                },

                handleConnectionRestored() {
                    const wasOffline = !this.isOnline;
                    this.isOnline = true;
                    if (wasOffline) {
                        this.syncQueue();
                        this.refreshCatalog(true);
                        // Automatically return to Online Mode if cashier didn't manually lock offline mode and cart is empty
                        if (!this.manualOfflineOverride && this.cartItems.length === 0 && !this.showPayModal && !this.completedOfflineTx) {
                            this.isOfflineMode = false;
                            this.showToast('Koneksi internet kembali stabil! Otomatis kembali ke Mode Kasir Online.', 'success');
                        } else {
                            this.showToast('Internet kembali aktif! Transaksi offline otomatis disinkronkan.', 'success');
                        }
                    }
                },

                async checkRealConnection() {
                    if (navigator.onLine === false) {
                        this.handleConnectionLost();
                        return;
                    }
                    try {
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 5000);
                        const res = await fetch('/user/ping', {
                            method: 'GET',
                            cache: 'no-store',
                            credentials: 'same-origin',
                            signal: controller.signal,
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        clearTimeout(timeoutId);
                        if (res.ok) {
                            this.handleConnectionRestored();
                        } else if (res.status >= 500) {
                            this.handleConnectionLost();
                        }
                    } catch (e) {
                        this.handleConnectionLost();
                    }
                },

                toggleOfflineMode() {
                    this.isOfflineMode = !this.isOfflineMode;
                    this.manualOfflineOverride = this.isOfflineMode;
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
