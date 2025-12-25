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

<div class="contents">
    <!-- Sidebar Navigation -->
    <nav id="sidebar" 
         class="fixed inset-y-0 left-0 z-[60] w-64 bg-gray-900 border-r border-gray-800 text-gray-100 flex flex-col transition-all duration-300 xl:translate-x-0"
         :class="$store.mobileNav.open ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'">
        
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-6 bg-gray-950/50 border-b border-gray-800">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 font-bold text-xl tracking-tight">
                @if($logoPath = \App\Models\Setting::get('store_sidebar_logo_path'))
                    <img src="{{ asset('storage/' . $logoPath) }}" class="max-h-10 w-auto object-contain" alt="Logo">
                @else
                    <x-application-logo class="block h-8 w-auto fill-current text-blue-500" />
                @endif
                <span>Apotek<span class="text-blue-500">.POS</span></span>
            </a>
            <!-- Close button for mobile -->
            <button @click="$store.mobileNav.close()" class="md:hidden text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-4 space-y-1 px-3">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                {{ __('Dashboard') }}
            </x-sidebar-link>

            @canany(['view products', 'manage product units'])
            <div x-data="{ expanded: {{ request()->routeIs('products.*') || request()->routeIs('master.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('products.*') || request()->routeIs('master.*') ? 'text-white' : 'text-gray-400' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        <span>Pengaturan Produk</span>
                    </div>
                    <svg :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    @can('view products')
                    <a href="{{ route('products.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('products.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Obat / Produk
                    </a>
                    @endcan
                    
                    @can('manage product units')
                    <a href="{{ route('master.categories') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.categories') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 7.586V3a1 1 0 011-1zm0 6h.01"></path></svg>
                        Kategori Obat
                    </a>
                    @endcan

                    @can('manage product units')
                    <a href="{{ route('master.product-units') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('master.product-units') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        Satuan Produk
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            @can('view stock')
            <x-sidebar-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')" icon="archive-box">
                {{ __('Stok & Opname') }}
            </x-sidebar-link>
            @endcan
            
            @can('view stock')
            <div x-data="{ expanded: {{ request()->routeIs('procurement.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('procurement.*') ? 'text-white' : 'text-gray-400' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span>Pengadaan</span>
                    </div>
                    <svg :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    <a href="{{ route('procurement.purchase-orders.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('procurement.purchase-orders.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Pesanan Pembelian (PO)
                    </a>
                    <a href="{{ route('procurement.goods-receipts.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('procurement.goods-receipts.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Penerimaan Pesanan
                    </a>
                </div>
            </div>
            @endcan

            @can('access pos')

            <x-sidebar-link :href="route('pos.cashier')" :active="request()->routeIs('pos.*')" icon="shopping-cart">
                {{ __('Kasir (POS)') }}
            </x-sidebar-link>
            @endcan

            @can('view reports')

            <x-sidebar-link :href="route('reports.sales')" :active="request()->routeIs('reports.*')" icon="chart-bar">
                {{ __('Laporan Penjualan') }}
            </x-sidebar-link>
            @endcan

            @can('view finance')
            <div x-data="{ expanded: {{ request()->routeIs('finance.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('finance.*') ? 'text-white' : 'text-gray-400' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Keuangan</span>
                    </div>
                    <svg :class="{'rotate-90': expanded}" class="w-4 h-4 transition-transform text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="expanded" class="mt-1 space-y-1 pl-3" x-collapse>
                    <a href="{{ route('finance.expenses') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('finance.expenses') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Pengeluaran
                    </a>
                    <a href="{{ route('finance.profit-loss') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('finance.profit-loss') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Laba Rugi
                    </a>
                </div>
            </div>
            @endcan

            @canany(['manage settings', 'manage users'])

            @can('manage settings')
            <x-sidebar-link :href="route('settings.store')" :active="request()->routeIs('settings.*')" icon="cog">
                {{ __('Toko') }}
            </x-sidebar-link>
            @endcan
            
            @can('manage users')
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users*')" icon="users">
                {{ __('User Management') }}
            </x-sidebar-link>
            @endcan
            @endcanany

            <!-- User Guide -->

            <x-sidebar-link :href="route('guide.index')" :active="request()->routeIs('guide.*')" icon="book-open">
                {{ __('Panduan Aplikasi') }}
            </x-sidebar-link>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-gray-800 bg-gray-950/30">
            <div class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer group">
                <a href="{{ route('profile') }}" wire:navigate class="flex flex-1 items-center gap-3 min-w-0">
                    @if (auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-200 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                    </div>
                </a>
                <button 
                    @click="$store.theme.toggle()"
                    class="text-gray-500 hover:text-yellow-400 dark:hover:text-blue-400 transition mr-2"
                    title="Toggle Theme"
                >
                    <svg x-show="$store.theme.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!$store.theme.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                <button wire:click="logout" title="Logout" class="text-gray-500 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Header -->
    <div class="xl:hidden bg-gray-900 text-white p-4 flex justify-between items-center w-full z-50 fixed top-0 left-0 right-0 shadow-lg">
        <div class="flex items-center gap-2">
            @if($logoPath = \App\Models\Setting::get('store_sidebar_logo_path'))
                <img src="{{ asset('storage/' . $logoPath) }}" class="max-h-8 w-auto object-contain" alt="Logo">
            @else
                <x-application-logo class="block h-6 w-auto fill-current text-blue-500" />
            @endif
            <span class="font-bold">Apotek.POS</span>
        </div>
        <button @click="$store.mobileNav.toggle()" class="text-gray-300 hover:text-white transition-colors p-1">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
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
