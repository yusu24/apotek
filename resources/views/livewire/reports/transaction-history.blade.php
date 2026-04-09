<div class="p-6 space-y-6">
    <div class="no-print flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Riwayat Transaksi') }}
            </h2>
            <p class="text-sm text-gray-500">Riwayat lengkap mutasi stok obat per item.</p>
        </div>
        <a href="{{ route('pdf.transaction-history', ['startDate' => $startDate, 'endDate' => $endDate, 'type' => $type, 'search' => $search]) }}" 
           target="_blank"
           class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span class="hidden sm:inline">Export PDF</span>
        </a>
    </div>
            
    <div class="bg-white rounded-xl shadow border overflow-hidden no-print">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-end gap-4">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1 md:items-end">
                <div class="flex flex-col shrink-0">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Tampilkan</label>
                    <select wire:model.live="perPage" class="border-gray-300 rounded-lg py-2 content-center pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all bg-white">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px] w-full">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Cari Produk</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input wire:model.live.debounce.300ms="search" 
                            type="text" placeholder="Nama atau barcode..." 
                            class="w-full pl-10 pr-4 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                    </div>
                </div>

                <div class="w-full md:w-36 shrink-0">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Tipe</label>
                    <select wire:model.live="type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 pr-8 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                        <option value="all">Semua Tipe</option>
                        <option value="sale">Penjualan</option>
                        <option value="in">Masuk (Beli)</option>
                        <option value="adjustment">Opname</option>
                        <option value="return">Retur Jual</option>
                        <option value="return-supplier">Retur Beli</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                    <div class="flex flex-col">
                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Periode Dari</label>
                        <x-date-picker wire:model.live="startDate" class="w-full md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm"></x-date-picker>
                    </div>
                    <span class="text-gray-400 font-bold self-end mb-2">-</span>
                    <div class="flex flex-col">
                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Sampai</label>
                        <x-date-picker wire:model.live="endDate" class="w-full md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm"></x-date-picker>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex items-center">
                <button wire:click="resetFilters" class="text-xs text-blue-600 font-bold hover:text-blue-800 transition py-2" title="Reset Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr class="text-xs font-bold uppercase text-gray-500 tracking-wider">
                        <th wire:click="sortByColumn('created_at')" class="px-6 py-4 cursor-pointer hover:bg-gray-100/50 transition-colors">
                            <div class="flex items-center gap-1">
                                Tanggal
                                @if($sortBy === 'created_at')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortByColumn('product')" class="px-6 py-4 cursor-pointer hover:bg-gray-100/50 transition-colors">
                            <div class="flex items-center gap-1">
                                Produk
                                @if($sortBy === 'product')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortByColumn('type')" class="px-6 py-4 cursor-pointer hover:bg-gray-100/50 transition-colors">
                            <div class="flex items-center gap-1">
                                Tipe
                                @if($sortBy === 'type')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4">Ref / Batch</th>
                        <th wire:click="sortByColumn('quantity')" class="px-6 py-4 text-right cursor-pointer hover:bg-gray-100/50 transition-colors">
                            <div class="flex items-center justify-end gap-1">
                                Qty
                                @if($sortBy === 'quantity')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4">Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($transactions as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs">{{ $item->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->product->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->product->barcode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $colors = [
                                        'sale' => 'text-blue-700 bg-blue-50 border-blue-100',
                                        'in' => 'text-green-700 bg-green-50 border-green-100',
                                        'adjustment' => 'text-amber-700 bg-amber-50 border-amber-100',
                                        'return' => 'text-purple-700 bg-purple-50 border-purple-100',
                                        'return-supplier' => 'text-red-700 bg-red-50 border-red-100',
                                    ];
                                    $typeColor = $colors[$item->type] ?? 'text-gray-700 bg-gray-50 border-gray-100';
                                    
                                    $labels = [
                                        'sale' => 'Penjualan',
                                        'in' => 'Masuk (Beli)',
                                        'adjustment' => 'Opname',
                                        'return' => 'Retur Jual',
                                        'return-supplier' => 'Retur Beli',
                                    ];
                                    $label = $labels[$item->type] ?? ucfirst($item->type);
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase border {{ $typeColor }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="font-mono text-xs">{{ $item->doc_ref ?: '-' }}</div>
                                @if($item->batch)
                                    <div class="text-[10px] text-gray-400 mt-0.5">Batch: {{ $item->batch->batch_no }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ $item->quantity < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item->quantity > 0 ? '+' : '' }}{{ number_format($item->quantity, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $item->user->name ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                Tidak ada transaksi yang ditemukan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                @include('components.custom-pagination', ['items' => $transactions])
            </div>
        @endif
    </div>
</div>
