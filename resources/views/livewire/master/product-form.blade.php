<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $product_id ? 'Edit Produk' : 'Tambah Produk' }}
        </h2>
        <a href="{{ route('products.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Obat -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.live.debounce.500ms="name" placeholder="Contoh: Paracetamol 500mg" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('name') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori & Satuan -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select wire:model.live="category_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                    <select wire:model.live="unit_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih Satuan</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>

                <!-- Barcode & Stok Min -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kode Barang / Barcode <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="barcode" placeholder="Scan atau ketik kode..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="text-xs text-blue-600 mt-1 italic">
                        *Otomatis: [2 Huruf Kategori] + [2 Huruf Satuan] + [3 Huruf Nama] + [3 Digit No. Urut]
                    </p>
                    @error('barcode') <span class="text-red-500 text-sm italic block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Stok Minimum <span class="text-red-500">*</span>
                        @if(!$canEditPrice)
                            <span class="text-xs text-red-600 font-normal">(Hanya Super Admin)</span>
                        @endif
                    </label>
                    <input type="number" wire:model="min_stock" placeholder="0"
                        {{ !$canEditPrice ? 'disabled' : '' }}
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$canEditPrice ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    @error('min_stock') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Harga Jual (Rp) <span class="text-red-500">*</span>
                        @if(!$canEditPrice)
                            <span class="text-xs text-red-600 font-normal">(Hanya Super Admin)</span>
                        @endif
                    </label>
                    <div x-data="money($wire.entangle('sell_price'))">
                    <input type="text" x-bind="input" placeholder="0"
                        {{ !$canEditPrice ? 'disabled' : '' }}
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$canEditPrice ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    </div>
                    @error('sell_price') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>
                
                <!-- Deskripsi -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Indikasi</label>
                    <textarea wire:model="description" rows="3" placeholder="Masukkan deskripsi produk..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>

                <!-- Image Upload (Permission Restricted) -->
                @if($canUploadImage)
                <div class="col-span-1 md:col-span-2 space-y-4 border-t pt-4 mt-2">
                    <label class="block text-sm font-bold text-gray-700">Gambar Produk</label>
                    
                    <div class="flex items-start gap-6">
                        <!-- Preview -->
                        <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 relative">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif ($current_image_path)
                                <img src="{{ asset('storage/' . $current_image_path) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @endif

                            <div wire:loading wire:target="image" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Controls -->
                        <div class="flex-1 space-y-3">
                            <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                            "/>
                            <p class="text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB.</p>
                            @error('image') <span class="text-red-500 text-sm italic block">{{ $message }}</span> @enderror

                            @if($current_image_path && !$delete_image && !$image)
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="delete_image" wire:model.live="delete_image" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                    <label for="delete_image" class="text-sm font-medium text-red-600">Hapus gambar saat ini</label>
                                </div>
                            @endif

                            @if($delete_image)
                                <p class="text-sm text-red-500 font-bold italic">Gambar akan dihapus saat disimpan.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('products.index') }}" wire:navigate class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold transition duration-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold transition duration-200 flex items-center gap-2">
                    {{ $product_id ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
