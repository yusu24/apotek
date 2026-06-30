<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Laporan Margin Produk</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="flex p-1 bg-gray-100 rounded-lg mr-2">
                <button wire:click="$set('reportMode', 'potential')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-md transition {{ $reportMode === 'potential' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Potensi Margin
                </button>
                <button wire:click="$set('reportMode', 'realized')" 
                    class="px-3 py-1.5 text-xs font-bold rounded-md transition {{ $reportMode === 'realized' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Margin Realisasi
                </button>
            </div>

            <div class="relative btn-export-dropdown" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="btn btn-export-excel" title="Export">
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
                    <a href="{{ route('pdf.product-margin', ['search' => $search, 'categoryFilter' => $categoryFilter, 'marginFilter' => $marginFilter, 'reportMode' => $reportMode, 'startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total Produk</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($statistics['total_products']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Margin Positif</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($statistics['products_with_positive_margin']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-emerald-600">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total Keuntungan</div>
            <div class="text-2xl font-bold text-emerald-700">Rp {{ number_format($statistics['total_margin_value'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Rata-rata Margin</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($statistics['average_margin_percentage'], 1) }}%</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow border p-4 mb-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 text-gray-700">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1 md:items-end">
                
                @if($reportMode === 'realized')
                <div class="flex gap-2 shrink-0">
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mulai</label>
                        <x-date-picker wire:model.live="startDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white" placeholder="Mulai"></x-date-picker>
                    </div>
                    <div class="flex flex-col">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Selesai</label>
                        <x-date-picker wire:model.live="endDate" class="block w-full py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white" placeholder="Selesai"></x-date-picker>
                    </div>
                </div>
                @endif

                <div class="flex-1 min-w-[150px] w-full">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 rounded-lg border-gray-300 text-sm py-1.5 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm" placeholder="Produk/Barcode...">
                    </div>
                </div>
                <div class="w-full md:w-36 shrink-0">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select wire:model.live="categoryFilter" class="w-full rounded-lg border-gray-300 text-sm py-1.5 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-36 shrink-0">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Akurasi</label>
                    <select wire:model.live="marginFilter" class="w-full rounded-lg border-gray-300 text-sm py-1.5 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                        <option value="all">Semua</option>
                        <option value="positive">Profit</option>
                        <option value="negative">Rugi</option>
                        <option value="high">Tinggi (>30%)</option>
                        <option value="low">Rendah (<10%)</option>
                    </select>
                </div>
            </div>
            
            <div class="shrink-0 flex items-center">
                <button wire:click="$set('search', ''); $set('categoryFilter', ''); $set('marginFilter', 'all'); $set('perPage', 10);" class="text-xs text-blue-600 font-bold hover:text-blue-800 transition py-2" title="Reset Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50 text-gray-500 font-normal uppercase text-xs tracking-widest">
                    <tr>
                        <th wire:click="sortByColumn('name')" class="px-6 py-4 text-left cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-1">
                                Produk
                                @if($sortBy === 'name')
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        
                        @if($reportMode === 'realized')
                            <th wire:click="sortByColumn('total_sold')" class="px-6 py-4 text-center cursor-pointer hover:bg-gray-100">Qty Laku</th>
                        @endif

                        <th wire:click="sortByColumn('avg_buy_price')" class="px-6 py-4 text-right cursor-pointer hover:bg-gray-100">
                            {{ $reportMode === 'potential' ? 'Harga Beli (L)' : 'HPP Rata-rata' }}
                        </th>
                        <th wire:click="sortByColumn('avg_sell_price')" class="px-6 py-4 text-right cursor-pointer hover:bg-gray-100">
                            {{ $reportMode === 'potential' ? 'Harga Jual' : 'Harga Jual Rerata' }}
                        </th>
                        <th wire:click="sortByColumn('margin_amount')" class="px-6 py-4 text-right cursor-pointer hover:bg-gray-100 font-bold">
                            {{ $reportMode === 'potential' ? 'Margin' : 'Total Margin' }}
                        </th>
                        <th wire:click="sortByColumn('margin_percentage')" class="px-6 py-4 text-right cursor-pointer hover:bg-gray-100">%</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $product->barcode }} | {{ $product->category->name ?? '-' }}</div>
                            </td>

                            @if($reportMode === 'realized')
                                <td class="px-6 py-4 text-center text-sm font-bold text-blue-600">
                                    {{ number_format($product->total_sold) }}
                                </td>
                            @endif

                            <td class="px-6 py-4 text-sm text-right text-gray-500">
                                @if($product->avg_buy_price || $product->last_buy_price)
                                    Rp {{ number_format($product->avg_buy_price ?? $product->last_buy_price, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-500 font-medium">
                                Rp {{ number_format($product->avg_sell_price ?? $product->sell_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span class="font-black {{ $product->margin_amount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($product->margin_amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="font-bold text-[10px] px-2 py-0.5 rounded-full {{ $product->margin_percentage >= 30 ? 'bg-emerald-100 text-emerald-700' : ($product->margin_percentage < 10 && $product->margin_percentage >= 0 ? 'bg-amber-100 text-amber-700' : ($product->margin_percentage < 0 ? 'bg-rose-100 text-rose-700' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ number_format($product->margin_percentage, 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table colspan="7" message="Tidak ada data untuk periode ini." />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
            @include('components.custom-pagination', ['items' => $products])
        </div>
    </div>

    {{-- Footer Legend --}}
    <div class="mt-4 flex gap-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2">
        <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Profit Tinggi (>30%)</div>
        <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Tipis (<10%)</div>
        <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-400"></span> Rugi (<0%)</div>
    </div>
</div>
