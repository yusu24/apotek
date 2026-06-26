<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $routeTitles = [
        'dashboard' => 'Dashboard',
        'profile' => 'Pengaturan Profil',
        
        // Products
        'products.index' => 'Data Obat / Produk',
        'products.create' => 'Tambah Obat / Produk',
        'products.edit' => 'Edit Obat / Produk',
        
        // Master
        'master.categories' => 'Kategori Produk',
        'master.product-units' => 'Konversi Satuan',
        'master.units' => 'Master Satuan',
        'master.suppliers' => 'Supplier',
        'master.customers' => 'Pelanggan',
        
        // Inventory
        'inventory.index' => 'Stok & Opname',
        'inventory.history' => 'Riwayat Transaksi Produk',
        'inventory.adjust' => 'Penyesuaian Stok',
        'inventory.returns.sales' => 'Retur Penjualan',
        'inventory.returns.purchase' => 'Retur Pembelian',
        
        // Reports
        'reports.sales' => 'Laporan Penjualan',
        'reports.sales-chart' => 'Grafik Penjualan',
        'reports.stock' => 'Laporan Stok',
        'reports.transaction-history' => 'Riwayat Transaksi Produk',
        'reports.product-margin' => 'Laporan Margin Produk',
        
        // Finance
        'finance.summary' => 'Ringkasan Keuangan',
        'finance.aging-report' => 'Hutang & Piutang',
        'finance.ppn-report' => 'Laporan PPN',
        'finance.profit-loss' => 'Laporan Laba Rugi',
        'finance.balance-sheet' => 'Neraca (Standar)',
        'finance.income-statement' => 'Laporan Arus Kas',
        'finance.expenses' => 'Pengeluaran',
        'finance.expense-categories' => 'Kategori Pengeluaran',
        'finance.opening-balance' => 'Neraca Saldo Awal',
        'finance.trial-balance' => 'Neraca Saldo Awal',
        'finance.cash-flow' => 'Laporan Arus Kas',
        'finance.assets' => 'Manajemen Aset Tetap',
        
        // Accounting
        'accounting.accounts.index' => 'Daftar Akun',
        'accounting.journals.index' => 'Jurnal Umum',
        'accounting.journals.create' => 'Tambah Jurnal Umum',
        'accounting.journals.edit' => 'Edit Jurnal Umum',
        'accounting.ledger' => 'Buku Besar',
        
        // Procurement
        'procurement.purchase-orders.index' => 'Pesanan Pembelian (PO)',
        'procurement.purchase-orders.create' => 'Buat PO Baru',
        'procurement.purchase-orders.view' => 'Lihat PO',
        'procurement.purchase-orders.edit' => 'Edit PO',
        'procurement.goods-receipts.index' => 'Penerimaan Pesanan',
        'procurement.goods-receipts.create' => 'Terima Pesanan Baru',
        'procurement.goods-receipts.edit' => 'Edit Penerimaan Pesanan',
        
        // Admin
        'admin.users.index' => 'Kelola User',
        'admin.users.create' => 'Tambah User',
        'admin.users.edit' => 'Edit User',
        'admin.roles.index' => 'Kelola Jabatan',
        'admin.roles.create' => 'Tambah Jabatan',
        'admin.roles.edit' => 'Edit Jabatan',
        'admin.backups' => 'Backup Data',
        'admin.activity-log' => 'Riwayat Aktivitas',
        
        // Guide
        'guide.index' => 'Panduan Aplikasi',
        'guide.detail' => 'Detail Panduan Aplikasi',
        'guide.handbook' => 'Buku Panduan',
    ];

    $currentRouteName = request()->route() ? request()->route()->getName() : '';
    $pageTitle = $routeTitles[$currentRouteName] ?? '';
    
    if (!$pageTitle && $currentRouteName) {
        $parts = explode('.', $currentRouteName);
        $lastPart = end($parts);
        $pageTitle = str_replace('-', ' ', ucwords($lastPart, '-'));
    }
    
    if (!$pageTitle) {
        $pageTitle = 'Apotek';
    }
