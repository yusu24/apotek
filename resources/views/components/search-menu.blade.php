@php
    $user = auth()->user();

    // Definisikan kategori utama (MENUS) dan halaman detailnya (PAGES)
    $categories = [];

    // 1. Kasir & Penjualan (K)
    $posPages = [];
    if (!$user || $user->can('access pos')) {
        $posPages[] = [
            'code' => 'POS',
            'title' => 'Kasir (POS)',
            'url' => route('pos.cashier'),
            'keywords' => 'pos kasir penjualan transaksi bayar checkout struk',
        ];
    }
    if ($user && $user->can('manage sales returns')) {
        $posPages[] = [
            'code' => 'RP',
            'title' => 'Retur Penjualan',
            'url' => route('inventory.returns.sales'),
            'keywords' => 'retur jual retur penjualan batal kasir pengembalian',
        ];
    }
    if (!empty($posPages)) {
        $categories[] = [
            'code' => 'K',
            'name' => 'Kasir & Penjualan',
            'pages' => $posPages,
        ];
    }

    // 2. Stok & Pengadaan (S)
    $stockPages = [];
    if (!$user || $user->can('view stock')) {
        $stockPages[] = [
            'code' => 'STK',
            'title' => 'Stok & Opname',
            'url' => route('inventory.index'),
            'keywords' => 'stok stock opname fisik sisa penyesuaian expired',
        ];
    }
    if ($user && $user->can('view purchase orders')) {
        $stockPages[] = [
            'code' => 'PO',
            'title' => 'Pesanan Pembelian (PO)',
            'url' => route('procurement.purchase-orders.index'),
            'keywords' => 'po purchase order order supplier distributor beli',
        ];
        $stockPages[] = [
            'code' => 'PO+',
            'title' => 'Buat PO Baru',
            'url' => route('procurement.purchase-orders.create'),
            'keywords' => 'buat po baru order baru tambah po',
        ];
    }
    if ($user && $user->can('view goods receipts')) {
        $stockPages[] = [
            'code' => 'GR',
            'title' => 'Penerimaan Pesanan',
            'url' => route('procurement.goods-receipts.index'),
            'keywords' => 'terima barang goods receipt receiving surat jalan faktur',
        ];
        $stockPages[] = [
            'code' => 'GR+',
            'title' => 'Terima Pesanan Baru',
            'url' => route('procurement.goods-receipts.create'),
            'keywords' => 'input faktur beli penerimaan barang baru surat jalan',
        ];
    }
    if ($user && $user->can('manage purchase returns')) {
        $stockPages[] = [
            'code' => 'RB',
            'title' => 'Retur Pembelian',
            'url' => route('inventory.returns.purchase'),
            'keywords' => 'retur beli retur supplier klaim barang rusak',
        ];
    }
    if (!empty($stockPages)) {
        $categories[] = [
            'code' => 'S',
            'name' => 'Stok & Pengadaan',
            'pages' => $stockPages,
        ];
    }

    // 3. Data Master (M)
    $masterPages = [];
    if (!$user || $user->can('view products')) {
        $masterPages[] = [
            'code' => 'OBT',
            'title' => 'Data Obat / Produk',
            'url' => route('products.index'),
            'keywords' => 'obat produk barang daftar harga hpp barcode sku',
        ];
        $masterPages[] = [
            'code' => 'OB+',
            'title' => 'Tambah Obat Baru',
            'url' => route('products.create'),
            'keywords' => 'tambah obat baru input obat produk baru',
        ];
    }
    if ($user && $user->can('manage categories')) {
        $masterPages[] = [
            'code' => 'KAT',
            'title' => 'Kategori Produk',
            'url' => route('master.categories'),
            'keywords' => 'kategori etalase golongan jenis obat sirup',
        ];
    }
    if ($user && $user->can('manage units')) {
        $masterPages[] = [
            'code' => 'SAT',
            'title' => 'Master Satuan',
            'url' => route('master.units'),
            'keywords' => 'satuan tablet botol strip box ampul tube vial pcs',
        ];
    }
    if ($user && $user->can('manage product units')) {
        $masterPages[] = [
            'code' => 'KON',
            'title' => 'Konversi Satuan',
            'url' => route('master.product-units'),
            'keywords' => 'konversi satuan multi satuan isi per box pecahan',
        ];
    }
    if ($user && $user->can('manage suppliers')) {
        $masterPages[] = [
            'code' => 'SUP',
            'title' => 'Supplier / Distributor',
            'url' => route('master.suppliers'),
            'keywords' => 'supplier distributor pbf pemasok vendor sales rekanan',
        ];
    }
    if ($user && $user->can('manage customers')) {
        $masterPages[] = [
            'code' => 'PEL',
            'title' => 'Pelanggan / Pasien',
            'url' => route('master.customers'),
            'keywords' => 'pelanggan customer pasien member data pembeli',
        ];
    }
    if (!empty($masterPages)) {
        $categories[] = [
            'code' => 'M',
            'name' => 'Master Data',
            'pages' => $masterPages,
        ];
    }

    // 4. Laporan (R)
    $reportPages = [];
    if (!$user || $user->can('view sales reports')) {
        $reportPages[] = [
            'code' => 'LPJ',
            'title' => 'Laporan Penjualan',
            'url' => route('reports.sales'),
            'keywords' => 'laporan penjualan omset rekap kasir harian bulanan sales',
        ];
        $reportPages[] = [
            'code' => 'GPJ',
            'title' => 'Grafik Penjualan',
            'url' => route('reports.sales-chart'),
            'keywords' => 'grafik penjualan tren sales statistik visual',
        ];
    }
    if (!$user || $user->can('view stock')) {
        $reportPages[] = [
            'code' => 'LST',
            'title' => 'Laporan Stok',
            'url' => route('reports.stock'),
            'keywords' => 'laporan stok rekap stok habis menipis expired kadaluarsa',
        ];
    }
    if ($user && $user->can('view stock movements')) {
        $reportPages[] = [
            'code' => 'MUT',
            'title' => 'Riwayat Transaksi Produk (Mutasi)',
            'url' => route('reports.transaction-history'),
            'keywords' => 'mutasi stok kartu stok keluar masuk obat riwayat barang',
        ];
    }
    if ($user && $user->can('view product margin report')) {
        $reportPages[] = [
            'code' => 'MRG',
            'title' => 'Laporan Margin Produk',
            'url' => route('reports.product-margin'),
            'keywords' => 'margin laba kotor keuntungan profit obat teruntung mark up',
        ];
    }
    if (!empty($reportPages)) {
        $categories[] = [
            'code' => 'R',
            'name' => 'Reports',
            'pages' => $reportPages,
        ];
    }

    // 5. Keuangan & Akuntansi (F)
    $financePages = [];
    if ($user && $user->can('view balance sheet')) {
        $financePages[] = [
            'code' => 'KUG',
            'title' => 'Ringkasan Keuangan',
            'url' => route('finance.summary'),
            'keywords' => 'ringkasan keuangan financial summary saldo kas performa',
        ];
        $financePages[] = [
            'code' => 'NRC',
            'title' => 'Neraca (Standar)',
            'url' => route('finance.balance-sheet'),
            'keywords' => 'neraca balance sheet aktiva pasiva aset modal ekuitas',
        ];
    }
    if ($user && $user->can('view trial balance')) {
        $financePages[] = [
            'code' => 'NSA',
            'title' => 'Neraca Saldo Awal',
            'url' => route('finance.trial-balance'),
            'keywords' => 'trial balance neraca saldo awal debit kredit balance',
        ];
    }
    if ($user && $user->can('view profit loss')) {
        $financePages[] = [
            'code' => 'LBR',
            'title' => 'Laporan Laba Rugi',
            'url' => route('finance.profit-loss'),
            'keywords' => 'laba rugi profit loss pnl pendapatan beban operasional',
        ];
    }
    if ($user && $user->can('view income statement')) {
        $financePages[] = [
            'code' => 'ARK',
            'title' => 'Laporan Arus Kas',
            'url' => route('finance.cash-flow'),
            'keywords' => 'arus kas cash flow uang masuk operasional investasi kas',
        ];
    }
    if ($user && $user->can('view general ledger')) {
        $financePages[] = [
            'code' => 'BKB',
            'title' => 'Buku Besar (General Ledger)',
            'url' => route('accounting.ledger'),
            'keywords' => 'buku besar ledger kartu akun coa transaksi akun',
        ];
    }
    if ($user && $user->can('view ppn report')) {
        $financePages[] = [
            'code' => 'PPN',
            'title' => 'Laporan PPN (Pajak)',
            'url' => route('finance.ppn-report'),
            'keywords' => 'ppn pajak pertambahan nilai faktur masukan keluaran spt',
        ];
    }
    if ($user && $user->can('view ap aging report')) {
        $financePages[] = [
            'code' => 'HUT',
            'title' => 'Hutang & Piutang (Aging)',
            'url' => route('finance.aging-report'),
            'keywords' => 'hutang piutang aging jatuh tempo tagihan supplier tempo',
        ];
    }
    if ($user && $user->can('view expenses')) {
        $financePages[] = [
            'code' => 'BIA',
            'title' => 'Pengeluaran & Pemasukan Kas',
            'url' => route('finance.expenses'),
            'keywords' => 'pengeluaran pemasukan kas operasional biaya beban kas',
        ];
    }
    if ($user && $user->can('view accounts')) {
        $financePages[] = [
            'code' => 'COA',
            'title' => 'Daftar Akun (COA)',
            'url' => route('accounting.accounts.index'),
            'keywords' => 'coa chart of accounts rekening akuntansi bagan akun',
        ];
    }
    if ($user && $user->can('view journals')) {
        $financePages[] = [
            'code' => 'JRN',
            'title' => 'Jurnal Umum',
            'url' => route('accounting.journals.index'),
            'keywords' => 'jurnal umum memorial entri debit kredit pembukuan',
        ];
    }
    if (!empty($financePages)) {
        $categories[] = [
            'code' => 'F',
            'name' => 'Finance & Accounting',
            'pages' => $financePages,
        ];
    }

    // 6. Admin & Pengaturan (A)
    $adminPages = [];
    if ($user && $user->can('manage settings')) {
        $adminPages[] = [
            'code' => 'TKO',
            'title' => 'Identitas Toko / Apotek',
            'url' => route('settings.store'),
            'keywords' => 'identitas toko nama apotek sia sipa alamat logo',
        ];
    }
    if ($user && $user->can('manage pos settings')) {
        $adminPages[] = [
            'code' => 'SET',
            'title' => 'Pengaturan Kasir',
            'url' => route('settings.pos'),
            'keywords' => 'pengaturan kasir printer struk kertas scanner thermal',
        ];
    }
    if ($user && $user->can('manage users')) {
        $adminPages[] = [
            'code' => 'USR',
            'title' => 'Kelola User',
            'url' => route('admin.users.index'),
            'keywords' => 'kelola user pengguna karyawan kasir akun login password',
        ];
        $adminPages[] = [
            'code' => 'ROL',
            'title' => 'Kelola Jabatan & Hak Akses',
            'url' => route('admin.roles.index'),
            'keywords' => 'kelola jabatan role permissions hak akses wewenang',
        ];
    }
    if ($user && $user->can('manage backups')) {
        $adminPages[] = [
            'code' => 'BAK',
            'title' => 'Backup Data',
            'url' => route('admin.backups'),
            'keywords' => 'backup database restore sql cadangan unduh data',
        ];
    }
    if ($user && $user->can('view activity logs')) {
        $adminPages[] = [
            'code' => 'LOG',
            'title' => 'Riwayat Aktivitas (Log)',
            'url' => route('admin.activity-log'),
            'keywords' => 'audit log aktivitas trail riwayat sistem log user',
        ];
    }
    if (!empty($adminPages)) {
        $categories[] = [
            'code' => 'A',
            'name' => 'Admin',
            'pages' => $adminPages,
        ];
    }

    // 7. Pages Cepat / Standalone
    $quickPages = [
        [
            'code' => 'H00',
            'title' => 'Home',
            'url' => route('dashboard'),
            'keywords' => 'home dashboard beranda utama',
        ],
        [
            'code' => 'RMS',
            'title' => 'Rumus & Perhitungan',
            'url' => route('guide.formulas'),
            'keywords' => 'rumus perhitungan kalkulasi logika laba rugi hpp margin tuslah embalase aging rop kas formula',
        ],
        [
            'code' => 'PAN',
            'title' => 'Panduan Aplikasi',
            'url' => route('guide.index'),
            'keywords' => 'panduan petunjuk manual book tutorial faq',
        ],
        [
            'code' => 'PRF',
            'title' => 'Profil Saya',
            'url' => route('profile'),
            'keywords' => 'profil profil saya ganti password akun user',
        ],
    ];
