<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Margin Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis profitabilitas berdasarkan harga beli dan harga jual</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportExcel" class="p-2 sm:px-4 sm:py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 shadow-md font-bold flex items-center justify-center gap-2 transition duration-200 text-sm" title="Export Excel">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Excel</span>
            </button>
            <button onclick="window.print()" class="p-2 sm:px-4 sm:py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold flex items-center justify-center gap-2 transition duration-200 text-sm" title="Export PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </button>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm text-gray-500 mb-1">Total Produk</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($statistics['total_products']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-500 mb-1">Margin Positif</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($statistics['products_with_positive_margin']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="text-sm text-gray-500 mb-1">Margin Negatif</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format($statistics['products_with_negative_margin']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-sm text-gray-500 mb-1">Rata-rata Margin</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($statistics['average_margin_percentage'], 1) }}%</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full rounded-lg border-gray-300 text-sm py-2" placeholder="Nama atau barcode...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select wire:model.live="categoryFilter" class="w-full rounded-lg border-gray-300 text-sm py-2">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Margin</label>
                <select wire:model.live="marginFilter" class="w-full rounded-lg border-gray-300 text-sm py-2">
                    <option value="all">Semua</option>
                    <option value="positive">Margin Positif</option>
                    <option value="negative">Margin Negatif</option>
                    <option value="high">Margin Tinggi (>30%)</option>
                    <option value="low">Margin Rendah (<10%)</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="$set('search', ''); $set('categoryFilter', ''); $set('marginFilter', 'all')" class="btn bg-gray-800 text-white hover:bg-gray-700 w-full shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortByColumn('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-1">
                                Produk
                                @if($sortBy === 'name')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th wire:click="sortByColumn('last_buy_price')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center justify-end gap-1">
                                Harga Beli
                                @if($sortBy === 'last_buy_price')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortByColumn('sell_price')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center justify-end gap-1">
                                Harga Jual
                                @if($sortBy === 'sell_price')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortByColumn('margin_amount')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center justify-end gap-1">
                                Margin (Rp)
                                @if($sortBy === 'margin_amount')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortByColumn('margin_percentage')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center justify-end gap-1">
                                Margin (%)
                                @if($sortBy === 'margin_percentage')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->barcode }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $product->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                @if($product->last_buy_price)
                                    <span class="font-medium text-gray-900">Rp {{ number_format($product->last_buy_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                                Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                @if($product->last_buy_price)
                                    <span class="font-bold {{ $product->margin_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Rp {{ number_format($product->margin_amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                @if($product->last_buy_price)
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-bold {{ $product->margin_percentage >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($product->margin_percentage, 1) }}%
                                        </span>
                                        @if($product->margin_percentage > 30)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Tinggi</span>
                                        @elseif($product->margin_percentage < 10 && $product->margin_percentage >= 0)
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Rendah</span>
                                        @elseif($product->margin_percentage < 0)
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Rugi</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-900">Tidak ada data</p>
                                    <p class="text-xs text-gray-500 mt-1">Coba ubah filter pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Note --}}
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Catatan:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>Harga beli diambil dari transaksi pembelian terakhir (Penerimaan Barang)</li>
                    <li>Margin dihitung berdasarkan: <strong>Harga Jual - Harga Beli</strong></li>
                    <li>Persentase margin: <strong>(Margin / Harga Beli) × 100%</strong></li>
                    <li>Produk yang belum pernah dibeli tidak memiliki data margin</li>
                </ul>
            </div>
        </div>
    </div>
</div>