@endphp

<div class="contents">
    <!-- Sidebar Navigation -->
    <nav id="sidebar" 
         class="sidebar-nav fixed inset-y-0 left-0 z-[60] bg-blue-950 border-r border-blue-800/50 text-gray-100 flex flex-col transition-all duration-300 xl:translate-x-0 -translate-x-full scrollbar-hide overflow-visible w-64"
         x-bind:class="$store.mobileNav.open ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'"
         >
        
        <!-- Sidebar Header -->
        <div class="h-16 flex flex-row items-center justify-between relative px-4 transition-all duration-300" x-bind:class="$store.sidebar.collapsed ? 'flex-col justify-center gap-1' : ''">
            <div class="w-8 xl:flex hidden" x-bind:class="{'xl:hidden': $store.sidebar.collapsed}"></div> <!-- Spacer for symmetry -->
            
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center text-xl tracking-tight transition-all duration-300 z-10" x-bind:class="$store.sidebar.collapsed ? 'w-full' : 'flex-1'">
                @if($logoPath = \App\Models\Setting::get('store_sidebar_logo_path'))
                    <img src="{{ asset('storage/' . $logoPath) }}" 
                         class="h-10 w-auto object-contain object-center transition-all duration-300" 
                         x-bind:class="$store.sidebar.collapsed ? 'h-7 max-w-[40px]' : 'h-10 w-auto scale-110'" 
                         alt="Logo">
                @else
                    <x-application-logo 
                         class="block fill-current text-blue-500 transition-all duration-300" 
                         x-bind:class="$store.sidebar.collapsed ? 'h-6 w-6' : 'h-8 w-auto'" />
                @endif
            </a>
            
            <!-- Desktop Toggle Button -->
            <button @click="$store.sidebar.toggle()" 
                    class="hidden xl:flex w-5 h-5 bg-blue-600 text-white rounded-full items-center justify-center hover:bg-blue-700 transition-all shadow-sm shrink-0 z-20"
                    title="Toggle Sidebar"
                    x-bind:class="$store.sidebar.collapsed ? 'mx-auto' : ''">
                <svg class="w-3 h-3 transition-transform duration-500" x-bind:style="$store.sidebar.collapsed ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Close button for mobile -->
            <button @click.prevent="$store.mobileNav.close()" class="xl:hidden absolute right-4 text-white hover:text-white transition-colors p-1 z-20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-4 space-y-1 px-3 scrollbar-hide">
            @can('view dashboard')
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                Dashboard
            </x-sidebar-link>
            @endcan


            @can('access pos')
            <x-sidebar-link :href="route('pos.cashier')" :active="request()->routeIs('pos.*')" icon="shopping-cart">
                Kasir (POS)
            </x-sidebar-link>
            @endcan

            <!-- Inventory & Procurement Group -->
            @canany(['view stock', 'import stock', 'adjust stock', 'view purchase orders', 'view goods receipts', 'manage expired products'])
            @php
                $isStockActive = (request()->routeIs('inventory.*') || request()->routeIs('procurement.*')) && !request()->routeIs('inventory.returns.*');
            @endphp
            <div x-data="{ expanded: {{ $isStockActive ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ $isStockActive ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" x-bind:class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="truncate" x-bind:class="{'xl:hidden': $store.sidebar.collapsed}">Stok & Pengadaan</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" x-bind:class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('view stock')
                    <a href="{{ route('inventory.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('inventory.index') || (request()->routeIs('inventory.*') && !request()->routeIs('inventory.returns.*')) ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Stok & Opname</span>
                    </a>
                    @endcan

                    @can('view purchase orders')
                    <a href="{{ route('procurement.purchase-orders.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('procurement.purchase-orders.*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Pesanan Pembelian (PO)</span>
                    </a>
                    @endcan

                    @can('view goods receipts')
                    <a href="{{ route('procurement.goods-receipts.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('procurement.goods-receipts.*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Penerimaan Pesanan</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Returns Group -->
            @canany(['manage sales returns', 'manage purchase returns'])
            <div x-data="{ expanded: {{ request()->routeIs('inventory.returns.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('inventory.returns.*') ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Retur Barang</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('manage sales returns')
                    <a href="{{ route('inventory.returns.sales') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('inventory.returns.sales') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Retur Penjualan</span>
                    </a>
                    @endcan

                    @can('manage purchase returns')
                    <a href="{{ route('inventory.returns.purchase') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('inventory.returns.purchase') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4 4m0 0l4-4m-4 4v8"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Retur Pembelian</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Products Group (Master Data) -->
            @canany(['view products', 'manage categories', 'manage units', 'manage product units', 'manage suppliers', 'manage customers'])
            <div x-data="{ expanded: {{ request()->routeIs('products.*') || request()->routeIs('master.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('products.*') || request()->routeIs('master.*') ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Data Master</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('view products')
                    <a href="{{ route('products.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('products.*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Obat / Produk</span>
                    </a>
                    @endcan
                    
                    @can('manage categories')
                    <a href="{{ route('master.categories') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('master.categories') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 7.586V3a1 1 0 011-1zm0 6h.01"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Kategori Produk</span>
                    </a>
                    @endcan

                    @can('manage units')
                    <a href="{{ route('master.units') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('master.units') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Master Satuan</span>
                    </a>
                    @endcan

                    @can('manage product units')
                    <a href="{{ route('master.product-units') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('master.product-units') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Konversi Satuan</span>
                    </a>
                    @endcan

                    @can('manage suppliers')
                    <a href="{{ route('master.suppliers') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('master.suppliers') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Supplier</span>
                    </a>
                    @endcan

                    @can('manage customers')
                    <a href="{{ route('master.customers') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('master.customers') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Pelanggan</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany
 
            <!-- Group: Laporan Keuangan -->
            @canany(['view trial balance', 'view balance sheet', 'view profit loss', 'view income statement', 'view general ledger', 'view ppn report', 'view ap aging report'])
            @php
                $isFinanceReportActive = request()->routeIs([
                                     'finance.income-statement', 
                                     'finance.cash-flow', 
                                     'finance.trial-balance', 
                                     'finance.ppn-report', 
                                     'finance.aging-report', 
                                     'finance.balance-sheet',
                                     'finance.profit-loss',
                                     'accounting.ledger'
                                 ]);
            @endphp
            <div x-data="{ expanded: {{ $isFinanceReportActive ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ $isFinanceReportActive ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Keuangan</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('view trial balance')
                    <a href="{{ route('finance.trial-balance') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.trial-balance') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Neraca Saldo Awal</span>
                    </a>
                    @endcan

                    @can('view balance sheet')
                    <a href="{{ route('finance.balance-sheet') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.balance-sheet') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Neraca (Standar)</span>
                    </a>
                    @endcan

                    @can('view profit loss')
                    <a href="{{ route('finance.profit-loss') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.profit-loss') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Laba Rugi</span>
                    </a>
                    @endcan

                    @can('view income statement')
                    <a href="{{ route('finance.cash-flow') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.cash-flow') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Arus Kas</span>
                    </a>
                    @endcan

                    @can('view general ledger')
                    <a href="{{ route('accounting.ledger') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.ledger') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Buku Besar</span>
                    </a>
                    @endcan

                    @can('view ppn report')
                    <a href="{{ route('finance.ppn-report') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.ppn-report') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1-0 .5.5 0 011 0zm5 5a.5.5 0 11-1-0 .5.5 0 011 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan PPN</span>
                    </a>
                    @endcan

                    @can('view ap aging report')
                    <a href="{{ route('finance.aging-report') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.aging-report') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Hutang & Piutang</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Group: Laporan Operasional -->
            @canany(['view reports', 'view sales reports', 'view stock', 'view product margin report', 'view stock movements'])
            @php
                $isOperationalReportActive = request()->routeIs('reports.*') && !request()->routeIs(['finance.*', 'accounting.*']);
            @endphp
            <div x-data="{ expanded: {{ $isOperationalReportActive ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ $isOperationalReportActive ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Operasional</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('view stock')
                    <a href="{{ route('reports.stock') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.stock') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Stok</span>
                    </a>
                    @endcan

                    @can('view sales reports')
                    <a href="{{ route('reports.sales') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.sales') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Penjualan</span>
                    </a>
                    @endcan

                    @can('view stock movements')
                    <a href="{{ route('reports.transaction-history') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.transaction-history') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Riwayat Transaksi Produk</span>
                    </a>
                    @endcan

                    @can('view product margin report')
                    <a href="{{ route('reports.product-margin') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.product-margin') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Laporan Margin Produk</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Group: Keuangan & Administrasi -->
            @canany(['view accounts', 'manage accounts', 'view journals', 'view opening balances', 'edit opening balances', 'lock opening balances', 'unlock opening balances', 'view expenses', 'manage expense categories', 'manage finance'])
            @php
                $isFinanceActive = request()->routeIs([
                                     'finance.opening-balance', 
                                     'accounting.journals.*', 
                                     'accounting.accounts.*', 
                                     'finance.expenses', 
                                     'finance.expense-categories', 
                                     'finance.assets'
                                   ]);
            @endphp
            <div x-data="{ expanded: {{ $isFinanceActive ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ $isFinanceActive ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Keuangan & Administrasi</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('view accounts')
                    <a href="{{ route('accounting.accounts.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.accounts.*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Daftar Akun</span>
                    </a>
                    @endcan

                    @canany(['edit opening balances', 'view opening balances', 'lock opening balances', 'unlock opening balances'])
                    <a href="{{ route('finance.opening-balance') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.opening-balance') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Neraca Saldo Awal</span>
                    </a>
                    @endcanany

                    @can('view journals')
                    <a href="{{ route('accounting.journals.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.journals.*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2-2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Jurnal Umum</span>
                    </a>
                    @endcan

                    @can('view expenses')
                    <a href="{{ route('finance.expenses') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.expenses') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Pengeluaran</span>
                    </a>
                    @endcan

                    @can('manage expense categories')
                    <a href="{{ route('finance.expense-categories') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.expense-categories') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 7.586V3a1 1 0 011-1zm0 6h.01"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Kategori Pengeluaran</span>
                    </a>
                    @endcan

                    @can('manage finance')
                    <a href="{{ route('finance.assets') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('finance.assets') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Manajemen Aset Tetap</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            @canany(['manage settings', 'manage pos settings', 'manage users', 'view activity logs', 'manage backups', 'view online users', 'view notifications'])
            <div x-data="{ expanded: {{ request()->routeIs('settings.*') || request()->routeIs('admin.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('settings.*') || request()->routeIs('admin.*') ? 'text-white' : 'text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Pengaturan Sistem</span>
                    </div>
                    <svg x-show="!$store.sidebar.collapsed" :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('manage settings')
                    <a href="{{ route('settings.store') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.store') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Identitas Toko</span>
                    </a>
                    @endcan

                    @can('manage pos settings')
                    <a href="{{ route('settings.pos') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.pos') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Pengaturan Kasir</span>
                    </a>
                    @endcan
                    
                    @can('manage users')
                    <a href="{{ route('admin.users.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Kelola User</span>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.roles*') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Kelola Jabatan</span>
                    </a>
                    @can('manage backups')
                    <a href="{{ route('admin.backups') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.backups') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Backup Data</span>
                    </a>
                    @endcan

                    @endcan

                    @can('view activity logs')
                    <a href="{{ route('admin.activity-log') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.activity-log') ? 'text-white bg-blue-800' : 'text-white hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-300" :class="$store.sidebar.collapsed ? 'xl:w-[26px] xl:h-[26px]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="truncate" :class="{'xl:hidden': $store.sidebar.collapsed}">Riwayat Aktivitas</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- User Guide (Scrollable) -->
            <x-sidebar-link :href="route('guide.index')" :active="request()->routeIs('guide.*')" icon="book-open">
                Panduan Aplikasi
            </x-sidebar-link>
        </div>
    </nav>

    <!-- Desktop Fixed Top Navbar -->
    @if(!request()->routeIs('pos.cashier'))
    <div id="desktop-navbar"
         class="fixed top-0 right-0 z-40 h-12 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 hidden xl:flex items-center justify-between px-6 transition-all duration-300"
         x-bind:style="$store.sidebar.collapsed ? 'left: 5rem' : 'left: 16rem'">
        <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">
                {{ $pageTitle }}
            </h1>
        </div>

        <!-- Right Section: Profile & Actions -->
        <div class="flex items-center gap-4">
            @can('view online users')
            <div class="text-gray-600 dark:text-gray-300">
                <livewire:layout.online-users :iconOnly="true" direction="down" textColor="text-gray-600 dark:text-gray-300" />
            </div>
            @endcan

            @can('view notifications')
            <div class="text-gray-600 dark:text-gray-300">
                <livewire:layout.notification-bell :iconOnly="true" direction="down" textColor="text-gray-600 dark:text-gray-300" />
            </div>
            @endcan
            
            <div class="my-1 border-r border-gray-200 dark:border-gray-800 h-6"></div>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <!-- Trigger Button -->
                <button @click="open = !open" class="flex items-center gap-2.5 px-2 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-left focus:outline-none">
                    <!-- Initials / Avatar Circle -->
                    <div class="relative shrink-0">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-205 dark:ring-gray-800 shrink-0">
                        @else
                            <div class="w-8 h-8 rounded-full bg-blue-950 flex items-center justify-center text-white font-bold text-xs shrink-0">
                                @php
                                    $words = explode(' ', auth()->user()->name);
                                    $initials = '';
                                    if (count($words) >= 2) {
                                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr(auth()->user()->name, 0, 2));
                                    }
                                @endphp
                                {{ $initials }}
                            </div>
                        @endif

                        @if(session()->has('impersonator_id'))
                            <!-- Pulsing amber dot for active impersonation -->
                            <span class="absolute bottom-0 right-0 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-600 ring-2 ring-white dark:ring-gray-900"></span>
                            </span>
                        @endif
                    </div>
                    
                    <!-- User Details -->
                    <div class="hidden sm:block">
                        <p class="text-xs font-medium text-blue-950 dark:text-white leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-gray-500 dark:text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                    </div>

                    <!-- Down Chevron -->
                    <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden">
                    
                    <div class="p-1">
                        <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Pengaturan Profil
                        </a>

                        <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                        @if(session()->has('impersonator_id'))
                            <a href="{{ route('admin.leave-impersonation') }}" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-amber-600 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-950/20 rounded-lg transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar Impersonate
                            </a>
                        @else
                            <button wire:click="logout" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mobile Header -->
    <div class="xl:hidden bg-blue-950 text-white h-12 flex justify-between items-center w-full z-50 fixed top-0 left-0 right-0 shadow-lg px-4">
        <div class="flex items-center gap-2">
            @if($logoPath = \App\Models\Setting::get('store_sidebar_logo_path'))
                <img src="{{ asset('storage/' . $logoPath) }}" class="h-8 w-auto object-contain" alt="Logo">
            @else
                <x-application-logo class="block h-6 w-auto fill-current text-blue-500" />
            @endif
        </div>
        <div class="flex items-center gap-3">
            @can('view online users')
            <livewire:layout.online-users :iconOnly="true" direction="down" textColor="text-white" />
            @endcan
            @can('view notifications')
            <livewire:layout.notification-bell :iconOnly="true" direction="down" />
            @endcan
            <button @click="$store.mobileNav.toggle()" class="text-gray-300 hover:text-white transition-colors p-1">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Backdrop -->
    <div x-show="$store.mobileNav.open" 
         @click="$store.mobileNav.close()"
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[55] xl:hidden">
    </div>
</div>
