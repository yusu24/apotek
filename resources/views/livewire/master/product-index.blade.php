<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Data Obat / Produk
        </h2>

    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <!-- Row 1: Search, Category, and Add Button -->
            <div class="flex flex-wrap gap-2 items-center">
                <!-- Search Box -->
                <div class="relative w-56">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="Cari obat..." 
                        class="block w-full pr-3 py-1.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
                        style="padding-left: 2.75rem !important;">
                </div>

                <!-- Category Filter -->
                <div class="relative">
                    <select wire:model.live="category_id" class="appearance-none block py-1.5 pl-3 pr-8 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 transition" title="Filter Kategori">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Spacer -->
                <div class="hidden md:block flex-1"></div>

                <!-- Buttons Area -->
                <div class="flex gap-2">
                    @can('import_master_data')
                    <button x-data @click="$dispatch('open-import-modal')" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm shrink-0" title="Import Excel">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span class="hidden xl:inline">Import</span>
                    </button>
                    @endcan

                    <a href="{{ route('products.create') }}" wire:navigate class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm shrink-0" title="Tambah Produk">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="hidden xl:inline">Tambah Produk</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Scrollable Table Container -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Min</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 mt-1 uppercase">{{ $product->barcode }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $categoryName = $product->category->name ?? '-';
                                    // Generate consistent color based on category name
                                    $colors = [
                                        ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                        ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                        ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                                        ['bg' => 'bg-pink-100', 'text' => 'text-pink-800'],
                                        ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
                                        ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                        ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                        ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
                                        ['bg' => 'bg-teal-100', 'text' => 'text-teal-800'],
                                        ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800'],
                                    ];
                                    $colorIndex = crc32($categoryName) % count($colors);
                                    $color = $colors[$colorIndex];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium leading-5 rounded-full {{ $color['bg'] }} {{ $color['text'] }}">
                                    {{ $categoryName }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                <span class="text-xs font-normal text-gray-500">/ {{ $product->unit->name ?? 'unit' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $product->min_stock > 0 ? 'text-orange-600' : 'text-gray-500' }}">
                                    {{ $product->min_stock }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <button wire:click="viewHistory({{ $product->id }})" 
                                        class="text-green-600 hover:text-green-900 transition duration-150" title="Riwayat Harga">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('products.edit', $product->id) }}" 
                                        class="text-blue-600 hover:text-blue-900 transition duration-150" title="Edit Obat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    
                                    <button wire:click="deleteProduct({{ $product->id }})" 
                                        wire:confirm="Hapus obat ini akan menghapus seluruh data batch dan stok terkait. Lanjutkan?"
                                        class="text-red-600 hover:text-red-900 transition duration-150" title="Hapus Obat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">Data obat tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $products->links() }}
        </div>
    </div>

    <!-- History Modal -->
    @if($showHistoryModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" x-data x-cloak>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeHistoryModal"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all my-8">
                <!-- Header -->
                <!-- Header -->
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center border-b border-blue-800">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="truncate">Riwayat Harga: {{ $historyProduct->name }}</span>
                    </h3>
                    <button wire:click="closeHistoryModal" class="text-blue-200 hover:text-white transition shrink-0 ml-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[80vh]">
                    <div class="space-y-6">
                        <!-- Sell Price History -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
                                <span class="bg-green-100 text-green-800 p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                                </span>
                                Riwayat Harga Jual
                            </h4>
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Update</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse($sellPriceHistory as $history)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-xs text-gray-900">
                                                    {{ $history['date']->format('d M Y H:i') }}
                                                    <div class="text-[10px] text-gray-500 uppercase">{{ $history['action'] }}</div>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-right font-bold text-gray-900">
                                                    Rp {{ number_format($history['new_price'], 0, ',', '.') }}
                                                    @if($history['action'] == 'updated')
                                                        <div class="text-gray-400 text-[10px] line-through">Rp {{ number_format($history['old_price'], 0, ',', '.') }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-600 truncate max-w-[100px]" title="{{ $history['user'] }}">
                                                    {{ $history['user'] }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-3 py-4 text-center text-xs text-gray-500 italic">Belum ada perubahan harga jual.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Buy Price History -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-800 p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </span>
                                Riwayat Harga Beli (PO)
                            </h4>
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tgl PO</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse($buyPriceHistory as $history)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-xs text-gray-900">
                                                    {{ \Carbon\Carbon::parse($history['date'])->format('d M Y') }}
                                                    <div class="text-[10px] text-blue-600">{{ $history['po_number'] }}</div>
                                                    <div class="text-[10px] text-gray-400">by {{ $history['user'] }}</div>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-600 truncate max-w-[120px]" title="{{ $history['supplier'] }}">
                                                    {{ $history['supplier'] }}
                                                </td>
                                                <td class="px-3 py-2 text-xs text-right font-bold text-gray-900">
                                                    Rp {{ number_format($history['price'], 0, ',', '.') }}
                                                    <div class="text-[10px] text-gray-500">/ {{ $history['unit'] }}</div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-3 py-4 text-center text-xs text-gray-500 italic">Belum ada riwayat pembelian.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Import Modal (Standalone) -->
    <div x-data="{ openImport: false }" @open-import-modal.window="openImport = true" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div x-show="openImport" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openImport = false"></div>

        <div x-show="openImport" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form action="{{ route('import.products') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Import Obat / Produk</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Unduh template Excel, isi data produk Anda, lalu upload kembali di sini.
                                        </p>
                                        
                                        <div class="mb-4">
                                            <a href="{{ route('import.download-product-template') }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download Template Excel
                                            </a>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Upload File Excel</label>
                                            <input type="file" name="file" accept=".xlsx, .xls" required class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-blue-50 file:text-blue-700
                                                hover:file:bg-blue-100
                                            "/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Import Sekarang</button>
                            <button type="button" @click="openImport = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
