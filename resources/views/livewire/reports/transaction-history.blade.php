<div class="p-6 space-y-6">
    <div class="no-print">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
            {{ __('Riwayat Transaksi Produk') }}
        </h2>
        <p class="text-sm text-gray-500 font-medium mt-1">Pantau pergerakan stok dan riwayat penjualan setiap produk.</p>
    </div>
            
    <!-- Filter Bar (Always Visible) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Product Search -->
            <div class="w-64 relative">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="searchProduct" type="text" placeholder="Cari produk..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm font-medium">
                </div>

                @if(count($searchresults) > 0)
                    <div class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden max-h-60 overflow-y-auto">
                        @foreach($searchresults as $product)
                            <button wire:click="selectProduct({{ $product->id }})" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-left transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.628.283a2 2 0 01-1.186.12l-1.423-.284a2 2 0 00-1.25.123l-1.033.516a2 2 0 01-1.002.273H3.5"></path></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ $product->barcode }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Date Filters (Always Visible) -->
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="startDate" class="px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-900">
                <span class="text-gray-400 text-sm">—</span>
                <input type="date" wire:model.live="endDate" class="px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-900">
            </div>
            
            <select wire:model.live="perPage" class="px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-gray-700">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            
            <button wire:click="resetFilters" class="btn btn-secondary">
                Reset
            </button>
        </div>

        <!-- Selected Products Tags -->
        @if(count($selectedProducts) > 0)
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($selectedProducts as $p)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-bold text-xs border border-blue-100 dark:border-blue-800">
                        {{ $p['name'] }}
                        <button wire:click="removeProduct({{ $p['id'] }})" class="hover:text-red-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- History Cards (Only show when products selected) -->

            @if(count($selectedProducts) > 0)
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    @foreach($selectedProducts as $p)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 h-fit" wire:key="history-{{ $p['id'] }}">
                            @php $history = $this->getHistory($p['id']); @endphp
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $p['name'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $p['barcode'] }}</p>
                                    <p class="text-[10px] text-blue-600 font-bold mt-1">{{ $history->total() }} transaksi ditemukan</p>
                                </div>
                                <button wire:click="removeProduct({{ $p['id'] }})" class="text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-xs font-medium uppercase text-gray-500 tracking-wider">
                                            <th class="px-4 py-3">Tanggal</th>
                                            <th class="px-4 py-3">Tipe</th>
                                            <th class="px-4 py-3 text-right">Qty</th>
                                            <th class="px-4 py-3">Ref / Batch</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                        @forelse($history as $item)
                                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/40 transition-colors">
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $item->created_at->format('d/m/y H:i') }}
                                                </td>
                                                <td class="px-4 py-4 text-sm">
                                                    @php
                                                        $colors = [
                                                            'sale' => 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
                                                            'in' => 'text-green-600 bg-green-50 dark:bg-green-900/20',
                                                            'adjustment' => 'text-amber-600 bg-amber-50 dark:bg-amber-900/20',
                                                            'return' => 'text-red-600 bg-red-50 dark:bg-red-900/20',
                                                        ];
                                                        $typeColor = $colors[$item->type] ?? 'text-gray-600 bg-gray-50';
                                                    @endphp
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $typeColor }}">
                                                        {{ $item->type }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 text-right text-sm text-gray-900 dark:text-white">
                                                    <span class="font-bold {{ in_array($item->type, ['sale', 'return-supplier']) ? 'text-red-600' : 'text-green-600' }}">
                                                        {{ in_array($item->type, ['sale', 'return-supplier']) ? '-' : '+' }}{{ number_format($item->quantity, 0) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="truncate max-w-[100px]" title="{{ $item->doc_ref }}">
                                                        {{ $item->doc_ref ?: '-' }}
                                                    </div>
                                                    <div class="text-[11px] font-mono text-gray-400">
                                                        {{ $item->batch->batch_no ?? '-' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                                    Belum ada riwayat transaksi.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                
                                <!-- Pagination -->
                                @if(count($history) > 0)
                                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                                        {{ $history->links() }}
                                    </div>
                                @endif
                            </div>
                            @if(count($history) > 0)
                                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50 text-center">
                                    <a href="{{ route('inventory.history', $p['id']) }}" wire:navigate class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-widest">
                                        Lihat Histori Lengkap &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Blank State -->
                <div class="flex flex-col items-center justify-center py-24 text-gray-400 space-y-4">
                    <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="font-medium">Belum ada produk yang dipilih untuk dipantau.</p>
                </div>
            @endif
    </div>
</div>
