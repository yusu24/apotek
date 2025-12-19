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
    <nav x-data="{ open: false }" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-800 text-gray-100 md:flex flex-col hidden transition-all duration-300">
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center px-6 bg-gray-950/50 border-b border-gray-800">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 font-bold text-xl tracking-tight">
                <x-application-logo class="block h-8 w-auto fill-current text-blue-500" />
                <span>Apotek<span class="text-blue-500">.POS</span></span>
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-4 space-y-1 px-3">
            
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                {{ __('Dashboard') }}
            </x-sidebar-link>

            @can('view products')
            <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="cube">
                {{ __('Obat / Produk') }}
            </x-sidebar-link>
            @endcan

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
                        Pesanan (PO)
                    </a>
                    <a href="{{ route('procurement.goods-receipts.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('procurement.goods-receipts.*') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Penerimaan
                    </a>
                </div>
            </div>
            @endcan

            @can('access pos')
            <div class="pt-4 pb-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                Transaksi
            </div>
            <x-sidebar-link :href="route('pos.cashier')" :active="request()->routeIs('pos.*')" icon="shopping-cart">
                {{ __('Kasir (POS)') }}
            </x-sidebar-link>
            @endcan

            @can('view reports')
            <div class="pt-4 pb-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                Analitik
            </div>
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
            <div class="pt-4 pb-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                Pengaturan
            </div>
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
            <div class="pt-4 pb-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                Bantuan
            </div>
            <x-sidebar-link :href="route('guide.index')" :active="request()->routeIs('guide.*')" icon="book-open">
                {{ __('Panduan Aplikasi') }}
            </x-sidebar-link>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-gray-800 bg-gray-950/30">
            <div class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-gray-800 transition cursor-pointer group" x-data="{ open: false }">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-200 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                </div>
                <button 
                    @click="$store.theme.toggle()"
                    class="text-gray-500 hover:text-yellow-400 dark:hover:text-blue-400 transition mr-2"
                    title="Toggle Theme"
                >
                    <!-- Sun icon (show in dark mode) -->
                    <svg x-show="$store.theme.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <!-- Moon icon (show in light mode) -->
                    <svg x-show="!$store.theme.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                <button wire:click="logout" title="Logout" class="text-gray-500 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Header (Visible only on small screens) -->
    <div class="md:hidden bg-gray-900 text-white p-4 flex justify-between items-center w-full z-50">
        <div class="flex items-center gap-2">
            <x-application-logo class="block h-6 w-auto fill-current text-blue-500" />
            <span class="font-bold">Apotek.POS</span>
        </div>
        <button @click="open = !open" x-data="{ open: false }" class="text-gray-300 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </div>
</div>
