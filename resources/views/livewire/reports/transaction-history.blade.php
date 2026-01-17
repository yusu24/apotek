<div class="p-6 space-y-6">
    <div class="no-print flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ __('Riwayat Transaksi') }}
            </h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau semua pergerakan stok dan transaksi.</p>
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
            
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 no-print">
        <div class="flex flex-wrap md:flex-nowrap gap-4 md:gap-6 items-center">
            <!-- Date Filters (Row 1 on Mobile, Left on Desktop) -->
            <div class="flex items-center gap-2 w-full md:w-auto order-1">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Periode</label>
                <div class="flex items-center gap-1.5 flex-1 md:flex-none">
                    <input type="date" wire:model.live="startDate" class="flex-1 md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="date" wire:model.live="endDate" class="flex-1 md:w-36 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <!-- Search (Row 2 on Mobile, Center on Desktop) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-2">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Cari Produk</label>
                <input wire:model.live.debounce.300ms="search" 
                    type="text" placeholder="Nama atau barcode..." 
                    class="w-full md:w-64 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <!-- Reset (Row 2 on Mobile, Right on Desktop) -->
            <div class="order-3 md:ml-auto">
                <button wire:click="resetFilters" class="px-3 md:px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 shadow-sm font-bold text-sm flex items-center justify-center gap-2 transition duration-200" title="Reset semua filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="hidden md:inline">Reset Filter</span>
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
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
