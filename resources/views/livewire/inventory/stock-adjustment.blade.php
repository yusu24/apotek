<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Penyesuaian Stok (Stock Adjustment)
        </h2>
        <a href="{{ route('inventory.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if($success_message)
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ $success_message }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <!-- Product Info -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Produk:</p>
                    <p class="font-bold text-gray-900">{{ $product_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Batch No:</p>
                    <p class="font-bold text-gray-900">{{ $batch_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Stok Saat Ini:</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $current_stock }}</p>
                </div>
            </div>
        </div>

        <form wire:submit="save">
            <div class="space-y-6">
                <!-- Adjustment Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Penyesuaian *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="adjustment_type" value="add" class="mr-2">
                            <span class="text-green-600 font-semibold">➕ Tambah Stok</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="adjustment_type" value="subtract" class="mr-2">
                            <span class="text-red-600 font-semibold">➖ Kurangi Stok</span>
                        </label>
                    </div>
                    @error('adjustment_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah {{ $adjustment_type === 'add' ? 'Penambahan' : 'Pengurangan' }} *
                    </label>
                    <input type="number" wire:model="quantity" min="1"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-lg font-bold">
                    @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    
                    @if($quantity)
                        <p class="mt-2 text-sm">
                            Stok setelah penyesuaian: 
                            <span class="font-bold {{ $adjustment_type === 'add' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $adjustment_type === 'add' ? ($current_stock + $quantity) : ($current_stock - $quantity) }}
                            </span>
                        </p>
                    @endif
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penyesuaian *</label>
                    <textarea wire:model="description" rows="3" 
                        placeholder="Contoh: Stok rusak, Stok hilang, Koreksi perhitungan, dll."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            @error('save') 
                <div class="mt-4 bg-red-100 text-red-800 p-3 rounded">{{ $message }}</div>
            @enderror

            <div class="mt-8 flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('inventory.index') }}" wire:navigate
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition text-sm">
                    Batal
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold transition shadow-md flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ $adjustment_type === 'add' ? 'Tambah Stok' : 'Kurangi Stok' }}
                </button>
            </div>
        </form>
    </div>
</div>
