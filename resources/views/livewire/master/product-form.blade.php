<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $product_id ? 'Edit Obat / Produk' : 'Tambah Obat Baru' }}
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
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Obat</label>
                    <input type="text" wire:model="name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('name') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori & Satuan -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                    <select wire:model="category_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Satuan</label>
                    <select wire:model="unit_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih Satuan</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>

                <!-- Barcode & Stok Min -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Barcode / Kode Obat</label>
                    <input type="text" wire:model="barcode" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('barcode') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Stok Minimum
                        @if(!$canEditPrice)
                            <span class="text-xs text-red-600 font-normal">(Hanya Super Admin)</span>
                        @endif
                    </label>
                    <input type="number" wire:model="min_stock" 
                        {{ !$canEditPrice ? 'disabled' : '' }}
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$canEditPrice ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    @error('min_stock') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Harga Jual (Rp)
                        @if(!$canEditPrice)
                            <span class="text-xs text-red-600 font-normal">(Hanya Super Admin)</span>
                        @endif
                    </label>
                    <input type="number" wire:model="sell_price" 
                        {{ !$canEditPrice ? 'disabled' : '' }}
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$canEditPrice ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    @error('sell_price') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                </div>
                
                <!-- Deskripsi -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Indikasi</label>
                    <textarea wire:model="description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('products.index') }}" wire:navigate class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold transition duration-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold transition duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ $product_id ? 'Update Obat' : 'Simpan Obat' }}
                </button>
            </div>
        </form>
    </div>
</div>
