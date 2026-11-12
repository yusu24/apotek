<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                Laporan Selisih Stok Opname
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Rekapitulasi stok minus, stok plus, dan total selisih antara stok fisik dan stok sistem
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
            <a href="{{ route('inventory.index') }}" wire:navigate class="btn btn-secondary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Halaman Stok & Opname</span>
            </a>

            <div class="relative btn-export-dropdown" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="btn btn-export-excel" title="Export Laporan">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="hidden sm:inline ml-1">Export</span>
                    <svg class="w-3 h-3 ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="dropdown-menu" x-show="open" x-cloak style="display:none">
                    <button wire:click="exportExcel" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                        Excel (.xlsx)
                    </button>
                    <a href="{{ route('pdf.stock-opname-variance', ['startDate' => $startDate, 'endDate' => $endDate, 'categoryFilter' => $categoryFilter, 'varianceType' => $varianceType, 'search' => $search]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Header -->
    <div class="hidden print:block">
        <div class="border-b-2 border-gray-900 pb-2 text-center uppercase">
            <h2 class="text-xl font-bold text-gray-800">LAPORAN SELISIH STOK OPNAME</h2>
            <div class="text-[12px] font-bold mt-1">
                PERIODE: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'AWAL' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'AKHIR' }}
            </div>
            <div class="text-[9px] text-gray-500 mt-1 italic normal-case">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <!-- Summary Cards: 1. Stok Minus, 2. Stok Plus, 3A. Total Selisih Bersih, 3B. Total Akumulasi Selisih -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 no-print">
        <!-- 1. Stok Minus -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-rose-100 dark:border-rose-900/40 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">1. Stok Minus (Fisik &lt; Sistem)</span>
                    <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-rose-600 dark:text-rose-400 tracking-tight tabular-nums">
                    -{{ number_format($stats['minus_qty'], 0, ',', '.') }}
                    <span class="text-xs font-medium text-gray-400">Unit</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs border-t border-gray-100 dark:border-gray-700 pt-2">
                    <span class="text-gray-500 dark:text-gray-400">Nilai HPP:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400 tabular-nums">-Rp {{ number_format($stats['minus_value'], 0, ',', '.') }}</span>
                </div>
                <div class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                    Dari <strong>{{ number_format($stats['minus_items']) }}</strong> produk yang berkurang
                </div>
            </div>
            <div class="w-full h-1 bg-rose-100 dark:bg-rose-950 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-rose-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- 2. Stok Plus -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-100 dark:border-emerald-900/40 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">2. Stok Plus (Fisik &gt; Sistem)</span>
                    <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight tabular-nums">
                    +{{ number_format($stats['plus_qty'], 0, ',', '.') }}
                    <span class="text-xs font-medium text-gray-400">Unit</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs border-t border-gray-100 dark:border-gray-700 pt-2">
                    <span class="text-gray-500 dark:text-gray-400">Nilai HPP:</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">+Rp {{ number_format($stats['plus_value'], 0, ',', '.') }}</span>
                </div>
                <div class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                    Dari <strong>{{ number_format($stats['plus_items']) }}</strong> produk yang bertambah
                </div>
            </div>
            <div class="w-full h-1 bg-emerald-100 dark:bg-emerald-950 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- 3A. Total Selisih Bersih (Nett: Plus - Minus) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-blue-100 dark:border-blue-900/40 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">3A. Total Selisih Bersih (Nett)</span>
                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold tracking-tight tabular-nums {{ $stats['net_qty'] < 0 ? 'text-rose-600 dark:text-rose-400' : ($stats['net_qty'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                    {{ $stats['net_qty'] > 0 ? '+' : '' }}{{ number_format($stats['net_qty'], 0, ',', '.') }}
                    <span class="text-xs font-medium text-gray-400">Unit</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs border-t border-gray-100 dark:border-gray-700 pt-2">
                    <span class="text-gray-500 dark:text-gray-400">Selisih Nilai:</span>
                    <span class="font-bold tabular-nums {{ $stats['net_value'] < 0 ? 'text-rose-600 dark:text-rose-400' : ($stats['net_value'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                        {{ $stats['net_value'] > 0 ? '+' : ($stats['net_value'] < 0 ? '-' : '') }}Rp {{ number_format(abs($stats['net_value']), 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                    Rumus: <strong>Stok Plus - Stok Minus</strong>
                </div>
            </div>
            <div class="w-full h-1 bg-blue-100 dark:bg-blue-950 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- 3B. Total Akumulasi Selisih (Absolut: Plus + Minus) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-amber-100 dark:border-amber-900/40 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">3B. Total Akumulasi Selisih</span>
                    <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight tabular-nums">
                    {{ number_format($stats['abs_qty'], 0, ',', '.') }}
                    <span class="text-xs font-medium text-gray-400">Unit Berbeda</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs border-t border-gray-100 dark:border-gray-700 pt-2">
                    <span class="text-gray-500 dark:text-gray-400">Total Nilai Absolut:</span>
                    <span class="font-bold text-amber-700 dark:text-amber-400 tabular-nums">Rp {{ number_format($stats['abs_value'], 0, ',', '.') }}</span>
                </div>
                <div class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                    Total <strong>{{ number_format($stats['total_records']) }}</strong> transaksi opname (Plus + Minus)
                </div>
            </div>
            <div class="w-full h-1 bg-amber-100 dark:bg-amber-950 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden no-print">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 flex flex-col md:flex-row justify-between items-end gap-4">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1 md:items-end flex-wrap">
                <div class="flex-1 min-w-[200px] w-full md:w-auto">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Cari Produk / Referensi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input wire:model.live.debounce.300ms="search" 
                            type="text" placeholder="Nama produk, barcode, ref, batch..." 
                            class="w-full pl-10 pr-4 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                    </div>
                </div>

                <div class="w-full md:w-40 shrink-0">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jenis Selisih</label>
                    <select wire:model.live="varianceType" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 pr-8 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                        <option value="all">Semua Selisih</option>
                        <option value="minus">Stok Minus (-)</option>
                        <option value="plus">Stok Plus (+)</option>
                    </select>
                </div>

                <div class="w-full md:w-40 shrink-0">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                    <select wire:model.live="categoryFilter" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 pr-8 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                        <x-date-picker wire:model.live="startDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white"></x-date-picker>
                    </div>
                    <span class="text-gray-400 font-bold self-end mb-2">-</span>
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                        <x-date-picker wire:model.live="endDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white"></x-date-picker>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex items-center">
                <button wire:click="resetFilters" class="btn btn-sm btn-secondary" title="Reset Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse custom-print-table">
                <thead class="bg-blue-950 text-white font-semibold uppercase text-xs tracking-wider border-b border-blue-900 print:bg-transparent print:text-black">
                    <tr>
                        <th class="px-4 py-3 text-left">No.</th>
                        <th wire:click="sortByColumn('created_at')" class="px-4 py-3 cursor-pointer hover:bg-blue-900 transition-colors whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                Tanggal
                                @if($sortBy === 'created_at')
                                    <svg class="w-3.5 h-3.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 whitespace-nowrap">No. Ref</th>
                        <th wire:click="sortByColumn('product')" class="px-4 py-3 cursor-pointer hover:bg-blue-900 transition-colors">
                            <div class="flex items-center gap-1">
                                Produk
                                @if($sortBy === 'product')
                                    <svg class="w-3.5 h-3.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 whitespace-nowrap">Batch</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Jenis</th>
                        <th wire:click="sortByColumn('quantity')" class="px-4 py-3 text-right cursor-pointer hover:bg-blue-900 transition-colors whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1">
                                Qty Selisih
                                @if($sortBy === 'quantity')
                                    <svg class="w-3.5 h-3.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">HPP / Satuan</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Nilai Selisih</th>
                        <th class="px-4 py-3">Keterangan / Alasan</th>
                        <th class="px-4 py-3 whitespace-nowrap">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @php $index = ($movements->currentPage() - 1) * $movements->perPage() + 1; @endphp
                    @forelse($movements as $item)
                        @php
                            $qty = (int) $item->quantity;
                            $unitHpp = (float) ($item->unit_hpp ?? 0);
                            $val = $qty * $unitHpp;
                            $isMinus = $qty < 0;
                        @endphp
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $index++ }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-gray-500 dark:text-gray-400">
                                {{ $item->doc_ref ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->product->name ?? '-' }}</div>
                                <div class="text-xs text-gray-400 font-mono">
                                    {{ $item->product->barcode ?? '-' }}
                                    @if(optional($item->product->category)->name)
                                        &bull; <span class="font-sans">{{ $item->product->category->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-gray-600 dark:text-gray-400">
                                {{ $item->batch->batch_no ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($isMinus)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        Stok Minus
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        Stok Plus
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-bold tabular-nums {{ $isMinus ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $qty > 0 ? '+' : '' }}{{ number_format($qty, 0, ',', '.') }}
                                <span class="text-xs font-normal text-gray-400">{{ $item->product->unit->name ?? 'pcs' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-400 tabular-nums">
                                Rp {{ number_format($unitHpp, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-bold tabular-nums {{ $isMinus ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $val > 0 ? '+' : ($val < 0 ? '-' : '') }}Rp {{ number_format(abs($val), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate" title="{{ $item->description }}">
                                {{ $item->description ?: '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->user->name ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <x-empty-table colspan="11" message="Belum ada data penyesuaian / selisih stok opname pada periode ini." />
                    @endforelse
                </tbody>
                @if($movements->count() > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-900/60 border-t-2 border-gray-200 dark:border-gray-700 text-xs">
                        <tr class="font-semibold text-rose-600 dark:text-rose-400 border-b border-gray-100 dark:border-gray-800">
                            <td colspan="6" class="px-4 py-2 text-right uppercase">1. Total Stok Minus (Fisik &lt; Sistem):</td>
                            <td class="px-4 py-2 text-right tabular-nums">-{{ number_format($stats['minus_qty'], 0, ',', '.') }} Unit</td>
                            <td></td>
                            <td class="px-4 py-2 text-right tabular-nums">-Rp {{ number_format($stats['minus_value'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="font-semibold text-emerald-600 dark:text-emerald-400 border-b border-gray-100 dark:border-gray-800">
                            <td colspan="6" class="px-4 py-2 text-right uppercase">2. Total Stok Plus (Fisik &gt; Sistem):</td>
                            <td class="px-4 py-2 text-right tabular-nums">+{{ number_format($stats['plus_qty'], 0, ',', '.') }} Unit</td>
                            <td></td>
                            <td class="px-4 py-2 text-right tabular-nums">+Rp {{ number_format($stats['plus_value'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="font-bold text-gray-900 dark:text-white bg-blue-50/50 dark:bg-blue-950/30 border-b border-gray-100 dark:border-gray-800">
                            <td colspan="6" class="px-4 py-2.5 text-right uppercase">3A. Total Selisih Bersih (Stok Plus - Stok Minus):</td>
                            <td class="px-4 py-2.5 text-right tabular-nums {{ $stats['net_qty'] < 0 ? 'text-rose-600' : ($stats['net_qty'] > 0 ? 'text-emerald-600' : '') }}">
                                {{ $stats['net_qty'] > 0 ? '+' : '' }}{{ number_format($stats['net_qty'], 0, ',', '.') }} Unit
                            </td>
                            <td></td>
                            <td class="px-4 py-2.5 text-right tabular-nums {{ $stats['net_value'] < 0 ? 'text-rose-600' : ($stats['net_value'] > 0 ? 'text-emerald-600' : '') }}">
                                {{ $stats['net_value'] > 0 ? '+' : ($stats['net_value'] < 0 ? '-' : '') }}Rp {{ number_format(abs($stats['net_value']), 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="font-bold text-amber-800 dark:text-amber-300 bg-amber-50/40 dark:bg-amber-950/20">
                            <td colspan="6" class="px-4 py-2.5 text-right uppercase">3B. Total Akumulasi Selisih (Stok Plus + Stok Minus):</td>
                            <td class="px-4 py-2.5 text-right tabular-nums">{{ number_format($stats['abs_qty'], 0, ',', '.') }} Unit</td>
                            <td></td>
                            <td class="px-4 py-2.5 text-right tabular-nums">Rp {{ number_format($stats['abs_value'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 no-print">
            @include('components.custom-pagination', ['items' => $movements])
        </div>
    </div>

    <!-- Explanation & Reconciliation Box -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 no-print">
        <div class="md:col-span-2 bg-gray-50 dark:bg-gray-900/20 p-5 rounded-2xl border border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400 space-y-2">
            <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Keterangan Istilah & Rumus Selisih Stok Opname</h4>
            <p>&bull; <strong>1. Stok Minus (Kurang)</strong>: Total barang yang jumlah fisiknya lebih sedikit dibandingkan stok di sistem (penyesuaian pengurangan stok).</p>
            <p>&bull; <strong>2. Stok Plus (Lebih)</strong>: Total barang yang jumlah fisiknya lebih banyak dibandingkan stok di sistem (penyesuaian penambahan stok).</p>
            <p>&bull; <strong>3A. Total Selisih Bersih (Nett)</strong>: Selisih bersih antara penambahan dan pengurangan stok (Rumus: <strong>Stok Plus - Stok Minus</strong>). Menunjukkan dampak bersih terhadap nilai persediaan.</p>
            <p>&bull; <strong>3B. Total Akumulasi Selisih (Absolut)</strong>: Jumlah keseluruhan perbedaan unit antara stok fisik dan sistem tanpa saling menghilangkan (Rumus: <strong>Stok Plus + |Stok Minus|</strong>). Menunjukkan tingkat akurasi stok fisik terhadap sistem.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-3">
            <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">
                Ringkasan Rekonsiliasi Opname
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-rose-600 dark:text-rose-400">
                    <span>Stok Minus ({{ $stats['minus_items'] }} produk)</span>
                    <span class="font-bold tabular-nums">-{{ number_format($stats['minus_qty'], 0, ',', '.') }} unit</span>
                </div>
                <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                    <span>Stok Plus ({{ $stats['plus_items'] }} produk)</span>
                    <span class="font-bold tabular-nums">+{{ number_format($stats['plus_qty'], 0, ',', '.') }} unit</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-dashed border-gray-200 dark:border-gray-700 font-bold text-gray-900 dark:text-white">
                    <span>Total Selisih Bersih</span>
                    <span class="tabular-nums {{ $stats['net_qty'] < 0 ? 'text-rose-600' : ($stats['net_qty'] > 0 ? 'text-emerald-600' : '') }}">
                        {{ $stats['net_qty'] > 0 ? '+' : '' }}{{ number_format($stats['net_qty'], 0, ',', '.') }} unit
                    </span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700 font-bold text-amber-700 dark:text-amber-400">
                    <span>Total Akumulasi Selisih</span>
                    <span class="tabular-nums">{{ number_format($stats['abs_qty'], 0, ',', '.') }} unit</span>
                </div>
            </div>
        </div>
    </div>
</div>