@endphp

<div x-data="{
        isOpen: false,
        query: '',
        selectedIndex: 0,
        activeCategory: null, // null = root view, string = drilled category
        categories: {{ Js::from($categories) }},
        quickPages: {{ Js::from($quickPages) }},

        open() {
            this.isOpen = true;
            this.query = '';
            this.activeCategory = null;
            this.selectedIndex = 0;
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        },

        close() {
            this.isOpen = false;
        },

        // Semua halaman flat dengan referensi category
        get allPages() {
            let pages = [];
            this.categories.forEach(cat => {
                cat.pages.forEach(p => {
                    pages.push({ ...p, categoryName: cat.name });
                });
            });
            this.quickPages.forEach(p => {
                pages.push({ ...p, categoryName: 'General' });
            });
            return pages;
        },

        // Komputasi list item yang sedang aktif untuk navigasi panah & enter
        get currentList() {
            const q = this.query.trim().toLowerCase();

            // Jika sedang drilldown ke suatu category
            if (this.activeCategory) {
                const cat = this.categories.find(c => c.name === this.activeCategory);
                if (!cat) return [];
                if (!q) {
                    return cat.pages.map(p => ({ type: 'page', ...p }));
                }
                return cat.pages
                    .filter(p => p.title.toLowerCase().includes(q) || p.code.toLowerCase().includes(q) || (p.keywords && p.keywords.toLowerCase().includes(q)))
                    .map(p => ({ type: 'page', ...p }));
            }

            // Jika query kosong: tampilkan MENUS + PAGES cepat
            if (!q) {
                let items = [];
                this.categories.forEach(cat => {
                    items.push({ type: 'menu', ...cat });
                });
                this.quickPages.forEach(p => {
                    items.push({ type: 'page', ...p });
                });
                return items;
            }

            // Jika sedang mengetik query: cari di MENUS dan PAGES
            let items = [];

            // Matching menus
            this.categories.forEach(cat => {
                if (cat.name.toLowerCase().includes(q) || cat.code.toLowerCase().includes(q)) {
                    items.push({ type: 'menu', ...cat });
                }
            });

            // Matching pages
            this.allPages.forEach(p => {
                if (p.title.toLowerCase().includes(q) || p.code.toLowerCase().includes(q) || (p.keywords && p.keywords.toLowerCase().includes(q))) {
                    items.push({ type: 'page', ...p });
                }
            });

            return items;
        },

        nextItem() {
            if (this.currentList.length === 0) return;
            this.selectedIndex = (this.selectedIndex + 1) % this.currentList.length;
            this.scrollToSelected();
        },

        prevItem() {
            if (this.currentList.length === 0) return;
            this.selectedIndex = (this.selectedIndex - 1 + this.currentList.length) % this.currentList.length;
            this.scrollToSelected();
        },

        selectItem(item) {
            if (!item) return;
            if (item.type === 'menu') {
                // Drill down ke category
                this.activeCategory = item.name;
                this.query = '';
                this.selectedIndex = 0;
                this.$nextTick(() => {
                    if (this.$refs.searchInput) this.$refs.searchInput.focus();
                });
            } else {
                // Langsung buka link halaman
                this.navigate(item.url);
            }
        },

        handleEsc() {
            if (this.activeCategory) {
                // Kembali ke root list
                this.activeCategory = null;
                this.selectedIndex = 0;
            } else {
                this.close();
            }
        },

        navigate(url) {
            this.close();
            if (window.Livewire) {
                Livewire.navigate(url);
            } else {
                window.location.href = url;
            }
        },

        scrollToSelected() {
            this.$nextTick(() => {
                const el = this.$refs.listContainer?.querySelector('[data-selected=\'true\']');
                if (el) {
                    el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            });
        }
     }"
     x-init="
        window.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (isOpen) {
                    close();
                } else {
                    open();
                }
            }
        });
     "
     x-on:open-search-menu.window="open()"
     x-on:close-search-menu.window="close()"
     x-cloak
     class="relative z-[100]">

    <!-- Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px]"></div>

    <!-- Modal Box -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-[0.98] -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-[0.98] -translate-y-1"
         @keydown.escape.window="handleEsc()"
         @keydown.down.prevent="nextItem()"
         @keydown.up.prevent="prevItem()"
         @keydown.enter.prevent="selectItem(currentList[selectedIndex])"
         class="fixed inset-x-4 top-14 sm:top-20 max-w-[540px] mx-auto bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200/80 dark:border-gray-800 overflow-hidden flex flex-col z-[101]">

        <!-- Search Header Input Bar -->
        <div class="flex items-center px-4 py-3.5 border-b border-gray-200/70 dark:border-gray-800">
            <!-- Orange Search Magnifier Icon (persis seperti screenshot) -->
            <svg class="w-5 h-5 text-[#ea580c] shrink-0 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input x-ref="searchInput"
                   x-model="query"
                   @input="selectedIndex = 0"
                   type="text"
                   placeholder="Jump by code, or search menus and people..."
                   class="w-full bg-transparent border-0 p-0 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-0 tracking-wide font-normal">
        </div>

        <!-- Drilled Breadcrumb Header (jika sedang membuka kategori) -->
        <div x-show="activeCategory" class="px-5 py-2 bg-gray-50/80 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-1.5 font-medium">
                <button type="button" @click="activeCategory = null; selectedIndex = 0" class="hover:text-orange-600 transition-colors">MENUS</button>
                <span>&rsaquo;</span>
                <span class="text-gray-800 dark:text-white font-semibold uppercase tracking-wider" x-text="activeCategory"></span>
            </div>
            <button type="button" @click="activeCategory = null; selectedIndex = 0" class="text-xs text-orange-600 hover:underline">
                &larr; Kembali
            </button>
        </div>

        <!-- List Body Container -->
        <div x-ref="listContainer" class="max-h-[380px] overflow-y-auto px-3 py-2 scrollbar-hide">
            
            <!-- SECTION: MENUS (hanya muncul saat root view dan ada menu yang cocok) -->
            <template x-if="!activeCategory && currentList.some(item => item.type === 'menu')">
                <div class="pt-1.5 pb-1 px-2">
                    <p class="text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">MENUS</p>
                </div>
            </template>

            <!-- Render Menu Items -->
            <template x-for="(item, index) in currentList" :key="item.type + '-' + (item.code || item.name) + '-' + index">
                <div>
                    <!-- Section Divider jika berpindah ke PAGES -->
                    <template x-if="!activeCategory && item.type === 'page' && (index === 0 || currentList[index - 1]?.type === 'menu')">
                        <div class="pt-3 pb-1 px-2">
                            <p class="text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">PAGES</p>
                        </div>
                    </template>

                    <!-- Item Row -->
                    <div @click="selectItem(item)"
                         @mouseenter="selectedIndex = index"
                         :data-selected="selectedIndex === index"
                         :class="selectedIndex === index ? 'bg-slate-100/80 dark:bg-slate-800/60' : 'hover:bg-slate-50 dark:hover:bg-slate-800/30'"
                         class="group flex items-center justify-between px-3 py-2 rounded-lg transition-colors cursor-pointer my-0.5">
                        
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Square Avatar Badge (Orange saat selected, Gray saat default) -->
                            <div :class="selectedIndex === index ? 'bg-[#ea580c] text-white shadow-xs' : 'bg-[#eef2f6] dark:bg-gray-800 text-[#475569] dark:text-gray-300'"
                                 class="w-7 h-6 rounded flex items-center justify-center font-bold text-xs shrink-0 tracking-tight transition-colors">
                                <span x-text="item.code"></span>
                            </div>

                            <!-- Title -->
                            <div class="min-w-0">
                                <span class="text-sm font-normal text-gray-800 dark:text-gray-100" x-text="item.type === 'menu' ? item.name : item.title"></span>
                            </div>
                        </div>

                        <!-- Right Info: Sub-items count untuk Menu -->
                        <template x-if="item.type === 'menu'">
                            <span class="text-xs text-gray-400 dark:text-gray-500 font-normal shrink-0" x-text="(item.pages ? item.pages.length : 0) + ' items'"></span>
                        </template>

                        <!-- Right Info untuk Page: Return / Enter Icon saat selected -->
                        <template x-if="item.type === 'page'">
                            <svg :class="selectedIndex === index ? 'opacity-100 text-gray-400 dark:text-gray-500' : 'opacity-0'"
                                 class="w-3.5 h-3.5 shrink-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="currentList.length === 0" class="py-10 px-4 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada menu atau halaman yang cocok dengan "<span class="font-semibold text-orange-600" x-text="query"></span>"</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Gunakan kode seperti <span class="font-mono text-gray-600 dark:text-gray-300">POS, STK, PO, OBT, NRC, H00</span> atau ketik nama menu</p>
            </div>
        </div>

        <!-- Footer Navigation Instructions (persis seperti di screenshot: ↑↓ move  enter open  esc back/close) -->
        <div class="px-5 py-2.5 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex items-center gap-5 text-xs text-gray-400 dark:text-gray-500">
            <span class="inline-flex items-center gap-1.5">
                <strong class="font-bold text-gray-700 dark:text-gray-300 tracking-tighter">↑↓</strong>
                <span>move</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <strong class="font-bold text-gray-700 dark:text-gray-300 font-sans">enter</strong>
                <span>open</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <strong class="font-bold text-gray-700 dark:text-gray-300 font-sans">esc</strong>
                <span>back / close</span>
            </span>
        </div>
    </div>
</div>
